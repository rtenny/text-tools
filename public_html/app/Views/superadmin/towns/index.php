<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-xl font-semibold text-white">Manage Towns</h3>
        <p class="text-gray-400 text-sm mt-1">Manage all available towns that can be assigned to projects</p>
    </div>
    <a href="<?= base_url('superadmin/towns/create') ?>" class="btn-primary flex items-center">
        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Create New Town
    </a>
</div>

<div class="card p-6">
    <?php if (!empty($towns)): ?>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Town Name</th>
                        <th>Projects Using</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($towns as $town): ?>
                        <tr>
                            <td class="font-medium text-white"><?= esc($town['name']) ?></td>
                            <td>
                                <?php if ($town['project_count'] > 0): ?>
                                    <span class="badge badge-info"><?= $town['project_count'] ?> project<?= $town['project_count'] > 1 ? 's' : '' ?></span>
                                <?php else: ?>
                                    <span class="text-gray-500 text-sm">Not assigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-gray-400">
                                <?= date('M j, Y', strtotime($town['created_at'])) ?>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <a href="<?= base_url('superadmin/towns/edit/' . $town['id']) ?>"
                                       class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center">
                                        <i data-lucide="pencil" class="w-4 h-4 mr-1"></i> Edit
                                    </a>
                                    <?php if ($town['project_count'] == 0): ?>
                                        <form action="<?= base_url('superadmin/towns/delete/' . $town['id']) ?>"
                                              method="post"
                                              onsubmit="return confirm('Are you sure you want to delete this town?');"
                                              class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm flex items-center">
                                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-600 text-sm cursor-not-allowed" title="Cannot delete town assigned to projects">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1 inline"></i> Delete
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="flex justify-center mb-4">
                <i data-lucide="map-pin" class="w-16 h-16 text-gray-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">No Towns Yet</h3>
            <p class="text-gray-400 mb-6">Create your first town to get started.</p>
            <a href="<?= base_url('superadmin/towns/create') ?>" class="btn-primary inline-flex items-center">
                <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Create Your First Town
            </a>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
