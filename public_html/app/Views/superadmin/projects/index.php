<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-xl font-semibold text-white">All Projects</h3>
        <p class="text-gray-400 text-sm mt-1">Manage all tenant projects and their AI configurations</p>
    </div>
    <a href="<?= base_url('superadmin/projects/create') ?>" class="btn-primary">
        ➕ Create New Project
    </a>
</div>

<div class="card p-6">
    <?php if (!empty($projects)): ?>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Slug</th>
                        <th>AI Provider</th>
                        <th>Languages</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td class="font-medium text-white"><?= esc($project['name']) ?></td>
                            <td class="text-gray-400 text-sm font-mono"><?= esc($project['slug']) ?></td>
                            <td>
                                <?php if ($project['default_ai_provider'] === 'claude'): ?>
                                    <span class="badge badge-info">Claude</span>
                                <?php else: ?>
                                    <span class="badge badge-success">OpenAI</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $languages = json_decode($project['languages'], true);
                                    $langLabels = [
                                        'en' => '🇬🇧 EN',
                                        'de' => '🇩🇪 DE',
                                        'es' => '🇪🇸 ES'
                                    ];
                                    echo implode(', ', array_map(function($lang) use ($langLabels) {
                                        return $langLabels[$lang] ?? strtoupper($lang);
                                    }, $languages));
                                ?>
                            </td>
                            <td>
                                <?php if ($project['is_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-gray-400">
                                <?= date('M j, Y', strtotime($project['created_at'])) ?>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <a href="<?= base_url('superadmin/projects/edit/' . $project['id']) ?>"
                                       class="text-indigo-400 hover:text-indigo-300 text-sm">
                                        ✏️ Edit
                                    </a>
                                    <form action="<?= base_url('superadmin/projects/delete/' . $project['id']) ?>"
                                          method="post"
                                          onsubmit="return confirm('Are you sure you want to delete this project? This will also delete all associated users.');"
                                          class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="text-6xl mb-4">📁</div>
            <h3 class="text-xl font-semibold text-white mb-2">No Projects Yet</h3>
            <p class="text-gray-400 mb-6">Create your first project to get started with multi-tenant AI services.</p>
            <a href="<?= base_url('superadmin/projects/create') ?>" class="btn-primary inline-block">
                ➕ Create Your First Project
            </a>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
