<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('superadmin/users') ?>" class="text-[#D4AF37] hover:text-[#C29F2F] text-sm flex items-center w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Admins
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h3 class="text-xl font-semibold text-white mb-6">Create New Admin</h3>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('superadmin/users/create') ?>" method="post">
            <?= csrf_field() ?>

            <!-- First Name -->
            <div class="mb-4">
                <label for="first_name" class="form-label">First Name *</label>
                <input type="text"
                       id="first_name"
                       name="first_name"
                       class="form-input"
                       value="<?= old('first_name') ?>"
                       placeholder="John"
                       required>
            </div>

            <!-- Last Name -->
            <div class="mb-4">
                <label for="last_name" class="form-label">Last Name *</label>
                <input type="text"
                       id="last_name"
                       name="last_name"
                       class="form-input"
                       value="<?= old('last_name') ?>"
                       placeholder="Doe"
                       required>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input"
                       value="<?= old('email') ?>"
                       placeholder="admin@example.com"
                       required>
                <p class="text-xs text-gray-500 mt-1">
                    Must be unique. Will be used for login.
                </p>
            </div>

            <!-- Project -->
            <div class="mb-6">
                <label for="project_id" class="form-label">Assign to Project *</label>
                <select id="project_id" name="project_id" class="form-select" required>
                    <option value="">Select Project</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= $project['id'] ?>" <?= old('project_id') == $project['id'] ? 'selected' : '' ?>>
                            <?= esc($project['name']) ?> (<?= ucfirst($project['default_ai_provider']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    This admin will manage users and settings for the selected project.
                </p>
            </div>

            <!-- Role Info -->
            <div class="mb-6">
                <label class="form-label">Role</label>
                <div class="p-4 bg-[#0f1419] rounded-lg border border-[#2d3561]">
                    <div class="flex items-center">
                        <span class="badge badge-info mr-3">Admin</span>
                        <span class="text-sm text-gray-400">Fixed role for project administrators</span>
                    </div>
                </div>
            </div>

            <!-- Password Info -->
            <div class="mb-6">
                <div class="p-4 bg-blue-900 bg-opacity-20 rounded-lg border border-blue-500">
                    <div class="flex items-start">
                        <i data-lucide="key" class="w-6 h-6 mr-3 text-[#D4AF37] flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-blue-200 font-semibold mb-2">Password Setup</p>
                            <p class="text-sm text-gray-300">
                                A temporary password will be auto-generated. After creating the admin,
                                you'll receive a password reset link to share with them.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="btn-primary flex items-center">
                    <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Create Admin
                </button>
                <a href="<?= base_url('superadmin/users') ?>" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="card p-4 mt-4 border-[#D4AF37]">
        <div class="flex items-start">
            <i data-lucide="lightbulb" class="w-6 h-6 mr-3 text-[#D4AF37] flex-shrink-0"></i>
            <div>
                <p class="text-sm text-gray-300 mb-2">
                    <strong>Admin Permissions:</strong>
                </p>
                <ul class="text-sm text-gray-400 space-y-1 list-disc list-inside">
                    <li>Can create and manage users within their project</li>
                    <li>Can generate password reset links for users</li>
                    <li>Cannot access other projects' data</li>
                    <li>Cannot create or modify projects</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
