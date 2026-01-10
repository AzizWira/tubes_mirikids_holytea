document.addEventListener("DOMContentLoaded", () => {
    // initScrollIndicator();
    // initRevealOnScroll();
    initInstructionPopup();
    // loadHomeData();
});

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

/* global $ */
(function () {
    const qs = (s, el = document) => el.querySelector(s);
    const qsa = (s, el = document) => Array.from(el.querySelectorAll(s));

    function escapeHtml(str) {
        return String(str ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    function rupiah(n) {
        if (n === null || n === undefined) return "-";
        return "Rp" + Number(n).toLocaleString("id-ID");
    }

    function renderTabbar(categories) {
        const tabbar = qs("#tabbar-menu");
        if (!tabbar) return;

        // hapus tombol kategori lama (biar gak dobel)
        qsa(
            "#tabbar-menu button[data-filter]:not([data-filter='all'])"
        ).forEach((b) => b.remove());

        categories
            .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
            .forEach((c) => {
                const btn = document.createElement("button");
                btn.className = "btn-menu";
                btn.dataset.filter = c.slug;
                btn.id = `btn-${c.slug}`;
                btn.textContent = c.name_short;
                tabbar.appendChild(btn);
            });

        // active class seperti versi statis
        const btns = tabbar.getElementsByClassName("btn-menu");
        for (let i = 0; i < btns.length; i++) {
            btns[i].addEventListener("click", function () {
                const current = tabbar.getElementsByClassName("active-tabbar");
                if (current[0])
                    current[0].className = current[0].className.replace(
                        " active-tabbar",
                        ""
                    );
                this.className += " active-tabbar";
            });
        }
    }

    function renderSections(productsByCategory, categories) {
        const container = qs("#menu-sections");
        if (!container) return;

        container.innerHTML = "";

        const ordered = [...categories].sort(
            (a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0)
        );
        const anims = [
            "fade-bottom",
            "fade-bottom2",
            "fade-bottom3",
            "fade-bottom4",
        ];

        ordered.forEach((cat) => {
            const products = productsByCategory?.[cat.slug] || [];
            const title =
                products[0]?.series_title || cat.name_short + " SERIES";

            const cards = products
                .map((p, idx) => {
                    const dataName = `${p.category_slug}-${idx + 1}`;
                    const anim = anims[idx % anims.length];
                    return `
            <div class="card-product reveal ${anim}"
                 data-name="${escapeHtml(dataName)}"
                 data-title="${escapeHtml(p.name)}"
                 data-image="${escapeHtml(p.image_url)}"
                 data-price="${escapeHtml(p.price)}"
                 data-slug="${escapeHtml(p.slug)}">
              <img src="${escapeHtml(p.image_url)}" alt="${escapeHtml(
                        p.name
                    )}" class="img-menu"/>
              <h3>Pesan Sekarang</h3>
            </div>
          `;
                })
                .join("");

            const section = document.createElement("div");
            section.className = cat.slug;
            section.id = cat.slug;
            section.innerHTML = `
        <h3 class="title-product reveal fade-bottom">${escapeHtml(title)}</h3>
        <div class="products-container">
          ${
              cards ||
              `<div style="text-align:center; font-size:14px; color:#777;">Menu belum tersedia</div>`
          }
        </div>
      `;
            container.appendChild(section);
        });
    }

    function applyFilter(slug) {
        const sections = qsa("#menu-sections > div[id]");
        if (!sections.length) return;

        if (slug === "all") {
            sections.forEach((s) => (s.style.display = "block"));
            return;
        }
        sections.forEach(
            (s) => (s.style.display = s.id === slug ? "block" : "none")
        );
    }

    function initTabbarFilter() {
        const tabbar = qs("#tabbar-menu");
        if (!tabbar) return;

        tabbar.addEventListener("click", (e) => {
            const btn = e.target.closest("button.btn-menu");
            if (!btn) return;
            applyFilter(btn.dataset.filter || "all");
        });
    }

    // preview modal (simple)
    function initPreviewClick() {
        document.addEventListener("click", (e) => {
            const card = e.target.closest(".products-container .card-product");
            if (!card) return;

            const overlay = qs("#products-preview");
            if (!overlay) return;

            const name = card.dataset.title || "-";
            const image = card.dataset.image || "";
            const price = card.dataset.price || "";
            const slug = card.dataset.slug || "";

            overlay.style.display = "flex";
            overlay.innerHTML = `
        <div class="preview-card active-preview" style="display:inline-block;">
          <i class="fas fa-times"></i>
          <div class="tes-tea">
            <div><img src="${escapeHtml(image)}" alt="${escapeHtml(
                name
            )}"></div>
            <div class="mboh-test">
              <h3>${escapeHtml(name)}</h3>
              <p class="test-text">Detail produk tersedia di halaman detail.</p>
              <div class="price">${escapeHtml(rupiah(price))};</div>
              <div class="buttons">
                <a href="/detail/${encodeURIComponent(
                    slug
                )}" class="detail">DETAIL</a>
              </div>
            </div>
          </div>
        </div>
      `;

            overlay
                .querySelector(".fa-times")
                ?.addEventListener("click", () => {
                    overlay.style.display = "none";
                    overlay.innerHTML = "";
                });

            overlay.onclick = (event) => {
                if (event.target === overlay) {
                    overlay.style.display = "none";
                    overlay.innerHTML = "";
                }
            };
        });
    }

    function initSponsorSlider() {
        if (typeof $ === "undefined" || !$(".customer-logos").length) return;

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

    async function boot() {
        initSponsorSlider();
        initTabbarFilter();
        initPreviewClick();

        const apiUrl = window.__MENU_API__ || "/api/menu";

        const res = await fetch(apiUrl, {
            headers: { Accept: "application/json" },
        });
        const json = await res.json();

        if (!json?.success) {
            qs(
                "#menu-sections"
            ).innerHTML = `<div style="text-align:center; padding:40px; font-size:14px; color:#777;">Gagal memuat menu.</div>`;
            return;
        }

        const categories = json.data?.categories || [];
        const productsByCategory = json.data?.products_by_category || {};

        renderTabbar(categories);
        renderSections(productsByCategory, categories);

        applyFilter("all");
    }

    document.addEventListener("DOMContentLoaded", () => {
        boot().catch((err) => {
            console.error(err);
            const el = qs("#menu-sections");
            if (el)
                el.innerHTML = `<div style="text-align:center; padding:40px; font-size:14px; color:#777;">
        Error load menu. Cek console & endpoint API.
      </div>`;
        });
    });
})();
