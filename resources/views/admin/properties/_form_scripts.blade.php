@php
    $citiesByState = isset($states) ? $states->keyBy('name')->map(fn($s) => $s->cities->pluck('name')->toArray())->toArray() : [];
    $listingSectionMapJs = [
        'title' => ['tab' => 'tab-basic', 'name' => 'Basic'],
        'dealer_id' => ['tab' => 'tab-basic', 'name' => 'Basic'],
        'slug' => ['tab' => 'tab-basic', 'name' => 'Basic'],
        'project_type_ids' => ['tab' => 'tab-basic', 'name' => 'Basic'],
        'description' => ['tab' => 'tab-basic', 'name' => 'Basic'],
        'status' => ['tab' => 'tab-status', 'name' => 'Status'],
        'featured_image' => ['tab' => 'tab-featured-image', 'name' => 'Featured image'],
        'featured_image_path' => ['tab' => 'tab-featured-image', 'name' => 'Featured image'],
        'state' => ['tab' => 'tab-address', 'name' => 'Address'],
        'city' => ['tab' => 'tab-address', 'name' => 'Address'],
        'address' => ['tab' => 'tab-address', 'name' => 'Address'],
        'short_address' => ['tab' => 'tab-address', 'name' => 'Address'],
        'town' => ['tab' => 'tab-address', 'name' => 'Address'],
        'dha_phase_id' => ['tab' => 'tab-address', 'name' => 'Address'],
        'is_dha_property' => ['tab' => 'tab-address', 'name' => 'Address'],
        'latitude' => ['tab' => 'tab-address', 'name' => 'Address'],
        'longitude' => ['tab' => 'tab-address', 'name' => 'Address'],
        'google_map' => ['tab' => 'tab-address', 'name' => 'Address'],
        'videos' => ['tab' => 'tab-videos', 'name' => 'Video'],
        'price_string' => ['tab' => 'tab-price', 'name' => 'Price'],
        'price_digits' => ['tab' => 'tab-price', 'name' => 'Price'],
        'property_type' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
        'bedrooms' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
        'bathrooms' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
        'garage' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
        'kitchen' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
        'area_marla' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
        'area_kanal' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
        'meta_title' => ['tab' => 'tab-seo', 'name' => 'SEO'],
        'meta_description' => ['tab' => 'tab-seo', 'name' => 'SEO'],
        'meta_keywords' => ['tab' => 'tab-seo', 'name' => 'SEO'],
        'canonical_url' => ['tab' => 'tab-seo', 'name' => 'SEO'],
        'amenities_description' => ['tab' => 'tab-amenities', 'name' => 'Amenities'],
    ];
