<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @stack('meta')

    {{-- Front theme CSS (from html template) --}}
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/plugins.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/style.css') }}">
    @stack('styles')

    <link rel="shortcut icon" href="{{ asset('theme/images/favicon.ico') }}">
</head>
<body data-base-url="{{ url('/') }}">
    {{-- Theme base URL for JS (e.g. map marker icon) --}}
    <script>window.themeBase = "{{ asset('theme') }}";</script>

    <!--loader (theme)-->
    <div class="loader-wrap">
        <div class="loader-inner">
            <svg>
                <defs>
                    <filter id="goo">
                        <fegaussianblur in="SourceGraphic" stdDeviation="2" result="blur" />
                        <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 5 -2" result="gooey" />
                        <fecomposite in="SourceGraphic" in2="gooey" operator="atop"/>
                    </filter>
                </defs>
            </svg>
        </div>
    </div>
    <!--loader end-->

    @yield('content')

    {{-- Front theme JS --}}
    <script src="{{ asset('theme/js/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/js/plugins.js') }}"></script>
    <script src="{{ asset('theme/js/scripts.js') }}"></script>
    <script>
    (function () {
        // Wishlist: single cookie-based logic for all pages (runs after theme scripts)
        if (window.__etihadWishlistUnifiedInline) return;
        window.__etihadWishlistUnifiedInline = true;

        function getCookieWishlist() {
            var match = document.cookie.match(/\betihad_wishlist=([^;]+)/);
            if (!match) return [];
            try { return JSON.parse(decodeURIComponent(match[1])); } catch (e) { return []; }
        }
        function normalizeIds(list) {
            var map = {};
            (list || []).forEach(function (v) {
                var n = Number(v);
                if (!isNaN(n) && isFinite(n) && n > 0) map[String(n)] = true;
            });
            return Object.keys(map).map(function (k) { return Number(k); }).sort(function (a, b) { return a - b; });
        }
        function setCookieWishlist(ids) {
            var norm = normalizeIds(ids);
            document.cookie = 'etihad_wishlist=' + encodeURIComponent(JSON.stringify(norm)) + ';path=/;max-age=31536000;SameSite=Lax';
            return norm;
        }
        function syncUi() {
            var list = normalizeIds(getCookieWishlist());
            var countEl = document.querySelector('.wish_count');
            if (countEl) countEl.textContent = String(list.length);
            var panelTitleCount = document.querySelector('.wish-list-title span');
            if (panelTitleCount) panelTitleCount.textContent = String(list.length);
            document.querySelectorAll('.wishlist-btn').forEach(function (btn) {
                var id = btn.getAttribute('data-property-id');
                if (!id) return;
                var numId = Number(id);
                var saved = list.indexOf(numId) >= 0;
                btn.classList.toggle('wishlist-saved', saved);
                btn.setAttribute('data-tooltip', saved ? 'Unsave' : 'Save');
                var icon = btn.querySelector('.wishlist-icon');
                if (icon) icon.className = 'wishlist-icon fa-heart ' + (saved ? 'fa-solid' : 'fa-regular');
            });
        }

        // Allow pages that render cards via AJAX (listing) to resync hearts after DOM updates.
        window.__etihadWishlistSyncUi = syncUi;

        function renderWishlistPanel() {
            var container = document.getElementById('wishlist-panel-container');
            if (!container) return;
            var list = normalizeIds(getCookieWishlist());
            if (!list.length) {
                container.innerHTML = '<p class="p-3 text-muted mb-0">No items yet.</p>';
                return;
            }
            var qs = '?ids=' + encodeURIComponent(list.join(','));
            fetch("{{ url('/wishlist/panel') }}" + qs, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) { return r.text(); })
              .then(function (html) {
                  container.innerHTML = html;
                  try { syncUi(); } catch (e) {}
              })
              .catch(function () {});
        }

        // Initial sync when DOM is ready (ensures correct saved/unsaved state on load)
        function runSync() { try { syncUi(); } catch (e) {} }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runSync);
        } else {
            runSync();
        }

        // One handler for all pages
        document.addEventListener('click', function (e) {
            var btn = e.target && e.target.closest ? e.target.closest('.wishlist-btn') : null;
            if (!btn) return;
            var id = btn.getAttribute('data-property-id');
            if (!id) return;
            e.preventDefault();
            e.stopPropagation();
            var list = normalizeIds(getCookieWishlist());
            var numId = Number(id);
            var idx = list.indexOf(numId);
            if (idx >= 0) list.splice(idx, 1);
            else list.push(numId);
            setCookieWishlist(list);
            try { syncUi(); } catch (err) {}
            try { renderWishlistPanel(); } catch (err2) {}
        }, true);

        document.addEventListener('click', function (e) {
            var openBtn = e.target && e.target.closest ? e.target.closest('.swl_btn') : null;
            if (!openBtn) return;
            setTimeout(function () { try { renderWishlistPanel(); } catch (err) {} }, 50);
        }, true);

        document.addEventListener('click', function (e) {
            var clearBtn = e.target && e.target.closest ? e.target.closest('.clear_wishlist') : null;
            if (!clearBtn) return;
            e.preventDefault();
            setCookieWishlist([]);
            try { syncUi(); } catch (err) {}
            try { renderWishlistPanel(); } catch (err2) {}
        }, true);

        window.addEventListener('focus', function () { try { syncUi(); } catch (e) {} });
    })();
    </script>
    @stack('scripts')
</body>
</html>
