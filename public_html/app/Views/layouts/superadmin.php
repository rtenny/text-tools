<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Superadmin Dashboard') ?> - Property Text Tools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Luxury Slate Theme */
        body {
            background-color: #1A1C1E;
            color: #e0e0e0;
        }
        .sidebar {
            background-color: #25282C;
            border-right: 1px solid #3a3d42;
        }
        .sidebar-link {
            color: #a8adb5;
            transition: all 0.2s;
        }
        .sidebar-link:hover, .sidebar-link.active {
            color: #ffffff;
            background-color: #3a3d42;
        }
        .card {
            background-color: #25282C;
            border: 1px solid #3a3d42;
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #D4AF37;
            color: #1A1C1E;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #C29F2F;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
        }
        .btn-secondary {
            background-color: #3a3d42;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-secondary:hover {
            background-color: #4a4d52;
        }
        .btn-danger {
            background-color: #dc2626;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-danger:hover {
            background-color: #b91c1c;
        }
        .form-input, .form-select, .form-textarea {
            background-color: #1A1C1E;
            border: 1px solid #3a3d42;
            color: #e0e0e0;
            padding: 0.5rem;
            border-radius: 6px;
            width: 100%;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }
        .form-label {
            color: #a8adb5;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            background-color: #1A1C1E;
            padding: 0.75rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            color: #a8adb5;
            border-bottom: 1px solid #3a3d42;
        }
        table td {
            padding: 0.75rem;
            border-bottom: 1px solid #3a3d42;
        }
        table tr:hover {
            background-color: #2a2d31;
        }
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success {
            background-color: #10b981;
            color: white;
        }
        .badge-danger {
            background-color: #ef4444;
            color: white;
        }
        .badge-info {
            background-color: #D4AF37;
            color: #1A1C1E;
        }
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background-color: #065f46;
            border: 1px solid #10b981;
            color: #d1fae5;
        }
        .alert-error {
            background-color: #7f1d1d;
            border: 1px solid #ef4444;
            color: #fecaca;
        }
        .alert-info {
            background-color: #2a2d31;
            border: 1px solid #D4AF37;
            color: #f5e6c8;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="sidebar w-64 flex-shrink-0 overflow-y-auto">
        <div class="p-6">
            <h1 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="building-2" class="mr-2 w-5 h-5"></i>
                Property Text Tools
            </h1>
            <p class="text-xs text-gray-400 mt-1">Superadmin Panel</p>
        </div>

        <nav class="px-4 pb-4">
            <a href="<?= base_url('superadmin/dashboard') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg <?= (current_url() == base_url('superadmin/dashboard')) ? 'active' : '' ?>">
                <i data-lucide="layout-dashboard" class="mr-3 w-5 h-5"></i>
                Dashboard
            </a>
            <a href="<?= base_url('superadmin/projects') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg <?= (strpos(current_url(), 'superadmin/projects') !== false) ? 'active' : '' ?>">
                <i data-lucide="folder" class="mr-3 w-5 h-5"></i>
                Projects
            </a>
            <a href="<?= base_url('superadmin/towns') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg <?= (strpos(current_url(), 'superadmin/towns') !== false) ? 'active' : '' ?>">
                <i data-lucide="map-pin" class="mr-3 w-5 h-5"></i>
                Towns
            </a>
            <a href="<?= base_url('superadmin/users') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg <?= (strpos(current_url(), 'superadmin/users') !== false) ? 'active' : '' ?>">
                <i data-lucide="users" class="mr-3 w-5 h-5"></i>
                Admins
            </a>

            <div class="border-t border-gray-700 my-4"></div>

            <a href="<?= base_url('logout') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg text-red-400">
                <i data-lucide="log-out" class="mr-3 w-5 h-5"></i>
                Logout
            </a>
        </nav>

        <div class="px-8 pb-6">
            <div class="text-xs text-gray-500">
                <p>Logged in as:</p>
                <p class="font-semibold text-gray-300"><?= esc(session()->get('email')) ?></p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="bg-[#25282C] border-b border-[#3a3d42] px-6 py-4">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-white"><?= esc($title ?? 'Dashboard') ?></h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-400">
                        <?= date('l, F j, Y') ?>
                    </span>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto p-6">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')): ?>
                <div class="alert alert-info">
                    <?= session()->getFlashdata('info') ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <?= $this->renderSection('scripts') ?>
    <script>
        lucide.createIcons();

        // Auto-fade success and info messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successAlerts = document.querySelectorAll('.alert-success, .alert-info');

            successAlerts.forEach(function(alert) {
                // Add transition for smooth fade
                alert.style.transition = 'opacity 0.5s ease-out';

                // Fade out after 5 seconds
                setTimeout(function() {
                    alert.style.opacity = '0';

                    // Remove from DOM after fade completes
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
