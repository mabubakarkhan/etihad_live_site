(function () {
  'use strict';

  var refreshTimer = null;
  var didRefresh = false;
  var scrollRepairTimer = null;

  var ANIMATED_SECTIONS = [
    {
      selector: 'section.choice',
      targets: '.choice-swiper-slide, .choice__heading span, .choice__scroll-wrapper',
      rootAlso: true,
    },
    {
      selector: 'section.engineers',
      targets: '.engineers__heading span, .engineers__letters, .engineers-drag, .engineers-swiper-container',
      rootAlso: true,
    },
    {
      selector: 'section.clients',
      targets: '.clients__heading, .clients-left-swiper-container, .clients-right-swiper-container, .clients-swiper-slide',
      rootAlso: true,
    },
    {
      selector: 'section.reviews',
      targets: '.reviews__heading span, .reviews-swiper-container, .reviews-swiper-slide',
      rootAlso: true,
    },
  ];

  function waitForContemporaryImages() {
    var images = Array.prototype.slice.call(
      document.querySelectorAll('.contemporary img')
    );

    if (!images.length) {
      return Promise.resolve();
    }

    return Promise.all(
      images.map(function (img) {
        if (img.complete && img.naturalWidth > 0) {
          return Promise.resolve();
        }

        return new Promise(function (resolve) {
          img.addEventListener('load', resolve, { once: true });
          img.addEventListener('error', resolve, { once: true });
        });
      })
    );
  }

  function tightenContemporaryScrollEnd() {
    if (window.innerWidth < 992 || !window.ScrollTrigger) {
      return;
    }

    var whySection = document.querySelector('section.why');
    if (!whySection) {
      return;
    }

    window.ScrollTrigger.getAll().forEach(function (st) {
      var trigger = st.trigger;
      if (!trigger || !trigger.classList || !trigger.classList.contains('contemporary__heading')) {
        return;
      }

      st.endTrigger = whySection;
      st.end = 'top bottom';
      if (typeof st.refresh === 'function') {
        st.refresh();
      }
    });
  }

  function isMostlyHidden(el) {
    if (!el) {
      return false;
    }

    var style = window.getComputedStyle(el);
    if (style.display === 'none' || style.visibility === 'hidden') {
      return true;
    }

    return parseFloat(style.opacity || '1') < 0.12;
  }

  function isSectionNearViewport(section) {
    var rect = section.getBoundingClientRect();
    var vh = window.innerHeight || document.documentElement.clientHeight;
    return rect.top < vh * 0.94 && rect.bottom > vh * 0.06;
  }

  function runChoiceCounters(section) {
    section.querySelectorAll('[data-choice-to]').forEach(function (node) {
      if (node.dataset.counted === '1') {
        return;
      }

      var to = parseInt(node.dataset.choiceTo, 10);
      if (!to || to < 1) {
        return;
      }

      node.dataset.counted = '1';
      var step = to / 60;
      var current = 0;
      var interval = window.setInterval(function () {
        current += step;
        if (current >= to) {
          current = to;
          window.clearInterval(interval);
        }
        node.textContent = Math.floor(current) + '+';
      }, 16);
    });
  }

  function repairSectionVisibility(cfg) {
    var gsap = window.gsap;
    var section = document.querySelector(cfg.selector);
    if (!section || !gsap || !isSectionNearViewport(section)) {
      return false;
    }

    var targets = Array.prototype.slice.call(section.querySelectorAll(cfg.targets));
    var needsRepair = cfg.rootAlso && isMostlyHidden(section);

    targets.forEach(function (el) {
      if (isMostlyHidden(el)) {
        needsRepair = true;
      }
    });

    if (!needsRepair) {
      return false;
    }

    if (cfg.rootAlso) {
      gsap.set(section, { autoAlpha: 1, scale: 1, visibility: 'visible' });
    }

    if (targets.length) {
      gsap.to(targets, {
        autoAlpha: 1,
        yPercent: 0,
        xPercent: 0,
        scale: 1,
        duration: 0.8,
        stagger: 0.06,
        ease: 'power3.out',
        overwrite: 'auto',
      });
    }

    if (cfg.selector === 'section.choice') {
      runChoiceCounters(section);
    }

    return true;
  }

  function repairAnimatedSections() {
    var repaired = false;

    ANIMATED_SECTIONS.forEach(function (cfg) {
      if (repairSectionVisibility(cfg)) {
        repaired = true;
      }
    });

    return repaired;
  }

  function settleScrollLayout() {
    if (!window.ScrollTrigger) {
      return;
    }

    if (typeof window.ScrollTrigger.sort === 'function') {
      window.ScrollTrigger.sort();
    }

    window.ScrollTrigger.refresh(true);
    window.dispatchEvent(new Event('scroll'));
    repairAnimatedSections();
  }

  function scheduleSettlePasses() {
    [120, 450, 1000, 1800].forEach(function (delay) {
      window.setTimeout(settleScrollLayout, delay);
    });
  }

  function refreshScrollAnimations() {
    if (window.gsap && window.ScrollTrigger) {
      window.gsap.registerPlugin(window.ScrollTrigger);
      window.ScrollTrigger.refresh(true);
    }

    window.dispatchEvent(new Event('resize'));

    window.setTimeout(function () {
      tightenContemporaryScrollEnd();
      settleScrollLayout();
      scheduleSettlePasses();
    }, 280);
  }

  function scheduleRefresh() {
    window.clearTimeout(refreshTimer);
    refreshTimer = window.setTimeout(function () {
      waitForContemporaryImages().then(refreshScrollAnimations);
    }, 120);
  }

  function refreshOnce() {
    if (didRefresh) {
      scheduleRefresh();
      return;
    }

    didRefresh = true;
    waitForContemporaryImages().then(refreshScrollAnimations);
  }

  function onScrollRepair() {
    window.clearTimeout(scrollRepairTimer);
    scrollRepairTimer = window.setTimeout(repairAnimatedSections, 100);
  }

  window.EtihadHomepageContemporaryFix = {
    refresh: scheduleRefresh,
    refreshOnce: refreshOnce,
    settleScrollLayout: settleScrollLayout,
    repairAnimatedSections: repairAnimatedSections,
  };

  window.EtihadHomepageScrollSetup = function () {
    if (window.EtihadHomepageDhaScroll && typeof window.EtihadHomepageDhaScroll.init === 'function') {
      window.EtihadHomepageDhaScroll.init();
    }

    window.requestAnimationFrame(function () {
      refreshOnce();
    });
  };

  window.addEventListener('scroll', onScrollRepair, { passive: true });
  window.addEventListener('resize', onScrollRepair);

  window.addEventListener('load', function () {
    window.setTimeout(function () {
      if (!didRefresh) {
        window.EtihadHomepageScrollSetup();
      } else {
        settleScrollLayout();
        scheduleSettlePasses();
      }
    }, 1800);
  });
})();
