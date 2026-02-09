<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('admin/users') ?>" class="text-indigo-400 hover:text-indigo-300">
        ← Back to Users
    </a>
</div>

<h1 class="text-3xl font-bold text-white mb-6">Create User</h1>

<div class="card p-8 max-w-2xl">
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
            <p class="text-xs text-gray-400 mt-1">The user will receive a password reset link to set their password.</p>
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
        <div class="mb-4">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text"
                   id="last_name"
                   name="last_name"
                   class="form-input"
                   value="<?= old('last_name') ?>"
                   placeholder="Doe"
                   required>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-900 bg-opacity-20 border border-blue-500 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-200">
                ℹ️ The user will be assigned to your project and will have access to the translation tools.
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex items-center space-x-4">
            <button type="submit" class="btn-primary">
                ✅ Create User
            </button>
            <a href="<?= base_url('admin/users') ?>" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
