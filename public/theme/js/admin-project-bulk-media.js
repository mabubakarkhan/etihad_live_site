(function () {
    var modal = document.getElementById('project-bulk-media-modal');
    if (!modal) return;

    var subtitle = document.getElementById('project-bulk-media-subtitle');
    var zipInput = document.getElementById('project-bulk-media-zip');
    var statusEl = document.getElementById('project-bulk-media-status');
    var previewWrap = document.getElementById('project-bulk-media-preview');
    var previewList = document.getElementById('project-bulk-media-preview-list');
    var warningsEl = document.getElementById('project-bulk-media-warnings');
    var resultEl = document.getElementById('project-bulk-media-result');
    var uploadBtn = document.getElementById('project-bulk-media-upload-btn');
    var importBtn = document.getElementById('project-bulk-media-import-btn');

    var state = {
        projectId: null,
        previewUrl: '',
        importUrl: '',
        token: null,
        canImport: false,
    };

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.getAttribute('content');
        var input = document.querySelector('input[name="_token"]');
        return input ? input.value : '';
    }

    function setStatus(message, type) {
        if (!statusEl) return;
        statusEl.classList.remove('hidden', 'border-rose-500/40', 'bg-rose-500/10', 'text-rose-800', 'dark:text-rose-200', 'border-sky-500/40', 'bg-sky-500/10', 'text-sky-800', 'dark:text-sky-200', 'border-emerald-500/40', 'bg-emerald-500/10', 'text-emerald-800', 'dark:text-emerald-200');
        if (!message) {
            statusEl.classList.add('hidden');
            statusEl.textContent = '';
            return;
        }
        statusEl.classList.remove('hidden');
        if (type === 'error') {
            statusEl.classList.add('border-rose-500/40', 'bg-rose-500/10', 'text-rose-800', 'dark:text-rose-200');
        } else if (type === 'loading') {
            statusEl.classList.add('border-sky-500/40', 'bg-sky-500/10', 'text-sky-800', 'dark:text-sky-200');
        } else {
            statusEl.classList.add('border-emerald-500/40', 'bg-emerald-500/10', 'text-emerald-800', 'dark:text-emerald-200');
        }
        statusEl.textContent = message;
    }

    function resetModal() {
        state.token = null;
        state.canImport = false;
        if (zipInput) zipInput.value = '';
        setStatus('', '');
        if (previewWrap) previewWrap.classList.add('hidden');
        if (previewList) previewList.innerHTML = '';
        if (warningsEl) {
            warningsEl.classList.add('hidden');
            warningsEl.innerHTML = '';
        }
        if (resultEl) {
            resultEl.classList.add('hidden');
            resultEl.innerHTML = '';
        }
        if (importBtn) importBtn.classList.add('hidden');
        if (uploadBtn) {
            uploadBtn.classList.remove('hidden');
            uploadBtn.disabled = false;
        }
    }

    function openModal(btn) {
        state.projectId = btn.getAttribute('data-project-id');
        state.previewUrl = btn.getAttribute('data-preview-url');
        state.importUrl = btn.getAttribute('data-import-url');
        if (subtitle) {
            subtitle.textContent = 'Project: ' + (btn.getAttribute('data-project-title') || '');
        }
        resetModal();
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        resetModal();
    }

    function renderPreviewItems(items) {
        if (!previewList) return;
        previewList.innerHTML = '';
        items.forEach(function (item) {
            var li = document.createElement('li');
            li.className = 'rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 bg-slate-50 dark:bg-slate-950/40';
            var label = item.label || item.folder || 'Item';
            var files = [];
            if (item.file) files.push(item.file);
            if (Array.isArray(item.files)) files = files.concat(item.files);
            if (item.type === 'detail_tabs' && Array.isArray(item.assignments)) {
                item.assignments.forEach(function (a) {
                    var names = Array.isArray(a.files) ? a.files.join(', ') : '';
                    files.push((a.tab || 'tab') + ': ' + names);
                });
            }
            li.innerHTML = '<strong class="text-slate-800 dark:text-slate-100">' + label + '</strong><br><span class="text-xs text-slate-500 dark:text-slate-400">' + files.join(' · ') + '</span>';
            previewList.appendChild(li);
        });
        if (previewWrap) previewWrap.classList.remove('hidden');
    }

    function renderWarnings(warnings) {
        if (!warningsEl) return;
        if (!warnings || !warnings.length) {
            warningsEl.classList.add('hidden');
            warningsEl.innerHTML = '';
            return;
        }
        warningsEl.classList.remove('hidden');
        warningsEl.innerHTML = '<p class="font-medium mb-1">Warnings</p><ul class="list-disc list-inside space-y-0.5">' +
            warnings.map(function (w) { return '<li>' + w + '</li>'; }).join('') +
            '</ul>';
    }

    document.querySelectorAll('.project-bulk-media-open').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal(btn); });
    });

    modal.querySelectorAll('.project-bulk-media-close, .project-bulk-media-backdrop').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    if (uploadBtn) {
        uploadBtn.addEventListener('click', function () {
            if (!zipInput || !zipInput.files || !zipInput.files.length) {
                setStatus('Please choose a ZIP file first.', 'error');
                return;
            }
            var formData = new FormData();
            formData.append('zip', zipInput.files[0]);
            formData.append('_token', csrfToken());

            uploadBtn.disabled = true;
            setStatus('Uploading and scanning ZIP…', 'loading');

            fetch(state.previewUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            })
                .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
                .then(function (result) {
                    uploadBtn.disabled = false;
                    if (!result.ok || !result.data.success) {
                        setStatus(result.data.message || 'Preview failed.', 'error');
                        return;
                    }
                    state.token = result.data.token;
                    state.canImport = !!result.data.can_import;
                    renderPreviewItems(result.data.items || []);
                    renderWarnings(result.data.warnings || []);
                    if (result.data.errors && result.data.errors.length) {
                        setStatus(result.data.errors.join(' '), 'error');
                        state.canImport = false;
                    } else if (!state.canImport) {
                        setStatus('No importable media found in this ZIP.', 'error');
                    } else {
                        setStatus('Preview ready. Confirm import to apply files.', 'success');
                        if (importBtn) importBtn.classList.remove('hidden');
                    }
                })
                .catch(function () {
                    uploadBtn.disabled = false;
                    setStatus('Upload failed. Please try again.', 'error');
                });
        });
    }

    if (importBtn) {
        importBtn.addEventListener('click', function () {
            if (!state.token || !state.canImport) return;

            importBtn.disabled = true;
            setStatus('Importing media…', 'loading');

            fetch(state.importUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ token: state.token }),
            })
                .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
                .then(function (result) {
                    importBtn.disabled = false;
                    if (!result.ok || !result.data.success) {
                        setStatus(result.data.message || 'Import failed.', 'error');
                        return;
                    }
                    setStatus(result.data.message || 'Import complete.', 'success');
                    if (importBtn) importBtn.classList.add('hidden');
                    if (uploadBtn) uploadBtn.classList.add('hidden');
                    if (resultEl) {
                        resultEl.classList.remove('hidden');
                        var html = '';
                        if (result.data.imported && result.data.imported.length) {
                            html += '<p class="font-medium text-emerald-700 dark:text-emerald-300">Imported:</p><ul class="list-disc list-inside text-slate-600 dark:text-slate-300">' +
                                result.data.imported.map(function (line) { return '<li>' + line + '</li>'; }).join('') + '</ul>';
                        }
                        if (result.data.skipped && result.data.skipped.length) {
                            html += '<p class="font-medium text-amber-700 dark:text-amber-300 mt-2">Skipped:</p><ul class="list-disc list-inside text-slate-600 dark:text-slate-300">' +
                                result.data.skipped.map(function (line) { return '<li>' + line + '</li>'; }).join('') + '</ul>';
                        }
                        resultEl.innerHTML = html;
                    }
                    state.token = null;
                })
                .catch(function () {
                    importBtn.disabled = false;
                    setStatus('Import failed. Please try again.', 'error');
                });
        });
    }
})();
