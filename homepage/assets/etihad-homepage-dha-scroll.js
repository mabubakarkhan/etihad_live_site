(function () {
  'use strict';

  var SELECTORS = '.dha-showcase--phases, .dha-showcase--projects';
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var matchMediaContexts = [];

  function getGsap() {
    return window.gsap || null;
  }

  function getScrollTrigger() {
    return window.ScrollTrigger || null;
  }

  function getScrollDistance(track, rail) {
    return Math.max(0, track.scrollWidth - rail.clientWidth);
  }

  function getIndexForX(cards, rail, x) {
    x = parseFloat(x) || 0;
    var viewportCenter = rail.clientWidth / 2;
    var closest = 0;
    var minDist = Infinity;

    cards.forEach(function (card, index) {
      var cardCenter = card.offsetLeft + card.offsetWidth / 2 + x;
      var dist = Math.abs(cardCenter - viewportCenter);
      if (dist < minDist) {
        minDist = dist;
        closest = index;
      }
    });

    return closest;
  }

  function targetXForIndex(cards, track, rail, index) {
    var clamped = Math.max(0, Math.min(cards.length - 1, index));
    if (cards.length <= 1) {
      return 0;
    }

    var card = cards[clamped];
    var cardCenter = card.offsetLeft + card.offsetWidth / 2;
    var viewportCenter = rail.clientWidth / 2;
    var x = viewportCenter - cardCenter;
    var minX = -getScrollDistance(track, rail);

    return Math.max(minX, Math.min(0, x));
  }

  function updateProgress(section, cards, index) {
    var fill = section.querySelector('.dha-showcase__progress span');
    var total = cards.length;
    var width = total > 0 ? ((index + 1) / total) * 100 : 0;
    var gsap = getGsap();

    if (fill) {
      if (gsap) {
        gsap.to(fill, {
          width: width + '%',
          duration: prefersReducedMotion ? 0 : 0.35,
          ease: 'power2.out',
          overwrite: 'auto',
        });
      } else {
        fill.style.width = width + '%';
      }
    }

    cards.forEach(function (card, cardIndex) {
      card.classList.toggle('is-active', cardIndex === index);
    });
  }

  function unslickIfNeeded(track) {
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.slick && window.jQuery(track).hasClass('slick-initialized')) {
      window.jQuery(track).slick('unslick');
    }
  }

  function initHorizontalShowcaseSection(section) {
    if (section.dataset.gsapHscrollInit === '1') {
      return;
    }

    var gsap = getGsap();
    var ScrollTrigger = getScrollTrigger();
    if (!gsap || !ScrollTrigger) {
      return;
    }

    gsap.registerPlugin(ScrollTrigger);
    if (window.Draggable) {
      gsap.registerPlugin(window.Draggable);
    }
    if (window.InertiaPlugin) {
      gsap.registerPlugin(window.InertiaPlugin);
    }

    var rail = section.querySelector('.dha-showcase__rail');
    var track = section.querySelector('.dha-showcase__cards');
    var cards = track ? Array.prototype.slice.call(track.querySelectorAll('.dha-showcase__card')) : [];

    if (!rail || !track || cards.length < 1) {
      return;
    }

    unslickIfNeeded(track);

    section.dataset.gsapHscrollInit = '1';
    section.classList.add('dha-showcase--gsap-scroll');

    var currentIndex = 0;
    var scrollTriggerInstance = null;
    var horizontalTween = null;
    var snapTrigger = null;
    var draggableInstance = null;

    function goToIndex(index, animate) {
      var clamped = Math.max(0, Math.min(cards.length - 1, index));
      var targetX = targetXForIndex(cards, track, rail, clamped);
      var useAnimation = animate !== false && !prefersReducedMotion;

      if (scrollTriggerInstance && cards.length > 1) {
        var progress = clamped / (cards.length - 1);
        var scrollPos = scrollTriggerInstance.start + progress * (scrollTriggerInstance.end - scrollTriggerInstance.start);

        if (useAnimation) {
          window.scrollTo({ top: scrollPos, behavior: 'smooth' });
        } else if (typeof scrollTriggerInstance.scroll === 'function') {
          scrollTriggerInstance.scroll(scrollPos);
        } else {
          window.scrollTo(0, scrollPos);
        }

        currentIndex = clamped;
        updateProgress(section, cards, clamped);
        return;
      }

      gsap.to(track, {
        x: targetX,
        duration: useAnimation ? 0.75 : 0,
        ease: 'power3.inOut',
        overwrite: 'auto',
        onUpdate: function () {
          currentIndex = getIndexForX(cards, rail, gsap.getProperty(track, 'x'));
          updateProgress(section, cards, currentIndex);
        },
        onComplete: function () {
          currentIndex = clamped;
          updateProgress(section, cards, clamped);
        },
      });
    }

    var prev = section.querySelector('[data-dha-prev]');
    var next = section.querySelector('[data-dha-next]');

    if (prev) {
      prev.addEventListener('click', function () {
        goToIndex(currentIndex - 1, true);
      });
    }

    if (next) {
      next.addEventListener('click', function () {
        goToIndex(currentIndex + 1, true);
      });
    }

    gsap.set(track, { x: 0 });
    updateProgress(section, cards, 0);

    if (cards.length < 2) {
      return;
    }

    var mm = gsap.matchMedia();
    matchMediaContexts.push(mm);

    mm.add('(min-width: 992px)', function () {
      if (draggableInstance) {
        draggableInstance.kill();
        draggableInstance = null;
      }

      var distance = getScrollDistance(track, rail);
      if (distance <= 0) {
        return;
      }

      horizontalTween = gsap.fromTo(
        track,
        { x: 0 },
        {
          x: function () {
            return targetXForIndex(cards, track, rail, cards.length - 1);
          },
          ease: 'none',
          scrollTrigger: {
          trigger: section,
          pin: section,
          pinSpacing: true,
          scrub: 0.85,
          anticipatePin: 1,
          invalidateOnRefresh: true,
          start: 'top top',
          end: function () {
            return '+=' + getScrollDistance(track, rail);
          },
        onUpdate: function () {
          var x = gsap.getProperty(track, 'x');
          currentIndex = getIndexForX(cards, rail, x);
          updateProgress(section, cards, currentIndex);
        },
          onEnter: function () {
            section.classList.add('is-pin-active');
          },
          onLeave: function () {
            section.classList.remove('is-pin-active');
          },
          onEnterBack: function () {
            section.classList.add('is-pin-active');
          },
          onLeaveBack: function () {
            section.classList.remove('is-pin-active');
          },
        },
      }
      );

      scrollTriggerInstance = horizontalTween.scrollTrigger;

      snapTrigger = ScrollTrigger.create({
        trigger: section,
        start: function () {
          return scrollTriggerInstance ? scrollTriggerInstance.start : 'top top';
        },
        end: function () {
          return scrollTriggerInstance ? scrollTriggerInstance.end : 'bottom top';
        },
        snap: {
          snapTo: 1 / (cards.length - 1),
          duration: { min: 0.12, max: 0.35 },
          delay: 0.03,
          ease: 'power1.inOut',
        },
        invalidateOnRefresh: true,
      });

      return function () {
        if (snapTrigger) {
          snapTrigger.kill();
          snapTrigger = null;
        }
        if (horizontalTween) {
          horizontalTween.kill();
          horizontalTween = null;
        }
        scrollTriggerInstance = null;
        gsap.set(track, { x: 0 });
      };
    });

    mm.add('(max-width: 991px)', function () {
      if (snapTrigger) {
        snapTrigger.kill();
        snapTrigger = null;
      }
      if (horizontalTween) {
        horizontalTween.kill();
        horizontalTween = null;
      }
      scrollTriggerInstance = null;

      gsap.set(track, { x: 0 });
      currentIndex = 0;
      updateProgress(section, cards, 0);

      if (prefersReducedMotion || !window.Draggable) {
        return;
      }

      draggableInstance = window.Draggable.create(track, {
        type: 'x',
        inertia: true,
        dragClickables: true,
        edgeResistance: 0.75,
        bounds: function () {
          return {
            minX: targetXForIndex(cards, track, rail, cards.length - 1),
            maxX: 0,
          };
        },
        onDragEnd: function () {
          goToIndex(getIndexForX(cards, rail, gsap.getProperty(track, 'x')), true);
        },
      })[0];

      return function () {
        if (draggableInstance) {
          draggableInstance.kill();
          draggableInstance = null;
        }
      };
    });
  }

  function init() {
    if (!getGsap() || !getScrollTrigger()) {
      return false;
    }

    document.querySelectorAll(SELECTORS).forEach(initHorizontalShowcaseSection);

    return true;
  }

  window.EtihadHomepageDhaScroll = {
    init: init,
    refresh: function () {
      var ScrollTrigger = getScrollTrigger();
      if (ScrollTrigger) {
        ScrollTrigger.refresh(true);
      }
    },
  };
})();
