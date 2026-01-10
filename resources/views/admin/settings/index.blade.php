@extends('admin.layout')

@section('title', 'Pengaturan')
@section('page_title', 'Pengaturan')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Pengaturan Website</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" id="btnReload">Reload</button>
                    <button class="btn btn-primary btn-sm" id="btnSave">Simpan</button>
                </div>
            </div>

            <div id="alert" class="alert alert-danger d-none"></div>
            <div id="ok" class="alert alert-success d-none">Berhasil disimpan.</div>

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">Google Maps URL</label>
                    <input class="form-control" id="maps_url" placeholder="https://www.google.com/maps/place/...">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Instagram URL</label>
                    <input class="form-control" id="instagram_url" placeholder="https://www.instagram.com/...">
                </div>

                <div class="col-12">
                    <label class="form-label">Maps Embed URL</label>
                    <textarea class="form-control" id="maps_embed_url" rows="2"
                        placeholder="https://www.google.com/maps?q=...&output=embed"></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" rows="2" placeholder="Alamat outlet..."></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Hari Buka</label>
                    <input class="form-control" id="open_days" placeholder="Senin - Minggu">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jam Buka</label>
                    <input class="form-control" id="open_hours" placeholder="10am - 9pm">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jam Jumat</label>
                    <input class="form-control" id="friday_hours" placeholder="1pm - 9pm">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Telepon</label>
                    <input class="form-control" id="phone" placeholder="+62...">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" id="email" placeholder="email@domain.com">
                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showErr(msg) {
            const el = document.getElementById('alert');
            el.textContent = msg;
            el.classList.remove('d-none');
            document.getElementById('ok').classList.add('d-none');
        }
        function clearErr() {
            const el = document.getElementById('alert');
            el.classList.add('d-none');
            el.textContent = '';
        }
        function showOk() {
            document.getElementById('ok').classList.remove('d-none');
            document.getElementById('alert').classList.add('d-none');
        }

        function fillForm(d) {
            const keys = ['maps_url', 'maps_embed_url', 'address', 'open_days', 'open_hours', 'friday_hours', 'phone', 'email', 'instagram_url'];
            keys.forEach(k => {
                const el = document.getElementById(k);
                if (el) el.value = d?.[k] ?? '';
            });
        }

        function collect() {
            const keys = ['maps_url', 'maps_embed_url', 'address', 'open_days', 'open_hours', 'friday_hours', 'phone', 'email', 'instagram_url'];
            const out = {};
            keys.forEach(k => out[k] = document.getElementById(k).value.trim() || null);
            return out;
        }

        async function loadSettings() {
            await window.__adminUserPromise;
            clearErr();
            try {
                const res = await apiFetch('/admin/settings');
                fillForm(res.data || {});
            } catch (e) {
                showErr(e.message || 'Gagal memuat pengaturan');
            }
        }

        document.getElementById('btnReload').addEventListener('click', loadSettings);

        document.getElementById('btnSave').addEventListener('click', async () => {
            await window.__adminUserPromise;
            clearErr();

            try {
                const payload = collect();
                const res = await apiFetch('/admin/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                fillForm(res.data || {});
                showOk();
            } catch (e) {
                showErr(e.message || 'Gagal menyimpan pengaturan');
            }
        });

        loadSettings();
    </script>
@endpush