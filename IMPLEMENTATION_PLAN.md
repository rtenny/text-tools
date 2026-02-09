# Multi-Tenant Property Description Generation System - Implementation Plan

## 🎯 Progress Status

**Last Updated**: 2026-02-09

| Phase | Status | Completion Date | Commit |
|-------|--------|----------------|--------|
| Phase 1: Database Foundation | ✅ **COMPLETE** | 2026-02-09 | 553e9a5 |
| Phase 2: Security & Authentication Layer | ✅ **COMPLETE** | 2026-02-09 | d33edd6 |
| Phase 3: AI Service Layer | ✅ **COMPLETE** | 2026-02-09 | a0f8498 |
| Phase 4: Superadmin Dashboard | ✅ **COMPLETE** | 2026-02-09 | df3b013 |
| Phase 5: Admin Dashboard | ✅ **COMPLETE** | 2026-02-09 | - |
| Phase 6: Three-Tab Interface | ⏳ **PENDING** | - | - |
| Phase 7: Auth Views & Dashboard Redirect | ✅ **COMPLETE** | 2026-02-09 | 709c2e6 |

**Next Steps**: Phase 5 complete! Admin dashboard with project-scoped user management ready. Proceed with Phase 6 (Three-Tab Interface)

---

## Context

The goal is to transform the existing proof-of-concept ([all-demos.php](public_html/all-demos.php)) into a production-ready multi-tenant CodeIgniter 4 application. The POC successfully demonstrates AI-powered property description generation, translation, and rewriting using Claude API, but lacks:

- **User management**: No authentication, authorization, or role-based access control
- **Multi-tenancy**: No project isolation or tenant-specific configurations
- **Database persistence**: No data storage, all operations are stateless
- **Flexibility**: Hardcoded languages and single AI provider (Claude only)
- **Security**: API keys stored in .env file, no encryption

The new system will support three user roles (Superadmin, Admin, User), multiple projects with isolated data, both Claude and OpenAI providers, and a configurable language system (starting with EN, DE, ES).

**Key Change from POC**: The POC generates descriptions in German and translates to other languages. The new system will generate in **English first**, then translate to German and Spanish, making English the default language for all operations.

---

## Database Schema

### Tables to Create

**1. projects**
- Stores tenant/project information
- Fields: `id`, `name`, `slug`, `languages` (JSON), `default_ai_provider` (enum: claude/openai), `api_key` (AES encrypted), `is_active`, `created_at`, `updated_at`
- Each project has its own AI provider and encrypted API key
- Languages stored as JSON array: `["en","de","es"]`

**2. users**
- Stores all system users (superadmins, admins, users)
- Fields: `id`, `project_id` (nullable for superadmin), `email`, `password_hash`, `first_name`, `last_name`, `role` (enum: superadmin/admin/user), `is_active`, `last_login_at`, `created_at`, `updated_at`
- Foreign key to projects with CASCADE delete
- Superadmins have NULL project_id

**3. password_resets**
- Tracks password reset tokens
- Fields: `id`, `user_id`, `token` (MD5 hash), `expires_at`, `used_at`, `created_at`
- Token format: `md5(date('Y-m-d H') . $user_id . $secretKey)`
- Expires after 1 hour with grace period

**4. activity_logs** (optional but recommended)
- Tracks AI operations for auditing
- Fields: `id`, `user_id`, `project_id`, `action`, `ai_provider`, `input_language`, `output_languages` (JSON), `tokens_used`, `created_at`

---

## Implementation Approach

### Phase 1: Database Foundation (Migrations & Models)

**Create Migrations** (in order):
1. `2026-02-09-000001_CreateProjectsTable.php`
2. `2026-02-09-000002_CreateUsersTable.php`
3. `2026-02-09-000003_CreatePasswordResetsTable.php`
4. `2026-02-09-000004_CreateActivityLogsTable.php`

**Create Models**:
- `ProjectModel.php` - CRUD for projects, handle JSON language field
- `UserModel.php` - User authentication, password hashing
- `PasswordResetModel.php` - Token management
- `ActivityLogModel.php` - Activity tracking

