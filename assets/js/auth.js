document.addEventListener('DOMContentLoaded', () => {
    // Login form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorEl = document.getElementById('login-error');
            const spinner = document.getElementById('login-spinner');
            const btn = document.getElementById('login-btn');

            errorEl.classList.add('hidden');
            spinner.classList.remove('hidden');
            btn.disabled = true;

            try {
                const formData = new FormData(loginForm);

                // Pass redirect param if present in URL
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('redirect')) {
                    formData.append('redirect', urlParams.get('redirect'));
                }

                const res = await fetch('/api/login.php', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    window.location.href = data.redirect || '/analyzer.php';
                } else {
                    errorEl.textContent = data.error || 'Login failed.';
                    errorEl.classList.remove('hidden');
                }
            } catch (err) {
                errorEl.textContent = 'Network error. Please try again.';
                errorEl.classList.remove('hidden');
            } finally {
                spinner.classList.add('hidden');
                btn.disabled = false;
            }
        });
    }

    // Register form
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorEl = document.getElementById('register-error');
            const spinner = document.getElementById('register-spinner');
            const btn = document.getElementById('register-btn');

            errorEl.classList.add('hidden');

            // Client-side validation
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;

            if (password !== confirm) {
                errorEl.textContent = 'Passwords do not match.';
                errorEl.classList.remove('hidden');
                return;
            }

            if (password.length < 8) {
                errorEl.textContent = 'Password must be at least 8 characters.';
                errorEl.classList.remove('hidden');
                return;
            }

            spinner.classList.remove('hidden');
            btn.disabled = true;

            try {
                const formData = new FormData(registerForm);
                const res = await fetch('/api/register.php', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    window.location.href = data.redirect || '/analyzer.php';
                } else {
                    errorEl.textContent = data.error || 'Registration failed.';
                    errorEl.classList.remove('hidden');
                }
            } catch (err) {
                errorEl.textContent = 'Network error. Please try again.';
                errorEl.classList.remove('hidden');
            } finally {
                spinner.classList.add('hidden');
                btn.disabled = false;
            }
        });
    }
});
