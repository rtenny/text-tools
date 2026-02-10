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
    <button type="submit" class="btn-primary flex items-center justify-center">
        <i data-lucide="lock" class="w-4 h-4 mr-2"></i> Reset Password
    </button>
</form>

<!-- Additional Info -->
<div class="mt-6 p-4 bg-blue-900 bg-opacity-20 border border-blue-500 rounded-lg">
    <p class="text-xs text-blue-200 flex items-start">
        <i data-lucide="info" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
        <span><strong>Security Note:</strong> After resetting your password, you'll be redirected to the login page where you can sign in with your new password.</span>
    </p>
</div>

<div class="mt-4 text-center">
    <a href="<?= base_url('login') ?>" class="text-sm text-[#D4AF37] hover:text-[#C29F2F] flex items-center justify-center">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Login
    </a>
</div>

<?= $this->endSection() ?>
