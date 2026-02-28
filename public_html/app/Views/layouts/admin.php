<?php
$currentUser = [
    'first_name' => session()->get('first_name'),
    'last_name'  => session()->get('last_name'),
    'email'      => session()->get('email'),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= view('partials/_head', ['title' => $title ?? null, 'defaultTitle' => 'Admin Dashboard']) ?>
    <link rel="stylesheet" href="<?= base_url('css/theme.css') ?>">
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
        <?= view('partials/_flash_messages') ?>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->renderSection('scripts') ?>
    <?= view('partials/_page_script') ?>
</body>
</html>