**Create Seeders**:
- `SuperadminSeeder.php` - Create initial superadmin (email: admin@texttools.local, password: admin123)
- `DemoProjectSeeder.php` - Optional demo project with admin

---

### Phase 2: Security & Authentication Layer

**Create Libraries**:

1. **`EncryptionService.php`**
   - Wrapper for CodeIgniter's Encryption service
   - Methods: `encrypt(string $plaintext): string`, `decrypt(string $ciphertext): string`
   - Used to encrypt/decrypt project API keys
   - Uses encryption key from .env file

2. **`PasswordResetService.php`**
   - Generate MD5 tokens: `md5(date('Y-m-d H') . $userId . $appKey)`
   - Validate tokens with 1-hour grace period (check current hour, then previous hour)
   - Methods: `generateToken()`, `validateToken()`, `createResetLink()`

**Create Filters** (Middleware):

1. **`AuthFilter.php`**
   - Check if user is logged in (session has `user_id`)
   - Redirect to /login if not authenticated

2. **`SuperadminFilter.php`**
   - Check if logged-in user has role `superadmin`
   - Deny access if not

3. **`AdminFilter.php`**
   - Check if user has role `admin` OR `superadmin`
   - Deny access if neither

4. **`TenantFilter.php`**
   - Inject project context into request for admins/users
   - Sets `$request->projectId` from session

**Register Filters** in `Config/Filters.php`:
```php
public array $aliases = [
    // ... existing filters
    'auth' => \App\Filters\AuthFilter::class,
    'superadmin' => \App\Filters\SuperadminFilter::class,
    'admin' => \App\Filters\AdminFilter::class,
    'tenant' => \App\Filters\TenantFilter::class,
];

public array $filters = [
    'auth' => ['before' => ['superadmin/*', 'admin/*', 'tools/*']],
    'superadmin' => ['before' => ['superadmin/*']],
    'admin' => ['before' => ['admin/*']],
    'tenant' => ['before' => ['admin/*', 'tools/*']],
];
```

**Create Auth Controllers**:

1. **`Auth/LoginController.php`**
   - Display login form
   - Authenticate user (verify email/password)
   - Store user info in session: `user_id`, `email`, `role`, `project_id`

2. **`Auth/LogoutController.php`**
   - Destroy session and redirect to login

3. **`Auth/PasswordResetController.php`**
   - Verify password reset token
   - Display password reset form
   - Update user password and mark token as used

**Create Helper** `auth_helper.php`:
```php
function current_user(): ?array { /* return user from session */ }
function is_superadmin(): bool { /* check role */ }
function is_admin(): bool { /* check role */ }
function user_project_id(): ?int { /* return project_id from session */ }
```

**Update `BaseController.php`**:
- Uncomment session initialization
- Load auth_helper by default
- Add protected properties: `$session`, `$currentUser`

---

### Phase 3: AI Service Layer

**Create AI Service Interface & Implementations**:

Directory: `app/Libraries/AIService/`

1. **`AIServiceInterface.php`**
   ```php
   interface AIServiceInterface {
       public function generateDescription(array $propertyData, string $targetLanguage): string;
       public function translateText(string $text, string $sourceLanguage, string $targetLanguage): string;
       public function rewriteText(string $text, string $language): string;
   }
   ```

2. **`ClaudeService.php`**
   - **Refactor from [all-demos.php](public_html/all-demos.php)** lines 34-109 (generateDescription)
   - **Key Changes**:
     - Change prompts from German to English generation
     - Line 36-64: Update prompt to generate in English instead of German
     - Accept target language parameter (de, es)
     - Use project's configured languages
   - Implement `translateText()` from lines 174-240 of POC
   - Implement `rewriteText()` from lines 111-172 of POC
   - Use model: `claude-sonnet-4-5-20250929`
   - Endpoint: `https://api.anthropic.com/v1/messages`
   - Headers: `x-api-key`, `anthropic-version: 2023-06-01`

3. **`OpenAIService.php`**
   - Implement same interface using OpenAI API
   - Model: `gpt-4o` or configurable
   - Endpoint: `https://api.openai.com/v1/chat/completions`
   - Header: `Authorization: Bearer {api_key}`
   - Similar prompt engineering for consistency with Claude

