<?php require_once __DIR__ . '/session.php'; require_once __DIR__ . '/config.php'; require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="stylesheet" href="/assets/css/custom.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4f46e5',
                        'primary-dark': '#4338ca',
                        secondary: '#6366f1',
                        accent: '#4f46e5',
                        success: '#16a34a',
                        warning: '#ea580c',
                        danger: '#dc2626',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white min-h-screen flex flex-col font-sans text-gray-900 antialiased">
    <!-- Spacer for fixed nav -->
    <div class="h-16 md:h-20"></div>

    <nav class="fixed top-0 left-0 right-0 z-50 px-3 md:px-4 pt-2 md:pt-3">
        <div class="max-w-5xl mx-auto bg-white/70 backdrop-blur-xl border border-gray-200/60 rounded-2xl shadow-[0_2px_20px_-6px_rgba(0,0,0,0.08)]">
            <div class="flex justify-between items-center h-11 md:h-12 px-3 md:px-4">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-1.5 flex-shrink-0">
                    <svg class="w-5 h-5 md:w-6 md:h-6" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <span class="text-xs md:text-sm font-semibold text-gray-900">ATS Analyzer</span>
                </a>

                <!-- Desktop Nav -->
                <?php
                    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
                    $currentPath = rtrim($currentPath, '/') ?: '/';
                ?>
                <div class="hidden md:flex items-center gap-1 relative" id="nav-links">
                    <div id="nav-indicator" class="absolute h-[30px] bg-gray-100 rounded-lg transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] opacity-0 top-1/2 -translate-y-1/2"></div>
                    <a href="/" data-nav class="nav-link relative z-10 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors <?= $currentPath === '/' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' ?>">Home</a>
                    <a href="/analyzer.php" data-nav class="nav-link relative z-10 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors <?= $currentPath === '/analyzer.php' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' ?>">Analyzer</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="/history.php" data-nav class="nav-link relative z-10 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors <?= $currentPath === '/history.php' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' ?>">History</a>
                        <?php if (isAdmin()): ?>
                            <a href="/admin" data-nav class="nav-link relative z-10 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors <?= str_starts_with($currentPath, '/admin') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' ?>">Admin</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Desktop Right -->
                <div class="hidden md:flex items-center gap-3">
                    <?php if (isLoggedIn()):
                        $initials = '';
                        $name = currentUserName() ?? 'User';
                        $parts = explode(' ', trim($name));
                        $initials = strtoupper(substr($parts[0], 0, 1));
                        if (count($parts) > 1) $initials .= strtoupper(substr(end($parts), 0, 1));
                    ?>
                        <div class="relative" id="profile-dropdown-wrapper">
                            <button id="profile-btn" class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100/80 transition" type="button">
                                <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center">
                                    <span class="text-[10px] font-semibold text-white leading-none"><?= e($initials) ?></span>
                                </div>
                            </button>
                            <!-- Dropdown -->
                            <div id="profile-dropdown" class="profile-dropdown absolute right-0 top-full mt-2 w-56 bg-white border border-gray-200/80 rounded-xl shadow-lg shadow-gray-200/50 py-1 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900 truncate"><?= e($name) ?></p>
                                    <p class="text-xs text-gray-400 truncate"><?= e(currentUserEmail() ?? '') ?></p>
                                </div>
                                <div class="py-1">
                                    <a href="/history.php" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        History
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <a href="/admin/" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Admin Panel
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <div class="border-t border-gray-100 py-1">
                                    <a href="/logout.php" class="flex items-center gap-2.5 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login.php" class="text-xs font-medium text-gray-500 hover:text-gray-900 transition px-2 py-1">Log in</a>
                        <a href="/register.php" class="text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 px-3.5 py-1.5 rounded-lg transition">Sign up</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100/80 transition">
                    <svg id="menu-icon-open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="menu-icon-close" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="mobile-menu md:hidden">
                <div class="px-3 pb-3 pt-1 border-t border-gray-100/80">
                    <a href="/" class="block px-3 py-2 text-sm rounded-lg transition <?= $currentPath === '/' ? 'text-gray-900 bg-gray-100/80 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' ?>">Home</a>
                    <a href="/analyzer.php" class="block px-3 py-2 text-sm rounded-lg transition <?= $currentPath === '/analyzer.php' ? 'text-gray-900 bg-gray-100/80 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' ?>">Analyzer</a>
                    <?php if (isLoggedIn()):
                        $mName = currentUserName() ?? 'User';
                        $mParts = explode(' ', trim($mName));
                        $mInitials = strtoupper(substr($mParts[0], 0, 1));
                        if (count($mParts) > 1) $mInitials .= strtoupper(substr(end($mParts), 0, 1));
                    ?>
                        <a href="/history.php" class="block px-3 py-2 text-sm rounded-lg transition <?= $currentPath === '/history.php' ? 'text-gray-900 bg-gray-100/80 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' ?>">History</a>
                        <?php if (isAdmin()): ?>
                            <a href="/admin/" class="block px-3 py-2 text-sm rounded-lg transition <?= str_starts_with($currentPath, '/admin') ? 'text-gray-900 bg-gray-100/80 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' ?>">Admin</a>
                        <?php endif; ?>
                        <div class="border-t border-gray-100/80 mt-2 pt-2">
                            <div class="flex items-center gap-3 px-3 py-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-white leading-none"><?= e($mInitials) ?></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate"><?= e($mName) ?></p>
                                    <p class="text-xs text-gray-400 truncate"><?= e(currentUserEmail() ?? '') ?></p>
                                </div>
                            </div>
                            <a href="/logout.php" class="flex items-center gap-2.5 px-3 py-2 mt-1 text-sm text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="border-t border-gray-100/80 mt-1 pt-1 flex gap-2 px-3 py-2">
                            <a href="/login.php" class="flex-1 text-center text-xs font-medium text-gray-600 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Log in</a>
                            <a href="/register.php" class="flex-1 text-center text-xs font-medium text-white bg-indigo-600 py-2 rounded-lg hover:bg-indigo-700 transition">Sign up</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-6 md:py-8">
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            const menu = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('menu-icon-open');
            const iconClose = document.getElementById('menu-icon-close');
            menu.classList.toggle('open');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });

        // Nav active indicator — slides from previous tab to current on page load
        const navLinks = document.getElementById('nav-links');
        const indicator = document.getElementById('nav-indicator');
        if (navLinks && indicator) {
            const links = Array.from(navLinks.querySelectorAll('[data-nav]'));
            const path = window.location.pathname.replace(/\/+$/, '') || '/';
            let activeIndex = -1;

            links.forEach((link, i) => {
                const linkPath = link.getAttribute('href').replace(/\/+$/, '') || '/';
                if (path === linkPath || (linkPath !== '/' && path.startsWith(linkPath))) {
                    activeIndex = i;
                }
            });

            if (activeIndex >= 0) {
                const containerRect = navLinks.getBoundingClientRect();
                const activeRect = links[activeIndex].getBoundingClientRect();
                const newLeft = activeRect.left - containerRect.left;
                const newWidth = activeRect.width;

                const prevIndex = parseInt(sessionStorage.getItem('nav-active-index') ?? '-1');

                if (prevIndex >= 0 && prevIndex !== activeIndex && prevIndex < links.length) {
                    // Start at previous tab position, then animate to current
                    const prevRect = links[prevIndex].getBoundingClientRect();
                    indicator.style.transition = 'none';
                    indicator.style.left = (prevRect.left - containerRect.left) + 'px';
                    indicator.style.width = prevRect.width + 'px';
                    indicator.style.opacity = '1';
                    indicator.offsetHeight; // force reflow
                    indicator.style.transition = '';
                    indicator.style.left = newLeft + 'px';
                    indicator.style.width = newWidth + 'px';
                } else {
                    // No previous or same tab — just show in place
                    indicator.style.left = newLeft + 'px';
                    indicator.style.width = newWidth + 'px';
                    indicator.style.opacity = '1';
                }

                sessionStorage.setItem('nav-active-index', activeIndex);
            }
        }

        // Profile dropdown toggle
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');
        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('open');
            });
            document.addEventListener('click', (e) => {
                if (!document.getElementById('profile-dropdown-wrapper')?.contains(e.target)) {
                    profileDropdown.classList.remove('open');
                }
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') profileDropdown.classList.remove('open');
            });
        }
    </script>
