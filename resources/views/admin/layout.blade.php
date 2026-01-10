<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - HolyTea')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/admin/dashboard">HolyTea Admin</a>

        <div class="d-flex align-items-center gap-2">
            <span class="text-white-50 small" id="adminUserInfo">Loading...</span>
            <button class="btn btn-outline-light btn-sm" id="btnLogout">Logout</button>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <aside class="col-md-2 bg-white border-end min-vh-100 p-3">
            <div class="fw-bold mb-2">Menu</div>

            <div class="list-group" id="sideMenu">
                <a href="/admin/dashboard" class="list-group-item list-group-item-action">Dashboard</a>

                <div class="mt-3 small text-muted">Admin 1</div>
                <a href="/admin/products" class="list-group-item list-group-item-action" data-role="admin1">Products</a>
                <a href="/admin/categories" class="list-group-item list-group-item-action" data-role="admin1">Categories</a>

                <div class="mt-3 small text-muted">Admin 2</div>
                <a href="/admin/testimonials" class="list-group-item list-group-item-action" data-role="admin2">Testimonials</a>
                <a href="/admin/settings" class="list-group-item list-group-item-action" data-role="admin2">Settings</a>
            </div>

            <div class="text-muted small mt-4">
                Role-based access: <span class="fw-semibold">admin1</span> / <span class="fw-semibold">admin2</span>
            </div>
        </aside>

        <main class="col-md-10 p-4">
            @yield('content')
        </main>

    </div>
</div>

<script>
    function getToken() {
        return localStorage.getItem('admin_token');
    }

    async function apiFetch(url, options = {}) {
        const token = getToken();
        const headers = Object.assign({
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }, options.headers || {});

        if (token) {
            headers['Authorization'] = 'Bearer ' + token;
        }

        const res = await fetch(url, { ...options, headers });
        const contentType = res.headers.get('content-type') || '';

        let data = null;
        if (contentType.includes('application/json')) {
            data = await res.json();
        } else {
            data = await res.text();
        }

        if (!res.ok) {
            const msg = (data && data.message) ? data.message : ('Request failed: ' + res.status);
            throw new Error(msg);
        }
        return data;
    }

    function logout() {
        localStorage.removeItem('admin_token');
        localStorage.removeItem('admin_user');
        window.location.href = '/login';
    }

    async function guardAdmin() {
        const token = getToken();
        if (!token) {
            return logout();
        }

        try {
            const me = await apiFetch('/api/auth/me', { method: 'GET' });
            const role = me?.data?.role || '-';
            const username = me?.data?.username || '-';

            window.__ADMIN_ROLE__ = role;

            document.getElementById('adminUserInfo').textContent = username + ' (' + role + ')';

            // hide menu yang tidak sesuai role
            document.querySelectorAll('#sideMenu a[data-role]').forEach(a => {
                const needRole = a.getAttribute('data-role');
                if (needRole && needRole !== role) {
                    a.classList.add('d-none');
                }
            });

            // highlight active
            const path = window.location.pathname;
            document.querySelectorAll('#sideMenu a').forEach(a => {
                if (a.getAttribute('href') === path) {
                    a.classList.add('active');
                }
            });

        } catch (e) {
            // token invalid
            logout();
        }
    }

    document.getElementById('btnLogout').addEventListener('click', async () => {
        try {
            await apiFetch('/api/auth/logout', { method: 'POST' });
        } catch (e) {
            // ignore
        }
        logout();
    });

    guardAdmin();
</script>

@stack('scripts')
</body>
</html>
