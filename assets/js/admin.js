// Admin panel shared JS - currently chart rendering is inline in admin pages
// This file is available for future admin interactions
document.addEventListener('DOMContentLoaded', () => {
    // Auto-dismiss status messages after 3 seconds
    const statusEl = document.querySelectorAll('[id$="-status"]');
    statusEl.forEach(el => {
        if (!el.classList.contains('hidden')) {
            setTimeout(() => el.classList.add('hidden'), 3000);
        }
    });
});
