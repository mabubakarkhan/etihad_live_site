(function ($) {
    'use strict';

    if (typeof $ !== 'function' || typeof $.fn.slick !== 'function') {
        return;
    }

    var $slider = $('#project-price-slider-track');
    if (!$slider.length || $slider.find('.project-price-slider__slide').length < 2) {
        return;
    }

    if ($slider.hasClass('slick-initialized')) {
        $slider.slick('setPosition');
        return;
    }

    $slider.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        dots: true,
        arrows: true,
        adaptiveHeight: true,
        prevArrow: '<button type="button" class="slick-prev project-price-slider__arrow" aria-label="Previous slide"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>',
        nextArrow: '<button type="button" class="slick-next project-price-slider__arrow" aria-label="Next slide"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>'
    });
})(window.jQuery);
