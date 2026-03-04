<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('superadmin/projects') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Projects
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h3 class="text-xl font-semibold text-white mb-6">Create New Project</h3>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('superadmin/projects/create') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Project Name -->
            <div class="mb-4">
                <label for="name" class="form-label">Project Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input"
                       value="<?= old('name') ?>"
                       placeholder="e.g., Demo Project"
                       required>
                <p class="text-xs text-gray-500 mt-1">A unique name for this project. Slug will be auto-generated.</p>
            </div>

            <!-- AI Provider -->
            <div class="mb-4">
                <label for="default_ai_provider" class="form-label">AI Provider *</label>
                <select id="default_ai_provider" name="default_ai_provider" class="form-select" required>
                    <option value="">Select AI Provider</option>
                    <option value="claude" <?= old('default_ai_provider') === 'claude' ? 'selected' : '' ?>>
                        Anthropic Claude (claude-sonnet-4-5-20250929)
                    </option>
                    <option value="openai" <?= old('default_ai_provider') === 'openai' ? 'selected' : '' ?>>
                        OpenAI GPT (gpt-5.2)
                    </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Choose which AI service to use for this project.</p>
            </div>

            <!-- API Key -->
            <div class="mb-4">
                <label for="api_key" class="form-label">API Key *</label>
                <input type="password"
                       id="api_key"
                       name="api_key"
                       class="form-input font-mono"
                       placeholder="sk-ant-... or sk-proj-..."
                       required>
                <p class="text-xs text-gray-500 mt-1">
                    API key will be encrypted before storage. Never logged or displayed after saving.
                </p>
            </div>

            <!-- Subscription Type -->
            <div class="mb-4">
                <label for="subscription_type" class="form-label">Subscription Type *</label>
                <select id="subscription_type" name="subscription_type" class="form-select" required
                        onchange="toggleLimitField(this.value)">
                    <option value="subscription" <?= old('subscription_type', 'subscription') === 'subscription' ? 'selected' : '' ?>>
                        Subscription (€/month)
                    </option>
                    <option value="lifetime" <?= old('subscription_type') === 'lifetime' ? 'selected' : '' ?>>
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
                       value="<?= old('daily_translation_limit', '50') ?>"
                       min="1"
                       placeholder="e.g., 50">
                <p class="text-xs text-gray-500 mt-1">Max AI translation calls per day. Leave blank for unlimited.</p>
            </div>

            <!-- Languages (Fixed) -->
            <div class="mb-6">
                <label class="form-label">Supported Languages</label>
                <div class="p-4 bg-[#1A1C1E] rounded-lg border border-[#3a3d42]">
                    <div class="flex space-x-4">
                        <span class="badge badge-info">English (EN)</span>
                        <span class="badge badge-info">German (DE)</span>
                        <span class="badge badge-info">Spanish (ES)</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Fixed language set. English is the source language, with translations to German and Spanish.
                    </p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="btn-primary flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Create Project
                </button>
                <a href="<?= base_url('superadmin/projects') ?>" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
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
