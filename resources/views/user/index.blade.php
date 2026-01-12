@extends('user.layout')

@section('title', 'HolyTea Indonesia')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    {{-- INTRO --}}
    <div class="intro" id="intro">
        <div class="text-intro">
            <h1 class="tittle-intro" style="color: #2c911e">
                Minuman Teh
                <span style="font-weight: 600">Terjangkau</span>, Dijamin
                <span style="font-weight: 600">Seger!</span>
            </h1>
            <p class="hastag-intro">#HOLYTEAINDONESIA</p>
        </div>

        <div class="con-img-intro">
            <img src="/assets/holytea-dashboard.png" alt="gambar variasi intro" class="img-intro" />
        </div>
    </div>

    {{-- SCROLL TO ABOUT --}}
    <a href="#about">
        <div class="scroll-container">
            <div class="scroll-bg">
                <div class="circle"></div>
            </div>
        </div>
    </a>

    {{-- VARIAN --}}
    <div class="varian">
        <div class="varian-menu">
            <div class="title-varmen" id="varian-menu">0+</div>
            <div class="desc-varmen">Varian Menu</div>
        </div>

        <div class="garis-varian"></div>

        <div class="varian-rasa">
            <div class="title-varsa" id="varian-rasa">0</div>
            <div class="desc-varsa">Varian Rasa</div>
        </div>
    </div>

    {{-- ABOUT --}}
    <div class="about" id="about">
        <h2 class="title-about">TENTANG KAMI</h2>
        <p class="desc-about">
            HolyTea Indonesia adalah minuman kekinian yang berbahan dasar teh.
            HolyTea Indonesia dibuat dari teh rebus asli dan pilihan, Ditambah
            dengan varian rasa yang beragam dan gula yang digunakan 100% gula asli
            yang dimana aman untuk dikonsumsi setiap Saat. <br /><br />
            Indonesia termasuk masih baru, Yaitu rilis pada 29 maret 2022. Varian
            HolyTea Indonesia juga beragam ada 5 varian rasa yang terdiri dari 30
            menu
        </p>
    </div>

    {{-- NEWS / SPONSOR --}}
    <div class="body">
        <div class="news" id="sponsor">
            <section class="customer-logos slider" id="news-slider">
                {{-- diisi via JS --}}
            </section>
        </div>
    </div>

    {{-- BEST SELLER --}}
    <div class="body">
        <div class="container-best" id="best">
            <h1 class="tittle-best">BEST SELLER MENU</h1>

            <div class="con-img-best" id="best-wrap">
                {{-- diisi via JS (biar gampang pas data kurang dari 6) --}}
            </div>
        </div>
    </div>

    {{-- PRODUK TERBARU --}}
    <div class="con-produk">
        <div class="title-produk">PRODUK</div>

        <div class="con-slide-produk" id="latest-products">
            {{-- diisi via JS --}}
        </div>

        <a href="{{ route('user.menu') }}">
            <div class="produk-buttom">
                <p class="lihat-lain">Lihat menu lain</p>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
            <div class="produk-buttom2">
                <p class="lihat-lain">Lihat menu lain</p>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>
    </div>

    {{-- MITRA --}}
    <div class="body">
        <div class="mitra">
            <div class="test-ae">
                <img src="/assets/mitra/kedai-mitra.png" alt="" class="img-mitra" />
            </div>
            <div class="con-desc-mitra">
                <h2 class="title-mitra">Yuk join mitra HolyTea Indonesia</h2>
                <p class="desc-title">
                    Segera join mitra HolyTea Indonesia untuk berkembang bersama di UMKM.
                </p>

                <a href="#" id="ig-link" target="_blank" rel="noopener">
                    <button class="btn-mitra">Join Franchise</button>
                </a>
            </div>

            <div class="con-bulat-mitra">
                <img src="/assets/mitra/bulat.png" alt="" class="bulat-mitra" />
            </div>
        </div>

        {{-- LOCATION --}}
        <div class="location">
            <div class="map-responsive">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d95665.00137561953!2d110.83952780275871!3d-6.79690481391049!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70db9ba6a1c967%3A0x73e6531a7ab4d729!2sHoly%20Tea%20Indonesia%20-%20Besito!5e0!3m2!1sen!2sid!4v1768184272326!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <div class="con-maps">
                <div class="maps-1-2">
                    <div class="maps-1">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-geo-alt-fill img-lokasi" viewBox="0 0 16 16">
                                <path
                                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                            </svg>
                        </div>
                        <div class="text-lokasi">
                            <p class="lokasi-maps" id="site-address">-</p>
                        </div>
                    </div>

                    <div class="maps-2">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-alarm-fill img-jam" viewBox="0 0 16 16">
                                <path
                                    d="M6 .5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H9v1.07a7.001 7.001 0 0 1 3.274 12.474l.601.602a.5.5 0 0 1-.707.708l-.746-.746A6.97 6.97 0 0 1 8 16a6.97 6.97 0 0 1-3.422-.892l-.746.746a.5.5 0 0 1-.707-.708l.602-.602A7.001 7.001 0 0 1 7 2.07V1h-.5A.5.5 0 0 1 6 .5zm2.5 5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9V5.5zM.86 5.387A2.5 2.5 0 1 1 4.387 1.86 8.035 8.035 0 0 0 .86 5.387zM11.613 1.86a2.5 2.5 0 1 1 3.527 3.527 8.035 8.035 0 0 0-3.527-3.527z" />
                            </svg>
                        </div>

                        <div class="text-jam">
                            <p class="jam-maps" id="open-hours">-</p>
                            <p class="jam-maps2" id="friday-hours">-</p>
                        </div>
                    </div>
                </div>

                <div class="maps-3">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-telephone-fill img-kontak" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                        </svg>
                    </div>

                    <div class="text-kontak">
                        <p class="telp-maps" id="site-phone">-</p>
                        <p class="email-maps" id="site-email">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- INSTRUCTION POPUP (khusus index) --}}
    <div class="instruction-bg" id="instruction-bg">
        <div class="instruction-container" id="instruction-container">
            <div class="container-instruction">
                <div class="instruction" id="i1">
                    <div class="content">
                        <div class="image">
                            <img src="/assets/popup-welcome.png" alt="">
                        </div>
                        <p class="content-p-tittle">
                            Selamat Datang<br />Di Website Holy Tea Indonesia
                        </p>
                        <p class="content-p">
                            Happy explore dan semoga suka dengan produk - produk kami.
                        </p>
                        <div class="btn">
                            <div class="button mulai" id="mulai-tes">
                                <p>Mulai</p>
                                <div class="block"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/index.js') }}"></script>
@endsection