@endphp
<script>
(function() {
    var listingSectionMap = @json($listingSectionMapJs);
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
    var addAmenityPreset = document.getElementById('property-add-amenity-from-preset');
    var amenityPresetSelect = document.getElementById('amenity-preset-select');
    function appendAmenityRow(title, icon) {
        if (!amenityContainer) return;
        var id = 'amenity_icon_' + (amenityCounter++);
        var safeTitle = (title || '').replace(/"/g, '&quot;');
        var safeIcon = (icon || '').replace(/"/g, '&quot;');
        var div = document.createElement('div');
        div.className = 'property-amenity-row flex gap-2 items-center flex-wrap';
        div.innerHTML = '<input type="text" name="amenity_titles[]" value="' + safeTitle + '" placeholder="Title" class="flex-1 min-w-[120px] rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"><div class="flex items-center gap-1"><input type="text" name="amenity_icons[]" value="' + safeIcon + '" placeholder="Icon" id="' + id + '" class="icon-picker-target w-28 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"><button type="button" class="icon-picker-btn px-2 py-1.5 rounded border border-slate-400 text-slate-600 dark:text-slate-400 text-xs hover:bg-slate-200 dark:hover:bg-slate-700" data-target="' + id + '">Pick</button></div><button type="button" class="property-remove-amenity px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 text-xs">Remove</button>';
        amenityContainer.appendChild(div);
        div.querySelector('.property-remove-amenity').addEventListener('click', function() { div.remove(); });
    }
    if (amenityContainer && addAmenity) {
        addAmenity.addEventListener('click', function() {
            appendAmenityRow('', '');
        });
        amenityContainer.querySelectorAll('.property-remove-amenity').forEach(function(btn) {
            btn.addEventListener('click', function() { btn.closest('.property-amenity-row').remove(); });
        });
    }
    if (amenityContainer && addAmenityPreset && amenityPresetSelect) {
        addAmenityPreset.addEventListener('click', function() {
            var selected = Array.prototype.slice.call(amenityPresetSelect.selectedOptions || []);
            if (!selected.length) return;
            selected.forEach(function(opt) {
                if (!opt || !opt.value) return;
                var title = opt.value;
                var icon = opt.getAttribute('data-icon') || '';
                appendAmenityRow(title, icon);
                opt.selected = false;
            });
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
        function showServerErrors(errors) {
            var topEl = document.getElementById('form-errors-top');
            var listEl = document.getElementById('form-errors-list');
            if (!topEl || !listEl) return;
            topEl.classList.add('hidden');
            listEl.innerHTML = '';
            Object.keys(errors || {}).forEach(function(field) {
                var messages = errors[field] || [];
                var section = listingSectionMap[field] || { tab: 'tab-basic', name: 'Basic' };
                messages.forEach(function(msg) {
                    var li = document.createElement('li');
                    li.className = 'flex flex-wrap items-baseline gap-1';
                    li.innerHTML = '<span>' + msg + ' <span class="font-medium">(Section: ' + section.name + ')</span></span>' +
                        '<button type="button" class="go-to-tab text-xs underline hover:no-underline font-semibold" data-tab="' + section.tab + '">Go to: ' + section.name + '</button>';
                    listEl.appendChild(li);
                });
            });
            if (listEl.children.length) {
                topEl.classList.remove('hidden');
                topEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        propForm.addEventListener('submit', function(e) {
            e.preventDefault();
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
                    return false;
                }
            }

            var submitBtn = propForm.querySelector('button[type="submit"]');
            var submitLabel = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving…';
            }

            if (typeof prepareDhaPropertyForSave === 'function') {
                prepareDhaPropertyForSave();
            }
            var formData = new FormData(propForm);
            var csrf = (propForm.querySelector('input[name="_token"]') || {}).value || '';
            fetch(propForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                credentials: 'same-origin'
            }).then(function(res) {
                return res.json().then(function(data) {
                    if (res.ok && data.success) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        var okEl = document.getElementById('form-success-top');
                        if (okEl) {
                            okEl.textContent = data.message || 'Listing updated.';
                            okEl.classList.remove('hidden');
                            okEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                        return;
                    }
                    if (res.status === 422 && data.errors) {
                        showServerErrors(data.errors);
                        return;
                    }
                    alert((data && data.message) || 'Save failed. Your form data is preserved — please try again.');
                });
            }).catch(function() {
                alert('Network error. Your form data is preserved — please try again.');
            }).finally(function() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitLabel;
                }
            });
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

    /** Price digits → string: crore / lac / thousand (compact), e.g. 4500000 → "45 lac" */
    var priceDigitsEl = document.getElementById('price_digits');
    var priceStringEl = document.getElementById('price_string');
    if (priceDigitsEl && priceStringEl) {
        function formatPriceCroreLacThousand(signedInt) {
            var neg = signedInt < 0;
            var n = Math.abs(Math.floor(signedInt));
            if (n === 0) return (neg ? 'minus ' : '') + 'zero';
            var parts = [];
            var crore = Math.floor(n / 10000000);
            n %= 10000000;
            var lac = Math.floor(n / 100000);
            n %= 100000;
            var thousand = Math.floor(n / 1000);
            n %= 1000;
            if (crore) parts.push(crore + ' crore');
            if (lac) parts.push(lac + ' lac');
            if (thousand) parts.push(thousand + ' thousand');
            if (n) parts.push(String(n));
            var core = parts.join(' ');
            return (neg ? 'minus ' : '') + core;
        }
        function syncPriceStringFromDigits() {
            var raw = (priceDigitsEl.value || '').trim();
            if (raw === '') {
                priceStringEl.value = '';
                return;
            }
            var num = parseFloat(raw);
            if (isNaN(num)) return;
            var intPart = Math.floor(Math.abs(num));
            var signedInt = num < 0 ? -intPart : intPart;
            priceStringEl.value = formatPriceCroreLacThousand(signedInt);
        }
        priceDigitsEl.addEventListener('input', syncPriceStringFromDigits);
        priceDigitsEl.addEventListener('change', syncPriceStringFromDigits);
    }

    function initDhaPropertyFields(root) {
        var scope = root || document;
        var isDhaPropertyEl = scope.querySelector('#is_dha_property');
        var dhaPhaseWrap = scope.querySelector('#dha-phase-select-wrap');
        var dhaPhaseSelect = scope.querySelector('#dha_phase_id');
        if (!isDhaPropertyEl || isDhaPropertyEl.dataset.dhaBound === '1') {
            return { isDhaPropertyEl: isDhaPropertyEl, dhaPhaseSelect: dhaPhaseSelect };
        }
        isDhaPropertyEl.dataset.dhaBound = '1';
        function syncDhaPropertyFields() {
            if (!dhaPhaseWrap || !dhaPhaseSelect) return;
            var isDha = isDhaPropertyEl.checked;
            dhaPhaseWrap.classList.toggle('hidden', !isDha);
            if (!isDha) {
                dhaPhaseSelect.value = '';
            }
        }
        isDhaPropertyEl.addEventListener('change', syncDhaPropertyFields);
        syncDhaPropertyFields();
        return { isDhaPropertyEl: isDhaPropertyEl, dhaPhaseSelect: dhaPhaseSelect };
    }
    initDhaPropertyFields(document);
    function prepareDhaPropertyForSave() {
        var isDhaPropertyEl = document.getElementById('is_dha_property');
        var dhaPhaseSelect = document.getElementById('dha_phase_id');
        if (isDhaPropertyEl && !isDhaPropertyEl.checked && dhaPhaseSelect) {
            dhaPhaseSelect.value = '';
        }
    }
    window.initAdminSectionPanel = function(root) {
        initDhaPropertyFields(root || document);
    };
    document.addEventListener('admin-section-panel-loaded', function(e) {
        initDhaPropertyFields((e.detail && e.detail.root) || document);
    });
    var sectionForm = document.getElementById('section-edit-form');
    if (sectionForm) {
        sectionForm.addEventListener('submit', function() {
            prepareDhaPropertyForSave();
        }, true);
    }
})();
</script>