4. **`AIServiceFactory.php`**
   - Factory pattern to create correct service instance
   - Method: `create(int $projectId): AIServiceInterface`
   - Flow:
     1. Load project from database
     2. Decrypt API key using EncryptionService
     3. Check `default_ai_provider` field
     4. Return new ClaudeService($apiKey) OR new OpenAIService($apiKey)

---

### Phase 4: Superadmin Dashboard

**Controllers**:

1. **`Superadmin/DashboardController.php`**
   - Display overview: total projects, total admins, recent activity
   - Protected by `auth` + `superadmin` filters

2. **`Superadmin/ProjectsController.php`**
   - CRUD operations for projects
   - Methods: `index()` (list), `create()` (form), `store()` (save), `edit($id)`, `update($id)`, `delete($id)`
   - On create/update: Encrypt API key before saving
   - Generate slug from name (e.g., "Demo Project" → "demo-project")

3. **`Superadmin/UsersController.php`**
   - Manage project admins (role: admin)
   - Methods: `index()`, `create()`, `store()`
   - Method: `generatePasswordResetLink($userId)` - Display modal with copy button

**Views**:

- Layout: `layouts/superadmin.php` - Navigation, sidebar, logout button
- `superadmin/dashboard.php`
- `superadmin/projects/index.php` - Table with edit/delete actions
- `superadmin/projects/create.php` - Form: name, AI provider (dropdown), API key (password field), languages (fixed: EN, DE, ES)
- `superadmin/projects/edit.php`
- `superadmin/users/index.php`
- `superadmin/users/create.php` - Form: email, first name, last name, project (dropdown), role (admin)

**Routes** in `Config/Routes.php`:
```php
$routes->group('superadmin', ['filter' => ['auth', 'superadmin']], function($routes) {
    $routes->get('dashboard', 'Superadmin\DashboardController::index');
    $routes->get('projects', 'Superadmin\ProjectsController::index');
    $routes->get('projects/create', 'Superadmin\ProjectsController::create');
    $routes->post('projects/create', 'Superadmin\ProjectsController::store');
    $routes->get('projects/edit/(:num)', 'Superadmin\ProjectsController::edit/$1');
    $routes->post('projects/edit/(:num)', 'Superadmin\ProjectsController::update/$1');
    $routes->post('projects/delete/(:num)', 'Superadmin\ProjectsController::delete/$1');
    $routes->get('users', 'Superadmin\UsersController::index');
    $routes->get('users/create', 'Superadmin\UsersController::create');
    $routes->post('users/create', 'Superadmin\UsersController::store');
    $routes->get('users/password-reset-link/(:num)', 'Superadmin\UsersController::generatePasswordResetLink/$1');
});
```

---

### Phase 5: Admin Dashboard (Project-Scoped)

**Controllers**:

1. **`Admin/DashboardController.php`**
   - Display project overview: total users, recent activity
   - Scoped to admin's project only (use TenantFilter)

2. **`Admin/UsersController.php`**
   - Manage users within admin's project only (role: user)
   - Methods: `index()`, `create()`, `store()`
   - Filter users by `project_id = session('project_id')`
   - Method: `generatePasswordResetLink($userId)` - Display modal with copy button

**Views**:

- Layout: `layouts/admin.php`
- `admin/dashboard.php`
- `admin/users/index.php`
- `admin/users/create.php` - Form: email, first name, last name (project auto-assigned)

**Routes**:
```php
$routes->group('admin', ['filter' => ['auth', 'admin', 'tenant']], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('users', 'Admin\UsersController::index');
    $routes->get('users/create', 'Admin\UsersController::create');
    $routes->post('users/create', 'Admin\UsersController::store');
    $routes->get('users/password-reset-link/(:num)', 'Admin\UsersController::generatePasswordResetLink/$1');
});
```

---

### Phase 6: Three-Tab Interface for Users

**Controllers**:

1. **`Tools/TranslatorController.php`**
   - Display three-tab interface (Tab 1 active by default)
   - AJAX endpoint: `translate()`
     - Accept: `source_text` (English)
     - Get project's AI service via AIServiceFactory
     - Translate to DE and ES
     - Return JSON: `{success: true, translations: {de: "...", es: "..."}}`

