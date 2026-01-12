{{-- /user/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'HolyTea Indonesia')</title>

    {{-- Favicon --}}
    <link rel="icon" href="https://drive.google.com/uc?export=view&id=1KAhrdmbD3r05XfujnPg6Dw4r0BndB2s-" />

    {{-- Fonts (sesuai CSS kamu yang pakai Poppins) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    {{-- FontAwesome (cukup 1 versi yang paling kepakai) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />

    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    {{-- Slick CSS (wajib untuk slider sponsor) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" />

    {{-- GLOBAL CSS (dipakai semua halaman: loader/navbar/cursor/progress/footer dll) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

    {{-- SLOT: CSS khusus halaman (index/menu/detail) --}}
    @yield('head')
</head>

<body class="example">
    {{-- LOADING (global) --}}
    <div class="loading" id="loading">
        <div class="wrapper-loading">
            <div class="circle-loading"></div>
            <div class="circle-loading"></div>
            <div class="circle-loading"></div>
            <div class="shadow-loading"></div>
            <div class="shadow-loading"></div>
            <div class="shadow-loading"></div>
            <span>Mohon Tunggu...</span>
        </div>
    </div>

    {{-- NAVBAR (global) --}}
    <div id="navbar-1">
        <nav id="navbar">
            <div class="navbar-left">
                <div class="logo">
                    {{-- default ke home user --}}
                    <a href="{{ route('user.home') }}">
                        <img src="{{ asset('assets/logo-holytea.png') }}" alt="logo" />
                    </a>
                </div>

                <ul class="nav_link">
                    <li class="hover-underline-animation">
                        <a href="{{ route('user.home') }}">Beranda</a>
                    </li>
                    <li class="hover-underline-animation">
                        <a href="{{ route('user.menu') }}">Menu</a>
                    </li>
                </ul>
            </div>

            <div class="menu-toggle">
                <input type="checkbox" />
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </div>

    {{-- CONTENT HALAMAN --}}
    @yield('content')

    {{-- FOOTER (global) --}}
    <footer>
        <div class="footer-text">
            <img src="{{ asset('assets/logo-holytea.png') }}" alt="logo" />
            <p>Copyright © 2025 Kelompok MiriKids</p>
        </div>
    </footer>

    {{-- CURSOR (global) --}}
    <div class="cursor"></div>
    <div class="cursorInner"></div>

    {{-- PROGRESS BAR + BACK TO TOP (global) --}}
    <div class="progress-bar">
        <button class="back-to-top hidden-top" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" class="back-to-top-icon" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
            </svg>
        </button>
    </div>

    {{-- VENDOR JS (global) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    {{-- GLOBAL JS (kalau kamu mau taruh helper global nanti, boleh dipakai) --}}

    <script src="{{ asset('js/app.js') }}"></script>

    {{-- SLOT: JS khusus halaman --}}
    @yield('scripts')
</body>

</html>