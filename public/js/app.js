/* public/js/app.js
   Global JS: navbar toggle, loading, cursor, progress bar, back-to-top
*/

(function () {
    // ----------------------------
    // 1) LOADING (hide on load)
    // ----------------------------
    window.addEventListener("load", () => {
        const loading = document.getElementById("loading");
        if (loading) loading.style.display = "none";
    });

    // ----------------------------
    // 2) HAMBURGER MENU
    // (safe: no duplicate listeners)
    // ----------------------------
    const nav = document.querySelector("#navbar-1 ul");
    const toggleInput = document.querySelector(".menu-toggle input");

    const openNav = () => {
        if (!nav || !toggleInput) return;
        nav.classList.add("slide");
        toggleInput.checked = true;
    };

    const closeNav = () => {
        if (!nav || !toggleInput) return;
        nav.classList.remove("slide");
        toggleInput.checked = false;
    };

    if (toggleInput && nav) {
        toggleInput.addEventListener("click", (e) => {
            // toggle slide class
            nav.classList.toggle("slide");
            // sync checkbox state
            toggleInput.checked = nav.classList.contains("slide");
            e.stopPropagation();
        });

        // close if click outside
        document.addEventListener("click", (e) => {
            if (!nav.classList.contains("slide")) return;

            const isClickInsideNav = nav.contains(e.target);
            const isClickOnToggle =
                e.target === toggleInput ||
                toggleInput.parentElement?.contains(e.target);

            if (!isClickInsideNav && !isClickOnToggle) {
                closeNav();
            }
        });

        // close on ESC
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeNav();
        });

        // optional: close when click a nav link (mobile)
        nav.querySelectorAll("a").forEach((a) => {
            a.addEventListener("click", () => closeNav());
        });
    }

    // ----------------------------
    // 3) CURSOR (desktop only)
    // ----------------------------
    const cursor = document.querySelector(".cursor");
    const cursorInner = document.querySelector(".cursorInner");

    if (cursor && cursorInner) {
        document.addEventListener("mousemove", (event) => {
            const css = `left:${event.clientX}px; top:${event.clientY}px;`;
            cursor.style.cssText = css;
            cursorInner.style.cssText = css;
        });
    }

    // ----------------------------
    // 4) PROGRESS BAR + BACK TO TOP
    // ----------------------------
    const showOnPx = 100;
    const backToTopButton = document.querySelector(".back-to-top");
    const pageProgressBar = document.querySelector(".progress-bar");

    const scrollContainer = () => document.documentElement || document.body;

    const goToTop = () => {
        document.body.scrollIntoView({ behavior: "smooth" });
    };

    const updateProgress = () => {
        const sc = scrollContainer();

        // progress bar
        if (pageProgressBar) {
            const scrollHeight = sc.scrollHeight - sc.clientHeight;
            const scrollTop = sc.scrollTop;

            const scrolledPercentage =
                scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;

            pageProgressBar.style.width = `${scrolledPercentage}%`;
        }

        // back to top visibility
        if (backToTopButton) {
            if (sc.scrollTop > showOnPx) {
                backToTopButton.classList.remove("hidden-top");
            } else {
                backToTopButton.classList.add("hidden-top");
            }
        }
    };

    document.addEventListener("scroll", updateProgress, { passive: true });
    window.addEventListener("resize", updateProgress);

    if (backToTopButton) {
        backToTopButton.addEventListener("click", goToTop);
    }

    // run once on init
    updateProgress();
})();
