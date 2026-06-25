(function () {
    'use strict';

    var section = document.getElementById('project-tabs-follow-content');
    if (!section) return;

    var body = section.querySelector('[data-tabs-follow-body]');
    var toggleWrap = section.querySelector('[data-tabs-follow-toggle-wrap]');
    var toggleBtn = section.querySelector('[data-tabs-follow-toggle]');
    if (!body || !toggleWrap || !toggleBtn) return;

    var collapsedMax = 380;

    function syncToggle() {
        var needsToggle = body.scrollHeight > collapsedMax + 24;
        if (!needsToggle) {
            body.classList.remove('is-collapsed', 'is-collapsible', 'is-expanded');
            toggleWrap.hidden = true;
            return;
        }

        toggleWrap.hidden = false;
        if (body.classList.contains('is-expanded')) {
            body.classList.remove('is-collapsed', 'is-collapsible');
            toggleBtn.textContent = 'Read less';
            toggleBtn.setAttribute('aria-expanded', 'true');
        } else {
            body.classList.add('is-collapsed', 'is-collapsible');
            body.classList.remove('is-expanded');
            toggleBtn.textContent = 'Read more';
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    }

    toggleBtn.addEventListener('click', function () {
        var expanded = body.classList.toggle('is-expanded');
        if (expanded) {
            body.classList.remove('is-collapsed', 'is-collapsible');
            toggleBtn.textContent = 'Read less';
            toggleBtn.setAttribute('aria-expanded', 'true');
        } else {
            body.classList.add('is-collapsed', 'is-collapsible');
            toggleBtn.textContent = 'Read more';
            toggleBtn.setAttribute('aria-expanded', 'false');
            body.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncToggle);
    } else {
        syncToggle();
    }

    window.addEventListener('resize', syncToggle);
})();
