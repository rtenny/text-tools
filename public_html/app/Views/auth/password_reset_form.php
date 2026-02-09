<?= $this->extend('layouts/guest') ?>

<?= $this->section('content') ?>

<h2 class="text-2xl font-bold text-white mb-2 text-center">Reset Your Password</h2>
<p class="text-sm text-gray-400 mb-6 text-center">Enter a new password for <?= esc($user['email']) ?></p>

<form action="<?= base_url("password-reset/{$userId}/{$token}") ?>" method="post">
    <?= csrf_field() ?>

    <!-- New Password -->
    <div class="mb-4">
        <label for="password" class="form-label">New Password</label>
        <input type="password"
               id="password"
               name="password"
               class="form-input"
               placeholder="Enter new password (min. 8 characters)"
               required
               autofocus>
        <p class="text-xs text-gray-500 mt-1">
            Must be at least 8 characters long.
        </p>
    </div>

    <!-- Confirm Password -->
    <div class="mb-6">
        <label for="password_confirm" class="form-label">Confirm New Password</label>
        <input type="password"
               id="password_confirm"
               name="password_confirm"
               class="form-input"
               placeholder="Confirm your new password"
               required>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn-primary">
        🔒 Reset Password
    </button>
</form>

<!-- Additional Info -->
<div class="mt-6 p-4 bg-indigo-900 bg-opacity-20 border border-indigo-500 rounded-lg">
    <p class="text-xs text-indigo-200">
        <strong>ℹ️ Security Note:</strong> After resetting your password, you'll be redirected to the login page where you can sign in with your new password.
    </p>
</div>

<div class="mt-4 text-center">
    <a href="<?= base_url('login') ?>" class="text-sm text-indigo-400 hover:text-indigo-300">
        ← Back to Login
    </a>
</div>

<?= $this->endSection() ?>
