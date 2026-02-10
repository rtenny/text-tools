<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('admin/users') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Users
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h3 class="text-xl font-semibold text-white mb-6">Create User</h3>

        <form action="<?= base_url('admin/users/create') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input"
                       value="<?= old('email') ?>"
                       placeholder="user@example.com"
                       required>
                <p class="text-xs text-gray-500 mt-1">The user will receive a password reset link to set their password.</p>
            </div>

            <!-- First Name -->
            <div class="mb-4">
                <label for="first_name" class="form-label">First Name *</label>
                <input type="text"
                       id="first_name"
                       name="first_name"
                       class="form-input"
                       value="<?= old('first_name') ?>"
                       placeholder="John"
                       required>
            </div>

            <!-- Last Name -->
            <div class="mb-6">
                <label for="last_name" class="form-label">Last Name *</label>
                <input type="text"
                       id="last_name"
                       name="last_name"
                       class="form-input"
                       value="<?= old('last_name') ?>"
                       placeholder="Doe"
                       required>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="btn-primary flex items-center">
                    <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Create User
                </button>
                <a href="<?= base_url('admin/users') ?>" class="btn-secondary">
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
                    <strong>User Access:</strong>
                </p>
                <p class="text-sm text-gray-400">
                    The user will be assigned to your project and will have access to the translation tools.
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
