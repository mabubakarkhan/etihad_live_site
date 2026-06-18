(function () {
    var form = document.getElementById('section-edit-form');
    if (!form) return;

    var contentEl = document.getElementById('section-content');
    var navEl = document.getElementById('section-tab-nav');
    var successEl = document.getElementById('section-success-top');
    var errorsTop = document.getElementById('form-errors-top');
    var errorsList = document.getElementById('form-errors-list');
    var statusEl = document.getElementById('section-save-status');
    var loading = false;

    function csrf() {
        var el = form.querySelector('input[name="_token"]');
        return el ? el.value : '';
    }

    function hideSuccess() {
        if (successEl) successEl.classList.add('hidden');
    }

    function showSuccess(msg) {
        if (!successEl) return;
        successEl.textContent = msg || 'Saved.';
        successEl.classList.remove('hidden');
        successEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideErrors() {
        if (errorsTop) errorsTop.classList.add('hidden');
        if (errorsList) errorsList.innerHTML = '';
    }

    function showErrors(errors) {
        if (!errorsTop || !errorsList) return;
        errorsList.innerHTML = '';
        Object.keys(errors || {}).forEach(function (field) {
            (errors[field] || []).forEach(function (msg) {
                var li = document.createElement('li');
                li.textContent = msg;
                errorsList.appendChild(li);
            });
        });
        if (errorsList.children.length) {
            errorsTop.classList.remove('hidden');
            errorsTop.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function syncQuill(root) {
        if (typeof Quill === 'undefined') return;
        (root || document).querySelectorAll('.quill-editor').forEach(function (el) {
            var q = Quill.find(el);
            var wrap = el.closest('.richtext-wrap');
            var ta = wrap ? wrap.querySelector('textarea.richtext') : null;
            if (q && ta) ta.value = q.root.innerHTML;
        });
    }

    function initQuill(root) {
        if (typeof Quill === 'undefined') return;
        (root || document).querySelectorAll('textarea.richtext').forEach(function (ta) {
            var wrap = ta.closest('.richtext-wrap');
            if (!wrap || wrap.querySelector('.quill-editor')) return;
            var div = document.createElement('div');
            div.className = 'quill-editor';
            div.style.minHeight = '120px';
            wrap.insertBefore(div, ta);
            var q = new Quill(div, { theme: 'snow', modules: { toolbar: [['bold', 'italic', 'underline'], ['link'], [{ list: 'ordered' }, { list: 'bullet' }]] } });
            q.root.innerHTML = ta.value;
            q.on('text-change', function () { ta.value = q.root.innerHTML; });
        });
    }

    function initTomSelect(root) {
        if (typeof TomSelect === 'undefined') return;
        var scope = root || document;
        var stateEl = scope.querySelector('#state-select');
        var cityEl = scope.querySelector('#city-select');
        if (!stateEl || !cityEl || stateEl.tomselect) return;
        var citiesByState = window.citiesByState || {};
        var stateSelect = new TomSelect(stateEl, { create: false, sortField: { field: 'text', direction: 'asc' } });
        var citySelect = new TomSelect(cityEl, { create: false, sortField: { field: 'text', direction: 'asc' } });
        stateSelect.on('change', function (val) {
            var cities = citiesByState[val] || [];
            citySelect.clearOptions();
            cities.forEach(function (c) { citySelect.addOption({ value: c, text: c }); });
            citySelect.clear(true);
            if (cities.indexOf('Lahore') >= 0) citySelect.setValue('Lahore');
            else if (cities.length) citySelect.setValue(cities[0]);
        });
    }

    function initPanel(root) {
        initQuill(root);
        initTomSelect(root);
        if (typeof window.initAdminSectionPanel === 'function') {
            window.initAdminSectionPanel(root);
        }
        document.dispatchEvent(new CustomEvent('admin-section-panel-loaded', { detail: { root: root } }));
    }

    function setActiveTab(section) {
        if (!navEl) return;
        navEl.querySelectorAll('.section-tab-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-section') === section);
        });
    }

    function loadSection(section, loadUrl, updateUrl) {
        if (loading || !contentEl) return;
        loading = true;
        hideSuccess();
        hideErrors();
        if (statusEl) statusEl.textContent = 'Loading section…';

        fetch(loadUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        }).then(function (res) {
            return res.json();
        }).then(function (data) {
            if (!data.success || !data.html) {
                throw new Error(data.message || 'Failed to load section.');
            }
            contentEl.innerHTML = data.html;
            form.setAttribute('data-section', section);
            form.setAttribute('data-update-url', updateUrl);
            setActiveTab(section);
            initPanel(contentEl);
            if (statusEl) statusEl.textContent = '';
            var url = new URL(window.location.href);
            url.pathname = url.pathname.replace(/\/edit-section\/[^/]+/, '/edit-section/' + section);
            window.history.replaceState({}, '', url.toString());
        }).catch(function (err) {
            if (statusEl) statusEl.textContent = err.message || 'Load failed.';
        }).finally(function () {
            loading = false;
        });
    }

    if (navEl) {
        navEl.addEventListener('click', function (e) {
            var btn = e.target.closest('.section-tab-btn');
            if (!btn) return;
            var section = btn.getAttribute('data-section');
            if (!section || section === form.getAttribute('data-section')) return;
            var loadUrl = btn.getAttribute('data-load-url');
            var updateUrl = form.getAttribute('data-update-url') || '';
            updateUrl = updateUrl.replace(/\/sections\/[^/?#]+/, '/sections/' + section);
            loadSection(section, loadUrl, updateUrl);
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        hideSuccess();
        hideErrors();
        syncQuill(contentEl);

        var saveBtn = form.querySelector('.section-save-btn');
        var label = saveBtn ? saveBtn.textContent : '';
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving…';
        }
        if (statusEl) statusEl.textContent = '';

        var url = form.getAttribute('data-update-url') || '';
        var body = new FormData(form);

        fetch(url, {
            method: 'POST',
            body: body,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf()
            },
            credentials: 'same-origin'
        }).then(function (res) {
            return res.json().then(function (data) {
                if (res.ok && data.success) {
                    showSuccess(data.message || 'Section saved.');
                    return;
                }
                if (res.status === 422 && data.errors) {
                    showErrors(data.errors);
                    return;
                }
                showErrors({ form: [(data && data.message) || 'Save failed. Please try again.'] });
            });
        }).catch(function () {
            showErrors({ form: ['Network error. Please try again.'] });
        }).finally(function () {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = label;
            }
        });
    });

    var fullBtn = form.querySelector('.full-save-btn');
    if (fullBtn) {
        fullBtn.addEventListener('click', function () {
            var fullUrl = form.getAttribute('data-full-edit-url');
            if (fullUrl) window.location.href = fullUrl;
        });
    }

    initPanel(contentEl);
})();
