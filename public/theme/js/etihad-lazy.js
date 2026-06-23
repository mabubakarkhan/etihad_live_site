/**
 * Portal-wide lazy media loader — starts after window.load (content + JS first).
 */
(function () {
  'use strict';

  var PLACEHOLDER =
    'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
  var observer = null;
  var started = false;

  var SKIP_IMG_SELECTOR =
    '.loader-wrap img, .navbar-logo img, .logo-holder img, img[loading="eager"], img[data-lazy-eager], img.etihad-lazy-skip';

  var BG_SELECTOR = [
    '.bg[data-lazy-bg]',
    '.etihad-lazy-bg',
    '.dha-phase-card-bg',
    '.dha-phase-lux-card__img',
    '.dha-main-lifestyle__card-img',
  ].join(',');

  function isInViewport(el) {
    var rect = el.getBoundingClientRect();
    return rect.top < window.innerHeight + 120 && rect.bottom > -120;
  }

  function shouldSkipImg(img) {
    if (!img || img.dataset.lazyPrepared === '1') {
      return true;
    }
    if (img.matches(SKIP_IMG_SELECTOR) || img.closest('.loader-wrap')) {
      return true;
    }
    if (img.getAttribute('loading') === 'eager' || img.dataset.lazyEager === '1') {
      return true;
    }
    if (img.classList.contains('etihad-lazy-skip')) {
      return true;
    }
    return false;
  }

  function ensureShimmer(el) {
    if (!el.querySelector('.etihad-lazy__shimmer')) {
      var shimmer = document.createElement('div');
      shimmer.className = 'etihad-lazy__shimmer';
      el.appendChild(shimmer);
    }
  }

  function markLoaded(el) {
    el.classList.add('is-loaded');
    el.dataset.lazyLoaded = '1';
    window.setTimeout(function () {
      var shimmer = el.querySelector('.etihad-lazy__shimmer');
      if (shimmer) {
        shimmer.remove();
      }
    }, 480);
  }

  function extractBgUrl(el) {
    if (el.dataset.lazyBg) {
      return el.dataset.lazyBg;
    }

    var style = el.getAttribute('style') || '';
    var match = style.match(/background-image\s*:\s*url\(\s*['"]?([^'")]+)['"]?\s*\)/i);
    if (match && match[1]) {
      return match[1];
    }

    var cssVar = window.getComputedStyle(el).getPropertyValue('--dha-card-bg').trim();
    if (cssVar) {
      var varMatch = cssVar.match(/url\(\s*['"]?([^'")]+)['"]?\s*\)/i);
      if (varMatch && varMatch[1]) {
        return varMatch[1];
      }
    }

    return '';
  }

  function prepareBg(el) {
    if (!el || el.dataset.lazyPrepared === '1' || el.dataset.lazyEager === '1') {
      return;
    }

    var url = extractBgUrl(el);
    if (!url) {
      return;
    }

    el.dataset.lazyPrepared = '1';
    el.dataset.lazyBg = url;
    el.classList.add('etihad-lazy', 'etihad-lazy-bg');
    el.style.backgroundImage = '';
    ensureShimmer(el);
  }

  function prepareImg(img) {
    if (shouldSkipImg(img)) {
      return;
    }

    if (img.complete && img.naturalWidth > 1 && !img.dataset.src && !img.dataset.lazySrc) {
      return;
    }

    var lazySrc = img.dataset.lazySrc || img.dataset.src || img.getAttribute('src');
    if (!lazySrc || lazySrc === PLACEHOLDER) {
      return;
    }

    img.dataset.lazyPrepared = '1';
    img.dataset.lazySrc = lazySrc;
    img.removeAttribute('data-src');
    img.classList.add('etihad-lazy-img');

    var wrap = img.closest('.etihad-lazy-wrap, .property-lazy-wrap');
    if (!wrap) {
      wrap = document.createElement('div');
      wrap.className = 'etihad-lazy etihad-lazy-wrap';
      img.parentNode.insertBefore(wrap, img);
      wrap.appendChild(img);
    } else {
      wrap.classList.add('etihad-lazy');
    }

    ensureShimmer(wrap);
    img.src = PLACEHOLDER;
    img.removeAttribute('loading');
  }

  function prepareIframe(iframe) {
    if (!iframe || iframe.dataset.lazyPrepared === '1' || !iframe.dataset.src) {
      return;
    }

    iframe.dataset.lazyPrepared = '1';
    var wrap = iframe.closest('.etihad-lazy-wrap, .property-lazy-wrap, .etihad-lazy');
    if (wrap) {
      wrap.classList.add('etihad-lazy');
      ensureShimmer(wrap);
    }
  }

  function loadElement(el) {
    if (!el || el.dataset.lazyLoaded === '1') {
      return;
    }

    var img = el.querySelector('img.etihad-lazy-img[data-lazy-src]');
    if (img) {
      var image = new Image();
      image.onload = function () {
        img.src = img.dataset.lazySrc;
        img.classList.add('is-visible');
        markLoaded(el);
      };
      image.onerror = function () {
        el.classList.add('is-error');
        markLoaded(el);
      };
      image.src = img.dataset.lazySrc;
      return;
    }

    var iframe = el.querySelector('iframe[data-src]');
    if (iframe && !iframe.getAttribute('src')) {
      iframe.onload = function () {
        markLoaded(el);
      };
      iframe.onerror = function () {
        el.classList.add('is-error');
        markLoaded(el);
      };
      iframe.src = iframe.dataset.src;
      return;
    }

    var url = el.dataset.lazyBg;
    if (!url) {
      return;
    }

    var preloader = new Image();
    preloader.onload = function () {
      el.style.backgroundImage = 'url("' + url.replace(/"/g, '\\"') + '")';
      markLoaded(el);
    };
    preloader.onerror = function () {
      el.classList.add('is-error');
      markLoaded(el);
    };
    preloader.src = url;
  }

  function queueElement(el) {
    if (!el || el.dataset.lazyLoaded === '1') {
      return;
    }

    if (el.dataset.lazyEager === '1' || isInViewport(el)) {
      loadElement(el);
      return;
    }

    if (observer) {
      if (el.dataset.lazyObserved !== '1') {
        el.dataset.lazyObserved = '1';
        observer.observe(el);
      }
    }
  }

  function scan(root) {
    root = root || document;

    root.querySelectorAll('.bg[data-bg]').forEach(function (el) {
      var bg = el.getAttribute('data-bg');
      if (!bg) {
        return;
      }
      el.dataset.lazyBg = bg;
      el.removeAttribute('data-bg');
      el.classList.add('etihad-lazy', 'etihad-lazy-bg');
    });

    root.querySelectorAll(BG_SELECTOR).forEach(prepareBg);
    root.querySelectorAll('[style*="--dha-card-bg"]').forEach(prepareBg);

    root.querySelectorAll('img').forEach(prepareImg);
    root.querySelectorAll('iframe[data-src]').forEach(prepareIframe);

    root.querySelectorAll('.etihad-lazy, .etihad-lazy-wrap, .etihad-lazy-bg, .property-lazy-wrap.etihad-lazy').forEach(queueElement);
  }

  function start() {
    if (started) {
      return;
    }
    started = true;

    observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            loadElement(entry.target);
            observer.unobserve(entry.target);
          }
        });
      },
      { rootMargin: '220px 0px', threshold: 0.01 }
    );

    scan(document);

    if (document.body) {
      new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
          mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === 1) {
              scan(node);
            }
          });
        });
      }).observe(document.body, { childList: true, subtree: true });
    }
  }

  window.EtihadLazy = {
    scan: scan,
    start: start,
    load: loadElement,
  };

  if (document.readyState === 'complete') {
    start();
  } else {
    window.addEventListener('load', start);
  }
})();
