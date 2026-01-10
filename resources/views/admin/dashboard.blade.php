@extends('admin.layout')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Varian Menu</div>
                    <div class="fs-3 fw-bold" id="countVarianMenu">-</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Varian Rasa</div>
                    <div class="fs-3 fw-bold" id="countVarianRasa">-</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small mb-2">Info Outlet</div>
                    <div class="fw-semibold" id="siteAddress">-</div>
                    <div class="small text-muted" id="siteHours">-</div>
                    <div class="small text-muted" id="siteContact">-</div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold">News Banner</div>
                        <span class="small text-muted" id="newsCount">-</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Image</th>
                                    <th>Sort</th>
                                </tr>
                            </thead>
                            <tbody id="newsTable">
                                <tr>
                                    <td colspan="3" class="text-muted">Memuat...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (async function initDashboard() {
            // tunggu auth init dari layout
            await window.__adminUserPromise;

            try {
                const home = await apiFetch('/home'); // API publik, tapi tetap oke dipakai dashboard
                const data = home.data;

                document.getElementById('countVarianMenu').textContent = data.counts?.varian_menu ?? '-';
                document.getElementById('countVarianRasa').textContent = data.counts?.varian_rasa ?? '-';

                const site = data.site || {};
                document.getElementById('siteAddress').textContent = site.address ?? '-';
                document.getElementById('siteHours').textContent =
                    `${site.open_days ?? ''} • ${site.open_hours ?? ''} (Jumat: ${site.friday_hours ?? '-'})`;
                document.getElementById('siteContact').textContent =
                    `${site.phone ?? '-'} • ${site.email ?? '-'}`;

                const news = Array.isArray(data.news) ? data.news : [];
                document.getElementById('newsCount').textContent = `${news.length} item`;

                const rows = news
                    .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
                    .map(n => `
                        <tr>
                            <td>${n.title ?? '-'}</td>
                            <td class="text-muted small">${n.image_url ?? '-'}</td>
                            <td>${n.sort_order ?? '-'}</td>
                        </tr>
                    `).join('');

                document.getElementById('newsTable').innerHTML = rows || `<tr><td colspan="3" class="text-muted">Tidak ada data</td></tr>`;
            } catch (e) {
                setAlert(e.message || 'Gagal memuat dashboard.');
            }
        })();
    </script>
@endpush