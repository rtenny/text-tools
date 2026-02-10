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
    <title><?= esc($title ?? 'Text Tools') ?> - Property Text Tools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-[#1a1a2e] text-[#e0e0e0] min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-[#16213e] border-b border-[#2d3561] px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">🏠</span>
                <div>
                    <h1 class="text-lg font-bold text-white">Property Text Tools</h1>
                    <p class="text-xs text-gray-400"><?= esc($projectName ?? 'User Panel') ?></p>
                </div>
            </div>
            <div class="flex items-center space-x-6">
                <span class="text-sm text-gray-400">
                    <?= esc($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?>
                </span>
                <a href="<?= base_url('logout') ?>" class="text-red-400 hover:text-red-300 text-sm flex items-center">
                    <span class="mr-1">🚪</span> Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Content Area -->
    <main class="flex-1 p-6">
        <div class="max-w-7xl mx-auto">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success mb-4 p-4 rounded-lg bg-[#065f46] border border-[#10b981] text-[#d1fae5]">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error mb-4 p-4 rounded-lg bg-[#7f1d1d] border border-[#ef4444] text-[#fecaca]">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <script src="<?= base_url('js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
