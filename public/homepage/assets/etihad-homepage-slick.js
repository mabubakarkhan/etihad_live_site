(function ($) {
  'use strict';

  if (typeof $ !== 'function' || typeof $.fn.slick !== 'function') {
    return;
  }

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let slidersInitialized = false;

  function buildSettings(overrides) {
    return $.extend(
      {
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: true,
        initialSlide: 0,
        autoplay: !prefersReducedMotion,
        autoplaySpeed: 3500,
        pauseOnHover: true,
        pauseOnFocus: true,
        arrows: false,
        dots: false,
        speed: 500,
        cssEase: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
        waitForAnimate: true,
        responsive: [
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
            },
          },
          {
            breakpoint: 1399,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
            },
          },
        ],
      },
      overrides || {}
    );
  }

  function buildListingSettings(slideCount, overrides) {
    return buildSettings(
      $.extend(
        {
          variableWidth: true,
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: slideCount > 2,
          autoplay: !prefersReducedMotion && slideCount > 1,
          responsive: [],
        },
        overrides || {}
      )
    );
  }

  function updateProgress(section, slick, currentIndex) {
    const fill = section.querySelector('.dha-showcase__progress span, .popular-listings__progress span');
    if (!fill || !slick || !slick.slideCount) {
      return;
    }

    const index = typeof currentIndex === 'number' ? currentIndex : slick.currentSlide || 0;
    const count = slick.slideCount;
    const normalized = ((index % count) + count) % count;
    const width = ((normalized + 1) / count) * 100;

    if (window.gsap) {
      window.gsap.to(fill, {
        width: width + '%',
        duration: prefersReducedMotion ? 0 : 0.35,
        ease: 'power2.out',
      });
    } else {
      fill.style.width = width + '%';
    }

    section.querySelectorAll('.dha-showcase__card, .popular-listings__card').forEach(function (card) {
      card.classList.remove('is-active');
    });

    if (slick.$slides && slick.$slides.length) {
      const activeSlide = slick.$slides.get(normalized);
      const card = activeSlide
        ? activeSlide.querySelector('.popular-listings__card, .dha-showcase__card')
        : null;

      if (card) {
        card.classList.add('is-active');
      }
    }
  }

  function bindControls(section, $slider, prevSelector, nextSelector) {
    const prev = section.querySelector(prevSelector);
    const next = section.querySelector(nextSelector);

    if (prev) {
      prev.addEventListener('click', function () {
        $slider.slick('slickPrev');
      });
    }

    if (next) {
      next.addEventListener('click', function () {
        $slider.slick('slickNext');
      });
    }

    $slider.on('init reInit afterChange', function (_event, slick, currentIndex) {
      updateProgress(section, slick, currentIndex);
    });
  }

  function initShowcaseSection(section) {
    /* Explore Projects uses GSAP horizontal scroll — see etihad-homepage-dha-scroll.js */
    if (section.classList.contains('dha-showcase--projects') || section.classList.contains('dha-showcase--phases')) {
      return;
    }

    const track = section.querySelector('.dha-showcase__cards');
    if (!track || track.classList.contains('slick-initialized')) {
      return;
    }

    const slideCount = track.children.length;
    if (slideCount < 1) {
      return;
    }

    const rail = section.querySelector('.dha-showcase__rail');
    if (rail) {
      rail.classList.add('homepage-slick-rail');
    }

    track.classList.add('homepage-slick');

    const $slider = $(track);

    $slider.slick(
      buildSettings({
        infinite: slideCount > 3,
        autoplay: !prefersReducedMotion && slideCount > 1,
        initialSlide: 0,
      })
    );

    $slider.slick('slickGoTo', 0, true);

    bindControls(section, $slider, '[data-dha-prev]', '[data-dha-next]');
    $slider.slick('setPosition');

    if (!prefersReducedMotion) {
      section.querySelectorAll('.dha-showcase__card').forEach(function (card) {
        const image = card.querySelector('img');

        card.addEventListener('mouseenter', function () {
          if (!window.gsap) {
            return;
          }

          window.gsap.to(card, {
            y: -8,
            borderColor: 'rgba(200, 162, 76, 0.28)',
            duration: 0.35,
            ease: 'power2.out',
          });

          if (image) {
            window.gsap.to(image, {
              scale: 1.08,
              duration: 0.5,
              ease: 'power2.out',
            });
          }
        });

        card.addEventListener('mouseleave', function () {
          if (!window.gsap) {
            return;
          }

          window.gsap.to(card, {
            y: 0,
            borderColor: 'rgba(255, 255, 255, 0.08)',
            duration: 0.35,
            ease: 'power2.out',
          });

          if (image) {
            window.gsap.to(image, {
              scale: 1.03,
              duration: 0.5,
              ease: 'power2.out',
            });
          }
        });
      });
    }
  }

  const hotOffersSliders = new Map();

  function initHotOffersSection() {
    const section = document.querySelector('.popular-listings--hot-offers');
    if (!section) {
      return;
    }

    section.querySelectorAll('.popular-listings__panel .popular-listings__grid').forEach(function (track) {
      if (track.classList.contains('slick-initialized') || track.children.length < 1) {
        return;
      }

      const rail = track.closest('.popular-listings__rail');
      if (rail) {
        rail.classList.add('homepage-slick-rail');
      }

      track.classList.add('homepage-slick');

      const panel = track.closest('.popular-listings__panel');
      const $slider = $(track);
      const isVisible = panel && !panel.hidden;
      const slideCount = track.children.length;

      $slider.slick(
        buildListingSettings(slideCount, {
          initialSlide: 0,
          autoplay: !prefersReducedMotion && isVisible && slideCount > 1,
        })
      );

      if (panel) {
        hotOffersSliders.set(panel.id, $slider);
      }

      $slider.slick('slickGoTo', 0, true);
      $slider.slick('setPosition');

      if (!isVisible) {
        $slider.slick('slickPause');
      }
    });
  }

  function onHotOffersTabChange(panelId) {
    hotOffersSliders.forEach(function ($slider, id) {
      if (id === panelId) {
        $slider.slick('setPosition');
        if (!prefersReducedMotion) {
          $slider.slick('slickGoTo', 0, true);
          $slider.slick('slickPlay');
        }
      } else {
        $slider.slick('slickPause');
      }
    });
  }

  function initGsapSectionMotion() {
    if (!window.gsap || !window.ScrollTrigger) {
      return;
    }

    document.querySelectorAll('.dha-showcase--projects').forEach(function (section) {
      const intro = section.querySelector('.dha-showcase__header');
      const orbs = section.querySelectorAll('.dha-showcase__orb');

      if (intro) {
        window.gsap.fromTo(
          intro,
          { autoAlpha: 0, y: 34 },
          {
            autoAlpha: 1,
            y: 0,
            duration: 0.9,
            ease: 'power3.out',
            scrollTrigger: {
              trigger: section,
              start: 'top 78%',
            },
          }
        );
      }

      orbs.forEach(function (orb, index) {
        window.gsap.to(orb, {
          y: index === 0 ? 85 : -70,
          x: index === 0 ? -25 : 20,
          ease: 'none',
          scrollTrigger: {
            trigger: section,
            start: 'top bottom',
            end: 'bottom top',
            scrub: 1.2,
          },
        });
      });
    });

    document.querySelectorAll('.popular-listings--dealers, .popular-listings--hot-offers').forEach(function (section) {
      const intro = section.querySelector('.popular-listings__intro');
      const glow = section.querySelector('.popular-listings__panel-glow');

      if (intro) {
        window.gsap.fromTo(
          intro,
          { autoAlpha: 0, y: 40 },
          {
            autoAlpha: 1,
            y: 0,
            duration: 0.9,
            ease: 'power3.out',
            scrollTrigger: {
              trigger: section,
              start: 'top 80%',
            },
          }
        );
      }

      if (glow) {
        window.gsap.to(glow, {
          y: 100,
          x: -30,
          ease: 'none',
          scrollTrigger: {
            trigger: section,
            start: 'top bottom',
            end: 'bottom top',
            scrub: 1.2,
          },
        });
      }
    });
  }

  function refreshSliders() {
    $('.slick-initialized').each(function () {
      $(this).slick('setPosition');
    });
  }

  function initAll() {
    if (slidersInitialized) {
      refreshSliders();
      return;
    }

    slidersInitialized = true;

    document.querySelectorAll('.dha-showcase').forEach(initShowcaseSection);
    initHotOffersSection();
    initGsapSectionMotion();
    refreshSliders();

    document.addEventListener('visibilitychange', function () {
      $('.slick-initialized').each(function () {
        if (document.hidden) {
          $(this).slick('slickPause');
        } else if (!prefersReducedMotion) {
          $(this).slick('slickPlay');
        }
      });
    });

    window.addEventListener('resize', function () {
      window.clearTimeout(window.__etihadSliderResizeTimer);
      window.__etihadSliderResizeTimer = window.setTimeout(refreshSliders, 150);
    });
  }

  window.EtihadHomepageSliders = {
    init: initAll,
    onHotOffersTabChange: onHotOffersTabChange,
    refresh: function () {
      if (!slidersInitialized) {
        initAll();
        return;
      }

      refreshSliders();
    },
  };

  $(function () {
    if (!document.querySelector('.progress-bar')) {
      initAll();
    }
  });
})(window.jQuery);
