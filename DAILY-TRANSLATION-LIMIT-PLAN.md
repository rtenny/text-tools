# Plan: Daily Translation Limit for Subscription Clients

## Context

The app is sold at two price points: €50/month (subscription) and €1000 one-off (lifetime). Subscription clients who do heavy translation work can generate API costs that exceed their monthly fee. This plan adds a configurable daily translation limit per project, enforced server-side, with a live usage counter in the UI.

## Architecture Decision

- **Limit is per-project** (not per-user) — the API key and billing are project-scoped.
- **Only `action='translate'` rows are counted** — translations are the bulk cost driver (one per language per user action, potentially 2–3x multiplied).
- **`activity_logs` table is already the source of truth** — a single `COUNT(*)` with a date filter on the indexed `created_at` column is all that's needed.
- **`daily_translation_limit = NULL` means unlimited** — lifetime projects and any project without a configured limit are unaffected.

---

## Step 1: Database Migration

**New file:** `public_html/app/Database/Migrations/2026-02-17-000007_AddSubscriptionToProjects.php`

Add two columns to the `projects` table:
- `subscription_type` ENUM('subscription', 'lifetime') DEFAULT 'subscription'
- `daily_translation_limit` INT UNSIGNED NULL DEFAULT NULL

Existing projects get `subscription_type = 'subscription'` and `daily_translation_limit = NULL` (unlimited) — no users will be blocked until a superadmin explicitly sets a limit.

Run: `cd public_html && php spark migrate`

---

## Step 2: Model Changes

### `public_html/app/Models/ProjectModel.php`
- Add `'subscription_type'` and `'daily_translation_limit'` to `$allowedFields`
- Add validation rules: `subscription_type` in_list, `daily_translation_limit` permit_empty|is_natural_no_zero

### `public_html/app/Models/ActivityLogModel.php`
Add new method:
```php
public function getTodayTranslationCount(int $projectId): int
{
    $result = $this->selectCount('id', 'total')
        ->where('project_id', $projectId)
        ->where('action', 'translate')
        ->where('DATE(created_at)', date('Y-m-d'))
        ->first();
    return (int) ($result['total'] ?? 0);
}
```

---

## Step 3: TranslatorController Changes

**File:** `public_html/app/Controllers/Tools/TranslatorController.php`

### In `translate()` — add limit check before the AI call:
```php
$project    = $projectModel->find($projectId);
$dailyLimit = $project['daily_translation_limit'];   // null = unlimited

if ($dailyLimit !== null) {
    $usedToday = $activityLogModel->getTodayTranslationCount($projectId);
    if ($usedToday >= $dailyLimit) {
        return $this->response->setStatusCode(429)->setJSON([
            'success'       => false,
            'error'         => 'Daily translation limit reached (' . $usedToday . '/' . $dailyLimit . ').',
            'limit_reached' => true,
            'used'          => $usedToday,
            'limit'         => $dailyLimit,
        ]);
    }
}
```

### In `index()` — pass usage data to the view:
```php
$dailyLimit = $project['daily_translation_limit'];
$todayUsed  = ($dailyLimit !== null) ? $activityLogModel->getTodayTranslationCount($projectId) : 0;
$data['dailyLimit']   = $dailyLimit;
$data['todayUsed']    = $todayUsed;
$data['isUnlimited']  = ($dailyLimit === null);
```

### Add `usage()` endpoint (GET, AJAX only):
Returns `{success, used, limit, is_unlimited, limit_reached}` JSON. Add route in `Routes.php` inside tools group:
```php
$routes->get('usage', 'Tools\TranslatorController::usage');
```

---

## Step 4: Superadmin Project Forms

### `public_html/app/Controllers/Superadmin/ProjectsController.php`
In `store()` and `update()`, read and save:
```php
$subscriptionType = $this->request->getPost('subscription_type') ?? 'subscription';
$dailyLimit       = $this->request->getPost('daily_translation_limit');
$data['subscription_type']       = $subscriptionType;
$data['daily_translation_limit'] = ($subscriptionType === 'lifetime' || $dailyLimit === '') ? null : (int) $dailyLimit;
```

