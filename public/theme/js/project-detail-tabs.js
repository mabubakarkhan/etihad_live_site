(function ($) {
    'use strict';

    if (typeof $ !== 'function') {
        return;
    }

    var root = document.getElementById('project-detail-tabs');
    if (!root) {
        return;
    }

    var navButtons = root.querySelectorAll('.project-detail-tabs__nav-btn');
    var panels = root.querySelectorAll('.project-detail-tabs__panel');

    function activateTab(index) {
        navButtons.forEach(function (btn) {
            var isActive = String(btn.getAttribute('data-tab-index')) === String(index);
            btn.classList.toggle('is-active', isActive);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        panels.forEach(function (panel, panelIndex) {
            var isActive = panelIndex === Number(index);
            panel.classList.toggle('is-active', isActive);
            if (isActive) {
                panel.removeAttribute('hidden');
                initSlidersInPanel(panel);
            } else {
                panel.setAttribute('hidden', 'hidden');
            }
        });
    }

    function initSlidersInPanel(panel) {
        if (!panel || typeof $.fn.slick !== 'function') {
            return;
        }

        $(panel).find('.project-detail-tabs__slider').each(function () {
            var $slider = $(this);
            if ($slider.hasClass('slick-initialized')) {
                $slider.slick('setPosition');
                return;
            }
            if ($slider.find('.project-detail-tabs__slide').length < 2) {
                return;
            }
            $slider.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: true,
                dots: true,
                arrows: true,
                adaptiveHeight: true,
                prevArrow: '<button type="button" class="slick-prev project-detail-tabs__arrow" aria-label="Previous slide"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>',
                nextArrow: '<button type="button" class="slick-next project-detail-tabs__arrow" aria-label="Next slide"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>'
            });
        });
    }

    navButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateTab(btn.getAttribute('data-tab-index'));
        });
    });

    var firstPanel = root.querySelector('.project-detail-tabs__panel.is-active');
    if (firstPanel) {
        initSlidersInPanel(firstPanel);
    }
})(window.jQuery);
