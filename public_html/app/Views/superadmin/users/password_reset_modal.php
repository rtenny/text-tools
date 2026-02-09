<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Link - <?= esc($user['email']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a2e;
            color: #e0e0e0;
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
        .form-input {
            background-color: #0f1419;
            border: 1px solid #2d3561;
            color: #e0e0e0;
            padding: 0.5rem;
            border-radius: 6px;
            width: 100%;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-2xl">
        <div class="card p-8">
            <div class="flex items-center mb-6">
                <span class="text-4xl mr-4">🔑</span>
                <div>
                    <h1 class="text-2xl font-bold text-white">Password Reset Link</h1>
                    <p class="text-gray-400 text-sm mt-1">For: <?= esc($user['email']) ?></p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm text-gray-400 mb-2">Reset Link:</label>
                <div class="flex items-center space-x-2">
                    <input type="text"
                           id="reset-link"
                           value="<?= esc($resetLink) ?>"
                           class="form-input font-mono text-sm"
                           readonly>
                    <button onclick="copyResetLink()"
                            id="copy-button"
                            class="btn-primary whitespace-nowrap">
                        📋 Copy Link
                    </button>
                </div>
            </div>

            <div class="bg-yellow-900 bg-opacity-20 border border-yellow-500 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-200">
                    <strong>⚠️ Important:</strong> This link expires in 1 hour. Share it with the admin immediately.
                </p>
            </div>

            <div class="bg-indigo-900 bg-opacity-20 border border-indigo-500 rounded-lg p-4">
                <p class="text-sm text-indigo-200 mb-2">
                    <strong>Instructions for the Admin:</strong>
                </p>
                <ol class="text-sm text-gray-300 space-y-1 list-decimal list-inside">
                    <li>Click the password reset link</li>
                    <li>Enter a new secure password</li>
                    <li>Login with their email and new password</li>
                </ol>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <button onclick="window.close()"
                        class="text-gray-400 hover:text-white text-sm">
                    Close Window
                </button>
                <a href="<?= base_url('superadmin/users') ?>"
                   class="text-indigo-400 hover:text-indigo-300 text-sm">
                    ← Back to Admins
                </a>
            </div>
        </div>

        <div class="card p-4 mt-4">
            <div class="flex items-start">
                <span class="text-xl mr-3">💡</span>
                <div>
                    <p class="text-xs text-gray-400">
                        <strong>Tip:</strong> You can generate a new password reset link at any time from the admins list.
                        Previous links will remain valid until they expire.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyResetLink() {
        const input = document.getElementById('reset-link');
        const button = document.getElementById('copy-button');

        // Select and copy
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices

        try {
            document.execCommand('copy');

            // Update button
            const originalText = button.innerHTML;
            button.innerHTML = '✅ Copied!';
            button.classList.add('bg-green-600');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600');
            }, 2000);
        } catch (err) {
            alert('Failed to copy. Please copy manually.');
        }

        // Deselect
        window.getSelection().removeAllRanges();
    }

    // Auto-select on page load
    window.onload = function() {
        document.getElementById('reset-link').select();
    };
    </script>
</body>
</html>
