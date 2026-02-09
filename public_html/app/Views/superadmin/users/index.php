<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-xl font-semibold text-white">Project Admins</h3>
        <p class="text-gray-400 text-sm mt-1">Manage administrators for all projects</p>
    </div>
    <a href="<?= base_url('superadmin/users/create') ?>" class="btn-primary">
        ➕ Create New Admin
    </a>
</div>

<!-- Display password reset link after admin creation -->
<?php if (session()->has('reset_link')): ?>
    <div class="card p-6 mb-6 border-indigo-500">
        <div class="flex items-start">
            <span class="text-2xl mr-3">🔑</span>
            <div class="flex-1">
                <h4 class="text-lg font-semibold text-white mb-2">Admin Created Successfully!</h4>
                <p class="text-sm text-gray-300 mb-3">
                    Password reset link for <strong><?= esc(session('admin_email')) ?></strong>:
                </p>
                <div class="flex items-center space-x-2">
                    <input type="text"
                           id="reset-link"
                           value="<?= esc(session('reset_link')) ?>"
                           class="form-input font-mono text-sm"
                           readonly>
                    <button onclick="copyResetLink()"
                            class="btn-primary whitespace-nowrap">
                        📋 Copy Link
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Share this link with the admin. It expires in 1 hour.
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card p-6">
    <?php if (!empty($admins)): ?>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td class="font-medium text-white">
                                <?= esc($admin['first_name'] . ' ' . $admin['last_name']) ?>
                            </td>
                            <td class="text-gray-400 text-sm"><?= esc($admin['email']) ?></td>
                            <td>
                                <span class="badge badge-info">
                                    <?= esc($admin['project_name'] ?? 'No Project') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($admin['is_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-gray-400">
                                <?php if ($admin['last_login_at']): ?>
                                    <?= date('M j, Y H:i', strtotime($admin['last_login_at'])) ?>
                                <?php else: ?>
                                    <span class="text-gray-600">Never</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-gray-400">
                                <?= date('M j, Y', strtotime($admin['created_at'])) ?>
                            </td>
                            <td>
                                <a href="<?= base_url('superadmin/users/password-reset-link/' . $admin['id']) ?>"
                                   class="text-indigo-400 hover:text-indigo-300 text-sm"
                                   target="_blank">
                                    🔑 Reset Password
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="text-6xl mb-4">👨‍💼</div>
            <h3 class="text-xl font-semibold text-white mb-2">No Admins Yet</h3>
            <p class="text-gray-400 mb-6">Create your first project administrator to manage a project.</p>
            <a href="<?= base_url('superadmin/users/create') ?>" class="btn-primary inline-block">
                ➕ Create Your First Admin
            </a>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyResetLink() {
    const input = document.getElementById('reset-link');
    input.select();
    document.execCommand('copy');

    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '✅ Copied!';
    button.classList.add('bg-green-600');

    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('bg-green-600');
    }, 2000);
}
</script>
<?= $this->endSection() ?>
