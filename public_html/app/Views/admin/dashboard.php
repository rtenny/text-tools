<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1 class="text-3xl font-bold text-white mb-6">Admin Dashboard</h1>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Total Users -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 mb-1">Total Users</p>
                <p class="text-3xl font-bold text-white"><?= $totalUsers ?></p>
            </div>
            <div class="text-4xl">👥</div>
        </div>
    </div>
</div>

<!-- Recent Users -->
<div class="card p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-white">Recent Users</h2>
        <a href="<?= base_url('admin/users/create') ?>" class="btn-primary">
            ➕ Add User
        </a>
    </div>

    <?php if (empty($recentUsers)): ?>
        <p class="text-gray-400 text-center py-8">No users yet. Create your first user to get started.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentUsers as $user): ?>
                        <tr>
                            <td class="text-white"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="px-2 py-1 text-xs rounded bg-green-900 text-green-200">Active</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded bg-red-900 text-red-200">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-gray-400"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('admin/users/password-reset-link/' . $user['id']) ?>"
                                   class="text-indigo-400 hover:text-indigo-300 text-sm">
                                    🔑 Reset Link
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (count($recentUsers) >= 5): ?>
            <div class="mt-4 text-center">
                <a href="<?= base_url('admin/users') ?>" class="text-indigo-400 hover:text-indigo-300">
                    View all users →
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Recent Activity -->
<?php if (!empty($recentActivity)): ?>
    <div class="card p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Recent Activity</h2>
        <div class="space-y-3">
            <?php foreach ($recentActivity as $activity): ?>
                <div class="flex items-start space-x-3 text-sm">
                    <span class="text-gray-500"><?= date('M d, H:i', strtotime($activity['created_at'])) ?></span>
                    <span class="text-gray-300">
                        <span class="text-white font-medium"><?= esc($activity['email']) ?></span>
                        <?= esc($activity['action']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
