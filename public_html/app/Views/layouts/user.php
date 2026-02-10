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
    <script src="https://unpkg.com/lucide@latest"></script>
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
                    <h1 class="text-lg font-bold text-white">Property Text Tools</h1>
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
