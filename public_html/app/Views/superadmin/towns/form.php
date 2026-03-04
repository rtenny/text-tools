<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<?php
$isEdit = $isEdit ?? false;
$town   = $town ?? [];
?>

<div class="mb-6">
    <a href="<?= base_url('superadmin/towns') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Towns
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h3 class="text-xl font-semibold text-white mb-6">
            <?= $isEdit ? 'Edit Town' : 'Create New Town' ?>
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

        <form action="<?= $isEdit ? base_url('superadmin/towns/edit/' . $town['id']) : base_url('superadmin/towns/create') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Town Name -->
            <div class="mb-6">
                <label for="name" class="form-label">Town Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input"
                       value="<?= old('name', $town['name'] ?? '') ?>"
                       placeholder="e.g., Marbella"
                       required
                       autofocus>
                <p class="text-xs text-gray-500 mt-1">Enter the name of the town. Must be unique.</p>
            </div>

            <?php if ($isEdit && isset($project_count) && $project_count > 0): ?>
            <!-- Usage Info (edit only) -->
            <div class="mb-6 p-4 bg-blue-900 bg-opacity-20 border border-blue-500 rounded-lg">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-5 h-5 mr-2 text-blue-400 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-blue-200">
                            This town is currently assigned to <strong><?= $project_count ?></strong> project<?= $project_count > 1 ? 's' : '' ?>.
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Submit Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="btn-primary flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> <?= $isEdit ? 'Update Town' : 'Create Town' ?>
                </button>
                <a href="<?= base_url('superadmin/towns') ?>" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php if (!$isEdit): ?>
    <!-- Info Box (create only) -->
    <div class="card p-4 mt-4 border-[#D4AF37]">
        <div class="flex items-start">
            <i data-lucide="info" class="w-6 h-6 mr-3 text-[#D4AF37] flex-shrink-0"></i>
            <div>
                <p class="text-sm text-gray-300 mb-2">
                    <strong>About Towns:</strong>
                </p>
                <ul class="text-sm text-gray-400 space-y-1 list-disc list-inside">
                    <li>Towns can be assigned to multiple projects</li>
                    <li>Projects will only see towns assigned to them in the property generator</li>
                    <li>After creating a town, assign it to projects via the project edit page</li>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