2. **`Tools/RewriterController.php`**
   - AJAX endpoint: `rewrite()`
     - Accept: `original_text` (English)
     - Rewrite in English using AI
     - Auto-translate rewritten text to DE and ES
     - Return JSON: `{success: true, rewritten: "...", translations: {de: "...", es: "..."}}`

3. **`Tools/GeneratorController.php`**
   - AJAX endpoint: `generate()`
     - Accept: form fields (location, type, beds, baths, living_area, plot_size, features)
     - Generate English property description using AI
     - Auto-translate to DE and ES
     - Return JSON: `{success: true, description: "...", translations: {de: "...", es: "..."}}`

**Views**:

- Layout: `layouts/user.php` - Simple header with logout
- `tools/index.php` - **Single-page interface with 3 tabs**

**Tab 1: Multilingual Translator**
- Textarea: "English Source Text" (200px height)
- Button: "Translate to All Languages"
- Output Grid (2 columns):
  - German translation (readonly, flag 🇩🇪, copy button)
  - Spanish translation (readonly, flag 🇪🇸, copy button)
- Loading spinners for each output box

**Tab 2: Unique Rewriter**
- Grid (2 columns):
  - Left: "Original English Description" (textarea)
  - Right: "Rewritten English Description" (readonly, spinner)
- Button: "Rewrite & Translate"
- Output Grid (2 columns):
  - German translation (readonly, copy button)
  - Spanish translation (readonly, copy button)

**Tab 3: Property Description Generator**
- Form (2-column grid):
  - Location (text input)
  - Property Type (dropdown: Villa, Apartment, Finca, Townhouse, Penthouse)
  - Bedrooms (dropdown: 1-5)
  - Bathrooms (dropdown: 1-3)
  - Living Area m² (number input)
  - Plot Size m² (number input)
  - Features (textarea: "Sea view, pool, garage...")
- Button: "Generate Description"
- Output Grid (3 boxes):
  - English description (readonly, large)
  - German translation (readonly, copy button)
  - Spanish translation (readonly, copy button)

**Frontend JavaScript** (`public/js/app.js`):
- **Reuse from [all-demos.php](public_html/all-demos.php)** lines 892-1166:
  - Tab switching (lines 894-907)
  - AJAX handlers (lines 909-1165)
  - Loading spinners (toggle `.loading` class)
  - Copy-to-clipboard functionality
- **Changes needed**:
  - Update endpoint URLs: `/tools/translate`, `/tools/rewrite`, `/tools/generate`
  - Update language keys from POC to match new structure
  - Add CSRF token to AJAX requests (CodeIgniter requirement)

**Styling** (`public/css/app.css`):
- **Reuse from [all-demos.php](public_html/all-demos.php)** lines 331-636
- Use Tailwind CSS via CDN for development: `<script src="https://cdn.tailwindcss.com"></script>`
- **Note**: CDN loads entire Tailwind (~3MB). Before production, switch to npm build process for optimized CSS (<10KB)
- Or extract inline styles to external CSS file

**Routes**:
```php
$routes->group('tools', ['filter' => ['auth', 'tenant']], function($routes) {
    $routes->get('/', 'Tools\TranslatorController::index');
    $routes->post('translate', 'Tools\TranslatorController::translate');
    $routes->post('rewrite', 'Tools\RewriterController::rewrite');
    $routes->post('generate', 'Tools\GeneratorController::generate');
});
```

---

### Phase 7: Auth Views & Role-Based Dashboard Redirect

**Auth Views**:

- Layout: `layouts/guest.php` - Minimal layout for login/password reset
- `auth/login.php` - Email/password form
- `auth/password_reset_form.php` - New password form with token validation

**Dashboard Redirect Controller** (`DashboardController.php`):
```php
public function index() {
    $role = session()->get('role');
    switch ($role) {
        case 'superadmin': return redirect()->to('/superadmin/dashboard');
        case 'admin': return redirect()->to('/admin/dashboard');
        case 'user': return redirect()->to('/tools');
        default: return redirect()->to('/login');
    }
}
```

