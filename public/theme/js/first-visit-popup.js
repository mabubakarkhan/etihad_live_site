(function () {
    'use strict';

    var STORAGE_VISITOR = 'etihad_visitor_id';
    var STORAGE_POPUP = 'etihad_fvp_seen';
    var COOKIE_DAYS = 365;

    function uuid() {
        if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
        return 'v-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
    }

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function setCookie(name, value, days) {
        var maxAge = days * 24 * 60 * 60;
        document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; max-age=' + maxAge + '; SameSite=Lax';
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function postJson(url, payload) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify(payload || {}),
            credentials: 'same-origin',
            keepalive: true
        }).catch(function () { return null; });
    }

    function resolveCfg() {
        if (window.__ETIHAD_FVP__ && typeof window.__ETIHAD_FVP__ === 'object') {
            return window.__ETIHAD_FVP__;
        }
        var root = document.getElementById('etihad-fvp') || document.getElementById('etihad-fvp-track');
        if (!root) return {};
        try {
            return JSON.parse(root.getAttribute('data-fvp') || '{}') || {};
        } catch (e) {
            return {};
        }
    }

    function init() {
        var cfg = resolveCfg();
        var root = document.getElementById('etihad-fvp');

        var isFirstVisit = false;
        var visitorId = localStorage.getItem(STORAGE_VISITOR) || getCookie(STORAGE_VISITOR);
        if (!visitorId) {
            isFirstVisit = true;
            visitorId = uuid();
            try { localStorage.setItem(STORAGE_VISITOR, visitorId); } catch (e) {}
            setCookie(STORAGE_VISITOR, visitorId, COOKIE_DAYS);
        }

        if (cfg.trackUrl) {
            postJson(cfg.trackUrl, {
                first_visit: isFirstVisit,
                path: window.location.pathname || '/'
            });
        }

        if (!root || !cfg.enabled) return;

        var forceShow = !!cfg.forceShow;
        var popupSeen = localStorage.getItem(STORAGE_POPUP) === '1' || getCookie(STORAGE_POPUP) === '1';
        if (!forceShow && (popupSeen || !isFirstVisit)) return;

        var cta = document.getElementById('etihad-fvp-cta');
        var backBtn = document.getElementById('etihad-fvp-back');
        var form = document.getElementById('etihad-fvp-form');
        var submitBtn = document.getElementById('etihad-fvp-submit');
        var msg = document.getElementById('etihad-fvp-msg');
        var closeEls = root.querySelectorAll('[data-fvp-close]');

        function markSeen() {
            if (forceShow) return;
            try { localStorage.setItem(STORAGE_POPUP, '1'); } catch (e) {}
            setCookie(STORAGE_POPUP, '1', COOKIE_DAYS);
        }

        function openPopup() {
            root.hidden = false;
            root.setAttribute('aria-hidden', 'false');
            root.classList.add('is-open');
            document.documentElement.classList.add('etihad-fvp-open');
        }

        function closePopup() {
            root.classList.remove('is-open', 'is-flipped');
            root.hidden = true;
            root.setAttribute('aria-hidden', 'true');
            document.documentElement.classList.remove('etihad-fvp-open');
            markSeen();
        }

        function flip(on) {
            root.classList.toggle('is-flipped', !!on);
        }

        Array.prototype.forEach.call(closeEls, function (el) {
            el.addEventListener('click', closePopup);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && root.classList.contains('is-open')) closePopup();
        });

        if (cta) cta.addEventListener('click', function () { flip(true); });
        if (backBtn) {
            backBtn.addEventListener('click', function () {
                flip(false);
                if (msg) { msg.className = 'etihad-fvp__msg'; msg.textContent = ''; }
            });
        }

        if (form && submitBtn) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                submitBtn.disabled = true;
                if (msg) { msg.className = 'etihad-fvp__msg'; msg.textContent = ''; }

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken()
                    },
                    credentials: 'same-origin'
                }).then(function (res) {
                    return res.json().catch(function () {
                        return { success: false, message: 'Invalid response.' };
                    });
                }).then(function (json) {
                    if (json && json.success) {
                        if (msg) {
                            msg.className = 'etihad-fvp__msg is-success';
                            msg.textContent = json.message || 'Thanks! We will contact you soon.';
                        }
                        form.reset();
                        markSeen();
                        setTimeout(closePopup, 1200);
                    } else if (msg) {
                        msg.className = 'etihad-fvp__msg is-error';
                        msg.textContent = (json && json.message) || 'Something went wrong. Please try again.';
                    }
                }).catch(function () {
                    if (msg) {
                        msg.className = 'etihad-fvp__msg is-error';
                        msg.textContent = 'Something went wrong. Please try again.';
                    }
                }).finally(function () {
                    submitBtn.disabled = false;
                });
            });
        }

        var delay = Math.max(0, parseInt(cfg.delayMs || 0, 10) || 0);
        if (delay > 0) {
            setTimeout(openPopup, delay);
        } else {
            openPopup();
        }
    }

    init();
})();
