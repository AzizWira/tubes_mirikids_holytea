@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<h3 class="mb-3">Settings</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <div id="alertBox" class="alert alert-danger d-none"></div>

        <div class="mb-3">
            <label class="form-label">Site Title</label>
            <input class="form-control" id="site_title">
        </div>

        <div class="mb-3">
            <label class="form-label">WhatsApp</label>
            <input class="form-control" id="whatsapp">
        </div>

        <div class="mb-3">
            <label class="form-label">Instagram</label>
            <input class="form-control" id="instagram">
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea class="form-control" id="address" rows="2"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">About</label>
            <textarea class="form-control" id="about" rows="3"></textarea>
        </div>

        <button class="btn btn-primary" id="btnSave">Simpan</button>
    </div>
</div>

@push('scripts')
<script>
const alertBox = document.getElementById('alertBox');
function showError(msg){ alertBox.textContent = msg; alertBox.classList.remove('d-none'); }
function hideError(){ alertBox.classList.add('d-none'); alertBox.textContent=''; }

async function loadSettings() {
    hideError();
    try {
        const res = await apiFetch('/api/admin/settings', { method: 'GET' });
        const s = res.data || {};
        document.getElementById('site_title').value = s.site_title || '';
        document.getElementById('whatsapp').value = s.whatsapp || '';
        document.getElementById('instagram').value = s.instagram || '';
        document.getElementById('address').value = s.address || '';
        document.getElementById('about').value = s.about || '';
    } catch (e) {
        showError(e.message);
    }
}

document.getElementById('btnSave').addEventListener('click', async () => {
    hideError();
    const payload = {
        site_title: document.getElementById('site_title').value,
        whatsapp: document.getElementById('whatsapp').value,
        instagram: document.getElementById('instagram').value,
        address: document.getElementById('address').value,
        about: document.getElementById('about').value,
    };

    try {
        await apiFetch('/api/admin/settings', { method: 'PUT', body: JSON.stringify(payload) });
        alert('Saved');
    } catch (e) {
        showError(e.message);
    }
});

loadSettings();
</script>
@endpush
@endsection
