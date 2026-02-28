<!DOCTYPE html>
<html lang="en">
<head>
    <?= view('partials/_head', ['title' => $title ?? null, 'defaultTitle' => 'Superadmin Dashboard']) ?>
    <link rel="stylesheet" href="<?= base_url('css/theme.css') ?>">
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
                    <span class="text-sm text-gray-400"><?= date('l, F j, Y') ?></span>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto p-6">
            <?= view('partials/_flash_messages') ?>
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <?= $this->renderSection('scripts') ?>
    <?= view('partials/_page_script') ?>
</body>
</html>
