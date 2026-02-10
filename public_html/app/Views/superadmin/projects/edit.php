<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('superadmin/projects') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Projects
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h3 class="text-xl font-semibold text-white mb-6">Edit Project: <?= esc($project['name']) ?></h3>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('superadmin/projects/edit/' . $project['id']) ?>" method="post">
            <?= csrf_field() ?>

            <!-- Project Name -->
            <div class="mb-4">
                <label for="name" class="form-label">Project Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input"
                       value="<?= old('name', $project['name']) ?>"
                       placeholder="e.g., Demo Project"
                       required>
                <p class="text-xs text-gray-500 mt-1">A unique name for this project. Slug will be auto-generated.</p>
            </div>

            <!-- AI Provider -->
            <div class="mb-4">
                <label for="default_ai_provider" class="form-label">AI Provider *</label>
                <select id="default_ai_provider" name="default_ai_provider" class="form-select" required>
                    <option value="claude" <?= old('default_ai_provider', $project['default_ai_provider']) === 'claude' ? 'selected' : '' ?>>
                        Anthropic Claude (claude-sonnet-4-5-20250929)
                    </option>
                    <option value="openai" <?= old('default_ai_provider', $project['default_ai_provider']) === 'openai' ? 'selected' : '' ?>>
                        OpenAI GPT (gpt-5.2)
                    </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Choose which AI service to use for this project.</p>
            </div>

            <!-- API Key -->
            <div class="mb-4">
                <label for="api_key" class="form-label">API Key</label>
                <input type="password"
                       id="api_key"
                       name="api_key"
                       class="form-input font-mono"
                       placeholder="Leave empty to keep current API key">
                <p class="text-xs text-gray-500 mt-1">
                    Only provide a new API key if you want to change it. Current key is encrypted and cannot be displayed.
                </p>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="is_active" class="form-label">Project Status *</label>
                <select id="is_active" name="is_active" class="form-select" required>
                    <option value="1" <?= old('is_active', $project['is_active']) == 1 ? 'selected' : '' ?>>
                        Active
                    </option>
                    <option value="0" <?= old('is_active', $project['is_active']) == 0 ? 'selected' : '' ?>>
                        Inactive
                    </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Inactive projects cannot be accessed by their users.
                </p>
            </div>

            <!-- Languages (Fixed - Display Only) -->
            <div class="mb-6">
                <label class="form-label">Supported Languages</label>
                <div class="p-4 bg-[#1A1C1E] rounded-lg border border-[#3a3d42]">
                    <div class="flex space-x-4">
                        <?php
                            $languages = is_string($project['languages']) ? json_decode($project['languages'], true) : $project['languages'];
                            $langLabels = [
                                'en' => 'English (EN)',
                                'de' => 'German (DE)',
                                'es' => 'Spanish (ES)'
                            ];
                            foreach ($languages as $lang): ?>
                                <span class="badge badge-info"><?= $langLabels[$lang] ?? strtoupper($lang) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="btn-primary flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Update Project
                </button>
                <a href="<?= base_url('superadmin/projects') ?>" class="btn-secondary">
                    Cancel
                </a>
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
</div>

<?= $this->endSection() ?>
