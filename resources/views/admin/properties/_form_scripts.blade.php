@php $citiesByState = isset($states) ? $states->keyBy('name')->map(fn($s) => $s->cities->pluck('name')->toArray())->toArray() : []; @endphp
<script>
(function() {
    var listingTabsWrapper = document.getElementById('listing-form-tabs');
    if (listingTabsWrapper) {
        var btns = listingTabsWrapper.querySelectorAll('.project-tab-btn');
        var panels = listingTabsWrapper.querySelectorAll('.tab-panel');
        function switchToTab(targetId) {
            window.requestAnimationFrame(function() {
                btns.forEach(function(b) {
                    b.classList.toggle('active', b.getAttribute('data-tab') === targetId);
                });
                panels.forEach(function(p) {
                    p.classList.toggle('active', p.id === targetId);
                });
            });
        }
        btns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetId = btn.getAttribute('data-tab');
                if (targetId === 'tab-gallery') {
                    setTimeout(function() { switchToTab(targetId); }, 0);
                } else {
                    switchToTab(targetId);
                }
            });
        });
        listingTabsWrapper.querySelectorAll('.project-tab-next').forEach(function(nextBtn) {
            nextBtn.addEventListener('click', function() {
                var nextId = nextBtn.getAttribute('data-next');
                if (nextId) {
                    if (nextId === 'tab-gallery') {
                        setTimeout(function() { switchToTab(nextId); }, 0);
                    } else {
                        switchToTab(nextId);
                    }
                }
            });
        });
        listingTabsWrapper.querySelectorAll('.project-tab-back').forEach(function(backBtn) {
            backBtn.addEventListener('click', function() {
                var prevId = backBtn.getAttribute('data-prev');
                if (prevId) {
                    if (prevId === 'tab-gallery') {
                        setTimeout(function() { switchToTab(prevId); }, 0);
                    } else {
                        switchToTab(prevId);
                    }
                }
            });
        });
    }

    var stateEl = document.getElementById('property-state-select');
    var cityEl = document.getElementById('property-city-select');
    if (stateEl && cityEl && typeof TomSelect !== 'undefined') {
        var citiesByState = @json($citiesByState);
        var stateSelect = new TomSelect(stateEl, { create: false, sortField: { field: 'text', direction: 'asc' } });
        var citySelect = new TomSelect(cityEl, { create: false, sortField: { field: 'text', direction: 'asc' } });
        stateSelect.on('change', function(val) {
            var cities = citiesByState[val] || [];
            citySelect.clearOptions();
            cities.forEach(function(c) { citySelect.addOption({ value: c, text: c }); });
            citySelect.clear(true);
            if (cities.indexOf('Lahore') >= 0) citySelect.setValue('Lahore');
            else if (cities.length) citySelect.setValue(cities[0]);
        });
    }

    var propType = document.getElementById('property_type');
    var propTypeExtra = document.getElementById('property-type-extra');
    if (propType && propTypeExtra) {
        function toggleExtra() {
            var v = (propType.value || '').toLowerCase();
            propTypeExtra.classList.toggle('hidden', v !== 'home' && v !== 'flat');
        }
        propType.addEventListener('change', toggleExtra);
        toggleExtra();
    }

    var areaMarla = document.getElementById('area_marla');
    var areaKanal = document.getElementById('area_kanal');
    if (areaMarla && areaKanal) {
        areaMarla.addEventListener('input', function() {
            var m = parseFloat(areaMarla.value);
            if (!isNaN(m) && m >= 0) areaKanal.value = (m / 20).toFixed(2);
        });
        areaKanal.addEventListener('input', function() {
            var k = parseFloat(areaKanal.value);
            if (!isNaN(k) && k >= 0) areaMarla.value = (k * 20).toFixed(2);
        });
    }

    var vgContainer = document.getElementById('property-video-gallery-container');
    var addVg = document.getElementById('property-add-video-gallery');
    if (vgContainer && addVg) {
        addVg.addEventListener('click', function() {
            var div = document.createElement('div');
            div.className = 'property-vg-row flex gap-2 items-start';
            div.innerHTML = '<textarea name="video_gallery[]" rows="2" placeholder="Embed code" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"></textarea><button type="button" class="property-remove-vg text-rose-600 dark:text-rose-400 text-xs hover:text-rose-500 px-2 py-1 rounded border border-rose-600/60">Remove</button>';
            vgContainer.appendChild(div);
            div.querySelector('.property-remove-vg').addEventListener('click', function() { div.remove(); });
        });
        vgContainer.querySelectorAll('.property-remove-vg').forEach(function(btn) {
            btn.addEventListener('click', function() { btn.closest('.property-vg-row').remove(); });
        });
    }

    var galleryList = document.getElementById('property-gallery-list');
    var galleryRemoveContainer = document.getElementById('property-gallery-remove-container');
    if (galleryList && galleryRemoveContainer) {
        galleryList.addEventListener('click', function(e) {
            var btn = e.target.closest('.property-remove-gallery');
            if (!btn) return;
            var path = btn.getAttribute('data-path');
            if (path) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'gallery_remove[]';
                inp.value = path;
                galleryRemoveContainer.appendChild(inp);
            }
            var row = btn.closest('.gallery-item-row');
            if (row) { row.style.display = 'none'; row.querySelectorAll('input').forEach(function(i) { i.disabled = true; }); }
        });
    }

    document.querySelectorAll('.property-add-list').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var name = btn.getAttribute('data-name');
            var container = document.querySelector('.property-list-container[data-name="' + name + '"]');
            if (!container) return;
            var div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = '<input type="text" name="' + name + '[]" value="" placeholder="Title" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"><button type="button" class="property-remove-list px-2 py-1 rounded border border-slate-400 text-slate-600 dark:text-slate-400 text-xs hover:bg-slate-200 dark:hover:bg-slate-700">Remove</button>';
            container.appendChild(div);
            div.querySelector('.property-remove-list').addEventListener('click', function() { div.remove(); });
        });
    });
    document.querySelectorAll('.property-remove-list').forEach(function(btn) {
        btn.addEventListener('click', function() { btn.closest('.flex.gap-2').remove(); });
    });

    var amenityCounter = document.querySelectorAll('.property-amenity-row').length;
    var amenityContainer = document.getElementById('property-amenities-container');
    var addAmenity = document.getElementById('property-add-amenity');
    if (amenityContainer && addAmenity) {
        addAmenity.addEventListener('click', function() {
            var id = 'amenity_icon_' + (amenityCounter++);
            var div = document.createElement('div');
            div.className = 'property-amenity-row flex gap-2 items-center flex-wrap';
            div.innerHTML = '<input type="text" name="amenity_titles[]" value="" placeholder="Title" class="flex-1 min-w-[120px] rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"><div class="flex items-center gap-1"><input type="text" name="amenity_icons[]" value="" placeholder="Icon" id="' + id + '" class="icon-picker-target w-28 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"><button type="button" class="icon-picker-btn px-2 py-1.5 rounded border border-slate-400 text-slate-600 dark:text-slate-400 text-xs hover:bg-slate-200 dark:hover:bg-slate-700" data-target="' + id + '">Pick</button></div><button type="button" class="property-remove-amenity px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 text-xs">Remove</button>';
            amenityContainer.appendChild(div);
            div.querySelector('.property-remove-amenity').addEventListener('click', function() { div.remove(); });
        });
        amenityContainer.querySelectorAll('.property-remove-amenity').forEach(function(btn) {
            btn.addEventListener('click', function() { btn.closest('.property-amenity-row').remove(); });
        });
    }

    function isValidUrl(s) {
        if (typeof s !== 'string' || !s.trim()) return false;
        try {
            var u = new URL(s.trim());
            return u.protocol === 'http:' || u.protocol === 'https:';
        } catch (e) { return false; }
    }
    function showInlineError(fieldId, msg) {
        var el = document.getElementById(fieldId + '-error-inline');
        if (el) { el.textContent = msg || ''; el.classList.toggle('hidden', !msg); }
    }
    var propForm = document.getElementById('property-form');
    if (propForm) {
        var canonicalInput = document.getElementById('canonical_url');
        if (canonicalInput) {
            canonicalInput.addEventListener('blur', function() {
                var v = (this.value || '').trim();
                if (!v) { showInlineError('canonical_url', ''); return; }
                showInlineError('canonical_url', isValidUrl(v) ? '' : 'Please enter a valid URL (e.g. https://example.com)');
            });
        }
        propForm.addEventListener('submit', function(e) {
            var errs = [];
            var titleEl = document.getElementById('title');
            showInlineError('title', '');
            if (canonicalInput) showInlineError('canonical_url', '');
            if (!titleEl || !(titleEl.value || '').trim()) {
                errs.push({ msg: 'Title is required.', tab: 'tab-basic', tabName: 'Basic' });
                if (titleEl) { showInlineError('title', 'Title is required.'); }
            }
            var canonVal = canonicalInput ? (canonicalInput.value || '').trim() : '';
            if (canonVal && !isValidUrl(canonVal)) {
                errs.push({ msg: 'Canonical URL must be a valid URL (e.g. https://...).', tab: 'tab-seo', tabName: 'SEO' });
                if (canonicalInput) showInlineError('canonical_url', 'Please enter a valid URL (e.g. https://example.com)');
            }
            var topEl = document.getElementById('form-errors-top');
            var listEl = document.getElementById('form-errors-list');
            if (topEl && listEl) {
                topEl.classList.add('hidden');
                listEl.innerHTML = '';
                if (errs.length) {
                    errs.forEach(function(o) {
                        var li = document.createElement('li');
                        li.className = 'flex flex-wrap items-baseline gap-1';
                        li.innerHTML = '<span>' + o.msg + ' <span class="font-medium">(Section: ' + o.tabName + ')</span></span>' +
                            '<button type="button" class="go-to-tab text-xs underline hover:no-underline font-semibold" data-tab="' + o.tab + '">Go to: ' + o.tabName + '</button>';
                        listEl.appendChild(li);
                    });
                    topEl.classList.remove('hidden');
                    topEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    e.preventDefault();
                    return false;
                }
            }
        });
    }
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.go-to-tab');
        if (!btn || !btn.dataset.tab) return;
        var tabId = btn.dataset.tab;
        var wrapper = document.getElementById('listing-form-tabs');
        if (!wrapper) return;
        var tabBtn = wrapper.querySelector('.project-tab-btn[data-tab="' + tabId + '"]');
        if (tabBtn) tabBtn.click();
    });
})();
</script>
