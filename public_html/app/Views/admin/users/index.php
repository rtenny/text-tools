<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-white">Manage Users</h1>
    <a href="<?= base_url('admin/users/create') ?>" class="btn-primary flex items-center">
        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Add User
    </a>
</div>

<?php if (empty($users)): ?>
    <div class="card p-8 text-center">
        <p class="text-gray-400 mb-4">No users yet.</p>
        <a href="<?= base_url('admin/users/create') ?>" class="btn-primary inline-block">
            Create your first user
        </a>
    </div>
<?php else: ?>
    <div class="card p-6">
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="text-white font-medium"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="px-2 py-1 text-xs rounded bg-green-900 text-green-200">Active</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded bg-red-900 text-red-200">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-gray-400">
                                <?= $user['last_login_at'] ? date('M d, Y H:i', strtotime($user['last_login_at'])) : 'Never' ?>
                            </td>
                            <td class="text-sm text-gray-400"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('admin/users/password-reset-link/' . $user['id']) ?>"
                                   class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center">
                                    <i data-lucide="key" class="w-4 h-4 mr-1"></i> Reset Link
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
