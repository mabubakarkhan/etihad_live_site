(function () {
    function initReveal() {
        var elements = document.querySelectorAll('.project-rd-reveal');
        if (!elements.length) return;
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        elements.forEach(function (el) { io.observe(el); });
    }

    function initGallery() {
        var main = document.getElementById('project-rd-main-image');
        if (!main) return;
        var hero = document.getElementById('project-rd-gallery');
        var thumbs = Array.prototype.slice.call(document.querySelectorAll('.project-rd-thumb'));
        var prevBtn = document.querySelector('.project-rd-slide-prev');
        var nextBtn = document.querySelector('.project-rd-slide-next');
        if (!thumbs.length) return;
        var openLinks = Array.prototype.slice.call(document.querySelectorAll('.project-rd-open-gallery'));
        var viewAllBtn = document.getElementById('project-rd-view-all-btn');
        var modal = document.getElementById('project-rd-gallery-modal');
        var modalImage = document.getElementById('project-rd-gallery-modal-image');
        var modalThumbsWrap = document.getElementById('project-rd-gallery-modal-thumbs');
        var modalPrev = document.getElementById('project-rd-gallery-prev');
        var modalNext = document.getElementById('project-rd-gallery-next');
        var modalClose = document.getElementById('project-rd-gallery-close');
        var index = thumbs.findIndex(function (t) { return t.classList.contains('is-active'); });
        if (index < 0) index = 0;
        var autoTimer = null;
        var modalOpen = false;

        function activate(i) {
            var btn = thumbs[i];
            if (!btn) return;
            var src = btn.getAttribute('data-image');
            var alt = btn.getAttribute('data-alt') || '';
            if (!src) return;
            main.src = src;
            main.alt = alt;
            thumbs.forEach(function (t) { t.classList.remove('is-active'); });
            btn.classList.add('is-active');
            index = i;
        }

        thumbs.forEach(function (btn) {
            btn.addEventListener('click', function () {
                activate(thumbs.indexOf(btn));
            });
        });
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                var nextIndex = index - 1;
                if (nextIndex < 0) nextIndex = thumbs.length - 1;
                activate(nextIndex);
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                var nextIndex = index + 1;
                if (nextIndex >= thumbs.length) nextIndex = 0;
                activate(nextIndex);
            });
        }
        function startAuto() {
            stopAuto();
            autoTimer = setInterval(function () {
                var nextIndex = index + 1;
                if (nextIndex >= thumbs.length) nextIndex = 0;
                activate(nextIndex);
            }, 5000);
        }
        function stopAuto() {
            if (autoTimer) {
                clearInterval(autoTimer);
                autoTimer = null;
            }
        }
        if (hero) {
            hero.addEventListener('mouseenter', stopAuto);
            hero.addEventListener('mouseleave', startAuto);
        }

        function renderModalThumbs() {
            if (!modalThumbsWrap) return;
            modalThumbsWrap.innerHTML = '';
            thumbs.forEach(function (btn, i) {
                var src = btn.getAttribute('data-image');
                var alt = btn.getAttribute('data-alt') || '';
                if (!src) return;
                var t = document.createElement('button');
                t.type = 'button';
                t.className = i === index ? 'is-active' : '';
                t.innerHTML = '<img src="' + src + '" alt="' + alt + '" loading="lazy">';
                t.addEventListener('click', function () {
                    activate(i);
                    renderModalImage();
                    renderModalThumbs();
                });
                modalThumbsWrap.appendChild(t);
            });
        }
        function renderModalImage() {
            if (!modalImage) return;
            var btn = thumbs[index];
            if (!btn) return;
            modalImage.src = btn.getAttribute('data-image') || '';
            modalImage.alt = btn.getAttribute('data-alt') || '';
        }
        function openModal(startIndex) {
            if (!modal) return;
            if (typeof startIndex === 'number' && startIndex >= 0 && startIndex < thumbs.length) {
                activate(startIndex);
            }
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('project-video-modal-open');
            document.body.classList.add('project-video-modal-open');
            modalOpen = true;
            stopAuto();
            renderModalImage();
            renderModalThumbs();
        }
        function closeModal() {
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.documentElement.classList.remove('project-video-modal-open');
            document.body.classList.remove('project-video-modal-open');
            modalOpen = false;
            if (!(hero && hero.matches(':hover'))) startAuto();
        }

        if (main) {
            main.addEventListener('click', function () { openModal(index); });
        }
        if (viewAllBtn) {
            viewAllBtn.addEventListener('click', function () { openModal(index); });
        }
        openLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var i = parseInt(link.getAttribute('data-index') || '0', 10);
                if (isNaN(i)) i = 0;
                openModal(i);
            });
        });
        if (modalPrev) modalPrev.addEventListener('click', function () {
            var nextIndex = index - 1;
            if (nextIndex < 0) nextIndex = thumbs.length - 1;
            activate(nextIndex);
            renderModalImage();
            renderModalThumbs();
        });
        if (modalNext) modalNext.addEventListener('click', function () {
            var nextIndex = index + 1;
            if (nextIndex >= thumbs.length) nextIndex = 0;
            activate(nextIndex);
            renderModalImage();
            renderModalThumbs();
        });
        if (modalClose) modalClose.addEventListener('click', closeModal);
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
        }
        document.addEventListener('keydown', function (e) {
            if (modalOpen && e.key === 'Escape') closeModal();
        });

        activate(index);
        startAuto();
    }

    function initInquiryForm() {
        var form = document.getElementById('project-rd-form');
        var button = document.getElementById('project-rd-submit');
        var message = document.getElementById('project-rd-form-msg');
        if (!form || !button || !message) return;

        function showMessage(text, ok) {
            message.textContent = text || '';
            message.className = 'project-rd-form-msg ' + (ok ? 'success' : 'error');
        }

        function submitRequest(e) {
            if (e) e.preventDefault();
            button.disabled = true;
            button.textContent = 'Please wait...';
            message.className = 'project-rd-form-msg';
            message.textContent = '';

            var fd = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(function (res) {
                return res.json().catch(function () { return { success: false, message: 'Invalid response.' }; });
            }).then(function (json) {
                if (json && json.success) {
                    showMessage((json.message || 'Your request has been sent successfully.'), true);
                    form.reset();
                } else {
                    showMessage((json && json.message) || 'Something went wrong. Please try again.', false);
                }
            }).catch(function () {
                showMessage('Something went wrong. Please try again.', false);
            }).finally(function () {
                button.disabled = false;
                button.textContent = 'Submit Inquiry';
            });
        }

        form.addEventListener('submit', submitRequest);
        button.addEventListener('click', submitRequest);
    }

    function initSmoothAnchors() {
        document.querySelectorAll('a[href^="#project-rd-"]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                var id = this.getAttribute('href');
                if (!id || id === '#') return;
                var target = document.querySelector(id);
                if (!target) return;
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function initInquiryFocus() {
        var links = document.querySelectorAll('a[href="#project-rd-inquiry"]');
        if (!links.length) return;
        links.forEach(function (link) {
            link.addEventListener('click', function () {
                var firstInput = document.querySelector('#project-rd-form input[name="name"]');
                if (!firstInput) return;
                window.setTimeout(function () {
                    firstInput.focus({ preventScroll: true });
                }, 420);
            });
        });
    }

    function initLeftNavActiveState() {
        var navLinks = Array.prototype.slice.call(document.querySelectorAll('.project-rd-leftnav a[href^="#project-rd-"]'));
        if (!navLinks.length) return;

        var sections = navLinks
            .map(function (link) {
                var id = link.getAttribute('href');
                if (!id) return null;
                var el = document.querySelector(id);
                return el ? { id: id, el: el, link: link } : null;
            })
            .filter(Boolean);

        if (!sections.length) return;

        function setActiveById(id) {
            navLinks.forEach(function (link) {
                link.classList.toggle('is-active', link.getAttribute('href') === id);
            });
        }

        var observer = new IntersectionObserver(function (entries) {
            var visible = entries
                .filter(function (entry) { return entry.isIntersecting; })
                .sort(function (a, b) { return b.intersectionRatio - a.intersectionRatio; });
            if (visible.length) {
                var id = '#' + visible[0].target.id;
                setActiveById(id);
            }
        }, {
            root: null,
            threshold: [0.2, 0.35, 0.5, 0.7],
            rootMargin: '-120px 0px -45% 0px'
        });

        sections.forEach(function (item) { observer.observe(item.el); });
        setActiveById(sections[0].id);
    }

    function initAccordions() {
        function bindAccordion(rootId) {
            var root = document.getElementById(rootId);
            if (!root) return;
            var toggles = root.querySelectorAll('.project-rd-acc-toggle');
            var inners = root.querySelectorAll('.project-rd-acc-inner');
            toggles.forEach(function (toggle, i) {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    var target = inners[i];
                    var isOpen = target && target.classList.contains('visible');
                    toggles.forEach(function (t) { t.classList.remove('act-accordion'); });
                    inners.forEach(function (x) { x.classList.remove('visible'); x.style.display = 'none'; });
                    if (!isOpen && target) {
                        toggle.classList.add('act-accordion');
                        target.classList.add('visible');
                        target.style.display = 'block';
                    }
                });
            });
        }
        bindAccordion('project-plans-accordion');
        bindAccordion('project-faqs-accordion');
    }

    function initFeaturedVideoModal() {
        var modal = document.getElementById('project-video-modal');
        var featuredBtn = document.getElementById('project-featured-video-btn');
        var galleryBtns = Array.prototype.slice.call(document.querySelectorAll('.project-rd-video-open'));
        var closeBtn = document.getElementById('project-video-close');
        var iframe = document.getElementById('project-video-iframe');
        if (!modal || !iframe || (!featuredBtn && galleryBtns.length === 0)) return;

        function openModal(url) {
            if (url) iframe.src = url;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('project-video-modal-open');
            document.body.classList.add('project-video-modal-open');
        }
        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            iframe.src = '';
            document.documentElement.classList.remove('project-video-modal-open');
            document.body.classList.remove('project-video-modal-open');
        }

        if (featuredBtn) {
            featuredBtn.addEventListener('click', function (e) {
                e.preventDefault();
                openModal(featuredBtn.getAttribute('data-embed-url'));
            });
        }
        galleryBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                openModal(btn.getAttribute('data-embed-url'));
            });
        });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
        });
    }

    function initAll() {
        initReveal();
        initGallery();
        initInquiryForm();
        initSmoothAnchors();
        initInquiryFocus();
        initLeftNavActiveState();
        initAccordions();
        initFeaturedVideoModal();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    window.projectRedesignMapInit = function () {
        var el = document.getElementById('project-rd-map');
        if (!el || typeof google === 'undefined' || !google.maps) return;
        var lat = parseFloat(el.getAttribute('data-latitude') || '0');
        var lng = parseFloat(el.getAttribute('data-longitude') || '0');
        if (!lat || !lng) return;

        var center = { lat: lat, lng: lng };
        var map = new google.maps.Map(el, {
            zoom: 14,
            center: center,
            scrollwheel: false,
            zoomControl: true,
            fullscreenControl: true,
            mapTypeControl: false,
            streetViewControl: true
        });

        var marker = new google.maps.Marker({ position: center, map: map });
        var title = el.getAttribute('data-infotitle') || '';
        var text = el.getAttribute('data-infotext') || '';
        if (title || text) {
            var info = new google.maps.InfoWindow({
                content: '<div><h3 style="margin:0 0 6px; font-size:14px;">' + title + '</h3><p style="margin:0; font-size:12px;">' + text + '</p></div>'
            });
            marker.addListener('click', function () { info.open(map, marker); });
        }
    };

    window.initProjectRedesignMap = function () {
        window.projectRedesignMapInit();
    };

    if (typeof google !== 'undefined' && google.maps) {
        window.projectRedesignMapInit();
    }
})();
