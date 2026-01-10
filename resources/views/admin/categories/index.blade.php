@extends('admin.layout')

@section('title', 'Kategori')
@section('page_title', 'Kategori')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Manajemen Kategori</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" id="btnReload">Reload</button>
                    <button class="btn btn-primary btn-sm" id="btnNew">Tambah</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="width:70px;">NO</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Series Title</th>
                            <th style="width:90px;">Sort</th>
                            <th class="text-end" style="width:170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rows">
                        <tr>
                            <td colspan="6" class="text-muted">Memuat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Modal Add/Edit --}}
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="formAlert" class="alert alert-danger d-none" role="alert"></div>

                    <input type="hidden" id="id">

                    <div class="mb-2">
                        <label class="form-label">Nama (name_short)</label>
                        <input class="form-control" id="name_short" placeholder="Contoh: Tea">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Slug</label>
                        <input class="form-control" id="slug" placeholder="Contoh: tea">
                        <div class="form-text">Huruf kecil, tanpa spasi (gunakan dash).</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Series Title</label>
                        <input class="form-control" id="series_title" placeholder="Contoh: AUTHENTIC TEA SERIES">
                        <div class="form-text">Ini yang akan tampil di detail produk (diambil dari kategori).</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="sort_order" placeholder="1" min="1">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" id="btnSave">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let categories = [];
        const modal = new bootstrap.Modal(document.getElementById('modalForm'));

        function fAlert(msg) {
            const el = document.getElementById('formAlert');
            el.textContent = msg;
            el.classList.remove('d-none');
        }
        function fClear() {
            const el = document.getElementById('formAlert');
            el.classList.add('d-none');
            el.textContent = '';
        }

        function setForm(cat) {
            document.getElementById('id').value = cat?.id ?? '';
            document.getElementById('name_short').value = cat?.name_short ?? '';
            document.getElementById('slug').value = cat?.slug ?? '';
            document.getElementById('series_title').value = cat?.series_title ?? '';
            document.getElementById('sort_order').value = cat?.sort_order ?? 1;
        }

        function renderTable(items) {
            const rows = items.map((c, index) => `
            <tr>
              <td>${index + 1}</td>
              <td>${c.name_short ?? '-'}</td>
              <td class="text-muted small">${c.slug ?? '-'}</td>
              <td class="text-muted small">${c.series_title ?? '-'}</td>
              <td>${c.sort_order ?? '-'}</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" data-act="edit" data-id="${c.id}">Edit</button>
                <button class="btn btn-sm btn-outline-danger" data-act="del" data-id="${c.id}">Hapus</button>
              </td>
            </tr>
          `).join('');

            document.getElementById('rows').innerHTML =
                rows || `<tr><td colspan="6" class="text-muted">Tidak ada data</td></tr>`;
        }

        async function loadCats() {
            await window.__adminUserPromise;

            try {
                const res = await apiFetch('/admin/categories');
                categories = Array.isArray(res.data) ? res.data : [];
                categories.sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0) || (a.id - b.id));
                renderTable(categories);
            } catch (e) {
                setAlert(e.message || 'Gagal memuat kategori');
            }
        }

        document.getElementById('btnReload').addEventListener('click', loadCats);

        document.getElementById('btnNew').addEventListener('click', () => {
            fClear();
            document.getElementById('modalTitle').textContent = 'Tambah Kategori';
            setForm({ sort_order: 1, series_title: '' });
            modal.show();
        });

        document.getElementById('rows').addEventListener('click', async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;

            const id = btn.getAttribute('data-id');
            const act = btn.getAttribute('data-act');

            if (act === 'edit') {
                fClear();
                document.getElementById('modalTitle').textContent = 'Edit Kategori';

                try {
                    const res = await apiFetch(`/admin/categories/${id}`);
                    setForm(res.data);
                    modal.show();
                } catch (err) {
                    setAlert(err.message || 'Gagal memuat detail kategori');
                }
            }

            if (act === 'del') {
                const cat = categories.find(x => String(x.id) === String(id));
                const label = cat ? `${cat.name_short} (${cat.slug})` : `ID ${id}`;

                if (!confirm(`Hapus kategori: ${label}?`)) return;

                try {
                    await apiFetch(`/admin/categories/${id}`, { method: 'DELETE' });
                    await loadCats();
                } catch (err) {
                    setAlert(err.message || 'Gagal menghapus kategori');
                }
            }
        });

        document.getElementById('btnSave').addEventListener('click', async () => {
            fClear();

            const id = document.getElementById('id').value;

            const payload = {
                name_short: document.getElementById('name_short').value.trim(),
                slug: document.getElementById('slug').value.trim(),
                series_title: document.getElementById('series_title').value.trim() || null,
                sort_order: Number(document.getElementById('sort_order').value || 1),
            };

            if (!payload.name_short) return fAlert('Nama kategori wajib diisi.');
            if (!payload.slug) return fAlert('Slug wajib diisi.');

            try {
                if (!id) {
                    await apiFetch('/admin/categories', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                } else {
                    await apiFetch(`/admin/categories/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                }

                modal.hide();
                await loadCats();
            } catch (err) {
                fAlert(err.message || 'Gagal menyimpan kategori');
            }
        });

        loadCats();
    </script>
@endpush