**Public Routes**:
```php
$routes->get('/', 'Home::index'); // Welcome page or redirect to /login
$routes->get('/login', 'Auth\LoginController::index');
$routes->post('/login', 'Auth\LoginController::authenticate');
$routes->get('/logout', 'Auth\LogoutController::index');
$routes->get('/password-reset/(:num)/(:segment)', 'Auth\PasswordResetController::verify/$1/$2');
$routes->post('/password-reset/(:num)/(:segment)', 'Auth\PasswordResetController::reset/$1/$2');
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
```

---

## Critical Files & Key Implementation Details

### 1. Encryption (API Keys)

**File**: `app/Libraries/EncryptionService.php`

```php
class EncryptionService {
    protected $encrypter;

    public function __construct() {
        $this->encrypter = \Config\Services::encrypter();
    }

    public function encrypt(string $plaintext): string {
        return base64_encode($this->encrypter->encrypt($plaintext));
    }

    public function decrypt(string $ciphertext): string {
        return $this->encrypter->decrypt(base64_decode($ciphertext));
    }
}
```

**Usage in ProjectsController**:
```php
$encryptionService = new \App\Libraries\EncryptionService();
$encryptedKey = $encryptionService->encrypt($this->request->getPost('api_key'));
// Save $encryptedKey to database
```

**Usage in AIServiceFactory**:
```php
$encryptionService = new \App\Libraries\EncryptionService();
$apiKey = $encryptionService->decrypt($project['api_key']);
// Pass $apiKey to ClaudeService or OpenAIService
```

**Security Notes**:
- Encryption key already configured in .env
- Never log or display decrypted API keys
- Use HTTPS in production

### 2. Password Reset (MD5 Token with 1-Hour Validity)

**File**: `app/Libraries/PasswordResetService.php`

```php
class PasswordResetService {
    private string $appKey;

    public function __construct() {
        $this->appKey = config('Encryption')->key;
    }

    public function generateToken(int $userId): string {
        $hour = date('Y-m-d H');
        return md5($hour . $userId . $this->appKey);
    }

    public function validateToken(int $userId, string $token): bool {
        // Check current hour
        if ($this->generateToken($userId) === $token) {
            return true;
        }
        // Check previous hour (grace period)
        $previousHour = date('Y-m-d H', strtotime('-1 hour'));
        $previousToken = md5($previousHour . $userId . $this->appKey);
        return $previousToken === $token;
    }

    public function createResetLink(int $userId): string {
        $token = $this->generateToken($userId);
        return base_url("password-reset/{$userId}/{$token}");
    }
}
```

**Flow**:
1. Admin clicks "Generate Password Reset Link" for a user
2. Display modal with link and copy button (no email sending)
3. User visits link
4. Validate token (check current hour, then previous hour)
5. Show password form if valid
6. Update password and mark token as used

### 3. AI Provider Toggling

**File**: `app/Libraries/AIService/AIServiceFactory.php`

```php
class AIServiceFactory {
    public static function create(int $projectId): AIServiceInterface {
        $projectModel = new ProjectModel();
        $project = $projectModel->find($projectId);

        if (!$project) {
            throw new \RuntimeException('Project not found');
        }

        $encryptionService = new \App\Libraries\EncryptionService();
        $apiKey = $encryptionService->decrypt($project['api_key']);

        switch ($project['default_ai_provider']) {
            case 'claude':
                return new ClaudeService($apiKey);
            case 'openai':
                return new OpenAIService($apiKey);
            default:
                throw new \RuntimeException('Invalid AI provider');
        }
    }
}
```

**Usage in Controllers**:
```php
$projectId = session()->get('project_id');
$aiService = AIServiceFactory::create($projectId);
$translation = $aiService->translateText($text, 'en', 'de');
```

### 4. Translation Workflow (English → German/Spanish)

**Change from POC**:
- POC: Generate in German → Translate to EN/RU/CZ/ES
- **New**: Generate in English → Translate to DE/ES

**Implementation in ClaudeService**:

