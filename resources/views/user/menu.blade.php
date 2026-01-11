@extends('user.layout')

@section('title', 'Menu - HolyTea Indonesia')

@section('head')
  <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
  {{-- slick (buat slider sponsor, kalau layout belum include) --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" />
@endsection

@section('content')
  {{-- INTRO (kalau intro di menu memang ada) --}}
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

  {{-- SPONSOR / NEWS --}}
  <div class="body">
    <div class="news" id="sponsor">
      <section class="customer-logoss slider" id="news-slider">
        {{-- diisi via JS --}}
      </section>
    </div>
  </div>

  {{-- BEST SELLER (tetap statis dulu) --}}
  <div class="body">
    <div class="container-best" id="best">
      <h1 class="tittle-best">BEST SELLER MENU</h1>

      <div class="con-img-best">
        <div class="best-left">
          <div class="left-1">
            <img src="/assets/best-seller/best-kiri.png" alt="best" class="img-left1" />
          </div>

          <div class="left-2">
            <div>
              <img src="/assets/best-seller/best-kanan-1.jpg" alt="best" class="img-best best-top reveal fade-bottom" />
            </div>
            <div>
              <img src="/assets/best-seller/best-kanan-4.jpg" alt="best" class="img-best reveal fade-bottom" />
            </div>
          </div>
        </div>

        <div class="best-right">
          <div class="right-1">
            <div>
              <img src="/assets/best-seller/best-kanan-2.jpg" alt="best" class="img-best best-top reveal fade-bottom2" />
            </div>
            <div>
              <img src="/assets/best-seller/best-kanan-5.jpg" alt="best" class="img-best reveal fade-bottom2" />
            </div>
          </div>

          <div class="right-2">
            <div>
              <img src="/assets/best-seller/best-kanan-3.jpg" alt="best" class="img-best best-top reveal fade-bottom3" />
            </div>
            <div>
              <img src="/assets/best-seller/best-kanan-6.jpg" alt="best" class="img-best reveal fade-bottom3" />
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- MENU --}}
    <div class="menu" id="menu">
      <div id="tabbar-menu" align="center">
        <button class="btn-menu active-tabbar" data-filter="all" id="btn-all">All</button>
        {{-- tombol kategori lainnya di-render JS --}}
      </div>

      <p class="jika">
        Jika Tampilan Menu Tidak Keluar Maka Lakukan Scroll Sedikit Ke Bawah*
      </p>

      {{-- tempat section kategori di-render JS --}}
      <div id="menu-sections"></div>
    </div>

    {{-- modal preview --}}
    <div class="products-preview" id="products-preview"></div>
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
  {{-- jquery + slick (kalau layout belum include) --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>

  <script>
    window.__MENU_API__ = "{{ url('/api/menu') }}";
  </script>
  <script src="{{ asset('js/menu.js') }}"></script>
@endsection