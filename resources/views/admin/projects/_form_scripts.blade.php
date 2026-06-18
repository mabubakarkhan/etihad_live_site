@php
    $projectSectionMapJs = [
        'title' => ['tab' => 'tab-basics', 'name' => 'Basics'],
        'slug' => ['tab' => 'tab-basics', 'name' => 'Basics'],
        'status' => ['tab' => 'tab-status', 'name' => 'Status'],
        'price' => ['tab' => 'tab-basics', 'name' => 'Basics'],
        'launch_year' => ['tab' => 'tab-basics', 'name' => 'Basics'],
        'description' => ['tab' => 'tab-basics', 'name' => 'Basics'],
        'project_type_ids' => ['tab' => 'tab-basics', 'name' => 'Basics'],
        'state' => ['tab' => 'tab-address', 'name' => 'Address'],
        'city' => ['tab' => 'tab-address', 'name' => 'Address'],
        'short_address' => ['tab' => 'tab-address', 'name' => 'Address'],
        'full_address' => ['tab' => 'tab-address', 'name' => 'Address'],
        'google_map' => ['tab' => 'tab-address', 'name' => 'Address'],
        'latitude' => ['tab' => 'tab-address', 'name' => 'Address'],
        'longitude' => ['tab' => 'tab-address', 'name' => 'Address'],
        'logo' => ['tab' => 'tab-media', 'name' => 'Project media'],
        'logo_path' => ['tab' => 'tab-media', 'name' => 'Project media'],
        'featured_image' => ['tab' => 'tab-media', 'name' => 'Project media'],
        'featured_image_path' => ['tab' => 'tab-media', 'name' => 'Project media'],
        'homepage_listing_image' => ['tab' => 'tab-media', 'name' => 'Project media'],
        'homepage_listing_image_path' => ['tab' => 'tab-media', 'name' => 'Project media'],
        'featured_youtube_url' => ['tab' => 'tab-featured-video', 'name' => 'Featured video'],
        'featured_video_title' => ['tab' => 'tab-featured-video', 'name' => 'Featured video'],
        'featured_video_description' => ['tab' => 'tab-featured-video', 'name' => 'Featured video'],
        'vr_tour_url' => ['tab' => 'tab-vr-tour', 'name' => 'VR Tour'],
        'about_developers' => ['tab' => 'tab-about', 'name' => 'About developers'],
        'project_file_pdf' => ['tab' => 'tab-media', 'name' => 'Brochure PDF'],
        'project_file_pdf_path' => ['tab' => 'tab-media', 'name' => 'Brochure PDF'],
        'noc_planning_content' => ['tab' => 'tab-noc', 'name' => 'NOC & planning'],
        'noc_planning_image' => ['tab' => 'tab-noc', 'name' => 'NOC & planning'],
        'meta_title' => ['tab' => 'tab-seo', 'name' => 'SEO'],
        'meta_description' => ['tab' => 'tab-seo', 'name' => 'SEO'],
        'meta_keywords' => ['tab' => 'tab-seo', 'name' => 'SEO'],
        'canonical_url' => ['tab' => 'tab-seo', 'name' => 'SEO'],
    ];
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    var projectSectionMap = @json($projectSectionMapJs);
    var tabsWrapper = document.getElementById('project-form-tabs');
    if (tabsWrapper) {
        var btns = tabsWrapper.querySelectorAll('.project-tab-btn');
        var panels = tabsWrapper.querySelectorAll('.tab-panel');
        function switchToTab(targetId) {
            var id = targetId;
            window.requestAnimationFrame(function() {
                btns.forEach(function(b) {
                    b.classList.toggle('active', b.getAttribute('data-tab') === id);
                });
                panels.forEach(function(p) {
                    p.classList.toggle('active', p.id === id);
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
        tabsWrapper.querySelectorAll('.project-tab-next').forEach(function(nextBtn) {
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
        tabsWrapper.querySelectorAll('.project-tab-back').forEach(function(backBtn) {
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

    var stateEl = document.getElementById('state-select');
    var cityEl = document.getElementById('city-select');
    if (stateEl && cityEl && typeof TomSelect !== 'undefined') {
        var citiesByState = window.citiesByState || {};
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

    if (typeof Quill !== 'undefined') {
        document.querySelectorAll('textarea.richtext').forEach(function(ta) {
            var wrap = ta.closest('.richtext-wrap');
            if (!wrap) return;
            var div = document.createElement('div');
            div.className = 'quill-editor';
            div.style.minHeight = '120px';
            wrap.insertBefore(div, ta);
            var q = new Quill(div, { theme: 'snow', modules: { toolbar: [['bold','italic','underline'],['link'],[{list:'ordered'},{list:'bullet'}]] } });
            q.root.innerHTML = ta.value;
            q.on('text-change', function() { ta.value = q.root.innerHTML; });
        });
    }

    window.projectFeatureIconCounter = window.projectFeatureIconCounter || document.querySelectorAll('.feature-row').length;
    document.getElementById('add-feature')?.addEventListener('click', function() {
        var c = document.getElementById('features-container');
        var id = 'feature_icon_' + (window.projectFeatureIconCounter++);
        var div = document.createElement('div');
        div.className = 'flex gap-2 items-center feature-row';
        div.innerHTML = '<input type="text" name="feature_titles[]" placeholder="Title" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" /><input type="text" name="feature_icons[]" placeholder="Icon name" id="' + id + '" class="w-32 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" /><button type="button" class="icon-picker-btn px-2 py-1 rounded border border-slate-400 text-slate-600 dark:text-slate-400 text-xs hover:bg-slate-200 dark:hover:bg-slate-700" data-target="' + id + '">Pick</button><button type="button" class="remove-feature text-rose-600 dark:text-rose-400 hover:text-rose-300 text-xs">Remove</button>';
        c.appendChild(div);
    });
    document.getElementById('features-container')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-feature')) e.target.closest('.feature-row')?.remove();
    });

    document.getElementById('add-price-plan')?.addEventListener('click', function() {
        var c = document.getElementById('price-plan-container');
        var i = document.createElement('input');
        i.type = 'text';
        i.name = 'price_plan_items[]';
        i.placeholder = 'e.g. 2 BHK from 45 Lac';
        i.className = 'block w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100';
        c.appendChild(i);
    });

    document.getElementById('add-faq')?.addEventListener('click', function() {
        var c = document.getElementById('faqs-container');
        var div = document.createElement('div');
        div.className = 'faq-row border border-slate-700 rounded-lg p-3 space-y-2';
        div.innerHTML = '<input type="text" name="faq_questions[]" placeholder="Question" class="block w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100" /><textarea name="faq_answers[]" rows="2" placeholder="Answer" class="block w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100"></textarea><button type="button" class="remove-faq text-rose-400 hover:text-rose-300 text-xs">Remove FAQ</button>';
        c.appendChild(div);
    });
    document.getElementById('faqs-container')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-faq')) e.target.closest('.faq-row')?.remove();
    });

    document.getElementById('add-plan')?.addEventListener('click', function() {
        var c = document.getElementById('plans-container');
        var div = document.createElement('div');
        div.className = 'plan-row border border-slate-700 rounded-lg p-3 flex flex-wrap gap-3 items-end';
        div.innerHTML = '<input type="hidden" name="existing_plan_images[]" value="" /><div class="flex-1 min-w-[200px]"><label class="block text-xs text-slate-400 mb-1">Title</label><input type="text" name="plan_titles[]" class="block w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100" /></div><div class="flex-1 min-w-[200px]"><label class="block text-xs text-slate-400 mb-1">Image</label><input type="file" accept="image/*" class="project-media-upload block w-full text-sm text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-200" data-upload-type="plan" /></div><button type="button" class="remove-plan text-rose-400 hover:text-rose-300 text-xs">Remove</button>';
        c.appendChild(div);
    });
    document.getElementById('plans-container')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-plan')) e.target.closest('.plan-row')?.remove();
    });

    function updatePricingPlaceIndexes() {
        var rows = document.querySelectorAll('#pricing-place-container .pricing-place-row');
        rows.forEach(function(row, idx) {
            row.querySelectorAll('input[name^="pricing_place_is_popular["]').forEach(function(inp) {
                inp.name = 'pricing_place_is_popular[' + idx + ']';
            });
        });
    }

    document.getElementById('add-pricing-place')?.addEventListener('click', function() {
        var c = document.getElementById('pricing-place-container');
        var idx = c ? c.querySelectorAll('.pricing-place-row').length : 0;
        var div = document.createElement('div');
        div.className = 'pricing-place-row border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-3';
        div.innerHTML =
            '<input type="hidden" name="existing_pricing_place_images[]" value="">' +
            '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">' +
            '<input type="text" name="pricing_place_titles[]" placeholder="Card title" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />' +
            '<input type="text" name="pricing_place_prices[]" placeholder="e.g. PKR 8.5M" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />' +
            '</div>' +
            '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">' +
            '<input type="text" name="pricing_place_feature_1[]" placeholder="Feature 1" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />' +
            '<input type="text" name="pricing_place_feature_2[]" placeholder="Feature 2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />' +
            '<input type="text" name="pricing_place_feature_3[]" placeholder="Feature 3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />' +
            '<input type="text" name="pricing_place_feature_4[]" placeholder="Feature 4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />' +
            '</div>' +
            '<div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end">' +
            '<input type="text" name="pricing_place_button_text[]" value="View Plan" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />' +
            '<label class="inline-flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">' +
            '<input type="hidden" name="pricing_place_is_popular[' + idx + ']" value="0">' +
            '<input type="checkbox" name="pricing_place_is_popular[' + idx + ']" value="1" class="rounded border-slate-400">' +
            'Mark as Most Popular</label></div>' +
            '<input type="file" accept="image/*" class="project-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="pricing_place" />' +
            '<button type="button" class="remove-pricing-place text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs">Remove card</button>';
        c.appendChild(div);
        updatePricingPlaceIndexes();
    });
    document.getElementById('pricing-place-container')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-pricing-place')) {
            e.target.closest('.pricing-place-row')?.remove();
            updatePricingPlaceIndexes();
        }
    });
    updatePricingPlaceIndexes();

    document.getElementById('add-testimonial')?.addEventListener('click', function() {
        var c = document.getElementById('testimonials-container');
        if (!c) return;
        var div = document.createElement('div');
        div.className = 'testimonial-row border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2';
        div.innerHTML =
            '<textarea name="testimonial_quotes[]" rows="2" placeholder="Quote" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"></textarea>' +
            '<div class="grid grid-cols-1 md:grid-cols-2 gap-2">' +
            '<input type="text" name="testimonial_names[]" placeholder="Client name" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">' +
            '<input type="text" name="testimonial_roles[]" placeholder="Role (e.g. Verified Buyer)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">' +
            '</div>' +
            '<button type="button" class="remove-testimonial text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs">Remove testimonial</button>';
        c.appendChild(div);
    });
    document.getElementById('testimonials-container')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-testimonial')) {
            e.target.closest('.testimonial-row')?.remove();
        }
    });

    document.getElementById('add-td')?.addEventListener('click', function() {
        var c = document.getElementById('td-container');
        var div = document.createElement('div');
        div.className = 'td-row flex gap-2';
        div.innerHTML = '<input type="text" name="td_titles[]" placeholder="Title" class="flex-1 rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100" /><input type="text" name="td_descriptions[]" placeholder="Description" class="flex-1 rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100" /><button type="button" class="remove-td text-rose-400 hover:text-rose-300 text-xs">Remove</button>';
        c.appendChild(div);
    });
    document.getElementById('td-container')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-td')) e.target.closest('.td-row')?.remove();
    });

    document.getElementById('add-video')?.addEventListener('click', function() {
        var c = document.getElementById('videos-container');
        var row = document.createElement('div');
        row.className = 'video-row flex gap-2 items-start';
        row.innerHTML = '<textarea name="video_urls[]" rows="3" placeholder="Paste iframe embed code from YouTube (Share → Embed)" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 font-mono placeholder-slate-400 dark:placeholder-slate-500"></textarea><button type="button" class="remove-video text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs py-1 px-2 rounded border border-rose-300 dark:border-rose-700 shrink-0">Remove</button>';
        c.appendChild(row);
    });
    document.getElementById('videos-container')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-video')) {
            var row = e.target.closest('.video-row');
            if (row) row.remove();
        }
    });

    function isValidUrl(s) {
        if (typeof s !== 'string' || !s.trim()) return false;
        try {
            var u = new URL(s.trim());
            return u.protocol === 'http:' || u.protocol === 'https:';
        } catch (e) { return false; }
    }
    function setProjectInlineError(errorElId, msg) {
        var el = document.getElementById(errorElId);
        if (el) { el.textContent = msg || ''; el.classList.toggle('hidden', !msg); }
    }
    var projectCanonical = document.getElementById('project_canonical_url');
    if (projectCanonical) {
        projectCanonical.addEventListener('blur', function() {
            var v = (this.value || '').trim();
            if (!v) { setProjectInlineError('project-canonical_url-error-inline', ''); return; }
            setProjectInlineError('project-canonical_url-error-inline', isValidUrl(v) ? '' : 'Please enter a valid URL (e.g. https://example.com)');
        });
    }
    function showProjectServerErrors(errors) {
        var topEl = document.getElementById('form-errors-top');
        var listEl = document.getElementById('form-errors-list');
        if (!topEl || !listEl) return;
        topEl.classList.add('hidden');
        listEl.innerHTML = '';
        Object.keys(errors || {}).forEach(function(field) {
            var messages = errors[field] || [];
            var section = projectSectionMap[field] || { tab: 'tab-basics', name: 'Basics' };
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

    var projectForm = document.getElementById('project-form');
    projectForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (typeof Quill !== 'undefined') {
            document.querySelectorAll('.quill-editor').forEach(function(el) {
                var q = Quill.find(el);
                var ta = el.closest('.richtext-wrap')?.querySelector('textarea.richtext');
                if (q && ta) ta.value = q.root.innerHTML;
            });
        }
        var errs = [];
        var titleEl = document.getElementById('project_title');
        setProjectInlineError('project-title-error-inline', '');
        if (projectCanonical) setProjectInlineError('project-canonical_url-error-inline', '');
        if (!titleEl || !(titleEl.value || '').trim()) {
            errs.push({ msg: 'Title is required.', tab: 'tab-basics', tabName: 'Basics' });
            if (titleEl) setProjectInlineError('project-title-error-inline', 'Title is required.');
        }
        var canonVal = projectCanonical ? (projectCanonical.value || '').trim() : '';
        if (canonVal && !isValidUrl(canonVal)) {
            errs.push({ msg: 'Canonical URL must be a valid URL (e.g. https://...).', tab: 'tab-seo', tabName: 'SEO' });
            if (projectCanonical) setProjectInlineError('project-canonical_url-error-inline', 'Please enter a valid URL (e.g. https://example.com)');
        }
        var topEl = document.getElementById('form-errors-top');
        var listEl = document.getElementById('form-errors-list');
        if (topEl && listEl && errs.length) {
            listEl.innerHTML = '';
            errs.forEach(function(o) {
                var li = document.createElement('li');
                li.className = 'flex flex-wrap items-baseline gap-1';
                li.innerHTML = '<span>' + o.msg + ' <span class="font-medium">(Section: ' + o.tabName + ')</span></span>' +
                    '<button type="button" class="go-to-tab text-xs underline hover:no-underline font-semibold" data-tab="' + o.tab + '">Go to: ' + o.tabName + '</button>';
                listEl.appendChild(li);
            });
            topEl.classList.remove('hidden');
            topEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return;
        }

        var submitBtn = projectForm.querySelector('button[type="submit"]');
        var submitLabel = submitBtn ? submitBtn.textContent : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving…';
        }

        var formData = new FormData(projectForm);
        var csrf = (projectForm.querySelector('input[name="_token"]') || {}).value || '';
        fetch(projectForm.action, {
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
                        okEl.textContent = data.message || 'Project updated.';
                        okEl.classList.remove('hidden');
                        okEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    return;
                }
                if (res.status === 422 && data.errors) {
                    showProjectServerErrors(data.errors);
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
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.go-to-tab');
        if (!btn || !btn.dataset.tab) return;
        var tabId = btn.dataset.tab;
        var wrapper = document.getElementById('project-form-tabs');
        if (!wrapper) return;
        var tabBtn = wrapper.querySelector('.project-tab-btn[data-tab="' + tabId + '"]');
        if (tabBtn) tabBtn.click();
    });
});
</script>
