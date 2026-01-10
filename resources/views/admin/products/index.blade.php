@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Products</h3>
    <button class="btn btn-primary" id="btnAdd">Tambah</button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>Harga</th>
                        <th>Active</th>
                        <th>Best</th>
                        <th style="width:160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <tr><td colspan="7" class="text-muted">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Form Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="alertBox" class="alert alert-danger d-none"></div>

        <input type="hidden" id="productId">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" id="name">
            </div>
            <div class="col-md-6">
                <label class="form-label">Slug</label>
                <input class="form-control" id="slug">
            </div>

            <div class="col-md-6">
                <label class="form-label">Price</label>
                <input class="form-control" id="price" type="number" min="0">
            </div>

            <div class="col-md-6">
                <label class="form-label">Sort Order</label>
                <input class="form-control" id="sort_order" type="number" min="0">
            </div>

            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="description" rows="3"></textarea>
            </div>

            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="is_active" checked>
                    <label class="form-check-label">Active</label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="is_best_seller">
                    <label class="form-check-label">Best Seller</label>
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label">Upload Image (opsional)</label>
                <input class="form-control" type="file" id="imageFile" accept="image/*">
                <div class="form-text">Akan upload ke storage dan disimpan sebagai 1 gambar di field images[0].</div>
            </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnSave">Simpan</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const tbody = document.getElementById('tbody');
    const modalEl = document.getElementById('modalForm');
    const modal = new bootstrap.Modal(modalEl);

    const alertBox = document.getElementById('alertBox');

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
    }
    function hideError() {
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
    }

    function rowHtml(item, idx) {
        return `
            <tr>
                <td>${idx+1}</td>
                <td>${escapeHtml(item.name ?? '')}</td>
                <td class="text-muted">${escapeHtml(item.slug ?? '')}</td>
                <td>${item.price ?? 0}</td>
                <td>${item.is_active ? 'Yes' : 'No'}</td>
                <td>${item.is_best_seller ? 'Yes' : 'No'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editItem(${item.id})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem(${item.id})">Hapus</button>
                </td>
            </tr>
        `;
    }

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    async function loadData() {
        try {
            tbody.innerHTML = `<tr><td colspan="7" class="text-muted">Loading...</td></tr>`;
            const res = await apiFetch('/api/admin/products', { method: 'GET' });
            const items = res.data || [];
            if (!items.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-muted">Belum ada data.</td></tr>`;
                return;
            }
            tbody.innerHTML = items.map((it, idx) => rowHtml(it, idx)).join('');
            window.__PRODUCTS__ = items;
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-danger">${escapeHtml(e.message)}</td></tr>`;
        }
    }

    function openCreate() {
        hideError();
        document.getElementById('modalTitle').textContent = 'Tambah Product';
        document.getElementById('productId').value = '';
        document.getElementById('name').value = '';
        document.getElementById('slug').value = '';
        document.getElementById('price').value = 0;
        document.getElementById('sort_order').value = 0;
        document.getElementById('description').value = '';
        document.getElementById('is_active').checked = true;
        document.getElementById('is_best_seller').checked = false;
        document.getElementById('imageFile').value = '';
        modal.show();
    }

    window.editItem = function(id) {
        hideError();
        const item = (window.__PRODUCTS__ || []).find(x => x.id === id);
        if (!item) return;

        document.getElementById('modalTitle').textContent = 'Edit Product';
        document.getElementById('productId').value = item.id;
        document.getElementById('name').value = item.name ?? '';
        document.getElementById('slug').value = item.slug ?? '';
        document.getElementById('price').value = item.price ?? 0;
        document.getElementById('sort_order').value = item.sort_order ?? 0;
        document.getElementById('description').value = item.description ?? '';
        document.getElementById('is_active').checked = !!item.is_active;
        document.getElementById('is_best_seller').checked = !!item.is_best_seller;
        document.getElementById('imageFile').value = '';
        modal.show();
    }

    async function uploadIfAny() {
        const file = document.getElementById('imageFile').files[0];
        if (!file) return null;

        const token = localStorage.getItem('admin_token');
        const form = new FormData();
        form.append('image', file);

        const res = await fetch('/api/admin/upload/product-image', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: form
        });

        const json = await res.json();
        if (!res.ok) {
            throw new Error(json?.message || 'Upload failed');
        }
        return json?.data?.url || null;
    }

    document.getElementById('btnAdd').addEventListener('click', openCreate);

    document.getElementById('btnSave').addEventListener('click', async () => {
        hideError();

        const id = document.getElementById('productId').value;
        const payload = {
            name: document.getElementById('name').value.trim(),
            slug: document.getElementById('slug').value.trim(),
            price: Number(document.getElementById('price').value || 0),
            sort_order: Number(document.getElementById('sort_order').value || 0),
            description: document.getElementById('description').value,
            is_active: document.getElementById('is_active').checked ? 1 : 0,
            is_best_seller: document.getElementById('is_best_seller').checked ? 1 : 0,
        };

        if (!payload.name || !payload.slug) return showError('Name dan slug wajib diisi.');

        try {
            const uploadedUrl = await uploadIfAny();
            if (uploadedUrl) {
                payload.images = [uploadedUrl];
            }

            if (!id) {
                await apiFetch('/api/admin/products', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
            } else {
                await apiFetch('/api/admin/products/' + id, {
                    method: 'PUT',
                    body: JSON.stringify(payload)
                });
            }

            modal.hide();
            await loadData();
        } catch (e) {
            showError(e.message);
        }
    });

    window.deleteItem = async function(id) {
        if (!confirm('Hapus product ini?')) return;
        try {
            await apiFetch('/api/admin/products/' + id, { method: 'DELETE' });
            await loadData();
        } catch (e) {
            alert(e.message);
        }
    }

    loadData();
</script>
@endpush
@endsection
