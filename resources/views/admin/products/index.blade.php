@extends('admin.layout')

@section('title', 'Produk')
@section('page_title', 'Produk')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex gap-2 mb-3">
                <input type="text" id="q" class="form-control" placeholder="Cari nama/slug...">
                <button class="btn btn-outline-primary" id="btnSearch">Cari</button>
                <button class="btn btn-primary" id="btnNew">Tambah</button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="width:70px;">NO</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Harga</th>
                            <th>Kategori</th>
                            <th>Series</th>
                            <th>Aktif</th>
                            <th class="text-end" style="width:170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rows">
                        <tr>
                            <td colspan="8" class="text-muted">Memuat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted" id="meta"></div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" id="prev">Prev</button>
                    <button class="btn btn-sm btn-outline-secondary" id="next">Next</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add/Edit --}}
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="formAlert" class="alert alert-danger d-none"></div>
                    <input type="hidden" id="id">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input class="form-control" id="name" placeholder="Nama produk">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input class="form-control" id="slug" placeholder="contoh: lemon-tea">
                            <div class="form-text">Huruf kecil, tanpa spasi (pakai dash).</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" id="price" min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" id="category_id"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Series (dari kategori)</label>
                            <div class="form-control bg-light" id="series_preview">-</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Gambar Produk</label>
                            <div class="d-flex gap-3 align-items-start">
                                <div style="width:120px;">
                                    <img id="imgPreview" src="" alt="preview" class="img-thumbnail w-100"
                                        style="aspect-ratio: 1 / 1; object-fit: cover; display:none;">
                                </div>

                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" id="image_file"
                                        accept="image/png,image/jpeg,image/webp">
                                    <div class="form-text">
                                        JPG/PNG/WEBP max 2MB. Saat simpan, file akan di-upload lalu `image_url` otomatis
                                        terisi.
                                    </div>

                                    <input type="hidden" id="image_url">
                                    <div class="small text-muted mt-2" id="imageUrlText"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea class="form-control" id="short_description" rows="2"></textarea>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">GoFood URL</label>
                            <input class="form-control" id="gofood_url" placeholder="https://...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GrabFood URL</label>
                            <input class="form-control" id="grabfood_url" placeholder="https://...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ShopeeFood URL</label>
                            <input class="form-control" id="shopeefood_url" placeholder="https://...">
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="fw-semibold mb-2">Nutrition</div>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Calories (kcal)</label>
                            <input type="number" class="form-control" id="calories_kcal" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sugar (g)</label>
                            <input type="number" class="form-control" id="sugar_g" step="0.01" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Protein (g)</label>
                            <input type="number" class="form-control" id="protein_g" step="0.01" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fat (g)</label>
                            <input type="number" class="form-control" id="fat_g" step="0.01" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <input class="form-control" id="nutrition_note" placeholder="Per porsi (estimasi)">
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="fw-semibold mb-2">Options (Dinamis)</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">Ukuran</div>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="addOptRow('size')">Tambah</button>
                            </div>
                            <div id="opt-size" class="d-grid gap-2"></div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">Es</div>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="addOptRow('ice')">Tambah</button>
                            </div>
                            <div id="opt-ice" class="d-grid gap-2"></div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">Gula</div>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="addOptRow('sugar')">Tambah</button>
                            </div>
                            <div id="opt-sugar" class="d-grid gap-2"></div>
                        </div>
                    </div>

                    <template id="tpl-opt-row">
                        <div class="input-group input-group-sm">
                            <input type="hidden" class="opt-id">
                            <input type="text" class="form-control opt-label" placeholder="Label (contoh: Regular)">
                            <button class="btn btn-outline-danger" type="button" onclick="removeOptRow(this)">✕</button>
                        </div>
                    </template>

                    <div class="mt-3 form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" checked>
                        <label class="form-check-label">Aktif</label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal" id="btnCancel">Batal</button>
                    <button class="btn btn-primary" id="btnSave">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let lastPayload = null;
        let categories = [];
        const modal = new bootstrap.Modal(document.getElementById('modalForm'));

        const $ = (id) => document.getElementById(id);

        function fAlert(msg) {
            const el = $('formAlert');
            el.textContent = msg;
            el.classList.remove('d-none');
        }
        function fClear() {
            const el = $('formAlert');
            el.classList.add('d-none');
            el.textContent = '';
        }

        function setBusy(isBusy) {
            $('btnSave').disabled = isBusy;
            $('btnCancel').disabled = isBusy;
            $('btnSave').textContent = isBusy ? 'Menyimpan...' : 'Simpan';
        }

        function getToken() {
            // Sesuaikan kalau nama key token kamu berbeda
            return localStorage.getItem('holytea_admin_token') || localStorage.getItem('token') || '';
        }

        async function uploadProductImage(file) {
            const fd = new FormData();
            fd.append('image', file);

            const res = await fetch('/api/admin/upload/product-image', {
                method: 'POST',
                credentials: 'omit',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${getToken()}`
                },
                body: fd,
            });

            const json = await res.json().catch(() => ({}));

            if (!res.ok || !json.success) {
                throw new Error(json.message || 'Upload gagal');
            }

            return json.data.url; // /storage/products/xxx.webp
        }

        function renderCategorySelect(selectedId = null) {
            const sel = $('category_id');

            // NO "All" -> semua produk wajib punya kategori
            const opts = categories.map(c => {
                return `<option value="${c.id}" data-series="${(c.series_title ?? '')}">
                  ${c.name_short ?? c.slug ?? ('ID ' + c.id)}
                </option>`;
            }).join('');

            sel.innerHTML = opts || `<option value="">(kategori kosong)</option>`;
            sel.disabled = categories.length === 0;

            if (selectedId !== null && selectedId !== undefined && selectedId !== '') {
                sel.value = String(selectedId);
            } else if (sel.options.length) {
                sel.selectedIndex = 0;
            }

            updateSeriesPreview();
        }

        function updateSeriesPreview() {
            const sel = $('category_id');
            const opt = sel?.options?.[sel.selectedIndex];
            const series = opt?.getAttribute('data-series') || '-';
            $('series_preview').textContent = series || '-';
        }

        async function loadCategories() {
            const res = await apiFetch('/admin/categories');
            categories = Array.isArray(res.data) ? res.data : [];

            // kalau masih ada kategori slug 'all' di DB, sembunyikan dari dropdown
            categories = categories.filter(c => String(c.slug || '').toLowerCase() !== 'all');

            categories.sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0) || (a.id - b.id));
        }

        function clearOptionsUI() {
            $('opt-size').innerHTML = '';
            $('opt-ice').innerHTML = '';
            $('opt-sugar').innerHTML = '';
        }

        window.addOptRow = function (type, data = null) {
            const host = document.getElementById(`opt-${type}`);
            const tpl = $('tpl-opt-row');
            const node = tpl.content.cloneNode(true);

            node.querySelector('.opt-id').value = data?.id ?? '';
            node.querySelector('.opt-label').value = data?.label ?? '';

            host.appendChild(node);
        }

        window.removeOptRow = function (btn) {
            btn.closest('.input-group').remove();
        }

        function fillOptionsUI(options) {
            clearOptionsUI();
            const types = ['size', 'ice', 'sugar'];
            for (const t of types) {
                const items = Array.isArray(options?.[t]) ? options[t] : [];
                items.forEach(it => addOptRow(t, it));
            }
        }

        function collectOptionsPayload() {
            const types = ['size', 'ice', 'sugar'];
            const out = { size: [], ice: [], sugar: [] };

            for (const t of types) {
                const host = document.getElementById(`opt-${t}`);
                const rows = host.querySelectorAll('.input-group');

                rows.forEach((row, idx) => {
                    const id = row.querySelector('.opt-id').value;
                    const label = row.querySelector('.opt-label').value.trim();
                    if (!label) return;

                    out[t].push({
                        id: id ? Number(id) : null,
                        label,
                        sort_order: idx + 1, // otomatis sesuai urutan UI
                        is_active: 1
                    });
                });
            }

            return out;
        }

        function setImagePreview(url) {
            const img = $('imgPreview');
            const txt = $('imageUrlText');

            if (url) {
                img.src = url;
                img.style.display = 'block';
                txt.textContent = url;
            } else {
                img.src = '';
                img.style.display = 'none';
                txt.textContent = '';
            }
        }

        function resetImageInput() {
            $('image_file').value = '';
        }

        function setForm(p) {
            $('id').value = p?.id ?? '';
            $('name').value = p?.name ?? '';
            $('slug').value = p?.slug ?? '';
            $('price').value = p?.price ?? 0;
            $('short_description').value = p?.short_description ?? '';
            $('is_active').checked = (p?.is_active ?? 1) ? true : false;

            $('gofood_url').value = p?.gofood_url ?? '';
            $('grabfood_url').value = p?.grabfood_url ?? '';
            $('shopeefood_url').value = p?.shopeefood_url ?? '';

            const n = p?.nutrition || null;
            $('calories_kcal').value = n?.calories_kcal ?? '';
            $('sugar_g').value = n?.sugar_g ?? '';
            $('protein_g').value = n?.protein_g ?? '';
            $('fat_g').value = n?.fat_g ?? '';
            $('nutrition_note').value = n?.note ?? '';

            // kategori + series
            renderCategorySelect(p?.category_id ?? null);

            // image
            $('image_url').value = p?.image_url ?? '';
            setImagePreview(p?.image_url ?? null);
            resetImageInput();

            // options
            fillOptionsUI(p?.options || { size: [], ice: [], sugar: [] });
        }

        function rowCategoryName(p) {
            if (p.category_name) return p.category_name;
            const c = categories.find(x => String(x.id) === String(p.category_id));
            return c?.name_short ?? c?.slug ?? (p.category_id ?? '-');
        }

        function rowSeriesTitle(p) {
            if (p.series_title) return p.series_title;
            const c = categories.find(x => String(x.id) === String(p.category_id));
            return c?.series_title ?? '-';
        }

        async function loadProducts(page = 1) {
            await window.__adminUserPromise;

            const q = $('q').value.trim();
            const url = `/admin/products?page=${page}${q ? `&q=${encodeURIComponent(q)}` : ''}`;

            try {
                const res = await apiFetch(url);
                lastPayload = res.data;

                const perPage = Number(lastPayload.per_page || 10);
                const curPage = Number(lastPayload.current_page || 1);
                const startNo = (curPage - 1) * perPage;

                const rows = (lastPayload.data || []).map((p, index) => `
                  <tr>
                    <td>${startNo + index + 1}</td>
                    <td>${p.name ?? '-'}</td>
                    <td class="text-muted small">${p.slug ?? '-'}</td>
                    <td>${p.price ?? 0}</td>
                    <td>${rowCategoryName(p)}</td>
                    <td class="text-muted small">${rowSeriesTitle(p)}</td>
                    <td>${p.is_active ? 'Ya' : 'Tidak'}</td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-outline-primary" data-id="${p.id}" data-act="edit">Edit</button>
                      <button class="btn btn-sm btn-outline-danger" data-id="${p.id}" data-act="del">Hapus</button>
                    </td>
                  </tr>
                `).join('');

                $('rows').innerHTML = rows || `<tr><td colspan="8" class="text-muted">Tidak ada data</td></tr>`;
                $('meta').textContent = `Page ${lastPayload.current_page} / ${lastPayload.last_page} • Total ${lastPayload.total}`;

                $('prev').disabled = !lastPayload.prev_page_url;
                $('next').disabled = !lastPayload.next_page_url;
            } catch (e) {
                setAlert(e.message || 'Gagal memuat produk');
            }
        }

        // preview saat pilih file
        $('image_file').addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            $('imgPreview').src = url;
            $('imgPreview').style.display = 'block';
            $('imageUrlText').textContent = 'File dipilih (akan di-upload saat Simpan)';
        });

        $('category_id').addEventListener('change', updateSeriesPreview);

        $('btnSearch').addEventListener('click', () => loadProducts(1));

        $('prev').addEventListener('click', () => {
            if (lastPayload?.prev_page_url) loadProducts((lastPayload.current_page || 1) - 1);
        });

        $('next').addEventListener('click', () => {
            if (lastPayload?.next_page_url) loadProducts((lastPayload.current_page || 1) + 1);
        });

        $('btnNew').addEventListener('click', async () => {
            fClear();
            $('modalTitle').textContent = 'Tambah Produk';
            setForm(null);
            clearOptionsUI();
            setImagePreview(null);
            modal.show();
        });

        $('rows').addEventListener('click', async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;

            const id = btn.getAttribute('data-id');
            const act = btn.getAttribute('data-act');

            if (act === 'edit') {
                fClear();
                $('modalTitle').textContent = 'Edit Produk';
                try {
                    const res = await apiFetch(`/admin/products/${id}`);
                    setForm(res.data);
                    modal.show();
                } catch (err) {
                    fAlert(err.message || 'Gagal memuat detail produk');
                }
            }

            if (act === 'del') {
                if (!confirm('Hapus produk ini?')) return;
                try {
                    await apiFetch(`/admin/products/${id}`, { method: 'DELETE' });
                    loadProducts(lastPayload?.current_page || 1);
                } catch (err) {
                    fAlert(err.message || 'Gagal menghapus produk');
                }
            }
        });

        $('btnSave').addEventListener('click', async () => {
            fClear();
            setBusy(true);

            try {
                const id = $('id').value;

                // upload dulu kalau ada file baru
                const file = $('image_file').files?.[0];
                let imageUrl = ($('image_url').value || '').trim() || null;

                if (file) {
                    imageUrl = await uploadProductImage(file);
                    $('image_url').value = imageUrl;
                    setImagePreview(imageUrl);
                    resetImageInput();
                }

                const payload = {
                    name: $('name').value.trim(),
                    slug: $('slug').value.trim(),
                    price: Number($('price').value || 0),
                    category_id: Number($('category_id').value || 0),
                    image_url: imageUrl,
                    short_description: $('short_description').value.trim() || null,

                    gofood_url: $('gofood_url').value.trim() || null,
                    grabfood_url: $('grabfood_url').value.trim() || null,
                    shopeefood_url: $('shopeefood_url').value.trim() || null,

                    nutrition: {
                        calories_kcal: $('calories_kcal').value ? Number($('calories_kcal').value) : null,
                        sugar_g: $('sugar_g').value ? Number($('sugar_g').value) : null,
                        protein_g: $('protein_g').value ? Number($('protein_g').value) : null,
                        fat_g: $('fat_g').value ? Number($('fat_g').value) : null,
                        note: $('nutrition_note').value.trim() || null,
                    },

                    options: collectOptionsPayload(),
                    is_active: $('is_active').checked ? 1 : 0,
                };

                if (!payload.name) return fAlert('Nama wajib diisi.');
                if (!payload.slug) return fAlert('Slug wajib diisi.');
                if (!payload.category_id) return fAlert('Kategori wajib dipilih.');
                if (categories.length === 0) return fAlert('Kategori kosong. Buat kategori dulu.');

                if (!id) {
                    await apiFetch('/admin/products', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                } else {
                    await apiFetch(`/admin/products/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                }

                modal.hide();
                loadProducts(lastPayload?.current_page || 1);
            } catch (err) {
                fAlert(err.message || 'Gagal menyimpan produk');
            } finally {
                setBusy(false);
            }
        });

        (async function init() {
            await window.__adminUserPromise;
            await loadCategories();
            renderCategorySelect(null);
            await loadProducts(1);
        })();
    </script>
@endpush