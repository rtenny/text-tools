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
    <?= view('partials/_head', ['title' => $title ?? null, 'defaultTitle' => 'Text Tools', 'appSuffix' => 'ListingLingo Text Tools']) ?>
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-[#1A1C1E] text-[#e0e0e0] min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-[#25282C] border-b border-[#3a3d42] px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i data-lucide="building-2" class="w-6 h-6"></i>
                <div>
                    <h1 class="text-lg font-bold text-white">ListingLingo Text Tools</h1>
                    <p class="text-xs text-gray-400"><?= esc($projectName ?? 'User Panel') ?></p>
                </div>
            </div>
            <div class="flex items-center space-x-6">
                <span class="text-sm text-gray-400">
                    <?= esc($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?>
                </span>
                <a href="<?= base_url('logout') ?>" class="text-red-400 hover:text-red-300 text-sm flex items-center">
                    <i data-lucide="log-out" class="mr-1 w-4 h-4"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Content Area -->
    <main class="flex-1 p-6">
        <div class="max-w-7xl mx-auto">
            <?= view('partials/_flash_messages') ?>
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <script src="<?= base_url('js/utils.js') ?>"></script>
    <script src="<?= base_url('js/translator.js') ?>"></script>
    <script src="<?= base_url('js/rewriter.js') ?>"></script>
    <script src="<?= base_url('js/generator.js') ?>"></script>
    <script src="<?= base_url('js/alttext.js') ?>"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
    <?= view('partials/_page_script') ?>
</body>
</html>
