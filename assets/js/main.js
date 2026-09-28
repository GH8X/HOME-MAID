/* ==========================================================================
   Maid4Condos — front-end behaviour
   No dependencies. Everything here is progressive enhancement: the site is
   fully usable and readable with JavaScript disabled.
   ========================================================================== */
(function () {
    "use strict";

    var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    function $(selector, scope) {
        return (scope || document).querySelector(selector);
    }
    function $$(selector, scope) {
        return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
    }

    /* ----------------------------------------------------------------------
       Google Analytics 4 conversion events
       Any element with data-ga-event fires that event on click. The allow-list
       mirrors includes/analytics.php so that nothing unexpected is sent.
       ---------------------------------------------------------------------- */
    var ALLOWED_EVENTS = [
        "quote_form_submit",
        "contact_form_submit",
        "phone_click",
        "email_click",
        "quote_cta_click",
        "service_view",
        "map_click"
    ];

    function track(eventName, label) {
        if (ALLOWED_EVENTS.indexOf(eventName) === -1) {
            return;
        }
        if (typeof window.gtag !== "function") {
            return;
        }
        window.gtag("event", eventName, {
            event_category: eventName.indexOf("form_submit") > -1 ? "form" : "engagement",
            event_label: label || "",
            transport_type: "beacon"
        });
    }

    function initEventTracking() {
        document.addEventListener("click", function (event) {
            var el = event.target.closest("[data-ga-event]");
            if (!el) {
                return;
            }
            track(el.getAttribute("data-ga-event"), el.getAttribute("data-ga-label"));
        });

        // Elements that only report on load (e.g. service page views).
        $$("[data-ga-event][hidden]").forEach(function (el) {
            track(el.getAttribute("data-ga-event"), el.getAttribute("data-ga-label"));
        });
    }

    /* ----------------------------------------------------------------------
       Sticky header
       ---------------------------------------------------------------------- */
    function initHeader() {
        var header = $("#siteHeader");
        var topbar = $("#topbar");
        if (!header) {
            return;
        }

        var onScroll = function () {
            var y = window.pageYOffset || document.documentElement.scrollTop;
            header.classList.toggle("is-stuck", y > 12);
            if (topbar) {
                topbar.style.maxHeight = y > 80 ? "0px" : "48px";
                topbar.style.opacity = y > 80 ? "0" : "1";
            }
        };

        onScroll();
        window.addEventListener("scroll", onScroll, { passive: true });
    }

    /* ----------------------------------------------------------------------
       Mega menu — keyboard support on top of the CSS hover behaviour
       ---------------------------------------------------------------------- */
    function initMegaMenu() {
        $$("[data-dropdown-toggle]").forEach(function (toggle) {
            var parent = toggle.closest(".has-menu");
            var panel = parent ? $(".mega", parent) : null;
            if (!panel) {
                return;
            }

            toggle.addEventListener("keydown", function (event) {
                if (event.key === "ArrowDown") {
                    event.preventDefault();
                    panel.classList.add("is-open");
                    var first = $("a", panel);
                    if (first) {
                        first.focus();
                    }
                }
            });

            parent.addEventListener("mouseleave", function () {
                panel.classList.remove("is-open");
            });

            parent.addEventListener("focusout", function (event) {
                if (!parent.contains(event.relatedTarget)) {
                    panel.classList.remove("is-open");
                }
            });

            panel.addEventListener("keydown", function (event) {
                if (event.key === "Escape") {
                    panel.classList.remove("is-open");
                    toggle.focus();
                }
            });
        });
    }

    /* ----------------------------------------------------------------------
       Mobile navigation drawer
       ---------------------------------------------------------------------- */
    function initMobileNav() {
        var toggle = $("[data-nav-toggle]");
        var nav = $("#mobileNav");
        if (!toggle || !nav) {
            return;
        }

        var panel = $(".mobile-nav__panel", nav);
        var lastFocused = null;

        function focusables() {
            return $$('a[href], button:not([disabled]), input, select, textarea', panel);
        }

        function open() {
            lastFocused = document.activeElement;
            nav.hidden = false;
            // Force a frame so the CSS transition runs.
            window.requestAnimationFrame(function () {
                nav.classList.add("is-open");
            });
            toggle.setAttribute("aria-expanded", "true");
            toggle.setAttribute("aria-label", "Close menu");
            document.body.classList.add("is-locked");
            var first = focusables()[0];
            if (first) {
                first.focus();
            }
        }

        function close() {
            nav.classList.remove("is-open");
            toggle.setAttribute("aria-expanded", "false");
            toggle.setAttribute("aria-label", "Open menu");
            document.body.classList.remove("is-locked");
            window.setTimeout(function () {
                nav.hidden = true;
            }, reduceMotion ? 0 : 320);
            if (lastFocused && typeof lastFocused.focus === "function") {
                lastFocused.focus();
            }
        }

        toggle.addEventListener("click", function () {
            if (nav.hidden) {
                open();
            } else {
                close();
            }
        });

        $$("[data-nav-close]", nav).forEach(function (el) {
            el.addEventListener("click", close);
        });

        // Close when a link is chosen.
        $$("a", panel).forEach(function (link) {
            link.addEventListener("click", function () {
                if (!link.hasAttribute("href")) {
                    return;
                }
                close();
            });
        });

        document.addEventListener("keydown", function (event) {
            if (nav.hidden) {
                return;
            }
            if (event.key === "Escape") {
                close();
                return;
            }
            if (event.key !== "Tab") {
                return;
            }
            // Keep focus inside the drawer while it is open.
            var items = focusables();
            if (!items.length) {
                return;
            }
            var first = items[0];
            var last = items[items.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });

        window.addEventListener("resize", function () {
            if (window.innerWidth >= 1080 && !nav.hidden) {
                close();
            }
        });
    }

    /* ----------------------------------------------------------------------
       Accordions (FAQ and extras)
       <details> works without JS; this adds the single-open behaviour.
       ---------------------------------------------------------------------- */
    function initAccordions() {
        $$("[data-accordion]").forEach(function (list) {
            var items = $$("details.faq", list);
            items.forEach(function (item) {
                item.addEventListener("toggle", function () {
                    if (!item.open) {
                        return;
                    }
                    items.forEach(function (other) {
                        if (other !== item && other.open) {
                            other.open = false;
                        }
                    });
                });
            });
        });
    }

    /* ----------------------------------------------------------------------
       FAQ category filter
       ---------------------------------------------------------------------- */
    function initFaqFilter() {
        var wrapper = $("[data-faq-filter]");
        if (!wrapper) {
            return;
        }
        var buttons = $$("[data-faq-filter-btn]", wrapper);
        var groups = $$("[data-faq-group]", wrapper);

        buttons.forEach(function (button) {
            button.addEventListener("click", function () {
                var key = button.getAttribute("data-faq-filter-btn");
                buttons.forEach(function (other) {
                    var active = other === button;
                    other.classList.toggle("is-active", active);
                    other.setAttribute("aria-selected", active ? "true" : "false");
                });
                groups.forEach(function (group) {
                    group.hidden = key !== "all" && group.getAttribute("data-faq-group") !== key;
                });
            });
        });
    }

    /* ----------------------------------------------------------------------
       Quote form — conditional fields and a friendlier client experience
       (the server always re-validates everything)
       ---------------------------------------------------------------------- */
    function initQuoteForm() {
        var form = $("[data-quote-form]");
        if (!form) {
            return;
        }

        var serviceRadios = $$("[data-service-radio]", form);
        var frequency = $("[data-frequency-select]", form);
        var frequencyHint = $("[data-frequency-hint]", form);
        var reveals = $$("[data-reveal-when]", form);

        function selectedService() {
            var checked = serviceRadios.filter(function (radio) {
                return radio.checked;
            })[0];
            return checked ? checked.value : "";
        }

        function syncConditionals() {
            var slug = selectedService();

            reveals.forEach(function (block) {
                var visible = block.getAttribute("data-reveal-when") === slug;
                block.hidden = !visible;
            });

            if (frequency && frequencyHint) {
                if (slug === "recurring-cleaning") {
                    frequencyHint.textContent = "AutoPilot schedules save 20% weekly, 15% bi-weekly and 5% monthly — no contract required.";
                } else if (slug === "move-in-move-out") {
                    frequencyHint.textContent = "Most moving cleans are one time — this package already includes the vacuum and all supplies.";
                } else {
                    frequencyHint.textContent = "Recurring schedules save up to 20% on every visit.";
                }
            }
        }

        serviceRadios.forEach(function (radio) {
            radio.addEventListener("change", syncConditionals);
        });
        syncConditionals();

        // Keep the chosen date valid.
        var dateInput = $("#q-date", form);
        if (dateInput) {
            var today = new Date().toISOString().split("T")[0];
            dateInput.min = today;
        }

        // Move focus to the error summary so screen readers announce it.
        var errorSummary = $("[data-form-errors]", form);
        if (errorSummary) {
            errorSummary.focus();
            errorSummary.scrollIntoView({ block: "center", behavior: reduceMotion ? "auto" : "smooth" });
        }

        // Prevent accidental double submissions.
        form.addEventListener("submit", function () {
            var button = $('button[type="submit"]', form);
            if (button) {
                window.setTimeout(function () {
                    button.disabled = true;
                    button.classList.add("is-busy");
                }, 10);
            }
        });
    }

    /* ----------------------------------------------------------------------
       Contact form error focus
       ---------------------------------------------------------------------- */
    function initContactForm() {
        var summary = $(".contact-form [data-form-errors]");
        if (summary) {
            summary.focus();
            summary.scrollIntoView({ block: "center", behavior: reduceMotion ? "auto" : "smooth" });
        }
    }

    /* ----------------------------------------------------------------------
       Reveal on scroll
       ---------------------------------------------------------------------- */
    function initReveal() {
        if (reduceMotion || !("IntersectionObserver" in window)) {
            return;
        }

        var targets = $$(".section-head, .trust-card, .service-card, .step, .testimonial, .extra-card, .credential-card, .faq-group, .platform-card, .contact-card, .split__copy, .split__media");
        if (!targets.length) {
            return;
        }

        targets.forEach(function (el, index) {
            el.classList.add("js-reveal");
            el.style.transitionDelay = (index % 4) * 70 + "ms";
        });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });

        targets.forEach(function (el) {
            observer.observe(el);
        });
    }

    /* ----------------------------------------------------------------------
       Back to top
       ---------------------------------------------------------------------- */
    function initToTop() {
        var button = $("[data-to-top]");
        if (!button) {
            return;
        }

        var onScroll = function () {
            button.hidden = (window.pageYOffset || document.documentElement.scrollTop) < 700;
        };
        onScroll();
        window.addEventListener("scroll", onScroll, { passive: true });

        button.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: reduceMotion ? "auto" : "smooth" });
        });
    }

    /* ----------------------------------------------------------------------
       Boot
       ---------------------------------------------------------------------- */
    function boot() {
        initHeader();
        initMegaMenu();
        initMobileNav();
        initAccordions();
        initFaqFilter();
        initQuoteForm();
        initContactForm();
        initReveal();
        initToTop();
        initEventTracking();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot);
    } else {
        boot();
    }
})();
