<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - HolyTea</title>

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4 fw-bold">Login Admin</h4>

                        <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>

                        <form id="loginForm">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" autocomplete="username" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" autocomplete="current-password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="btnLogin">
                                Masuk
                            </button>
                        </form>

                        <p class="text-center text-muted mt-3 small mb-0">
                            © {{ date('Y') }} HolyTea
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        const form = document.getElementById('loginForm');
        const alertBox = document.getElementById('alertBox');
        const btn = document.getElementById('btnLogin');

        function showError(msg) {
            alertBox.textContent = msg;
            alertBox.classList.remove('d-none');
        }

        function hideError() {
            alertBox.classList.add('d-none');
            alertBox.textContent = '';
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideError();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                return showError('Username dan password wajib diisi.');
            }

            btn.disabled = true;
            btn.textContent = 'Memproses...';

            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        username,
                        password
                    })
                });

                const json = await res.json();

                if (!res.ok) {
                    return showError(json?.message || 'Login gagal.');
                }

                // simpan token
                localStorage.setItem('admin_token', json.data.token);
                localStorage.setItem('admin_user', JSON.stringify(json.data.user));

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
