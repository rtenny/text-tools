<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('superadmin/towns') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Towns
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h3 class="text-xl font-semibold text-white mb-6">Create New Town</h3>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('superadmin/towns/create') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Town Name -->
            <div class="mb-6">
                <label for="name" class="form-label">Town Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input"
                       value="<?= old('name') ?>"
                       placeholder="e.g., Marbella"
                       required
                       autofocus>
                <p class="text-xs text-gray-500 mt-1">Enter the name of the town. Must be unique.</p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="btn-primary flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Create Town
                </button>
                <a href="<?= base_url('superadmin/towns') ?>" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
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
</div>

<?= $this->endSection() ?>
