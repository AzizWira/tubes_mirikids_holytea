@extends('admin.layout')

@section('title', 'News Banner')
@section('page_title', 'News Banner')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="fw-semibold">Daftar Banner</div>
                    <div class="text-muted small">Kelola banner yang tampil di beranda.</div>
                </div>
                <button class="btn btn-primary btn-sm" id="btnAdd">Tambah</button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Image URL</th>
                            <th>Aktif</th>
                            <th>Sort</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="5" class="text-muted">Memuat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="formBanner">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="bannerId">
                    <div class="mb-3">
                        <label class="form-label">Judul (opsional)</label>
                        <input type="text" class="form-control" id="title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Banner *</label>
                        <div class="d-flex gap-2">
                            <input type="file" class="form-control" id="image_file" accept="image/*">
                        </div>
                        <div class="form-text">Unggah gambar banner. Wajib diisi untuk data baru.</div>
                        <div class="small" id="uploadStatus"></div>
                        <div class="mt-2">
                            <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded border d-none" style="max-height:140px">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Sort Order</label>
                            <input type="number" min="0" class="form-control" id="sort_order" value="0">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        Gunakan sort order terkecil untuk ditampilkan paling awal.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let modal, editingId = null, currentImageUrl = null;

        const tbody = document.getElementById('tableBody');
        const form = document.getElementById('formBanner');
        const modalTitle = document.getElementById('modalTitle');

        const field = id => document.getElementById(id);

        function renderRows(list) {
            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-muted">Belum ada data.</td></tr>`;
                return;
            }
            tbody.innerHTML = list.map(item => `
                <tr data-id="${item.id}">
                    <td>${item.title ?? '-'}</td>
                    <td class="small text-truncate" style="max-width:220px">${item.image_url}</td>
                    <td>
                        <span class="badge ${item.is_active ? 'bg-success' : 'bg-secondary'}">
                            ${item.is_active ? 'Aktif' : 'Nonaktif'}
                        </span>
                    </td>
                    <td>${item.sort_order ?? 0}</td>
                    <td class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-secondary" onclick="editBanner(${item.id})">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBanner(${item.id})">Hapus</button>
                    </td>
                </tr>
            `).join('');
        }

        async function loadData() {
            clearAlert();
            tbody.innerHTML = `<tr><td colspan="5" class="text-muted">Memuat...</td></tr>`;
            try {
                const data = await apiFetch('/admin/news-banners');
                const sorted = (data || []).sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
                renderRows(sorted);
            } catch (e) {
                setAlert(e.message || 'Gagal memuat data.');
                tbody.innerHTML = `<tr><td colspan="5" class="text-danger">Gagal memuat.</td></tr>`;
            }
        }

        function openModal(createMode = true, data = {}) {
            editingId = createMode ? null : data.id;
            modalTitle.textContent = createMode ? 'Tambah Banner' : 'Edit Banner';

            field('title').value = data.title ?? '';
            field('sort_order').value = data.sort_order ?? 0;
            field('is_active').checked = data.is_active ?? true;

            // Track current image and update preview
            currentImageUrl = data.image_url ?? null;
            const preview = document.getElementById('imagePreview');
            if (currentImageUrl) {
                preview.src = currentImageUrl;
                preview.classList.remove('d-none');
            } else {
                preview.src = '';
                preview.classList.add('d-none');
            }

            modal.show();
        }

        window.editBanner = async (id) => {
            try {
                const data = await apiFetch(`/admin/news-banners/${id}`);
                openModal(false, data);
            } catch (e) {
                setAlert(e.message || 'Gagal memuat data.');
            }
        };

        window.deleteBanner = async (id) => {
            if (!confirm('Hapus banner ini?')) return;
            try {
                await apiFetch(`/admin/news-banners/${id}`, { method: 'DELETE' });
                await loadData();
            } catch (e) {
                setAlert(e.message || 'Gagal menghapus.');
            }
        };

        async function uploadImage(file) {
            const status = document.getElementById('uploadStatus');
            status.textContent = 'Mengunggah...';
            const fd = new FormData();
            fd.append('file', file);

            const res = await fetch(`${API_BASE}/admin/upload/news-banner-image`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem(TOKEN_KEY) || ''}`,
                },
                body: fd,
            });

            const json = await res.json().catch(() => null);
            if (!res.ok) {
                throw new Error(json?.message || 'Gagal upload');
            }
            status.textContent = 'Upload sukses';
            return json.url;
        }

        document.getElementById('image_file').addEventListener('change', async (e) => {
            const file = e.target.files?.[0];
            if (!file) return;
            try {
                const url = await uploadImage(file);
                currentImageUrl = url;
                const preview = document.getElementById('imagePreview');
                preview.src = url;
                preview.classList.remove('d-none');
            } catch (err) {
                setAlert(err.message);
                document.getElementById('uploadStatus').textContent = 'Upload gagal';
            } finally {
                e.target.value = '';
            }
        });

        form.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            clearAlert();

            const payload = {
                title: field('title').value || null,
                image_url: currentImageUrl,
                sort_order: Number(field('sort_order').value || 0),
                is_active: field('is_active').checked ? 1 : 0,
            };

            // Require image on create; keep existing on edit
            if (!editingId && !payload.image_url) {
                setAlert('Silakan unggah gambar banner terlebih dahulu.');
                return;
            }

            try {
                if (editingId) {
                    await apiFetch(`/admin/news-banners/${editingId}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                } else {
                    await apiFetch('/admin/news-banners', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                }
                modal.hide();
                await loadData();
            } catch (e) {
                setAlert(e.message || 'Gagal menyimpan.');
            }
        });

        document.getElementById('btnAdd').addEventListener('click', () => openModal(true, {}));

        (async function initPage() {
            await window.__adminUserPromise; // guard
            modal = new bootstrap.Modal(document.getElementById('bannerModal'));
            loadData();
        })();
    </script>
@endpush