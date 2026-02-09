<?= $this->extend('layouts/guest') ?>

<?= $this->section('content') ?>

<h2 class="text-2xl font-bold text-white mb-6 text-center">Sign In</h2>

<form action="<?= base_url('login') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="form-label">Email Address</label>
        <input type="email"
               id="email"
               name="email"
               class="form-input"
               value="<?= old('email') ?>"
               placeholder="your.email@example.com"
               required
               autofocus>
    </div>

    <!-- Password -->
    <div class="mb-6">
        <label for="password" class="form-label">Password</label>
        <input type="password"
               id="password"
               name="password"
               class="form-input"
               placeholder="Enter your password"
               required>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn-primary">
        🔓 Sign In
    </button>
</form>

<!-- Additional Info -->
<div class="mt-6 text-center">
    <p class="text-sm text-gray-400">
        Forgot your password? Contact your administrator for a reset link.
    </p>
</div>

<!-- Demo Credentials (Development Only) -->
<?php if (ENVIRONMENT === 'development'): ?>
    <div class="mt-6 p-4 bg-yellow-900 bg-opacity-20 border border-yellow-500 rounded-lg">
        <p class="text-xs text-yellow-200 font-semibold mb-2">🔧 Development Mode - Demo Credentials:</p>
        <div class="text-xs text-gray-300 space-y-1">
            <p><strong>Superadmin:</strong> admin@texttools.local / admin123</p>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