### `public_html/app/Views/superadmin/projects/create.php` and `edit.php`
Add a "Subscription Type" select (subscription/lifetime) and a "Daily Translation Limit" number input. The limit field is hidden via JavaScript when "Lifetime" is selected.

### `public_html/app/Views/superadmin/projects/index.php`
Add a "Subscription" column showing the type and limit (e.g., "Sub (50/day)" or "Lifetime").

---

## Step 5: Tools UI — Usage Counter

### `public_html/app/Views/tools/index.php`
Inject limit data into `window.TRANSLATION_LIMIT` JavaScript object (alongside existing CSRF/BASE_URL injection):
```php
window.TRANSLATION_LIMIT = <?= json_encode([
    'used'          => $todayUsed,
    'limit'         => $dailyLimit,
    'is_unlimited'  => $isUnlimited,
    'limit_reached' => (!$isUnlimited && $todayUsed >= ($dailyLimit ?? 0)),
]) ?>;
```

### `public_html/app/Views/tools/tabs/translator.php`
Add a usage progress bar (hidden for unlimited projects):
```
Translations today: 23 / 50  [====        ]
```
Shows in gold (`#D4AF37`), turns red when limit is reached.

Pass the new variables when rendering the partial view:
```php
view('tools/tabs/translator', [..., 'todayUsed' => $todayUsed, 'dailyLimit' => $dailyLimit, 'isUnlimited' => $isUnlimited])
```

---

## Step 6: Frontend JS — Live Counter and Button Disabling

### `public_html/public/js/utils.js`
Modify `translateLanguage()` to:
- On success: call `updateUsageCounter(1)` to increment the live counter
- On `limit_reached: true`: call `setUsageCounterAtLimit(used, limit)` to show final state

Add new utility functions:
- `updateUsageCounter(increment)` — updates `#usage-count` text and `#usage-bar` width/color; calls `disableTranslateButtons()` if limit reached
- `setUsageCounterAtLimit(used, limit)` — sets counter to final state without incrementing
- `disableTranslateButtons()` — disables translator submit, generator translate button, rewriter button
- `showTranslateLimitMessage()` — shows an error message in each tab's error element

### `public_html/public/js/app.js`
After all `init*()` calls, add:
```javascript
if (window.TRANSLATION_LIMIT && window.TRANSLATION_LIMIT.limit_reached) {
    disableTranslateButtons();
    showTranslateLimitMessage();
}
```
This ensures buttons are disabled immediately on page load when the limit is already reached.

---

## Step 7: Admin Dashboard (Optional Enhancement)

### `public_html/app/Controllers/Admin/DashboardController.php`
Add `todayTranslations` and `dailyLimit` to view data.

### `public_html/app/Views/admin/dashboard.php`
Add a "Translations Today" statistics card showing `X / Y` (or just `X` if unlimited). Shows in red if at the limit.

---

## Execution Order

1. Run migration
2. Update `ProjectModel` and `ActivityLogModel`
3. Update `TranslatorController` (enforce + pass data)
4. Update `Routes.php`
5. Update superadmin project forms and controller
6. Update `tools/index.php` and `tools/tabs/translator.php`
7. Update `utils.js` and `app.js`
8. Update admin dashboard (optional)

## Edge Cases

- **Timezone:** `DATE(created_at)` uses PHP server timezone via `date('Y-m-d')`. Ensure PHP and MySQL timezones align.
- **Concurrent requests:** Two simultaneous requests may both pass the check when at limit-1. Acceptable as a soft cap — no transaction needed for this use case.
- **Rewriter auto-translate:** If limit hits mid-chain, rewrite succeeds but individual translation boxes show the limit error. Acceptable UX for an edge case.
- **Existing projects:** All existing projects get `NULL` limit after migration — no users are blocked until superadmin sets a limit.

## Verification

1. Create a test project in superadmin with `subscription_type = subscription` and `daily_translation_limit = 3`
2. Log in as a user in that project
3. Translate text 3 times — counter should update from 0→1→2→3
4. On the 4th attempt, the translate button should be disabled and an error message shown
5. Check the admin dashboard shows `3 / 3` in red
6. Check superadmin project list shows "Sub (3/day)"
7. Change the project to Lifetime — verify no counter appears and translations are unrestricted
