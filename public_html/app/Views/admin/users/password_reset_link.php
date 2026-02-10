<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('admin/users') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Users
    </a>
</div>

<h1 class="text-3xl font-bold text-white mb-6">Password Reset Link</h1>

<div class="card p-8 max-w-2xl">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-white mb-2">User Details</h2>
        <p class="text-gray-400">
            <span class="font-medium text-white"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></span>
            <br>
            <?= esc($user['email']) ?>
        </p>
    </div>

    <div class="bg-yellow-900 bg-opacity-20 border border-yellow-500 rounded-lg p-4 mb-6">
        <p class="text-sm text-yellow-200 mb-2 flex items-start">
            <i data-lucide="alert-triangle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
            <span><strong>Important:</strong> This password reset link is valid for 1 hour.</span>
        </p>
        <p class="text-xs text-yellow-300 ml-6">
            Send this link to the user via email or another secure channel. They will use it to set their password.
        </p>
    </div>

    <div class="mb-4">
        <label class="form-label">Password Reset Link</label>
        <div class="flex items-center space-x-2">
            <input type="text"
                   id="resetLink"
                   class="form-input"
                   value="<?= esc($resetLink) ?>"
                   readonly>
            <button type="button"
                    onclick="copyToClipboard()"
                    class="btn-primary flex-shrink-0 flex items-center">
                <i data-lucide="copy" class="w-4 h-4 mr-2"></i> Copy
            </button>
        </div>
        <p id="copyStatus" class="text-sm text-green-400 mt-2 hidden flex items-center">
            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Link copied to clipboard!
        </p>
    </div>

    <div class="mt-6">
        <a href="<?= base_url('admin/users') ?>" class="btn-secondary">
            Done
        </a>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyToClipboard() {
    const linkInput = document.getElementById('resetLink');
    const status = document.getElementById('copyStatus');

    // Select and copy
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices

    try {
        document.execCommand('copy');
        status.classList.remove('hidden');

        // Hide status after 3 seconds
        setTimeout(() => {
            status.classList.add('hidden');
        }, 3000);
    } catch (err) {
        console.error('Failed to copy:', err);
        alert('Failed to copy link. Please copy manually.');
    }
}
</script>
<?= $this->endSection() ?>
