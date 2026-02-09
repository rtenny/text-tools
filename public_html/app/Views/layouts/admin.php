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
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #2d3561;
        }
        th {
            background-color: #0f1419;
            font-weight: 600;
            color: #a0a0c0;
        }
        tr:hover {
            background-color: #1a1f3a;
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
            background-color: #1e3a8a;
            border: 1px solid #3b82f6;
            color: #dbeafe;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="flex h-screen">
    <!-- Sidebar -->
    <aside class="sidebar w-64 flex-shrink-0">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-white flex items-center">
                <span class="mr-2">🏠</span>
                Text Tools
            </h1>
            <p class="text-sm text-gray-400 mt-1">Admin Panel</p>
        </div>

        <nav class="mt-6">
            <a href="<?= base_url('admin/dashboard') ?>"
               class="sidebar-link flex items-center px-6 py-3 <?= uri_string() === 'admin/dashboard' ? 'active' : '' ?>">
                <span class="mr-3">📊</span>
                Dashboard
            </a>
            <a href="<?= base_url('admin/users') ?>"
               class="sidebar-link flex items-center px-6 py-3 <?= strpos(uri_string(), 'admin/users') === 0 ? 'active' : '' ?>">
                <span class="mr-3">👥</span>
                Manage Users
            </a>
        </nav>

        <!-- User Info & Logout -->
        <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm font-medium text-white"><?= esc($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></p>
                    <p class="text-xs text-gray-400"><?= esc($currentUser['email']) ?></p>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" class="btn-secondary block text-center text-sm">
                🚪 Logout
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
</body>
</html>