```php
public function generateDescription(array $propertyData, string $targetLanguage): string {
    // Updated prompt to generate in English
    $prompt = "You are a professional real estate agent. Generate an engaging property description in English based on the following details...";
    // Generate in English first
    $englishDescription = $this->callClaudeAPI($prompt);

    // If targetLanguage is not 'en', translate
    if ($targetLanguage !== 'en') {
        return $this->translateText($englishDescription, 'en', $targetLanguage);
    }

    return $englishDescription;
}

public function translateText(string $text, string $sourceLanguage, string $targetLanguage): string {
    $languageMap = [
        'de' => 'German',
        'es' => 'Spanish (European)',
        'en' => 'English'
    ];

    $prompt = "Translate the following property description from {$languageMap[$sourceLanguage]} to {$languageMap[$targetLanguage]}...";
    // Call API and return translation
}
```

**In Controllers** (TranslatorController, RewriterController, GeneratorController):

```php
// Get project's configured languages
$project = $projectModel->find($projectId);
$languages = json_decode($project['languages'], true); // ["en","de","es"]

$translations = [];
foreach ($languages as $lang) {
    if ($lang !== 'en') { // Skip English source
        $translations[$lang] = $aiService->translateText($sourceText, 'en', $lang);
    }
}

return $this->response->setJSON([
    'success' => true,
    'translations' => $translations
]);
```

---

## Existing Code to Reuse

### From [all-demos.php](public_html/all-demos.php):

1. **ClaudeService Implementation**:
   - Lines 34-109: `generateDescription()` - Update prompts for English generation
   - Lines 111-172: `rewriteGermanText()` - Adapt for English rewriting
   - Lines 174-240: `translateText()` - Use as-is with language parameter updates

2. **Frontend JavaScript**:
   - Lines 892-1166: Tab switching, AJAX handlers, spinner logic, error handling
   - Update API endpoints and add CSRF tokens

3. **CSS Styling**:
   - Lines 331-636: Complete styling for three-tab interface, forms, buttons, spinners
   - Extract to `public/css/app.css`

4. **HTML Structure**:
   - Lines 640-889: Three-tab interface layout
   - Adapt to CI4 views with layouts and includes

### From Existing CI4 Setup:

- `BaseController.php`: Extend for all controllers, uncomment session loading
- `Filters.php`: Add custom filter aliases
- `Database.php`: Already configured for MySQLi
- Encryption service: Available via `\Config\Services::encrypter()`

---

## Implementation Sequence

### Week 1: Foundation (Days 1-3)

**✅ Day 1: Database & Models** (COMPLETED - 2026-02-09)
- ✅ Create 4 migrations (projects, users, password_resets, activity_logs)
- ✅ Run migrations: `php spark migrate`
- ✅ Create 4 models (ProjectModel, UserModel, PasswordResetModel, ActivityLogModel)
- ✅ Create 2 seeders (SuperadminSeeder, DemoProjectSeeder)
- ✅ Run seeders: `php spark db:seed SuperadminSeeder`

**✅ Day 2-3: Auth System** (COMPLETED - 2026-02-09)
- ✅ Create EncryptionService and PasswordResetService libraries
- ✅ Create 4 filters (AuthFilter, SuperadminFilter, AdminFilter, TenantFilter)
- ✅ Register filters in Config/Filters.php
- ⏳ Create Auth controllers (LoginController, LogoutController, PasswordResetController) - *Deferred to Phase 7*
- ⏳ Create auth views (login, password reset form) - *Deferred to Phase 7*
- ✅ Create auth_helper with utility functions
- ✅ Update BaseController to load session and helpers
- ⏳ Test login/logout flow - *Will test after controllers/views created*

### Week 2: Admin Interfaces (Days 4-7)

**Day 4-5: Superadmin Dashboard**
- Create Superadmin controllers (DashboardController, ProjectsController, UsersController)
- Create superadmin views (dashboard, projects CRUD, users CRUD)
- Implement encryption for API keys in ProjectsController
- Implement password reset link generation with modal
- Add routes for superadmin section
- Test project creation and admin creation

**Day 6-7: Admin Dashboard**
- Create Admin controllers (DashboardController, UsersController)
- Create admin views (dashboard, users CRUD)
- Implement TenantFilter for project scoping
- Test admin user management within project

