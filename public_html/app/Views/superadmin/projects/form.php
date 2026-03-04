<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<?php
$isEdit  = $isEdit ?? false;
$project = $project ?? [];
?>

<div class="mb-6">
    <a href="<?= base_url('superadmin/projects') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Projects
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h3 class="text-xl font-semibold text-white mb-6">
            <?= $isEdit ? 'Edit Project: ' . esc($project['name']) : 'Create New Project' ?>
        </h3>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= $isEdit ? base_url('superadmin/projects/edit/' . $project['id']) : base_url('superadmin/projects/create') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Project Name -->
            <div class="mb-4">
                <label for="name" class="form-label">Project Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input"
                       value="<?= old('name', $project['name'] ?? '') ?>"
                       placeholder="e.g., Demo Project"
                       required>
                <p class="text-xs text-gray-500 mt-1">A unique name for this project. Slug will be auto-generated.</p>
            </div>

            <!-- AI Provider -->
            <div class="mb-4">
                <label for="default_ai_provider" class="form-label">AI Provider *</label>
                <select id="default_ai_provider" name="default_ai_provider" class="form-select" required>
                    <option value="">Select AI Provider</option>
                    <option value="claude" <?= old('default_ai_provider', $project['default_ai_provider'] ?? '') === 'claude' ? 'selected' : '' ?>>
                        Anthropic Claude (claude-sonnet-4-5-20250929)
                    </option>
                    <option value="openai" <?= old('default_ai_provider', $project['default_ai_provider'] ?? '') === 'openai' ? 'selected' : '' ?>>
                        OpenAI GPT (gpt-5.2)
                    </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Choose which AI service to use for this project.</p>
            </div>

            <!-- API Key -->
            <div class="mb-4">
                <label for="api_key" class="form-label">API Key<?= $isEdit ? '' : ' *' ?></label>
                <input type="password"
                       id="api_key"
                       name="api_key"
                       class="form-input font-mono"
                       placeholder="<?= $isEdit ? 'Leave empty to keep current API key' : 'sk-ant-... or sk-proj-...' ?>"
                       <?= $isEdit ? '' : 'required' ?>>
                <p class="text-xs text-gray-500 mt-1">
                    <?= $isEdit
                        ? 'Only provide a new API key if you want to change it. Current key is encrypted and cannot be displayed.'
                        : 'API key will be encrypted before storage. Never logged or displayed after saving.' ?>
                </p>
            </div>

            <?php if ($isEdit): ?>
            <!-- Status (edit only) -->
            <div class="mb-6">
                <label for="is_active" class="form-label">Project Status *</label>
                <select id="is_active" name="is_active" class="form-select" required>
                    <option value="1" <?= old('is_active', $project['is_active']) == 1 ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= old('is_active', $project['is_active']) == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Inactive projects cannot be accessed by their users.</p>
            </div>
            <?php endif; ?>

            <!-- Subscription Type -->
            <div class="mb-4">
                <label for="subscription_type" class="form-label">Subscription Type *</label>
                <select id="subscription_type" name="subscription_type" class="form-select" required
                        onchange="toggleLimitField(this.value)">
                    <option value="subscription" <?= old('subscription_type', $project['subscription_type'] ?? 'subscription') === 'subscription' ? 'selected' : '' ?>>
                        Subscription (€/month)
                    </option>
                    <option value="lifetime" <?= old('subscription_type', $project['subscription_type'] ?? 'subscription') === 'lifetime' ? 'selected' : '' ?>>
                        Lifetime (€999 one-off)
                    </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Lifetime projects have no daily translation limit.</p>
            </div>

            <!-- Daily Translation Limit -->
            <div class="mb-6" id="limit-field">
                <label for="daily_translation_limit" class="form-label">Daily Translation Limit</label>
                <input type="number"
                       id="daily_translation_limit"
                       name="daily_translation_limit"
                       class="form-input"
                       value="<?= old('daily_translation_limit', $project['daily_translation_limit'] ?? ($isEdit ? '' : '50')) ?>"
                       min="1"
                       placeholder="e.g., 50">
                <p class="text-xs text-gray-500 mt-1">Max AI translation calls per day. Leave blank for unlimited.</p>
            </div>

            <!-- Languages -->
            <div class="mb-6">
                <label class="form-label">Supported Languages</label>
                <div class="p-4 bg-[#1A1C1E] rounded-lg border border-[#3a3d42]">
                    <div class="flex space-x-4">
                        <?php if ($isEdit && !empty($project['languages'])): ?>
                            <?php
                            $languages  = is_string($project['languages']) ? json_decode($project['languages'], true) : $project['languages'];
                            $langLabels = ['en' => 'English (EN)', 'de' => 'German (DE)', 'es' => 'Spanish (ES)'];
                            foreach ($languages as $lang): ?>
                                <span class="badge badge-info"><?= $langLabels[$lang] ?? strtoupper($lang) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="badge badge-info">English (EN)</span>
                            <span class="badge badge-info">German (DE)</span>
                            <span class="badge badge-info">Spanish (ES)</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!$isEdit): ?>
                    <p class="text-xs text-gray-500 mt-2">
                        Fixed language set. English is the source language, with translations to German and Spanish.
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="btn-primary flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> <?= $isEdit ? 'Update Project' : 'Create Project' ?>
                </button>
                <a href="<?= base_url('superadmin/projects') ?>" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php if ($isEdit): ?>

    <!-- Town Assignment Section -->
    <div class="card p-6 mt-6">
        <h3 class="text-xl font-semibold text-white mb-4">Assign Towns</h3>
        <p class="text-gray-400 text-sm mb-4">
            Select which towns should be available for this project in the property generator.
        </p>

        <form action="<?= base_url('superadmin/projects/assign-towns/' . $project['id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-6">
                <label for="town_ids" class="form-label">Available Towns</label>
                <select name="town_ids[]" id="town_ids" class="form-select" multiple>
                    <?php foreach ($allTowns as $town): ?>
                        <option value="<?= $town['id'] ?>"
                                <?= in_array($town['id'], $assignedTownIds) ? 'selected' : '' ?>>
                            <?= esc($town['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Select multiple towns by holding Ctrl/Cmd. Use the search box to filter towns.
                </p>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="btn-primary flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Update Town Assignments
                </button>
            </div>
        </form>
    </div>

    <!-- Project Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-1">Project ID</p>
            <p class="text-white font-mono">#<?= $project['id'] ?></p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-1">Slug</p>
            <p class="text-white font-mono"><?= esc($project['slug']) ?></p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-1">Created At</p>
            <p class="text-white"><?= date('F j, Y, g:i a', strtotime($project['created_at'])) ?></p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-1">Last Updated</p>
            <p class="text-white"><?= date('F j, Y, g:i a', strtotime($project['updated_at'])) ?></p>
        </div>
    </div>

    <!-- Select2 for Towns Dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-selection--multiple {
            background-color: #1A1C1E !important;
            border: 1px solid #3a3d42 !important;
            border-radius: 6px !important;
            min-height: 42px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #D4AF37 !important;
            border: none !important;
            color: #000 !important;
            padding: 4px 8px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #000 !important;
            margin-right: 5px !important;
        }
        .select2-dropdown {
            background-color: #1A1C1E !important;
            border: 1px solid #3a3d42 !important;
        }
        .select2-container--default .select2-results__option--highlighted {
            background-color: #D4AF37 !important;
            color: #000 !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #2A2C2E !important;
            border: 1px solid #3a3d42 !important;
            color: #fff !important;
        }
        .select2-container--default .select2-results__option {
            color: #fff !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#town_ids').select2({
                placeholder: 'Search and select towns...',
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <?php else: ?>

    <!-- Info Box (create only) -->
    <div class="card p-4 mt-4 border-[#D4AF37]">
        <div class="flex items-start">
            <i data-lucide="lightbulb" class="w-6 h-6 mr-3 text-[#D4AF37] flex-shrink-0"></i>
            <div>
                <p class="text-sm text-gray-300 mb-2">
                    <strong>After creating a project:</strong>
                </p>
                <ul class="text-sm text-gray-400 space-y-1 list-disc list-inside">
                    <li>Create an admin user for this project</li>
                    <li>The admin can then create regular users</li>
                    <li>Users can access the three-tab interface (Generator, Translator, Rewriter)</li>
                </ul>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<script>
function toggleLimitField(type) {
    document.getElementById('limit-field').style.display = (type === 'lifetime') ? 'none' : '';
}
document.addEventListener('DOMContentLoaded', function () {
    toggleLimitField(document.getElementById('subscription_type').value);
});
</script>

<?= $this->endSection() ?>
