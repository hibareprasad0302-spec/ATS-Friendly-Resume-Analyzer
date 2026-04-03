<?php
$pageTitle = 'Login';
$pageScript = 'auth.js';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="stylesheet" href="/assets/css/custom.css">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] }, colors: { primary: '#4f46e5', 'primary-dark': '#4338ca', danger: '#dc2626' } } }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center font-sans text-gray-900 antialiased px-4 relative overflow-hidden">

    <!-- Decorative blobs -->
    <div class="hero-blob" style="width:400px;height:400px;background:radial-gradient(circle,#818cf8 0%,transparent 70%);top:-100px;right:-120px;animation:blobDrift1 10s ease-in-out infinite;opacity:0.6"></div>
    <div class="hero-blob" style="width:350px;height:350px;background:radial-gradient(circle,#a78bfa 0%,transparent 70%);bottom:-80px;left:-100px;animation:blobDrift2 12s ease-in-out infinite;opacity:0.55"></div>
    <div class="hero-blob" style="width:200px;height:200px;background:radial-gradient(circle,#22d3ee 0%,transparent 70%);top:40%;left:-40px;animation:blobDrift1 14s ease-in-out infinite;opacity:0.4"></div>

    <div class="relative z-10 w-full max-w-sm">
        <!-- Logo -->
        <a href="/" class="flex items-center justify-center gap-2 mb-8">
            <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="4" y="2" width="18" height="24" rx="3" fill="#4f46e5" opacity="0.15"/>
                <rect x="4" y="2" width="18" height="24" rx="3" stroke="#4f46e5" stroke-width="1.5" fill="none"/>
                <line x1="8" y1="9" x2="18" y2="9" stroke="#4f46e5" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
                <line x1="8" y1="13" x2="16" y2="13" stroke="#4f46e5" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
                <line x1="8" y1="17" x2="14" y2="17" stroke="#4f46e5" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
                <circle cx="22" cy="22" r="8" fill="white"/>
                <circle cx="22" cy="22" r="7" fill="none" stroke="#e5e7eb" stroke-width="2"/>
                <path d="M 22 15 A 7 7 0 1 1 15.05 19.5" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round"/>
                <path d="M 19 22 L 21 24 L 25.5 19.5" fill="none" stroke="#4f46e5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="text-base font-semibold text-gray-900">ATS Analyzer</span>
        </a>

        <!-- Card -->
        <div class="bg-white border border-gray-200/80 rounded-2xl p-8 shadow-sm">
            <h1 class="text-xl font-semibold text-gray-900 mb-6 text-center">Welcome back</h1>

            <form id="login-form">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none text-sm"
                        placeholder="you@example.com">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2.5 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none text-sm"
                            placeholder="Enter your password">
                        <button type="button" class="pwd-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" data-target="password">
                            <svg class="eye-open w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="eye-closed w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3l18 18M10.5 10.677a2 2 0 002.823 2.823"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.362 7.561C5.68 8.74 4.279 10.42 3 12c1.889 2.991 5.282 6 9 6 1.55 0 3.043-.523 4.395-1.35M12 6c3.718 0 7.111 3.009 9 6-.947 1.498-2.057 2.85-3.362 3.83"/></svg>
                        </button>
                    </div>
                </div>

                <p id="login-error" class="text-sm text-danger mb-4 hidden"></p>

                <button type="submit" id="login-btn"
                    class="w-full bg-gray-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800 transition flex items-center justify-center gap-2">
                    <span>Login</span>
                    <span class="spinner hidden" id="login-spinner"></span>
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-6">
                Don't have an account? <a href="/register.php" class="text-indigo-600 hover:text-indigo-700">Sign up</a>
            </p>
        </div>
    </div>

    <script src="/assets/js/<?= e($pageScript) ?>"></script>
    <script>
    document.querySelectorAll('.pwd-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.querySelector('.eye-open').classList.toggle('hidden', isHidden);
            btn.querySelector('.eye-closed').classList.toggle('hidden', !isHidden);
        });
    });
    </script>
</body>
</html>
