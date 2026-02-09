<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Superadmin Dashboard') ?> - Property Text Tools</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a2e;
            color: #e0e0e0;
        }
        .sidebar {
            background-color: #16213e;
            border-right: 1px solid #2d3561;
        }
        .sidebar-link {
            color: #a0a0c0;
            transition: all 0.2s;
        }
        .sidebar-link:hover, .sidebar-link.active {
            color: #ffffff;
            background-color: #2d3561;
        }
        .card {
            background-color: #16213e;
            border: 1px solid #2d3561;
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #6366f1;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #4f46e5;
        }
        .btn-secondary {
            background-color: #2d3561;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-secondary:hover {
            background-color: #3d4571;
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
            background-color: #0f1419;
            border: 1px solid #2d3561;
            color: #e0e0e0;
            padding: 0.5rem;
            border-radius: 6px;
            width: 100%;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #6366f1;
        }
        .form-label {
            color: #a0a0c0;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            background-color: #0f1419;
            padding: 0.75rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            color: #a0a0c0;
            border-bottom: 1px solid #2d3561;
        }
        table td {
            padding: 0.75rem;
            border-bottom: 1px solid #2d3561;
        }
        table tr:hover {
            background-color: #1f2937;
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
            background-color: #3b82f6;
            color: white;
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
            background-color: #1e3a8a;
            border: 1px solid #3b82f6;
            color: #dbeafe;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="sidebar w-64 flex-shrink-0 overflow-y-auto">
        <div class="p-6">
            <h1 class="text-xl font-bold text-white flex items-center">
                <span class="mr-2">🏠</span>
                Property Text Tools
            </h1>
            <p class="text-xs text-gray-400 mt-1">Superadmin Panel</p>
        </div>

        <nav class="px-4 pb-4">
            <a href="<?= base_url('superadmin/dashboard') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg <?= (current_url() == base_url('superadmin/dashboard')) ? 'active' : '' ?>">
                <span class="mr-3">📊</span>
                Dashboard
            </a>
            <a href="<?= base_url('superadmin/projects') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg <?= (strpos(current_url(), 'superadmin/projects') !== false) ? 'active' : '' ?>">
                <span class="mr-3">📁</span>
                Projects
            </a>
            <a href="<?= base_url('superadmin/users') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg <?= (strpos(current_url(), 'superadmin/users') !== false) ? 'active' : '' ?>">
                <span class="mr-3">👥</span>
                Admins
            </a>

            <div class="border-t border-gray-700 my-4"></div>

            <a href="<?= base_url('logout') ?>" class="sidebar-link flex items-center px-4 py-3 mb-1 rounded-lg text-red-400">
                <span class="mr-3">🚪</span>
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
        <header class="bg-[#16213e] border-b border-[#2d3561] px-6 py-4">
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
</body>
</html>
