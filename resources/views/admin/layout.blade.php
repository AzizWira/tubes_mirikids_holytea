<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .sidebar {
            min-height: 100vh;
        }

        .sidebar .nav-link.active {
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">

            {{-- SIDEBAR --}}
            <aside class="col-12 col-md-3 col-lg-2 bg-dark text-white sidebar p-3">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <div class="fw-bold">HolyTea</div>
                        <div class="small text-white-50" id="roleLabel">Admin</div>
                    </div>
                    <button class="btn btn-sm btn-outline-light d-md-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#sidebarMenu">
                        Menu
                    </button>
                </div>

                <div class="collapse d-md-block" id="sidebarMenu">
                    <div id="sidebarContent" class="d-grid gap-2">
                        {{-- menu akan di-render via JS berdasarkan role --}}
                    </div>

                    <hr class="border-secondary my-4">

                    <button class="btn btn-outline-light w-100" id="btnLogout">Logout</button>
                </div>
            </aside>

            {{-- MAIN --}}
            <main class="col-12 col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="m-0">@yield('page_title', 'Dashboard')</h4>
                    <div class="small text-muted" id="userBadge"></div>
                </div>

                <div id="pageAlert" class="alert alert-danger d-none" role="alert"></div>

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const API_BASE = '/api';
        const TOKEN_KEY = 'holytea_admin_token';
        const USER_KEY = 'holytea_admin_user';

        const ADMIN1_PAGES = ['/admin/products', '/admin/categories'];
        const ADMIN2_PAGES = ['/admin/testimonials', '/admin/settings'];

        function setAlert(msg) {
            const el = document.getElementById('pageAlert');
            el.textContent = msg;
            el.classList.remove('d-none');
        }

        function clearAlert() {
            const el = document.getElementById('pageAlert');
            el.classList.add('d-none');
            el.textContent = '';
        }

        function hardLogoutToLogin() {
            localStorage.removeItem(TOKEN_KEY);
            localStorage.removeItem(USER_KEY);
            window.location.href = '/login';
        }

        async function apiFetch(path, options = {}) {
            const token = localStorage.getItem(TOKEN_KEY);

            const headers = {
                'Accept': 'application/json',
                ...(options.headers || {}),
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await fetch(`${API_BASE}${path}`, { ...options, headers });
            const json = await res.json().catch(() => null);

            // token invalid / expired
            if (res.status === 401) {
                hardLogoutToLogin();
                return null;
            }

            if (!res.ok) {
                const msg = json?.message || 'Terjadi kesalahan.';
                throw new Error(msg);
            }

            return json;
        }

        function renderSidebar(role) {
            const currentPath = window.location.pathname;
            const el = document.getElementById('sidebarContent');

            const item = (href, label) => {
                const active = currentPath === href ? 'active' : '';
                return `<a href="${href}" class="nav-link text-white ${active}">${label}</a>`;
            };

            if (role === 'admin1') {
                document.getElementById('roleLabel').textContent = 'HolyTea Admin 1';
                el.innerHTML = `
                    <div class="nav flex-column gap-2">
                        ${item('/admin/dashboard', 'Dashboard')}
                        ${item('/admin/products', 'Produk')}
                        ${item('/admin/categories', 'Kategori')}
                    </div>
                `;
            } else if (role === 'admin2') {
                document.getElementById('roleLabel').textContent = 'HolyTea Admin 2';
                el.innerHTML = `
                    <div class="nav flex-column gap-2">
                        ${item('/admin/dashboard', 'Dashboard')}
                        ${item('/admin/testimonials', 'Testimoni')}
                        ${item('/admin/settings', 'Pengaturan')}
                    </div>
                `;
            } else {
                document.getElementById('roleLabel').textContent = 'Admin';
                el.innerHTML = `
                    <div class="nav flex-column gap-2">
                        ${item('/admin/dashboard', 'Dashboard')}
                    </div>
                `;
            }
        }

        function enforceRoleAccess(role) {
            const path = window.location.pathname;

            // dashboard selalu boleh
            if (path === '/admin/dashboard') return;

            // admin1 tidak boleh akses admin2 pages
            if (ADMIN2_PAGES.includes(path) && role !== 'admin2') {
                window.location.href = '/admin/dashboard';
                return;
            }

            // admin2 tidak boleh akses admin1 pages
            if (ADMIN1_PAGES.includes(path) && role !== 'admin1') {
                window.location.href = '/admin/dashboard';
                return;
            }
        }

        async function requireAuthAndInitLayout() {
            clearAlert();

            const token = localStorage.getItem(TOKEN_KEY);

            // kalau tidak ada token, lempar login
            if (!token) {
                window.location.href = '/login';
                return null;
            }

            // selalu ambil user terbaru dari API (biar role up to date)
            const me = await apiFetch('/auth/me');
            if (!me) return null;

            const user = me.data;

            // kalau user inactive, logout
            if (user?.is_active === 0 || user?.is_active === false) {
                hardLogoutToLogin();
                return null;
            }

            localStorage.setItem(USER_KEY, JSON.stringify(user));

            document.getElementById('userBadge').textContent = `${user.username} • ${user.role}`;
            renderSidebar(user.role);

            // role guard: kalau akses halaman yang bukan haknya → dashboard
            enforceRoleAccess(user.role);

            return user;
        }

        // init layout auth guard
        window.__adminUserPromise = requireAuthAndInitLayout();

        document.getElementById('btnLogout').addEventListener('click', async () => {
            try {
                await apiFetch('/auth/logout', { method: 'POST' });
            } catch (_) {
                // ignore
            } finally {
                hardLogoutToLogin();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>