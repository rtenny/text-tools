<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login') ?> - Property Text Tools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
        }
        .card {
            background-color: #16213e;
            border: 1px solid #2d3561;
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #6366f1;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            transition: background-color 0.2s;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #4f46e5;
        }
        .form-input {
            background-color: #0f1419;
            border: 1px solid #2d3561;
            color: #e0e0e0;
            padding: 0.75rem;
            border-radius: 6px;
            width: 100%;
        }
        .form-input:focus {
            outline: none;
            border-color: #6366f1;
        }
        .form-label {
            color: #a0a0c0;
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
            background-color: #1e3a8a;
            border: 1px solid #3b82f6;
            color: #dbeafe;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white flex items-center justify-center">
                <span class="mr-2">🏠</span>
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
</body>
</html>
