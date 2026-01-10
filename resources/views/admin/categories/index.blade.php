@extends('admin.layout')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Categories</h3>
    <button class="btn btn-primary" id="btnAdd">Tambah</button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Sort</th>
                        <th>Active</th>
                        <th style="width:160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <tr><td colspan="6" class="text-muted">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Form Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="alertBox" class="alert alert-danger d-none"></div>

        <input type="hidden" id="catId">

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input class="form-control" id="name">
        </div>

        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input class="form-control" id="slug">
        </div>

        <div class="mb-3">
            <label class="form-label">Sort Order</label>
            <input class="form-control" id="sort_order" type="number" min="0">
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" checked>
            <label class="form-check-label">Active</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary" id="btnSave">Simpan</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const tbody = document.getElementById('tbody');
    const modal = new bootstrap.Modal(document.getElementById('modalForm'));
    const alertBox = document.getElementById('alertBox');

    function showError(msg){ alertBox.textContent = msg; alertBox.classList.remove('d-none'); }
    function hideError(){ alertBox.classList.add('d-none'); alertBox.textContent=''; }
    function esc(s){ return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;'); }

    async function loadData() {
        try {
            tbody.innerHTML = `<tr><td colspan="6" class="text-muted">Loading...</td></tr>`;
            const res = await apiFetch('/api/admin/categories', { method: 'GET' });
            const items = res.data || [];
            window.__CATS__ = items;

            if (!items.length) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-muted">Belum ada data.</td></tr>`;
                return;
            }

            tbody.innerHTML = items.map((it, idx) => `
                <tr>
                    <td>${idx+1}</td>
                    <td>${esc(it.name ?? '')}</td>
                    <td class="text-muted">${esc(it.slug ?? '')}</td>
                    <td>${it.sort_order ?? 0}</td>
                    <td>${it.is_active ? 'Yes' : 'No'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editItem(${it.id})">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteItem(${it.id})">Hapus</button>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-danger">${esc(e.message)}</td></tr>`;
        }
    }

    function openCreate(){
        hideError();
        document.getElementById('modalTitle').textContent = 'Tambah Category';
        document.getElementById('catId').value = '';
        document.getElementById('name').value = '';
        document.getElementById('slug').value = '';
        document.getElementById('sort_order').value = 0;
        document.getElementById('is_active').checked = true;
        modal.show();
    }

    window.editItem = (id) => {
        hideError();
        const it = (window.__CATS__ || []).find(x => x.id === id);
        if (!it) return;
        document.getElementById('modalTitle').textContent = 'Edit Category';
        document.getElementById('catId').value = it.id;
        document.getElementById('name').value = it.name ?? '';
        document.getElementById('slug').value = it.slug ?? '';
        document.getElementById('sort_order').value = it.sort_order ?? 0;
        document.getElementById('is_active').checked = !!it.is_active;
        modal.show();
    };

    document.getElementById('btnAdd').addEventListener('click', openCreate);

    document.getElementById('btnSave').addEventListener('click', async () => {
        hideError();

        const id = document.getElementById('catId').value;
        const payload = {
            name: document.getElementById('name').value.trim(),
            slug: document.getElementById('slug').value.trim(),
            sort_order: Number(document.getElementById('sort_order').value || 0),
            is_active: document.getElementById('is_active').checked ? 1 : 0,
        };

        if (!payload.name || !payload.slug) return showError('Name dan slug wajib diisi.');

        try {
            if (!id) {
                await apiFetch('/api/admin/categories', { method: 'POST', body: JSON.stringify(payload) });
            } else {
                await apiFetch('/api/admin/categories/' + id, { method: 'PUT', body: JSON.stringify(payload) });
            }
            modal.hide();
            await loadData();
        } catch (e) {
            showError(e.message);
        }
    });

    window.deleteItem = async (id) => {
        if (!confirm('Hapus category ini?')) return;
        try {
            await apiFetch('/api/admin/categories/' + id, { method: 'DELETE' });
            await loadData();
        } catch (e) {
            alert(e.message);
        }
    };

    loadData();
</script>
@endpush
@endsection
