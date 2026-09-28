/* ==========================================================================
   Maid4Condos — admin panel behaviour
   ========================================================================== */
(function () {
    "use strict";

    function $(selector, scope) {
        return (scope || document).querySelector(selector);
    }
    function $$(selector, scope) {
        return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
    }

    /* ---------------------------------------------------------------------
       Mobile sidebar
       --------------------------------------------------------------------- */
    function initSidebar() {
        var toggles = $$("[data-admin-nav-toggle]");
        if (!toggles.length) {
            return;
        }

        function setOpen(open) {
            document.body.classList.toggle("nav-open", open);
            toggles.forEach(function (toggle) {
                if (toggle.tagName === "BUTTON") {
                    toggle.setAttribute("aria-expanded", open ? "true" : "false");
                }
            });
            var backdrop = $(".admin-backdrop");
            if (backdrop) {
                backdrop.hidden = !open;
            }
        }

        toggles.forEach(function (toggle) {
            toggle.addEventListener("click", function () {
                setOpen(!document.body.classList.contains("nav-open"));
            });
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && document.body.classList.contains("nav-open")) {
                setOpen(false);
            }
        });
    }

    /* ---------------------------------------------------------------------
       Destructive action confirmation
       --------------------------------------------------------------------- */
    function initConfirms() {
        document.addEventListener("submit", function (event) {
            var form = event.target;
            if (!form || !form.hasAttribute || !form.hasAttribute("data-confirm")) {
                return;
            }
            if (!window.confirm(form.getAttribute("data-confirm"))) {
                event.preventDefault();
            }
        });
    }

    /* ---------------------------------------------------------------------
       Double-submit protection
       --------------------------------------------------------------------- */
    function initSubmitGuard() {
        document.addEventListener("submit", function (event) {
            var form = event.target;
            var button = form.querySelector('button[type="submit"]');
            if (button) {
                window.setTimeout(function () {
                    button.disabled = true;
                    button.classList.add("is-busy");
                }, 10);
            }
        });
    }

    /* ---------------------------------------------------------------------
       Slug preview
       --------------------------------------------------------------------- */
    function initSlugPreview() {
        var name = $("#s-name");
        var slug = $("#s-slug");
        var preview = $("#slug-preview");
        if (!name || !slug || !preview) {
            return;
        }

        function slugify(value) {
            return String(value)
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, "-")
                .replace(/^-+|-+$/g, "");
        }

        var manual = slug.value.trim() !== "";
        slug.addEventListener("input", function () {
            manual = true;
            preview.textContent = slugify(slug.value);
        });
        name.addEventListener("blur", function () {
            if (!manual) {
                slug.value = slugify(name.value);
                preview.textContent = slug.value;
            }
        });
    }

    /* ---------------------------------------------------------------------
       JSON editor (page sections)
       --------------------------------------------------------------------- */
    function initJsonEditor() {
        var editor = $("[data-json-editor]");
        if (!editor) {
            return;
        }
        var status = $("[data-json-status]");

        function validate() {
            if (!status) {
                return;
            }
            try {
                JSON.parse(editor.value);
                status.textContent = "Valid JSON — ready to save.";
                status.style.color = "#0A6355";
            } catch (error) {
                status.textContent = "Invalid JSON: " + error.message;
                status.style.color = "#B4422A";
            }
        }

        editor.addEventListener("input", validate);
        validate();

        var format = $("[data-json-format]");
        if (format) {
            format.addEventListener("click", function () {
                try {
                    editor.value = JSON.stringify(JSON.parse(editor.value), null, 2);
                    validate();
                } catch (error) {
                    if (status) {
                        status.textContent = "Cannot format: " + error.message;
                        status.style.color = "#B4422A";
                    }
                }
            });
        }
    }

    /* ---------------------------------------------------------------------
       Checklist template helper (service editor)
       --------------------------------------------------------------------- */
    function initChecklistTemplate() {
        var button = $("[data-fill-checklist]");
        var textarea = $("#s-checklist");
        if (!button || !textarea) {
            return;
        }

        var template = [
            "Bathrooms | Bathtub and shower enclosure | Inside, out and behind toilet | Lights, mirrors, sink, countertops | Light switches, door knobs | Empty garbage bin | Vacuum / mop floors",
            "Bedrooms | Make beds | Dust counters, table tops, picture frames | Clean mirrors and polish ornaments | Wipe switches, door knobs | Vacuum / mop floors",
            "Kitchen | Microwave inside | Stove top | Fridge front polished | Backsplash, counter tops, cupboard fronts | Sink | Replace garbage and recycling liners | Vacuum / mop floors",
            "Living & dining room | General tidying | Tidy sofa seating | Table tops, picture frames, window ledges | Clean mirrors and polish ornaments | Vacuum / mop floors",
            "Laundry room | Clear air vent | Clear lint trap"
        ].join("\n");

        button.addEventListener("click", function () {
            if (textarea.value.trim() !== "" && !window.confirm("Replace the current checklist with the template?")) {
                return;
            }
            textarea.value = template;
            textarea.focus();
        });
    }

    /* ---------------------------------------------------------------------
       Boot
       --------------------------------------------------------------------- */
    function boot() {
        initSidebar();
        initConfirms();
        initSubmitGuard();
        initSlugPreview();
        initJsonEditor();
        initChecklistTemplate();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot);
    } else {
        boot();
    }
})();
