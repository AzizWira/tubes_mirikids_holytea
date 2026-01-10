@extends('admin.layout')

@section('title', 'Testimoni')
@section('page_title', 'Testimoni')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                <input type="text" id="q" class="form-control" style="max-width:260px"
                    placeholder="Cari nama/produk/pesan...">

                <select id="filter_product_id" class="form-select" style="max-width:320px">
                    <option value="">Semua Produk</option>
                </select>

                <button class="btn btn-outline-primary" id="btnSearch">Cari</button>
                <button class="btn btn-primary" id="btnNew">Tambah</button>

                <div class="ms-auto small text-muted" id="meta"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="width:70px">NO</th>
                            <th>Produk</th>
                            <th>Nama</th>
                            <th style="width:90px">Rating</th>
                            <th>Pesan</th>
                            <th style="width:90px">Aktif</th>
                            <th class="text-end" style="width:180px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rows">
                        <tr>
                            <td colspan="7" class="text-muted">Memuat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="small text-muted" id="meta2"></div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" id="prev">Prev</button>
                    <button class="btn btn-sm btn-outline-secondary" id="next">Next</button>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Add/Edit --}}
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Testimoni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="formAlert" class="alert alert-danger d-none"></div>

                    <input type="hidden" id="id">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Produk <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_product_id" required></select>
                            <div class="form-text">Wajib pilih produk.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama (opsional)</label>
                            <input class="form-control" id="name" placeholder="contoh: Bima">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Rating (1-5)</label>
                            <select class="form-select" id="rating">
                                <option value="">(kosong)</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>

                        <div class="col-md-9">
                            <label class="form-label">Avatar URL (opsional)</label>
                            <input class="form-control" id="avatar_url"
                                placeholder="/storage/avatars/xxx.jpg atau https://...">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Pesan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" rows="3" placeholder="Isi testimoni..."></textarea>
                        </div>
                    </div>

                    <div class="mt-3 form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" checked>
                        <label class="form-check-label">Aktif</label>
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
        let lastPayload = null;
        let productOptions = [];
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

        function escapeHtml(str) {
            return String(str ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", "&#039;");
        }

        async function loadProductOptions() {
            await window.__adminUserPromise;
            const res = await apiFetch('/admin/products-options');
            productOptions = Array.isArray(res.data) ? res.data : [];
            renderFilterProducts();
        }

        function renderFilterProducts() {
            const sel = document.getElementById('filter_product_id');
            const current = sel.value;

            const opts = productOptions.map(p => {
                const label = `${p.name}${p.slug ? ' (@' + p.slug + ')' : ''}`;
                return `<option value="${p.id}">${escapeHtml(label)}</option>`;
            }).join('');

            sel.innerHTML = `<option value="">Semua Produk</option>` + opts;

            // restore selection if exists
            if (current) sel.value = current;
        }

        function renderProductSelect(selectedId = null) {
            const sel = document.getElementById('edit_product_id');
            const opts = productOptions.map(p => {
                const label = `${p.name}${p.slug ? ' (@' + p.slug + ')' : ''}`;
                return `<option value="${p.id}">${escapeHtml(label)}</option>`;
            }).join('');

            sel.innerHTML = opts || `<option value="">(produk kosong)</option>`;

            if (selectedId) sel.value = String(selectedId);
            else sel.selectedIndex = 0;
        }

        function setForm(t) {
            document.getElementById('id').value = t?.id ?? '';
            renderProductSelect(t?.product_id ?? null);

            document.getElementById('name').value = t?.name ?? '';
            document.getElementById('rating').value = t?.rating ?? '';
            document.getElementById('message').value = t?.message ?? '';
            document.getElementById('avatar_url').value = t?.avatar_url ?? '';
            document.getElementById('edit_is_active').checked = (t?.is_active ?? 1) ? true : false;
        }

        function renderTable(payload) {
            const items = payload?.data || [];
            const rows = items.map((t, idx) => {
                const no = ((payload.current_page - 1) * payload.per_page) + (idx + 1);
                const productLabel = t.product_name ? `${t.product_name}${t.product_slug ? ' (@' + t.product_slug + ')' : ''}` : `ID ${t.product_id}`;
                const rating = t.rating ? t.rating : '-';
                const msg = escapeHtml(t.message ?? '').slice(0, 140) + ((t.message ?? '').length > 140 ? '…' : '');

                return `
                            <tr>
                                <td>${no}</td>
                                <td class="fw-semibold">${escapeHtml(productLabel)}</td>
                                <td>${escapeHtml(t.name ?? '-')}</td>
                                <td>${rating}</td>
                                <td class="text-muted small">${msg}</td>
                                <td>${t.is_active ? 'Ya' : 'Tidak'}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-act="edit" data-id="${t.id}">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" data-act="del" data-id="${t.id}">Hapus</button>
                                </td>
                            </tr>
                        `;
            }).join('');

            document.getElementById('rows').innerHTML = rows || `<tr><td colspan="7" class="text-muted">Tidak ada data</td></tr>`;
            document.getElementById('meta').textContent = `Page ${payload.current_page} / ${payload.last_page} • Total ${payload.total}`;
            document.getElementById('meta2').textContent = `Menampilkan ${items.length} data`;

            document.getElementById('prev').disabled = !payload.prev_page_url;
            document.getElementById('next').disabled = !payload.next_page_url;
        }

        async function loadTestimonials(page = 1) {
            await window.__adminUserPromise;

            const q = document.getElementById('q').value.trim();
            const productId = document.getElementById('filter_product_id').value;

            let url = `/admin/testimonials?page=${page}`;
            if (q) url += `&q=${encodeURIComponent(q)}`;
            if (productId) url += `&product_id=${encodeURIComponent(productId)}`;

            const res = await apiFetch(url);
            lastPayload = res.data;
            renderTable(lastPayload);
        }

        document.getElementById('btnSearch').addEventListener('click', () => loadTestimonials(1));
        document.getElementById('filter_product_id').addEventListener('change', () => loadTestimonials(1));

        document.getElementById('prev').addEventListener('click', () => {
            if (lastPayload?.prev_page_url) loadTestimonials((lastPayload.current_page || 1) - 1);
        });
        document.getElementById('next').addEventListener('click', () => {
            if (lastPayload?.next_page_url) loadTestimonials((lastPayload.current_page || 1) + 1);
        });

        document.getElementById('btnNew').addEventListener('click', () => {
            fClear();
            document.getElementById('modalTitle').textContent = 'Tambah Testimoni';
            setForm({ is_active: 1, rating: '' });
            modal.show();
        });

        document.getElementById('rows').addEventListener('click', async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;

            const id = btn.getAttribute('data-id');
            const act = btn.getAttribute('data-act');

            if (act === 'edit') {
                fClear();
                document.getElementById('modalTitle').textContent = 'Edit Testimoni';
                try {
                    const res = await apiFetch(`/admin/testimonials/${id}`);
                    setForm(res.data);
                    modal.show();
                } catch (err) {
                    fAlert(err.message || 'Gagal memuat detail testimoni');
                }
            }

            if (act === 'del') {
                if (!confirm('Hapus testimoni ini?')) return;
                try {
                    await apiFetch(`/admin/testimonials/${id}`, { method: 'DELETE' });
                    await loadTestimonials(lastPayload?.current_page || 1);
                } catch (err) {
                    fAlert(err.message || 'Gagal menghapus testimoni');
                }
            }
        });

        document.getElementById('btnSave').addEventListener('click', async () => {
            fClear();

            const id = document.getElementById('id').value;

            const productId = Number(document.getElementById('edit_product_id').value || 0);
            if (!productId) return fAlert('Produk wajib dipilih.');

            const message = document.getElementById('message').value.trim();
            if (!message) return fAlert('Pesan wajib diisi.');

            const payload = {
                product_id: productId,
                name: document.getElementById('name').value.trim() || null,
                rating: document.getElementById('rating').value ? Number(document.getElementById('rating').value) : null,
                message,
                avatar_url: document.getElementById('avatar_url').value.trim() || null,
                is_active: document.getElementById('edit_is_active').checked ? 1 : 0,
            };

            try {
                if (!id) {
                    await apiFetch(`/admin/testimonials`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                } else {
                    await apiFetch(`/admin/testimonials/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                }

                modal.hide();
                await loadTestimonials(lastPayload?.current_page || 1);
            } catch (err) {
                fAlert(err.message || 'Gagal menyimpan testimoni');
            }
        });

        (async function init() {
            await window.__adminUserPromise;
            await loadProductOptions();
            await loadTestimonials(1);
        })();
    </script>
@endpush