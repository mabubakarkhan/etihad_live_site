/**
 * Etihad site-wide front scripts (wishlist, floating contact, theme config).
 * Loaded once from layouts/front.blade.php — no inline jQuery blocks in views.
 */
(function () {
    'use strict';

    var body = document.body;
    if (body && body.dataset.themeBase) {
        window.themeBase = body.dataset.themeBase.replace(/\/$/, '');
    }

    /* --- Wishlist (cookie + panel) --- */
    if (window.__etihadWishlistUnified) return;
    window.__etihadWishlistUnified = true;

    var wishlistPanelUrl = (body && body.dataset.wishlistPanelUrl) || '/wishlist/panel';

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
        fetch(wishlistPanelUrl + qs, {
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

    function runSync() { try { syncUi(); } catch (e) {} }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runSync);
    } else {
        runSync();
    }

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

    /* --- Floating contact popup --- */
    if (window.location.search.indexOf('popup=1') === -1) {
        var openBtn = document.getElementById('floating-contact-open');
        var overlay = document.getElementById('contact-popup-overlay');
        var closeBtn = document.getElementById('contact-popup-close');
        var frame = document.getElementById('contact-popup-frame');
        var contactPopupUrl = (body && body.dataset.contactPopupUrl) || '';

        if (openBtn && overlay && closeBtn && frame && contactPopupUrl) {
            function openPopup() {
                if (!frame.getAttribute('src')) frame.setAttribute('src', contactPopupUrl);
                overlay.classList.add('is-open');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
            function closePopup() {
                overlay.classList.remove('is-open');
                overlay.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
            openBtn.addEventListener('click', openPopup);
            closeBtn.addEventListener('click', closePopup);
            overlay.addEventListener('click', function (e) { if (e.target === overlay) closePopup(); });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && overlay.classList.contains('is-open')) closePopup();
            });
        }
    }

    /* --- Back to top --- */
    var topBtn = document.getElementById('floating-back-to-top');
    if (topBtn) {
        var scrollThreshold = 400;
        function updateTopBtn() {
            if (window.scrollY > scrollThreshold) {
                topBtn.classList.add('is-visible');
            } else {
                topBtn.classList.remove('is-visible');
            }
        }
        topBtn.addEventListener('click', function () {
            try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (e) { window.scrollTo(0, 0); }
        });
        updateTopBtn();
        window.addEventListener('scroll', updateTopBtn, { passive: true });
    }

    /* --- Fixed footer spacer (theme .height-emulator; same as portal) --- */
    function syncHeightEmulator() {
        if (typeof window.jQuery === 'undefined') return;
        var $footer = window.jQuery('.main-footer');
        var $emu = window.jQuery('.height-emulator');
        if (!$footer.length || !$emu.length) return;
        if (window.jQuery(window).width() < 1069) return;
        $emu.css({ height: $footer.outerHeight(true) });
    }
    window.__etihadSyncHeightEmulator = syncHeightEmulator;
    if (typeof window.jQuery !== 'undefined') {
        window.jQuery(function () {
            syncHeightEmulator();
            window.jQuery(window).on('resize.etihadFooter', syncHeightEmulator);
            window.jQuery(window).on('load.etihadFooter', syncHeightEmulator);
            setTimeout(syncHeightEmulator, 400);
        });
    }
})();
