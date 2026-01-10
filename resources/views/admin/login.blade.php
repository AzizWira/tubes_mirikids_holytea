<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - HolyTea</title>

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Login Admin</h4>
                        <p class="text-muted mb-4">Masuk menggunakan akun admin1 / admin2.</p>

                        <div id="alert" class="alert alert-danger d-none" role="alert"></div>

                        <form id="loginForm" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="fake_username"
                                    autocomplete="off" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="fake_password"
                                    autocomplete="new-password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="btnLogin">
                                Masuk
                            </button>
                        </form>

                        <hr class="my-4">
                        <div class="small text-muted">
                            Default:
                            <code>admin1/admin123</code> atau <code>admin2/admin123</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/api';
        const TOKEN_KEY = 'holytea_admin_token';
        const USER_KEY = 'holytea_admin_user';

        function showError(msg) {
            const el = document.getElementById('alert');
            el.textContent = msg;
            el.classList.remove('d-none');
        }

        function hideError() {
            const el = document.getElementById('alert');
            el.classList.add('d-none');
            el.textContent = '';
        }

        // Kalau sudah punya token, coba verifikasi -> langsung ke dashboard
        (async function autoRedirectIfLoggedIn() {
            const token = localStorage.getItem(TOKEN_KEY);
            if (!token) return;

            try {
                const res = await fetch(`${API_BASE}/auth/me`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                    }
                });

                if (res.ok) {
                    window.location.href = '/admin/dashboard';
                } else {
                    // token invalid
                    localStorage.removeItem(TOKEN_KEY);
                    localStorage.removeItem(USER_KEY);
                }
            } catch (e) {
                // kalau server down, biarkan user login manual
            }
        })();

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            hideError();

            const btn = document.getElementById('btnLogin');
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            const payload = {
                username: document.getElementById('username').value.trim(),
                password: document.getElementById('password').value,
            };

            try {
                const res = await fetch(`${API_BASE}/auth/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const json = await res.json().catch(() => null);

                if (!res.ok) {
                    const msg = json?.message || 'Gagal login. Periksa username/password.';
                    showError(msg);
                    return;
                }

                // simpan token + user
                localStorage.setItem(TOKEN_KEY, json.data.token);
                localStorage.setItem(USER_KEY, JSON.stringify(json.data.user));

                // redirect ke dashboard
                window.location.href = '/admin/dashboard';
            } catch (err) {
                showError('Tidak bisa terhubung ke server.');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Masuk';
            }
        });
    </script>

</body>

</html>