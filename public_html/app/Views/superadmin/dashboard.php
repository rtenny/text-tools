<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Projects -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm mb-1">Total Projects</p>
                <p class="text-3xl font-bold text-white"><?= $totalProjects ?></p>
            </div>
            <div><i data-lucide="folder" class="w-10 h-10 text-[#D4AF37]"></i></div>
        </div>
        <div class="mt-4">
            <span class="badge badge-success"><?= $activeProjects ?> Active</span>
            <?php if ($totalProjects - $activeProjects > 0): ?>
                <span class="badge badge-danger ml-2"><?= $totalProjects - $activeProjects ?> Inactive</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Total Admins -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm mb-1">Project Admins</p>
                <p class="text-3xl font-bold text-white"><?= $totalAdmins ?></p>
            </div>
            <div><i data-lucide="user-cog" class="w-10 h-10 text-[#D4AF37]"></i></div>
        </div>
        <div class="mt-4">
            <a href="<?= base_url('superadmin/users') ?>" class="text-[#D4AF37] text-sm hover:underline">Manage Admins →</a>
        </div>
    </div>

    <!-- Total Users -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm mb-1">Total Users</p>
                <p class="text-3xl font-bold text-white"><?= $totalUsers ?></p>
            </div>
            <div><i data-lucide="users" class="w-10 h-10 text-[#D4AF37]"></i></div>
        </div>
        <div class="mt-4">
            <span class="text-gray-400 text-sm">Across all projects</span>
        </div>
    </div>

    <!-- System Status -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm mb-1">System Status</p>
                <p class="text-lg font-bold text-green-400">All Systems Operational</p>
            </div>
            <div><i data-lucide="check-circle" class="w-10 h-10 text-green-400"></i></div>
        </div>
        <div class="mt-4">
            <span class="text-gray-400 text-xs">Last checked: <?= date('H:i:s') ?></span>
        </div>
    </div>
</div>

<!-- Recent Projects and Admins -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Projects -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Recent Projects</h3>
            <a href="<?= base_url('superadmin/projects') ?>" class="text-[#D4AF37] text-sm hover:underline">View All →</a>
        </div>

        <?php if (!empty($recentProjects)): ?>
            <div class="space-y-3">
                <?php foreach ($recentProjects as $project): ?>
                    <div class="flex items-center justify-between p-3 bg-[#1A1C1E] rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-white"><?= esc($project['name']) ?></p>
                            <p class="text-xs text-gray-400 mt-1">
                                <?= ucfirst($project['default_ai_provider']) ?> •
                                Created <?= date('M j, Y', strtotime($project['created_at'])) ?>
                            </p>
                        </div>
                        <div>
                            <?php if ($project['is_active']): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-400 text-center py-8">No projects yet. <a href="<?= base_url('superadmin/projects/create') ?>" class="text-[#D4AF37] hover:underline">Create your first project</a></p>
        <?php endif; ?>
    </div>

    <!-- Recent Admins -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Recent Admins</h3>
            <a href="<?= base_url('superadmin/users') ?>" class="text-[#D4AF37] text-sm hover:underline">View All →</a>
        </div>

        <?php if (!empty($recentAdmins)): ?>
            <div class="space-y-3">
                <?php foreach ($recentAdmins as $admin): ?>
                    <div class="flex items-center justify-between p-3 bg-[#1A1C1E] rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-white"><?= esc($admin['first_name'] . ' ' . $admin['last_name']) ?></p>
                            <p class="text-xs text-gray-400 mt-1">
                                <?= esc($admin['email']) ?> •
                                Added <?= date('M j, Y', strtotime($admin['created_at'])) ?>
                            </p>
                        </div>
                        <div>
                            <?php if ($admin['is_active']): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-400 text-center py-8">No admins yet. <a href="<?= base_url('superadmin/users/create') ?>" class="text-[#D4AF37] hover:underline">Create your first admin</a></p>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Activity -->
<?php if (!empty($recentActivity)): ?>
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Recent Activity</h3>

        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Project</th>
                        <th>Action</th>
                        <th>AI Provider</th>
                        <th>Tokens</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentActivity as $activity): ?>
                        <tr>
                            <td><?= esc($activity['email'] ?? 'Unknown') ?></td>
                            <td><?= esc($activity['project_name'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge badge-info"><?= esc($activity['action']) ?></span>
                            </td>
                            <td><?= ucfirst($activity['ai_provider']) ?></td>
                            <td><?= number_format($activity['tokens_used'] ?? 0) ?></td>
                            <td class="text-sm text-gray-400"><?= date('M j, H:i', strtotime($activity['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="mt-8 flex flex-wrap gap-4">
    <a href="<?= base_url('superadmin/projects/create') ?>" class="btn-primary flex items-center justify-center">
        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Create New Project
    </a>
    <a href="<?= base_url('superadmin/users/create') ?>" class="btn-secondary flex items-center justify-center">
        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Add New Admin
    </a>
</div>

<?= $this->endSection() ?>