### Week 3: AI Integration & Tools (Days 8-12)

**✅ Day 8-9: AI Service Layer** (COMPLETED - 2026-02-09)
- ✅ Create AIServiceInterface
- ✅ Refactor ClaudeService from all-demos.php
  - ✅ Update generateDescription to use English prompts
  - ✅ Adapt translateText for EN→DE/ES
  - ✅ Adapt rewriteText for English
- ✅ Implement OpenAIService (using GPT-5.2 model)
- ✅ Create AIServiceFactory
- ⏳ Test both providers with real API keys - *Will test after UI is built*

**Day 10-11: Three-Tab Interface**
- Create Tools controllers (TranslatorController, RewriterController, GeneratorController)
- Create tools/index.php view with three tabs
- Extract CSS from all-demos.php to public/css/app.css
- Extract JavaScript from all-demos.php to public/js/app.js
- Update AJAX endpoints and add CSRF tokens
- Implement copy-to-clipboard functionality
- Test all three tabs with real AI calls

**Day 12: Integration & Testing**
- Create DashboardController for role-based redirect
- Test complete user flows:
  - Superadmin creates project → Creates admin → Admin creates user → User accesses tools
  - Password reset link generation and validation
  - AI provider switching (Claude ↔ OpenAI)
  - Multi-language translation (EN→DE/ES)
- Fix bugs and edge cases

### Week 4: Polish & Documentation (Day 13)

**Day 13: Final Polish**
- Add activity logging (optional)
- Responsive design improvements
- Error handling and validation messages
- Update README with setup instructions
- Document environment variables
- Production deployment checklist

---

## Verification & Testing

### Manual Testing Checklist

**Authentication & Authorization**:
- [ ] Login with superadmin credentials (admin@texttools.local / admin123)
- [ ] Logout successfully clears session
- [ ] Unauthenticated users redirected to /login
- [ ] Superadmin can access /superadmin/* routes
- [ ] Admin can access /admin/* routes but not /superadmin/*
- [ ] User can access /tools/* routes only
- [ ] Role-based dashboard redirect works (/dashboard → correct section)

**Superadmin Features**:
- [ ] Create new project with encrypted API key
- [ ] Edit project (API key remains encrypted)
- [ ] Delete project (cascades to users)
- [ ] Create project admin with role selection
- [ ] Generate password reset link for admin (copy button works)
- [ ] List all projects and admins

**Admin Features**:
- [ ] Admin only sees users from their own project
- [ ] Create new user within project (auto-assigned project_id)
- [ ] Generate password reset link for user
- [ ] Cannot access other projects' data (tenant isolation)

**Password Reset**:
- [ ] Generated link is valid for current hour
- [ ] Link expires after 1 hour
- [ ] Grace period works (link valid for previous hour)
- [ ] After password reset, login with new password succeeds
- [ ] Token marked as used and cannot be reused

**AI Integration**:
- [ ] Switch project AI provider (claude ↔ openai)
- [ ] Both providers generate English descriptions correctly
- [ ] Translations work for both DE and ES
- [ ] API key decryption works in AIServiceFactory
- [ ] Error handling for invalid API keys

**Three-Tab Interface**:
- [ ] **Tab 1 (Translator)**: Enter English text → Translate to DE/ES → Copy buttons work
- [ ] **Tab 2 (Rewriter)**: Enter English text → Rewrite in EN → Auto-translate to DE/ES
- [ ] **Tab 3 (Generator)**: Fill form → Generate EN description → Auto-translate to DE/ES
- [ ] Loading spinners show/hide correctly
- [ ] Error messages display for validation failures
- [ ] AJAX requests include CSRF token

**Multi-Tenancy**:
- [ ] Create 2 projects with different users
- [ ] User in Project A cannot see data from Project B
- [ ] Each project uses its own AI provider and API key
- [ ] Languages configured per project work correctly

### Unit Tests (Optional)

**Models**:
- Test CRUD operations for ProjectModel, UserModel
- Test JSON encoding/decoding for languages field
- Test password hashing in UserModel

**Libraries**:
- Test EncryptionService: encrypt → decrypt returns original value
- Test PasswordResetService: token generation and validation
- Test AIServiceFactory: correct service instantiation based on provider

**Filters**:
- Test AuthFilter redirects unauthenticated users
- Test SuperadminFilter denies non-superadmin access
- Test TenantFilter injects correct project_id

### Integration Tests (Optional)

**Feature Tests**:
- Test login flow with valid/invalid credentials
- Test password reset flow end-to-end
- Test AJAX endpoints return correct JSON
- Test multi-tenant data isolation (queries filtered by project_id)

---

## Environment Configuration

### Required .env Variables

```ini
# Application
CI_ENVIRONMENT = development # Change to 'production' for live
app.baseURL = 'http://text-tools.local/'

# Database
database.default.hostname = localhost
database.default.database = texttools
database.default.username = texttools
database.default.password = texttools

# Encryption (already configured)
encryption.key = hex2bin:34e052b69d64449ea458ede9bc74d65628f4fe12c9ef1f1a3e5b6b1b0d0f75ff

# Session
app.sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler'
app.sessionCookieName = 'texttools_session'
app.sessionExpiration = 7200 # 2 hours
```

### Production Checklist

- [ ] Set `CI_ENVIRONMENT = production`
- [ ] Change session driver to DatabaseHandler for multi-server setups
- [ ] Enable CSRF protection (uncomment in Config/Filters.php)
- [ ] Use HTTPS (forcehttps filter already enabled)
- [ ] Disable debug toolbar (automatic in production mode)
- [ ] Set strong database password
- [ ] Rotate encryption key if needed
- [ ] **Switch from Tailwind CDN to npm build** (see below)
- [ ] Configure email for password resets (future enhancement)
- [ ] Set up backups for database
- [ ] Monitor API usage and costs

#### Tailwind CSS Production Setup

**Current**: Using Tailwind CDN (~3MB, loads entire framework, development only)

**Production Setup**:
```bash
# Install Tailwind CSS
npm install -D tailwindcss

# Initialize config
npx tailwindcss init

# Create tailwind.config.js
module.exports = {
  content: ["./app/Views/**/*.php"],
  theme: {
    extend: {},
  },
  plugins: [],
}

