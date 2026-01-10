@extends('user.layout')

@section('title', 'Detail Menu - HolyTea Indonesia')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}" />
    <link rel="icon" href="https://drive.google.com/uc?export=view&id=1KAhrdmbD3r05XfujnPg6Dw4r0BndB2s-" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
@endsection
@section('content')
    <main class="detail-wrap" data-slug="{{ $slug ?? request()->route('slug') ?? '' }}">
        {{-- breadcrumb/back --}}
        <div class="top-actions">
            <a href="{{ route('user.menu') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Menu
            </a>
        </div>

        {{-- hero --}}
        <section class="hero">
            <div class="hero-media">
                <img id="drinkImage" src="" alt="" class="drink-image" />

                <div class="badge" id="bestSellerBadge" style="display:none;">
                    <i class="fa-solid fa-fire"></i>
                    Best Seller
                </div>
            </div>

            <div class="hero-info">
                <p class="category" id="seriesTitle">-</p>
                <h1 class="title" id="productName">-</h1>

                <div class="meta">
                    <div class="rating" id="ratingWrap" style="display:none;">
                        <span class="stars" id="ratingStars" aria-label="rating"></span>
                        <span class="rating-text" id="ratingText"></span>
                    </div>
                    <div class="price" id="productPrice">-</div>
                </div>

                <p class="desc" id="productDesc">-</p>

                <div class="quick-spec">
                    <div class="spec">
                        <div class="spec-title">Ukuran</div>
                        <div class="spec-value" id="specSize">-</div>
                    </div>
                    <div class="spec">
                        <div class="spec-title">Es</div>
                        <div class="spec-value" id="specIce">-</div>
                    </div>
                    <div class="spec">
                        <div class="spec-title">Gula</div>
                        <div class="spec-value" id="specSugar">-</div>
                    </div>
                </div>

                <div class="cta">
                    <a class="cta-btn gojek" id="gofoodLink" href="#" target="_blank" rel="noopener">
                        <i class="fa-solid fa-motorcycle"></i> GoFood
                    </a>
                    <a class="cta-btn grab" id="grabfoodLink" href="#" target="_blank" rel="noopener">
                        <i class="fa-solid fa-bag-shopping"></i> GrabFood
                    </a>
                    <a class="cta-btn shopee" id="shopeefoodLink" href="#" target="_blank" rel="noopener">
                        <i class="fa-solid fa-store"></i> ShopeeFood
                    </a>
                </div>

                <div class="note" id="nutritionNote">
                    <i class="fa-solid fa-circle-info"></i>
                    <span id="nutritionNoteText">Informasi nutrisi bersifat estimasi per porsi.</span>
                </div>
            </div>
        </section>

        {{-- nutrition --}}
        <section class="section">
            <div class="section-head">
                <h2 class="section-title">Informasi Nutrisi</h2>
                <p class="section-subtitle" id="nutritionSubtitle">
                    Estimasi nutrisi untuk 1 porsi.
                </p>
            </div>

            <div class="nutrition-grid">
                <div class="nutri-card">
                    <div class="nutri-top">
                        <span class="nutri-label">Kalori</span>
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="nutri-value"><span id="nutCalories">-</span> <span>kcal</span></div>
                    <div class="nutri-foot">Energi total</div>
                </div>

                <div class="nutri-card">
                    <div class="nutri-top">
                        <span class="nutri-label">Gula</span>
                        <i class="fa-solid fa-cube"></i>
                    </div>
                    <div class="nutri-value"><span id="nutSugar">-</span> <span>g</span></div>
                    <div class="nutri-foot">Per porsi</div>
                </div>

                <div class="nutri-card">
                    <div class="nutri-top">
                        <span class="nutri-label">Protein</span>
                        <i class="fa-solid fa-dumbbell"></i>
                    </div>
                    <div class="nutri-value"><span id="nutProtein">-</span> <span>g</span></div>
                    <div class="nutri-foot">Kandungan protein</div>
                </div>

                <div class="nutri-card">
                    <div class="nutri-top">
                        <span class="nutri-label">Lemak</span>
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <div class="nutri-value"><span id="nutFat">-</span> <span>g</span></div>
                    <div class="nutri-foot">Total lemak</div>
                </div>
            </div>

            <div class="nutrition-note" id="nutritionPills"></div>
        </section>

        {{-- testimonials --}}
        <section class="section section-feedback" id="feedback">
            <div class="section-head">
                <h2 class="section-title">Testimoni</h2>
                <p class="section-subtitle">Apa kata mereka tentang menu ini</p>
            </div>

            <div class="feedback" id="feedbackWrap">
                <button class="fb-nav fb-prev" aria-label="Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="fb-track" id="fbTrack"></div>

                <button class="fb-nav fb-next" aria-label="Berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <div class="fb-dots" aria-label="indikator testimoni" id="fbDots"></div>

            <div id="noTestimonials"
                style="display:none; text-align:center; margin-top:14px; font-weight:700; color:rgba(0,0,0,.55);">
                Belum ada testimoni untuk menu ini.
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        // Pastikan base API benar, dan tidak pernah mengarah ke /api/detail
        // hasilnya: http://127.0.0.1:8000/api
        window.__DETAIL_API_BASE__ = "{{ url('/api') }}";
    </script>
    <script src="{{ asset('js/detail.js') }}"></script>
@endsection