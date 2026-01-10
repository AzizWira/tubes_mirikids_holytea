(function () {
    // ===== Helpers =====
    function rupiah(n) {
        const num = Number(n || 0);
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        })
            .format(num)
            .replace(",00", "");
    }

    function safeText(el, text) {
        if (!el) return;
        el.textContent = text ?? "-";
    }

    function safeAttr(el, attr, value) {
        if (!el) return;
        if (value === null || value === undefined || value === "") return;
        el.setAttribute(attr, value);
    }

    function initials(name) {
        const s = (name || "").trim();
        if (!s) return "??";
        const parts = s.split(/\s+/).slice(0, 2);
        return parts.map((p) => p[0].toUpperCase()).join("");
    }

    function timeAgo(dateStr) {
        if (!dateStr) return "";
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return "";
        const diff = Date.now() - d.getTime();
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);

        if (minutes < 60) return `${Math.max(1, minutes)} menit lalu`;
        if (hours < 24) return `${hours} jam lalu`;
        if (days < 7) return `${days} hari lalu`;
        const weeks = Math.floor(days / 7);
        if (weeks < 5) return `${weeks} minggu lalu`;
        const months = Math.floor(days / 30);
        return `${months} bulan lalu`;
    }

    function renderStars(container, avg) {
        if (!container) return;
        container.innerHTML = "";
        if (avg === null || avg === undefined) return;

        const rating = Math.max(0, Math.min(5, Number(avg)));
        const full = Math.floor(rating);
        const half = rating - full >= 0.5 ? 1 : 0;
        const empty = 5 - full - half;

        for (let i = 0; i < full; i++)
            container.insertAdjacentHTML(
                "beforeend",
                `<i class="fa-solid fa-star"></i>`
            );
        if (half)
            container.insertAdjacentHTML(
                "beforeend",
                `<i class="fa-solid fa-star-half-stroke"></i>`
            );
        for (let i = 0; i < empty; i++)
            container.insertAdjacentHTML(
                "beforeend",
                `<i class="fa-regular fa-star"></i>`
            );
    }

    function joinLabels(arr) {
        if (!Array.isArray(arr) || arr.length === 0) return "-";
        return arr
            .map((x) => x.label)
            .filter(Boolean)
            .join(" / ");
    }

    function escapeHtml(str) {
        return String(str ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    // ===== DOM =====
    const wrap = document.querySelector(".detail-wrap");
    if (!wrap) return;

    // slug: ambil dari data attribute, fallback ke URL segment terakhir
    let slug = (wrap.dataset.slug || "").trim();
    if (!slug) {
        const parts = window.location.pathname.split("/").filter(Boolean);
        slug = parts[parts.length - 1] || "";
    }

    // base api: dari blade window.__DETAIL_API_BASE__ (ex: http://127.0.0.1:8000/api)
    // fallback: /api
    let apiBase = window.__DETAIL_API_BASE__ || "/api";
    apiBase = String(apiBase).replace(/\/+$/, ""); // remove trailing slash

    // hero
    const drinkImage = document.getElementById("drinkImage");
    const bestSellerBadge = document.getElementById("bestSellerBadge");
    const seriesTitle = document.getElementById("seriesTitle");
    const productName = document.getElementById("productName");
    const productPrice = document.getElementById("productPrice");
    const productDesc = document.getElementById("productDesc");

    const gofoodLink = document.getElementById("gofoodLink");
    const grabfoodLink = document.getElementById("grabfoodLink");
    const shopeefoodLink = document.getElementById("shopeefoodLink");

    // rating
    const ratingWrap = document.getElementById("ratingWrap");
    const ratingStars = document.getElementById("ratingStars");
    const ratingText = document.getElementById("ratingText");

    // specs
    const specSize = document.getElementById("specSize");
    const specIce = document.getElementById("specIce");
    const specSugar = document.getElementById("specSugar");

    // nutrition
    const nutritionSubtitle = document.getElementById("nutritionSubtitle");
    const nutritionNoteText = document.getElementById("nutritionNoteText");
    const nutCalories = document.getElementById("nutCalories");
    const nutSugar = document.getElementById("nutSugar");
    const nutProtein = document.getElementById("nutProtein");
    const nutFat = document.getElementById("nutFat");
    const nutritionPills = document.getElementById("nutritionPills");

    // testimonials
    const fbTrack = document.getElementById("fbTrack");
    const fbDots = document.getElementById("fbDots");
    const feedbackWrap = document.getElementById("feedbackWrap");
    const noTestimonials = document.getElementById("noTestimonials");
    const prevBtn = document.querySelector(".fb-prev");
    const nextBtn = document.querySelector(".fb-next");

    // ===== Testimonials Slider =====
    let idx = 0;
    let cards = [];
    let dots = [];
    let timer = null;

    function renderFeedback(i) {
        cards.forEach((c, k) => c.classList.toggle("active", k === i));
        dots.forEach((d, k) => d.classList.toggle("active", k === i));
    }

    function next() {
        if (!cards.length) return;
        idx = (idx + 1) % cards.length;
        renderFeedback(idx);
    }

    function prev() {
        if (!cards.length) return;
        idx = (idx - 1 + cards.length) % cards.length;
        renderFeedback(idx);
    }

    function startAuto() {
        stopAuto();
        timer = setInterval(() => {
            if (cards.length > 1) next();
        }, 5000);
    }

    function stopAuto() {
        if (timer) clearInterval(timer);
        timer = null;
    }

    function renderMiniStars(r) {
        const rating = Math.max(0, Math.min(5, Number(r)));
        const full = Math.floor(rating);
        const half = rating - full >= 0.5 ? 1 : 0;
        const empty = 5 - full - half;

        let html = "";
        for (let i = 0; i < full; i++)
            html += `<i class="fa-solid fa-star"></i>`;
        if (half) html += `<i class="fa-solid fa-star-half-stroke"></i>`;
        for (let i = 0; i < empty; i++)
            html += `<i class="fa-regular fa-star"></i>`;
        return html;
    }

    function renderTestimonials(list) {
        if (!fbTrack || !fbDots) return;

        fbTrack.innerHTML = "";
        fbDots.innerHTML = "";
        idx = 0;

        if (!Array.isArray(list) || list.length === 0) {
            if (feedbackWrap) feedbackWrap.style.display = "none";
            if (fbDots) fbDots.style.display = "none";
            if (noTestimonials) noTestimonials.style.display = "block";
            stopAuto();
            return;
        }

        if (feedbackWrap) feedbackWrap.style.display = "grid";
        if (fbDots) fbDots.style.display = "flex";
        if (noTestimonials) noTestimonials.style.display = "none";

        list.slice(0, 10).forEach((t, i) => {
            const name = t.name || "Anonim";
            const rating = Number(t.rating || 0);
            const msg = t.message || "";
            const when = timeAgo(t.created_at);
            const starsMini = renderMiniStars(rating);

            fbTrack.insertAdjacentHTML(
                "beforeend",
                `
          <article class="fb-card ${i === 0 ? "active" : ""}">
            <div class="fb-top">
              <div class="avatar">${initials(name)}</div>
              <div class="who">
                <div class="name">${escapeHtml(name)}</div>
                <div class="stars-mini">${starsMini}</div>
              </div>
            </div>
            <p class="fb-text">${escapeHtml(msg)}</p>
            <div class="fb-foot">${escapeHtml(when)}</div>
          </article>
        `
            );

            fbDots.insertAdjacentHTML(
                "beforeend",
                `<span class="dot ${
                    i === 0 ? "active" : ""
                }" data-index="${i}"></span>`
            );
        });

        cards = Array.from(document.querySelectorAll(".fb-card"));
        dots = Array.from(document.querySelectorAll(".dot"));

        dots.forEach((d) => {
            d.addEventListener("click", () => {
                idx = parseInt(d.getAttribute("data-index"), 10);
                renderFeedback(idx);
            });
        });

        if (nextBtn) nextBtn.onclick = next;
        if (prevBtn) prevBtn.onclick = prev;

        renderFeedback(idx);
        startAuto();
    }

    // ===== Fetch & Render =====
    async function loadDetail() {
        if (!slug) {
            safeText(productName, "Slug produk tidak ditemukan.");
            return;
        }

        // PASTIKAN endpoint API sesuai permintaan kamu:
        // /api/products/{slug}
        const url = `${apiBase}/products/${encodeURIComponent(slug)}`;

        let json;
        try {
            const res = await fetch(url, {
                headers: { Accept: "application/json" },
            });
            json = await res.json();

            if (!res.ok || !json.success) {
                throw new Error(json?.message || "Gagal mengambil data produk");
            }
        } catch (e) {
            safeText(productName, "Gagal memuat detail produk");
            safeText(productDesc, e.message || "Server error");
            return;
        }

        const data = json.data || {};
        const product = data.product || {};
        const options = data.options || {};
        const nutrition = data.nutrition || {};
        const testimonials = Array.isArray(data.testimonials)
            ? data.testimonials
            : [];
        const rating = data.rating || null;

        // hero
        safeText(seriesTitle, product.series_title || "-");
        safeText(productName, product.name || "-");
        safeText(productDesc, product.short_description || "-");
        safeText(productPrice, rupiah(product.price || 0));

        safeAttr(drinkImage, "src", product.image_url || "");
        safeAttr(drinkImage, "alt", product.name || "Produk");

        // best seller badge
        const isBestSeller = Boolean(
            product.is_best_seller || product.best_seller
        );
        if (bestSellerBadge)
            bestSellerBadge.style.display = isBestSeller
                ? "inline-flex"
                : "none";

        // links
        safeAttr(gofoodLink, "href", product.gofood_url || "#");
        safeAttr(grabfoodLink, "href", product.grabfood_url || "#");
        safeAttr(shopeefoodLink, "href", product.shopeefood_url || "#");

        // rating summary
        const avg = rating?.avg ?? null;
        const count = rating?.count ?? 0;

        if (ratingWrap && avg !== null) {
            ratingWrap.style.display = "flex";
            renderStars(ratingStars, avg);
            safeText(ratingText, `${avg} (${count} ulasan)`);
            ratingStars?.setAttribute("aria-label", `rating ${avg} dari 5`);
        } else if (ratingWrap) {
            ratingWrap.style.display = "none";
        }

        // specs
        safeText(specSize, joinLabels(options.size));
        safeText(specIce, joinLabels(options.ice));
        safeText(specSugar, joinLabels(options.sugar));

        // nutrition
        safeText(nutCalories, nutrition.calories_kcal ?? "-");
        safeText(nutSugar, nutrition.sugar_g ?? "-");
        safeText(nutProtein, nutrition.protein_g ?? "-");
        safeText(nutFat, nutrition.fat_g ?? "-");

        if (nutritionNoteText) {
            nutritionNoteText.textContent =
                nutrition.note ||
                "Informasi nutrisi bersifat estimasi per porsi.";
        }

        if (nutritionSubtitle) {
            const sizeDefault =
                Array.isArray(options.size) && options.size[0]?.label
                    ? options.size[0].label
                    : null;
            nutritionSubtitle.textContent = sizeDefault
                ? `Estimasi nutrisi untuk 1 porsi (${sizeDefault}).`
                : `Estimasi nutrisi untuk 1 porsi.`;
        }

        // pills default
        if (nutritionPills) {
            nutritionPills.innerHTML = "";
            const defaults = [
                { icon: "fa-leaf", text: "Bahan pilihan" },
                { icon: "fa-snowflake", text: "Segar diminum dingin" },
                { icon: "fa-thumbs-up", text: "Bisa request less sugar" },
            ];
            defaults.forEach((p) => {
                nutritionPills.insertAdjacentHTML(
                    "beforeend",
                    `<div class="pill"><i class="fa-solid ${p.icon}"></i> ${p.text}</div>`
                );
            });
        }

        // testimonials
        renderTestimonials(testimonials);
    }

    // ===== INIT (fix utama: DOMContentLoaded bisa sudah lewat) =====
    function init() {
        loadDetail();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        // DOM sudah siap → tetap jalan
        init();
    }
})();
