<?php
$currentUser = [
    'first_name' => session()->get('first_name'),
    'last_name' => session()->get('last_name'),
    'email' => session()->get('email'),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Dashboard') ?> - Property Text Tools</title>
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
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #3a3d42;
        }
        th {
            background-color: #1A1C1E;
            font-weight: 600;
            color: #a8adb5;
        }
        tr:hover {
            background-color: #2a2d31;
        }
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .alert-error {
            background-color: #7f1d1d;
            border: 1px solid #ef4444;
            color: #fecaca;
        }
        .alert-success {
            background-color: #065f46;
            border: 1px solid #10b981;
            color: #d1fae5;
        }
        .alert-info {
            background-color: #2a2d31;
            border: 1px solid #D4AF37;
            color: #f5e6c8;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="flex h-screen">
    <!-- Sidebar -->
    <aside class="sidebar w-64 flex-shrink-0 relative">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-white flex items-center">
                <i data-lucide="building-2" class="mr-2 w-6 h-6"></i>
                Text Tools
            </h1>
            <p class="text-sm text-gray-400 mt-1">Admin Panel</p>
        </div>

        <nav class="mt-6">
            <a href="<?= base_url('admin/dashboard') ?>"
               class="sidebar-link flex items-center px-6 py-3 <?= uri_string() === 'admin/dashboard' ? 'active' : '' ?>">
                <i data-lucide="layout-dashboard" class="mr-3 w-5 h-5"></i>
                Dashboard
            </a>
            <a href="<?= base_url('admin/users') ?>"
               class="sidebar-link flex items-center px-6 py-3 <?= strpos(uri_string(), 'admin/users') === 0 ? 'active' : '' ?>">
                <i data-lucide="users" class="mr-3 w-5 h-5"></i>
                Manage Users
            </a>
        </nav>

        <!-- User Info & Logout -->
        <div class="absolute bottom-0 left-0 w-full p-6 border-t border-gray-700 bg-[#25282C]">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm font-medium text-white"><?= esc($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></p>
                    <p class="text-xs text-gray-400"><?= esc($currentUser['email']) ?></p>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" class="btn-secondary block text-center text-sm flex items-center justify-center">
                <i data-lucide="log-out" class="mr-2 w-4 h-4"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
        <!-- Alerts -->
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

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-error">
                <ul class="list-disc list-inside">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <?= $this->renderSection('content') ?>
    </main>

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
