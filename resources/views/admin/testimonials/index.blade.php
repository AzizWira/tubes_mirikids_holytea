@extends('admin.layout')

@section('title', 'Testimonials')

@section('content')
<h3 class="mb-3">Testimonials</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="text-muted">Halaman ini bisa dikembangkan (list + form). Untuk sekarang: test koneksi API.</div>
        <button class="btn btn-outline-primary mt-3" id="btnTest">Load Testimonials</button>

        <pre class="mt-3 p-3 bg-light border rounded" id="out" style="max-height:340px; overflow:auto;">Klik tombol untuk test...</pre>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnTest').addEventListener('click', async () => {
    const out = document.getElementById('out');
    out.textContent = 'Loading...';
    try {
        const res = await apiFetch('/api/admin/testimonials', { method: 'GET' });
        out.textContent = JSON.stringify(res, null, 2);
    } catch (e) {
        out.textContent = e.message;
    }
});
</script>
@endpush
@endsection
