<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>404 - Halaman Tidak Ditemukan</title>

    <link rel="stylesheet" href="{{ asset('css/404.css') }}" />
    <link rel="icon" href="https://drive.google.com/uc?export=view&id=1KAhrdmbD3r05XfujnPg6Dw4r0BndB2s-" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
</head>

<body>
    <main class="nf-wrap">
        <section class="nf-card" aria-label="Halaman tidak ditemukan">
            <div class="nf-badge">
                <i class="fa-solid fa-triangle-exclamation"></i>
                404
            </div>

            <h1 class="nf-title">Halaman tidak ditemukan</h1>
            <p class="nf-desc">
                Maaf, halaman yang kamu cari tidak tersedia atau sudah dipindahkan.
            </p>

            <div class="nf-actions">
                <a class="nf-btn primary" href="{{ url('/') }}">
                    <i class="fa-solid fa-house"></i>
                    Ke Beranda
                </a>

                <a class="nf-btn" href="{{ url('/menu') }}">
                    <i class="fa-solid fa-mug-hot"></i>
                    Lihat Menu
                </a>

                <button class="nf-btn ghost" type="button" onclick="history.back()">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali
                </button>
            </div>

            <div class="nf-help">
                <span>URL:</span>
                <code>{{ request()->path() }}</code>
            </div>
        </section>

        <div class="nf-bg" aria-hidden="true">
            <div class="blob b1"></div>
            <div class="blob b2"></div>
            <div class="grid"></div>
        </div>
    </main>
</body>

</html>