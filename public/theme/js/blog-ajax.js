/**
 * Blog list AJAX pagination with history.pushState / popstate.
 * Refresh and direct links keep the same ?page=N URL.
 */
(function () {
    'use strict';

    var root = document.getElementById('blog-ajax-root');
    if (!root) {
        return;
    }

    var listEl = document.getElementById('blog-posts-ajax');
    var loaderEl = document.getElementById('blog-ajax-loader');
    var busy = false;

    function setLoading(on) {
        root.classList.toggle('is-loading', on);
        if (loaderEl) {
            if (on) {
                loaderEl.hidden = false;
                loaderEl.setAttribute('aria-hidden', 'false');
            } else {
                loaderEl.hidden = true;
                loaderEl.setAttribute('aria-hidden', 'true');
            }
        }
    }

    function scrollToList() {
        var top = root.getBoundingClientRect().top + window.pageYOffset - 90;
        window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
    }

    function fetchPage(url, push) {
        if (busy) {
            return;
        }
        busy = true;
        setLoading(true);

        var fetchUrl = url;
        try {
            var u = new URL(url, window.location.origin);
            u.searchParams.set('ajax', '1');
            fetchUrl = u.toString();
        } catch (e) {
            fetchUrl = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'ajax=1';
        }

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        })
            .then(function (res) {
                if (!res.ok) {
                    throw new Error('Blog pagination failed');
                }
                return res.json();
            })
            .then(function (data) {
                if (!data || typeof data.html !== 'string') {
                    throw new Error('Invalid blog response');
                }
                listEl.innerHTML = data.html;
                if (push) {
                    var clean = data.url || url;
                    try {
                        var cu = new URL(clean, window.location.origin);
                        cu.searchParams.delete('ajax');
                        clean = cu.pathname + cu.search + cu.hash;
                    } catch (e2) {
                        clean = url;
                    }
                    history.pushState({ blogAjax: true }, '', clean);
                }
                scrollToList();
            })
            .catch(function () {
                window.location.href = url;
            })
            .finally(function () {
                busy = false;
                setLoading(false);
            });
    }

    root.addEventListener('click', function (e) {
        var link = e.target.closest('a.blog-page-link');
        if (!link || !root.contains(link)) {
            return;
        }
        if (link.classList.contains('is-disabled') || link.getAttribute('aria-disabled') === 'true') {
            e.preventDefault();
            return;
        }
        var href = link.getAttribute('href');
        if (!href || href === '#' || link.getAttribute('aria-current') === 'page') {
            e.preventDefault();
            return;
        }
        e.preventDefault();
        fetchPage(href, true);
    });

    window.addEventListener('popstate', function () {
        if (!document.getElementById('blog-ajax-root')) {
            return;
        }
        fetchPage(window.location.href, false);
    });
})();
