(function () {
    var dataEl = document.getElementById('dha-gallery-data');
    var lightbox = document.getElementById('dha-gallery-lightbox');
    if (!dataEl || !lightbox) return;

    if (lightbox.parentNode !== document.body) {
        document.body.appendChild(lightbox);
    }

    var images = [];
    try {
        images = JSON.parse(dataEl.textContent || '[]');
    } catch (e) {
        return;
    }
    if (!images.length) return;

    var current = 0;
    var mainImg = document.getElementById('dha-gallery-lightbox-image');
    var counter = document.getElementById('dha-gallery-lightbox-counter');
    var thumbs = lightbox.querySelectorAll('[data-gallery-thumb]');
    var gridItems = document.querySelectorAll('[data-gallery-index]');

    function renderIcons() {
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }

    function setSlide(index) {
        if (!images.length) return;
        current = (index + images.length) % images.length;
        var item = images[current];
        if (!item || !mainImg) return;

        mainImg.src = item.url;
        mainImg.alt = item.alt || '';
        if (counter) {
            counter.textContent = (current + 1) + ' / ' + images.length;
        }
        thumbs.forEach(function (thumb) {
            var i = parseInt(thumb.getAttribute('data-gallery-thumb'), 10);
            thumb.classList.toggle('is-active', i === current);
        });
        var activeThumb = lightbox.querySelector('[data-gallery-thumb="' + current + '"]');
        if (activeThumb && activeThumb.scrollIntoView) {
            activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    function openLightbox(index) {
        setSlide(index);
        lightbox.hidden = false;
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('dha-gallery-open');
        renderIcons();
    }

    function closeLightbox() {
        lightbox.hidden = true;
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('dha-gallery-open');
    }

    gridItems.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var index = parseInt(btn.getAttribute('data-gallery-index'), 10);
            openLightbox(isNaN(index) ? 0 : index);
        });
    });

    lightbox.querySelectorAll('[data-gallery-close]').forEach(function (el) {
        el.addEventListener('click', closeLightbox);
    });

    var prevBtn = lightbox.querySelector('[data-gallery-prev]');
    var nextBtn = lightbox.querySelector('[data-gallery-next]');
    if (prevBtn) prevBtn.addEventListener('click', function () { setSlide(current - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { setSlide(current + 1); });

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            var index = parseInt(thumb.getAttribute('data-gallery-thumb'), 10);
            if (!isNaN(index)) setSlide(index);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (lightbox.hidden) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') setSlide(current - 1);
        if (e.key === 'ArrowRight') setSlide(current + 1);
    });

    if (window.location.hash === '#dha-gallery') {
        setTimeout(function () { openLightbox(0); }, 300);
    }
})();