# Create input CSS file (app/Assets/css/input.css)
@tailwind base;
@tailwind components;
@tailwind utilities;

# Add build scripts to package.json
"scripts": {
  "build:css": "tailwindcss -i ./app/Assets/css/input.css -o ./public/css/app.css --minify",
  "watch:css": "tailwindcss -i ./app/Assets/css/input.css -o ./public/css/app.css --watch"
}

# Build for production
npm run build:css
```

**Update layouts** to use built CSS:
```php
<!-- Replace CDN script -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- With built CSS -->
<link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
```

**Result**: Optimized CSS file (<10KB) with only used classes, significantly faster page loads

---

## Future Enhancements

1. **Email Integration**: Send password reset links via email instead of manual copy
2. **API Usage Tracking**: Track token usage and costs per project in activity_logs
3. **Configurable Languages**: Allow superadmin to configure languages per project (not hardcoded EN/DE/ES)
4. **Batch Processing**: Upload CSV of properties, generate descriptions in bulk
5. **Templates**: Save and reuse property description templates
6. **User Preferences**: Save preferred AI settings per user
7. **API Endpoints**: Expose REST API for external integrations
8. **Analytics Dashboard**: Project-level statistics (generations, translations, costs)
9. **Version Control**: Track changes to generated descriptions
10. **Multi-Factor Authentication**: Add 2FA for superadmin accounts

---

## Summary

This implementation transforms the proof-of-concept single-file application into a production-ready multi-tenant system with:

- **Secure authentication** with role-based access control (3 roles)
- **Multi-tenancy** with project isolation and encrypted API keys
- **Flexible AI integration** supporting both Claude and OpenAI
- **English-first workflow** (generate/rewrite in EN, translate to DE/ES)
- **Scalable architecture** using CodeIgniter 4 MVC patterns
- **User-friendly interface** with three-tab design and AJAX interactions

The plan prioritizes reusing existing code from the POC while properly structuring it within the CodeIgniter 4 framework. Estimated implementation time: 13 days for a single developer working full-time.
