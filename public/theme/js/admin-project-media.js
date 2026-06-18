(function () {
    var form = document.getElementById('project-form') || document.getElementById('section-edit-form');
    if (!form) return;

    var uploadUrl = form.getAttribute('data-upload-url') || '';
    var projectId = form.getAttribute('data-project-id') || '';
    var csrf = (form.querySelector('input[name="_token"]') || {}).value || '';

    var REMOVE_TO_PATH = {
        remove_logo: 'logo_path',
        remove_featured_image: 'featured_image_path',
        remove_homepage_listing_image: 'homepage_listing_image_path',
        remove_address_image: 'address_image_path',
        remove_developer_logo: 'developer_logo_path',
        remove_noc_planning_image: 'noc_planning_image_path',
        remove_project_file_pdf: 'project_file_pdf_path',
        remove_invest_image: 'invest_image_path'
    };

    function getUploadToken() {
        var el = document.getElementById('project-upload-token');
        return el ? el.value : '';
    }

    function snapshotFiles(input) {
        if (!input || !input.files) return [];
        return Array.prototype.slice.call(input.files);
    }

    function uploadFile(file, type) {
        if (!uploadUrl || !file) {
            return Promise.reject(new Error('Upload is not configured.'));
        }
        var body = new FormData();
        body.append('file', file);
        body.append('type', type);
        body.append('_token', csrf);
        if (projectId) body.append('project_id', projectId);
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

    function setMediaMsg(container, kind, text) {
        if (!container) return;
        var msg = container.querySelector('.project-media-msg');
        if (!msg) {
            msg = document.createElement('p');
            msg.className = 'project-media-msg text-xs mt-1';
            container.appendChild(msg);
        }
        msg.textContent = text || '';
        msg.className = 'project-media-msg text-xs mt-1 ' + (
            kind === 'ok' ? 'text-emerald-600 dark:text-emerald-400' :
            kind === 'err' ? 'text-rose-600 dark:text-rose-400' :
            'text-sky-600 dark:text-sky-400'
        );
    }

    function getMediaFieldRoot(wrap) {
        if (!wrap) return null;
        return wrap.parentElement;
    }

    function clearExistingBlocks(root) {
        if (!root) return;
        root.querySelectorAll('.project-media-preview').forEach(function (el) {
            el.remove();
        });
        Array.prototype.slice.call(root.children).forEach(function (child) {
            if (child.matches('[data-media-wrap]')) return;
            if (child.querySelector('input[type="checkbox"][name^="remove_"]') || child.querySelector('img[alt]')) {
                child.remove();
            }
        });
    }

    function instantRemoveSingleMedia(removeCheckbox) {
        if (!removeCheckbox || !removeCheckbox.checked) return;
        var pathName = REMOVE_TO_PATH[removeCheckbox.name];
        if (pathName) {
            var pathInput = form.querySelector('input[name="' + pathName + '"]');
            if (pathInput) pathInput.value = '';
        }
        var block = removeCheckbox.closest('.mb-2, p.text-xs, p');
        var root = block ? block.parentElement : null;
        if (block && block.querySelector('input[type="checkbox"][name^="remove_"]')) {
            block.remove();
        }
        if (root) {
            root.querySelectorAll('.project-media-preview').forEach(function (el) {
                el.remove();
            });
        }
    }

    form.querySelectorAll('input[type="checkbox"][name^="remove_"]').forEach(function (cb) {
        cb.addEventListener('change', function () {
            if (!cb.checked) return;
            if (cb.name === 'remove_featured_video') {
                var panel = cb.closest('#tab-featured-video') || form;
                panel.querySelectorAll('textarea[name="featured_youtube_url"], input[name="featured_video_title"]').forEach(function (el) {
                    el.value = '';
                });
                var desc = panel.querySelector('textarea[name="featured_video_description"]');
                if (desc) desc.value = '';
                var label = cb.closest('label');
                if (label) label.remove();
                return;
            }
            instantRemoveSingleMedia(cb);
        });
    });

    function buildPreviewHtml(url, isPdf, withRemove) {
        var safeUrl = (url || '').replace(/"/g, '&quot;');
        var removeBtn = withRemove
            ? ' <button type="button" class="project-media-remove-preview px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 text-xs hover:bg-rose-600/10">Remove</button>'
            : '';
        if (isPdf) {
            return '<a href="' + safeUrl + '" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Uploaded PDF — open</a>' + removeBtn;
        }
        return '<img src="' + safeUrl + '" alt="" class="h-16 w-16 object-cover rounded-lg border border-slate-300 dark:border-slate-700" />' +
            '<a href="' + safeUrl + '" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Open full size</a>' +
            removeBtn;
    }

    function showPreview(container, url, isPdf, replace) {
        if (!container || !url) return;
        if (replace) {
            clearExistingBlocks(getMediaFieldRoot(container.matches('[data-media-wrap]') ? container : container.closest('[data-media-wrap]')));
        }
        var wrap = container.matches('[data-media-wrap]') ? container : container.closest('[data-media-wrap]');
        if (!wrap) wrap = container;
        var preview = wrap.querySelector('.project-media-preview');
        if (!preview) {
            preview = document.createElement('div');
            preview.className = 'project-media-preview mb-2 flex items-center gap-3 flex-wrap';
            var fileInput = wrap.querySelector('.project-media-upload');
            if (fileInput) {
                wrap.insertBefore(preview, fileInput);
            } else {
                wrap.appendChild(preview);
            }
        }
        preview.innerHTML = buildPreviewHtml(url, isPdf, true);
        var removePreviewBtn = preview.querySelector('.project-media-remove-preview');
        if (removePreviewBtn) {
            removePreviewBtn.addEventListener('click', function () {
                var uploadInput = wrap.querySelector('.project-media-upload');
                var pathName = uploadInput ? uploadInput.getAttribute('data-path-name') : '';
                if (pathName) {
                    var pathInput = form.querySelector('input[name="' + pathName + '"]');
                    if (pathInput) pathInput.value = '';
                }
                var fieldRoot = getMediaFieldRoot(wrap);
                var removeCb = fieldRoot ? fieldRoot.querySelector('input[type="checkbox"][name^="remove_"]') : null;
                if (removeCb) removeCb.checked = true;
                preview.remove();
            });
        }
    }

    function clearRowImagePreview(row) {
        if (!row) return;
        row.querySelectorAll('.mb-2.flex.items-center, .project-media-preview').forEach(function (el) {
            el.remove();
        });
    }

    function showRowImagePreview(row, url, pathInput) {
        if (!row || !url) return;
        clearRowImagePreview(row);
        var uploadInput = row.querySelector('.project-media-upload');
        var fileCol = uploadInput ? uploadInput.closest('.flex-1') : null;
        if (!fileCol) return;
        var preview = document.createElement('div');
        preview.className = 'project-media-preview mb-2 flex items-center gap-3 flex-wrap';
        preview.innerHTML = buildPreviewHtml(url, false, true);
        fileCol.insertBefore(preview, uploadInput);
        var removePreviewBtn = preview.querySelector('.project-media-remove-preview');
        if (removePreviewBtn) {
            removePreviewBtn.addEventListener('click', function () {
                if (pathInput) pathInput.value = '';
                preview.remove();
            });
        }
    }

    function clearRowExistingImage(row) {
        if (!row) return;
        row.querySelectorAll('.project-media-preview, .mb-2.flex.items-center').forEach(function (el) {
            el.remove();
        });
    }

    function uploadSingle(input, file, type, pathInput, container) {
        var wrap = container && container.matches('[data-media-wrap]') ? container : (container && container.closest('[data-media-wrap]'));
        var root = getMediaFieldRoot(wrap || container);

        if (type === 'plan' || type === 'pricing_place') {
            clearRowExistingImage(container);
        } else if (type !== 'gallery' && root) {
            clearExistingBlocks(root);
            root.querySelectorAll('input[type="checkbox"][name^="remove_"]').forEach(function (cb) {
                cb.checked = false;
            });
        }

        setMediaMsg(wrap || container, 'wait', 'Uploading, please wait…');
        return uploadFile(file, type).then(function (data) {
            if (pathInput) pathInput.value = data.path || '';
            if (root && type !== 'plan' && type !== 'pricing_place') {
                var removeCb = root.querySelector('input[type="checkbox"][name^="remove_"]');
                if (removeCb) removeCb.checked = false;
            }

            if (type === 'plan' || type === 'pricing_place') {
                showRowImagePreview(container, data.url, pathInput);
            } else {
                showPreview(wrap || container, data.url, type === 'project_file_pdf', false);
            }
            setMediaMsg(wrap || container, 'ok', data.message || 'Uploaded successfully.');
        }).catch(function (err) {
            setMediaMsg(wrap || container, 'err', err.message || 'Upload failed.');
        });
    }

    var galleryList = document.getElementById('project-gallery-list');
    var galleryRemoveContainer = document.getElementById('gallery-remove-container');

    function nextGalleryOrder() {
        if (!galleryList) return 0;
        var max = -1;
        galleryList.querySelectorAll('input[name="gallery_order[]"]').forEach(function (inp) {
            var n = parseInt(inp.value, 10);
            if (!isNaN(n) && n > max) max = n;
        });
        return max + 1;
    }

    function removeGalleryRow(row) {
        if (!row) return;
        var path = row.getAttribute('data-path') || '';
        if (!path) {
            var pathInput = row.querySelector('input[name="gallery_paths[]"]');
            path = pathInput ? pathInput.value : '';
        }
        if (path && galleryRemoveContainer) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'gallery_remove[]';
            inp.value = path;
            galleryRemoveContainer.appendChild(inp);
        }
        row.remove();
    }

    function appendGalleryRow(path, url, order) {
        if (!galleryList) return;
        var div = document.createElement('div');
        div.className = 'gallery-item-row flex items-center gap-2 flex-wrap';
        div.setAttribute('data-path', path || '');
        div.innerHTML =
            '<input type="hidden" name="gallery_paths[]" value="' + (path || '').replace(/"/g, '&quot;') + '" />' +
            '<img src="' + (url || '').replace(/"/g, '&quot;') + '" alt="" class="h-12 w-12 object-cover rounded" loading="lazy" />' +
            '<input type="number" name="gallery_order[]" value="' + order + '" min="0" class="w-16 rounded border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2 py-1 text-sm" placeholder="Order" />' +
            '<span class="text-xs text-emerald-600 dark:text-emerald-400">Uploaded</span>' +
            '<button type="button" class="remove-gallery-item text-rose-600 dark:text-rose-400 hover:text-rose-500 text-xs py-1 px-2 rounded border border-rose-300 dark:border-rose-700">Remove</button>';
        galleryList.appendChild(div);
    }

    function showGalleryProgress(current, total) {
        var status = document.getElementById('project-gallery-upload-status');
        var text = document.getElementById('project-gallery-upload-status-text');
        if (text) {
            text.textContent = total > 1
                ? ('Uploading image ' + current + ' of ' + total + ', please wait…')
                : 'Uploading image, please wait…';
        }
        if (status) status.classList.remove('hidden');
    }

    function hideGalleryProgress() {
        var status = document.getElementById('project-gallery-upload-status');
        if (status) status.classList.add('hidden');
    }

    function uploadGalleryFiles(files) {
        var list = Array.isArray(files) ? files.slice() : snapshotFiles({ files: files });
        if (!list.length) return;

        var errEl = document.getElementById('project-gallery-upload-error');
        if (errEl) errEl.classList.add('hidden');

        var total = list.length;
        var failed = 0;
        var chain = Promise.resolve();

        list.forEach(function (file, index) {
            chain = chain.then(function () {
                showGalleryProgress(index + 1, total);
                return uploadFile(file, 'gallery').then(function (data) {
                    appendGalleryRow(data.path, data.url, nextGalleryOrder());
                }).catch(function () {
                    failed++;
                });
            });
        });

        chain.finally(function () {
            hideGalleryProgress();
            if (failed > 0 && errEl) {
                errEl.textContent = failed === total
                    ? 'Gallery upload failed. Please try again.'
                    : (failed + ' of ' + total + ' images failed to upload. The rest were added.');
                errEl.classList.remove('hidden');
            }
        });
    }

    if (galleryList) {
        galleryList.addEventListener('click', function (e) {
            if (!e.target.classList.contains('remove-gallery-item')) return;
            removeGalleryRow(e.target.closest('.gallery-item-row'));
        });
    }

    form.addEventListener('change', function (e) {
        var input = e.target;
        if (!input.classList.contains('project-media-upload')) return;

        var type = input.getAttribute('data-upload-type') || '';
        var files = snapshotFiles(input);
        input.value = '';

        if (type === 'gallery') {
            uploadGalleryFiles(files);
            return;
        }

        var file = files[0];
        if (!file) return;

        var pathInput = null;
        var container = input.closest('[data-media-wrap]') || input.parentElement;

        if (input.getAttribute('data-path-name')) {
            pathInput = ensurePathInput(input.getAttribute('data-path-name'));
        } else if (type === 'plan') {
            var planRow = input.closest('.plan-row');
            pathInput = planRow ? planRow.querySelector('input[name="existing_plan_images[]"]') : null;
            container = planRow;
        } else if (type === 'pricing_place') {
            var pricingRow = input.closest('.pricing-place-row');
            pathInput = pricingRow ? pricingRow.querySelector('input[name="existing_pricing_place_images[]"]') : null;
            container = pricingRow;
        }

        uploadSingle(input, file, type, pathInput, container);
    });
})();
