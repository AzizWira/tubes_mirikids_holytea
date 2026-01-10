// public/js/index.js
document.addEventListener("DOMContentLoaded", () => {
    initScrollIndicator();
    initRevealOnScroll();
    initInstructionPopup();
    loadHomeData();
});

function initScrollIndicator() {
    const scrollBg = document.querySelector(".scroll-bg");
    const circleScroll = document.querySelector(".circle");
    if (!scrollBg || !circleScroll) return;

    scrollBg.addEventListener("mouseover", () => {
        circleScroll.style.marginTop = "20px";
    });

    scrollBg.addEventListener("mouseout", () => {
        circleScroll.style.marginTop = "0px";
    });
}

function initRevealOnScroll() {
    function reveal() {
        const reveals = document.querySelectorAll(".reveal");
        for (let i = 0; i < reveals.length; i++) {
            const windowHeight = window.innerHeight;
            const elementTop = reveals[i].getBoundingClientRect().top;
            const elementVisible = 0;

            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add("active");
            } else {
                reveals[i].classList.remove("active");
            }
        }
    }

    window.addEventListener("scroll", reveal);
    reveal();
}

function initInstructionPopup() {
    const btnMulai = document.getElementById("mulai-tes");
    const instructionBg = document.getElementById("instruction-bg");
    const container = document.getElementById("instruction-container");

    if (!btnMulai || !instructionBg || !container) return;

    btnMulai.addEventListener("click", () => {
        instructionBg.style.animation = "fadeOut .5s ease-in-out forwards";
        instructionBg.style.animationDelay = ".5s";

        container.style.animation = "scaleOut .5s ease-in-out forwards";
        container.style.animationDelay = "0s";
    });
}

async function loadHomeData() {
    try {
        const res = await fetch("/api/home");
        const json = await res.json();
        if (!json.success) return;

        const data = json.data;

        // counts
        document.getElementById("varian-menu").textContent = `${
            data.counts?.varian_menu ?? 0
        }+`;
        document.getElementById("varian-rasa").textContent = `${
            data.counts?.varian_rasa ?? 0
        }`;

        // news slider
        renderNews(data.news || []);
        initSlickNews();

        // best seller
        renderBestSeller(data.best_seller || { left: [], right: [] });

        // latest products
        renderLatestProducts(data.latest_products || []);

        // site info
        renderSite(data.site || {});
    } catch (e) {
        console.error("Gagal fetch /api/home:", e);
    }
}

function renderNews(items) {
    const el = document.getElementById("news-slider");
    if (!el) return;

    el.innerHTML = "";

    if (!items.length) {
        el.innerHTML = `
      <div class="slide">
        <img src="/assets/sponsor/poster-order.jpg" alt="poster">
      </div>
    `;
        return;
    }

    items.forEach((n) => {
        el.innerHTML += `
      <div class="slide">
        <img src="${n.image_url}" alt="${escapeHtml(n.title || "news")}">
      </div>
    `;
    });
}

function initSlickNews() {
    // slick butuh jquery
    if (
        typeof window.$ === "undefined" ||
        !document.querySelector(".customer-logos")
    )
        return;

    $(".customer-logos").slick({
        slidesToShow: 2,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 4000,
        arrows: false,
        dots: false,
        pauseOnHover: false,
        responsive: [
            {
                breakpoint: 600,
                settings: { slidesToShow: 1, slidesToScroll: 1 },
            },
        ],
    });
}

function renderBestSeller(best) {
    const wrap = document.getElementById("best-wrap");
    if (!wrap) return;

    const left = best.left?.[0];
    const right = best.right || [];

    // fallback image
    const L = left?.image_url || "/assets/best-seller/best-kiri.png";

    const r0 = right[0]?.image_url || "/assets/best-seller/best-kanan-1.jpg";
    const r1 = right[1]?.image_url || "/assets/best-seller/best-kanan-2.jpg";
    const r2 = right[2]?.image_url || "/assets/best-seller/best-kanan-3.jpg";
    const r3 = right[3]?.image_url || "/assets/best-seller/best-kanan-4.jpg";
    const r4 = right[4]?.image_url || "/assets/best-seller/best-kanan-5.jpg";
    const r5 = right[5]?.image_url || "/assets/best-seller/best-kanan-6.jpg";

    wrap.innerHTML = `
    <div class="best-left">
      <div class="left-1">
        <img src="${L}" alt="best seller" class="img-left1" />
      </div>

      <div class="left-2">
        <div>
          <img src="${r0}" alt="best seller 1" class="img-best best-top reveal fade-bottom" />
        </div>
        <div>
          <img src="${r3}" alt="best seller 4" class="img-best reveal fade-bottom" />
        </div>
      </div>
    </div>

    <div class="best-right">
      <div class="right-1">
        <div>
          <img src="${r1}" alt="best seller 2" class="img-best best-top reveal fade-bottom2" />
        </div>
        <div>
          <img src="${r4}" alt="best seller 5" class="img-best reveal fade-bottom2" />
        </div>
      </div>

      <div class="right-2">
        <div>
          <img src="${r2}" alt="best seller 3" class="img-best best-top reveal fade-bottom3" />
        </div>
        <div>
          <img src="${r5}" alt="best seller 6" class="img-best reveal fade-bottom3" />
        </div>
      </div>
    </div>
  `;
}

function renderLatestProducts(items) {
    const el = document.getElementById("latest-products");
    if (!el) return;

    el.innerHTML = "";

    if (!items.length) {
        el.innerHTML = `<div class="slide-produk"><img src="/assets/tea-series/lemon-tea.svg" class="img-slide" alt=""></div>`;
        return;
    }

    items.forEach((p) => {
        el.innerHTML += `
      <a href="/detail/${encodeURIComponent(p.slug)}" class="slide-produk">
        <img src="${p.image_url}" alt="${escapeHtml(
            p.name || "produk"
        )}" class="img-slide" />
      </a>
    `;
    });
}

function renderSite(site) {
    const maps = document.getElementById("maps-embed");
    if (maps) maps.src = site.maps_embed_url || "";

    const addr = document.getElementById("site-address");
    if (addr) addr.textContent = site.address || "-";

    const open = document.getElementById("open-hours");
    if (open)
        open.textContent = `${site.open_days || "-"} : (${
            site.open_hours || "-"
        })`;

    const fri = document.getElementById("friday-hours");
    if (fri) fri.textContent = `Jumat : (${site.friday_hours || "-"})`;

    const phone = document.getElementById("site-phone");
    if (phone) phone.textContent = site.phone || "-";

    const email = document.getElementById("site-email");
    if (email) email.textContent = site.email || "-";

    const ig = document.getElementById("ig-link");
    if (ig)
        ig.href =
            site.instagram_url || "https://www.instagram.com/holyteaindonesia/";
}

function escapeHtml(str) {
    return String(str)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}
