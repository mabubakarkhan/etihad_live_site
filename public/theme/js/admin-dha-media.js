(function () {
    var form = document.getElementById('dha-form') || document.getElementById('dha-phase-form');
    if (!form) return;

    var uploadUrl = form.getAttribute('data-upload-url') || '';
    var phaseId = form.getAttribute('data-phase-id') || '';
    var csrf = (form.querySelector('input[name="_token"]') || {}).value || '';

    function getUploadToken() {
        var el = document.getElementById('dha-phase-upload-token');
        return el ? el.value : '';
    }

    function snapshotFiles(input) {
        if (!input || !input.files) return [];
        return Array.prototype.slice.call(input.files);
    }

    function escAttr(s) {
        return String(s || '').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }

    function uploadFile(file, type) {
        if (!uploadUrl || !file) {
            return Promise.reject(new Error('Upload is not configured.'));
        }
        var body = new FormData();
        body.append('file', file);
        body.append('type', type);
        body.append('_token', csrf);
        if (phaseId) body.append('phase_id', phaseId);
        var token = getUploadToken();
        if (token) body.append('upload_token', token);

        return fetch(uploadUrl, {
            method: 'POST',
            body: body,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        }).then(function (res) {
            return res.json().then(function (data) {
                if (!res.ok || !data.success) {
                    var msg = (data && (data.message || (data.errors && data.errors.file && data.errors.file[0]))) || 'Upload failed.';
                    throw new Error(msg);
                }
                return data;
            });
        });
    }

    function ensurePathInput(name) {
        var el = form.querySelector('input[name="' + name + '"]');
        if (!el) {
            el = document.createElement('input');
            el.type = 'hidden';
            el.name = name;
            form.appendChild(el);
        }
        return el;
    }

    function setBlock(id, show) {
        var el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', !show);
    }

    function setText(id, text) {
        var el = document.getElementById(id);
        if (el) el.textContent = text || '';
    }

    function clearFeaturedBlocks(root) {
        if (!root) return;
        root.querySelectorAll('.dha-media-preview, .dha-featured-existing-block').forEach(function (el) {
            el.remove();
        });
    }

    function uploadFeatured(file, pathInput, statusPrefix, uploadType, previewClass) {
        var prefix = statusPrefix || 'dha-featured';
        var type = uploadType || 'featured';
        var imgClass = previewClass || 'dha-featured-preview-img';
        setBlock(prefix + '-upload-status', true);
        setBlock(prefix + '-upload-success', false);
        setBlock(prefix + '-upload-error', false);

        return uploadFile(file, type).then(function (data) {
            if (pathInput) pathInput.value = data.path || '';
            var removeName = type === 'card' ? 'remove_card_image' : 'remove_featured_image';
            var removeCb = form.querySelector('input[name="' + removeName + '"]');
            if (removeCb) removeCb.checked = false;

            var existingId = prefix + '-existing';
            var previewId = prefix + '-preview';
            var existing = document.getElementById(existingId);
            if (existing) existing.classList.add('hidden');

            var preview = document.getElementById(previewId);
            if (preview) {
                preview.classList.remove('hidden');
                var img = preview.querySelector('.' + imgClass.split(' ')[0]);
                if (!img) {
                    img = document.createElement('img');
                    img.className = imgClass + ' h-20 rounded border border-slate-300 dark:border-slate-700 object-cover';
                    if (type === 'card') {
                        img.classList.add('w-20');
                    }
                    img.alt = '';
                    preview.insertBefore(img, preview.firstChild);
                }
                img.src = data.url;
                img.classList.remove('hidden');
            }

            setBlock(prefix + '-upload-success', true);
            setText(prefix + '-upload-success', data.message || 'Uploaded successfully.');
        }).catch(function (err) {
            setBlock(prefix + '-upload-error', true);
            setText(prefix + '-upload-error', err.message || 'Upload failed.');
        }).finally(function () {
            setBlock(prefix + '-upload-status', false);
        });
    }

    function bindRemoveImage(checkboxName, pathInputName, previewIds, existingIds) {
        var removeInput = form.querySelector('input[name="' + checkboxName + '"]');
        if (!removeInput) return;
        removeInput.addEventListener('change', function () {
            if (!removeInput.checked) return;
            var pathInput = form.querySelector('input[name="' + pathInputName + '"]');
            if (pathInput) pathInput.value = '';
            previewIds.forEach(function (id) {
                var el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
            existingIds.forEach(function (id) {
                var el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
        });
    }

    bindRemoveImage('remove_featured_image', 'featured_image_path', ['dha-featured-preview', 'dha-phase-featured-preview'], ['dha-featured-existing', 'dha-phase-featured-existing']);
    bindRemoveImage('remove_card_image', 'card_image_path', ['dha-phase-card-preview'], ['dha-phase-card-existing']);

    var galleryList = document.getElementById('dha-phase-gallery-list');

    function appendGalleryRow(path, url) {
        if (!galleryList) return;
        var div = document.createElement('div');
        div.className = 'dha-gallery-item relative flex items-center gap-2';
        div.setAttribute('data-path', path || '');
        div.innerHTML =
            '<img src="' + escAttr(url) + '" class="h-20 w-20 object-cover rounded-lg border border-slate-300 dark:border-slate-700" alt="" loading="lazy" />' +
            '<span class="text-xs text-emerald-600 dark:text-emerald-400">Uploaded</span>' +
            '<button type="button" class="dha-remove-gallery text-xs px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 hover:bg-rose-600/10">Remove</button>' +
            '<input type="hidden" name="image_gallery_paths[]" value="' + escAttr(path) + '" />';
        galleryList.appendChild(div);
    }

    function uploadGalleryFiles(files) {
        var list = Array.isArray(files) ? files.slice() : snapshotFiles({ files: files });
        if (!list.length) return;

        setBlock('dha-phase-gallery-upload-status', true);
        setBlock('dha-phase-gallery-upload-error', false);

        var total = list.length;
        var failed = 0;
        var chain = Promise.resolve();

        list.forEach(function (file, index) {
            chain = chain.then(function () {
                setText('dha-phase-gallery-upload-status-text', total > 1
                    ? ('Uploading image ' + (index + 1) + ' of ' + total + '…')
                    : 'Uploading image…');
                return uploadFile(file, 'image_gallery').then(function (data) {
                    appendGalleryRow(data.path, data.url);
                }).catch(function () {
                    failed++;
                });
            });
        });

        chain.finally(function () {
            setBlock('dha-phase-gallery-upload-status', false);
            if (failed > 0) {
                setBlock('dha-phase-gallery-upload-error', true);
                setText('dha-phase-gallery-upload-error', failed === total
                    ? 'Gallery upload failed. Please try again.'
                    : (failed + ' of ' + total + ' images failed. The rest were added.'));
            }
        });
    }

    function appendPlotMapRow(path, url) {
        var container = document.getElementById('dha-phase-plot-maps');
        if (!container) return;
        var row = document.createElement('div');
        row.className = 'dha-plot-item flex flex-wrap items-end gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700';
        row.innerHTML =
            '<img src="' + escAttr(url) + '" class="h-16 w-16 object-cover rounded plot-map-thumb border border-slate-300 dark:border-slate-700" alt="" />' +
            '<input type="hidden" name="plot_map_paths[]" value="' + escAttr(path) + '" />' +
            '<input type="text" name="plot_map_titles[]" placeholder="Map title" class="flex-1 min-w-[140px] rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" />' +
            '<span class="text-xs text-emerald-600 dark:text-emerald-400">Uploaded</span>' +
            '<button type="button" class="dha-remove-plot text-xs px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 hover:bg-rose-600/10">Remove</button>';
        container.appendChild(row);
    }

    function uploadPlotMapFiles(files) {
        var list = Array.isArray(files) ? files.slice() : snapshotFiles({ files: files });
        if (!list.length) return;

        setBlock('dha-phase-plot-upload-status', true);
        setBlock('dha-phase-plot-upload-error', false);

        var total = list.length;
        var failed = 0;
        var chain = Promise.resolve();

        list.forEach(function (file, index) {
            chain = chain.then(function () {
                setText('dha-phase-plot-upload-status-text', total > 1
                    ? ('Uploading plot map ' + (index + 1) + ' of ' + total + '…')
                    : 'Uploading plot map…');
                return uploadFile(file, 'plot_maps').then(function (data) {
                    appendPlotMapRow(data.path, data.url);
                }).catch(function () {
                    failed++;
                });
            });
        });

        chain.finally(function () {
            setBlock('dha-phase-plot-upload-status', false);
            if (failed > 0) {
                setBlock('dha-phase-plot-upload-error', true);
                setText('dha-phase-plot-upload-error', failed === total
                    ? 'Plot map upload failed. Please try again.'
                    : (failed + ' of ' + total + ' plot maps failed. The rest were added.'));
            }
        });
    }

    if (galleryList) {
        galleryList.addEventListener('click', function (e) {
            if (e.target.classList.contains('dha-remove-gallery')) {
                e.target.closest('.dha-gallery-item')?.remove();
            }
        });
    }

    var plotContainer = document.getElementById('dha-phase-plot-maps');
    if (plotContainer) {
        plotContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('dha-remove-plot')) {
                e.target.closest('.dha-plot-item')?.remove();
            }
        });
    }

    form.addEventListener('change', function (e) {
        var input = e.target;
        if (!input.classList.contains('dha-media-upload')) return;

        var type = input.getAttribute('data-upload-type') || '';
        var files = snapshotFiles(input);
        input.value = '';

        if (type === 'image_gallery') {
            uploadGalleryFiles(files);
            return;
        }
        if (type === 'plot_maps') {
            uploadPlotMapFiles(files);
            return;
        }

        var file = files[0];
        if (!file) return;

        if (type === 'featured' || type === 'card') {
            var pathName = input.getAttribute('data-path-name') || (type === 'card' ? 'card_image_path' : 'featured_image_path');
            var pathInput = ensurePathInput(pathName);
            var prefix = input.getAttribute('data-status-prefix') || (type === 'card' ? 'dha-phase-card' : 'dha-featured');
            var previewClass = input.getAttribute('data-preview-class') || 'dha-featured-preview-img';
            uploadFeatured(file, pathInput, prefix, type, previewClass);
            return;
        }

        if (type === 'phase_pdf') {
            var pdfPathInput = ensurePathInput('phase_pdf_path');
            var pdfPrefix = input.getAttribute('data-status-prefix') || 'dha-phase-pdf';
            setBlock(pdfPrefix + '-upload-status', true);
            setBlock(pdfPrefix + '-upload-success', false);
            setBlock(pdfPrefix + '-upload-error', false);

            uploadFile(file, 'phase_pdf').then(function (data) {
                if (pdfPathInput) pdfPathInput.value = data.path || '';
                var removeCb = form.querySelector('input[name="remove_phase_pdf"]');
                if (removeCb) removeCb.checked = false;
                var existing = document.getElementById('dha-phase-pdf-existing');
                if (existing) existing.classList.add('hidden');
                var preview = document.getElementById('dha-phase-pdf-preview');
                if (preview) {
                    preview.classList.remove('hidden');
                    preview.innerHTML = '<a href="' + escAttr(data.url) + '" target="_blank" rel="noopener" class="hover:underline">PDF ready — open preview</a>';
                }
                setBlock(pdfPrefix + '-upload-success', true);
                setText(pdfPrefix + '-upload-success', data.message || 'PDF uploaded successfully.');
            }).catch(function (err) {
                setBlock(pdfPrefix + '-upload-error', true);
                setText(pdfPrefix + '-upload-error', err.message || 'Upload failed.');
            }).finally(function () {
                setBlock(pdfPrefix + '-upload-status', false);
            });
        }
    });
})();
