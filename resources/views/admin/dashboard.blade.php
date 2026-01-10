@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Dashboard</h3>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="fw-bold mb-1">Status</div>
                <div class="text-muted">Anda berhasil login sebagai admin.</div>
                <div class="mt-2">
                    <span class="badge text-bg-primary" id="roleBadge">Role: ...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="fw-bold mb-1">Panduan</div>
                <div class="text-muted small">
                    Admin 1: Products, Categories<br>
                    Admin 2: Testimonials, Settings
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const role = window.__ADMIN_ROLE__ || localStorage.getItem('admin_user');
        const badge = document.getElementById('roleBadge');
        badge.textContent = 'Role: ' + (window.__ADMIN_ROLE__ || '-');
    })();
</script>
@endpush
@endsection
