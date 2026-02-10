<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('superadmin/towns') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Towns
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h3 class="text-xl font-semibold text-white mb-6">Edit Town</h3>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('superadmin/towns/edit/' . $town['id']) ?>" method="post">
            <?= csrf_field() ?>

            <!-- Town Name -->
            <div class="mb-6">
                <label for="name" class="form-label">Town Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input"
                       value="<?= old('name', $town['name']) ?>"
                       placeholder="e.g., Marbella"
                       required
                       autofocus>
                <p class="text-xs text-gray-500 mt-1">Enter the name of the town. Must be unique.</p>
            </div>

            <!-- Usage Info -->
            <?php if ($project_count > 0): ?>
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
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Update Town
                </button>
                <a href="<?= base_url('superadmin/towns') ?>" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
