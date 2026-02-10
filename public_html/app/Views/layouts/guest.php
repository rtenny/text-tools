<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login') ?> - Property Text Tools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Luxury Slate Theme */
        body {
            background: linear-gradient(135deg, #1A1C1E 0%, #25282C 100%);
            min-height: 100vh;
        }
        .card {
            background-color: #25282C;
            border: 1px solid #3a3d42;
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #D4AF37;
            color: #1A1C1E;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #C29F2F;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
        }
        .form-input {
            background-color: #1A1C1E;
            border: 1px solid #3a3d42;
            color: #e0e0e0;
            padding: 0.75rem;
            border-radius: 6px;
            width: 100%;
        }
        .form-input:focus {
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
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white flex items-center justify-center">
                <i data-lucide="building-2" class="mr-2 w-8 h-8"></i>
                Property Text Tools
            </h1>
            <p class="text-gray-400 mt-2">AI-Powered Property Descriptions</p>
        </div>

        <!-- Main Card -->
        <div class="card p-8">
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

            <?= $this->renderSection('content') ?>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                &copy; <?= date('Y') ?> Property Text Tools. All rights reserved.
            </p>
        </div>
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
