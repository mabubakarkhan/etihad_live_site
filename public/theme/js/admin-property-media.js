(function () {
    var form = document.getElementById('property-form') || document.getElementById('section-edit-form');
    if (!form) return;

    var uploadUrl = form.getAttribute('data-upload-url') || '';
    var propertyId = form.getAttribute('data-property-id') || '';
    var csrf = (form.querySelector('input[name="_token"]') || {}).value || '';

    function getUploadToken() {
        var el = document.getElementById('property-upload-token');
        return el ? el.value : '';
    }

    function showEl(id, show) {
        var el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', !show);
    }

    function setText(id, text) {
        var el = document.getElementById(id);
        if (el) el.textContent = text || '';
    }

    function uploadImage(file, type) {
        if (!uploadUrl || !file) {
            return Promise.reject(new Error('Upload is not configured.'));
        }
        var body = new FormData();
        body.append('file', file);
        body.append('type', type);
        body.append('_token', csrf);
        if (propertyId) body.append('property_id', propertyId);
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

    var featuredFile = document.getElementById('property-featured-file');
    var featuredPathInput = document.getElementById('featured_image_path');
    var removeFeatured = document.getElementById('remove_featured_image');
    var featuredExisting = document.getElementById('property-featured-existing');
    var featuredPreview = document.getElementById('property-featured-preview');

    function ensureFeaturedPreviewRemoveBtn() {
        if (!featuredPreview) return;
        if (featuredPreview.querySelector('.property-featured-remove-btn')) return;
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'property-featured-remove-btn px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 text-xs hover:bg-rose-600/10';
        btn.textContent = 'Remove';
        btn.addEventListener('click', function () {
            instantClearFeatured();
        });
        featuredPreview.appendChild(btn);
    }

    function updateFeaturedPreview(url) {
        if (!featuredPreview) return;
        var img = featuredPreview.querySelector('.property-featured-preview-img');
        if (!img) return;
        if (url) {
            img.src = url;
            img.classList.remove('hidden');
            showEl('property-featured-preview', true);
            ensureFeaturedPreviewRemoveBtn();
        } else {
            img.src = '';
            img.classList.add('hidden');
            showEl('property-featured-preview', false);
        }
    }

    function instantClearFeatured() {
        if (featuredPathInput) featuredPathInput.value = '';
        if (removeFeatured) removeFeatured.checked = true;
        updateFeaturedPreview('');
        if (featuredExisting) featuredExisting.classList.add('hidden');
        showEl('property-featured-upload-success', false);
        showEl('property-featured-upload-error', false);
    }

    if (removeFeatured) {
        removeFeatured.addEventListener('change', function () {
            if (removeFeatured.checked) {
                instantClearFeatured();
            }
        });
    }

    if (featuredFile) {
        featuredFile.addEventListener('change', function () {
            var picked = snapshotFiles(featuredFile);
            featuredFile.value = '';
            var file = picked[0];
            if (!file) return;

            if (featuredExisting) featuredExisting.classList.add('hidden');
            if (removeFeatured) removeFeatured.checked = false;

            showEl('property-featured-upload-error', false);
            showEl('property-featured-upload-success', false);
            showEl('property-featured-upload-status', true);

            uploadImage(file, 'featured').then(function (data) {
                if (featuredPathInput) featuredPathInput.value = data.path || '';
                if (removeFeatured) removeFeatured.checked = false;
                if (featuredExisting) featuredExisting.classList.add('hidden');
                updateFeaturedPreview(data.url);
                setText('property-featured-upload-success', data.message || 'Featured image uploaded successfully.');
                showEl('property-featured-upload-success', true);
            }).catch(function (err) {
                setText('property-featured-upload-error', err.message || 'Upload failed.');
                showEl('property-featured-upload-error', true);
            }).finally(function () {
                showEl('property-featured-upload-status', false);
            });
        });
    }

    var galleryFile = document.getElementById('property-gallery-file');
    var galleryList = document.getElementById('property-gallery-list');
    var galleryRemoveContainer = document.getElementById('property-gallery-remove-container');

    function snapshotFiles(fileInput) {
        if (!fileInput || !fileInput.files) return [];
        return Array.prototype.slice.call(fileInput.files);
    }

    function nextGalleryOrder() {
        if (!galleryList) return 0;
        var orders = galleryList.querySelectorAll('input[name="gallery_order[]"]');
        var max = -1;
        orders.forEach(function (inp) {
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
        div.className = 'property-gallery-item flex gap-3 items-center flex-wrap gallery-item-row';
        div.setAttribute('data-path', path || '');
        div.innerHTML =
            '<input type="hidden" name="gallery_paths[]" value="' + (path || '').replace(/"/g, '&quot;') + '" />' +
            '<input type="number" name="gallery_order[]" value="' + order + '" min="0" class="w-16 rounded border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2 py-1 text-sm" />' +
            '<img src="' + (url || '').replace(/"/g, '&quot;') + '" alt="" class="h-14 w-20 object-cover rounded border border-slate-300 dark:border-slate-700" loading="lazy" />' +
            '<span class="text-xs text-emerald-600 dark:text-emerald-400">Uploaded</span>' +
            '<button type="button" class="property-remove-gallery px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 text-xs hover:bg-rose-600/10" data-path="' + (path || '').replace(/"/g, '&quot;') + '">Remove</button>';
        galleryList.appendChild(div);
    }

    if (galleryList) {
        galleryList.addEventListener('click', function (e) {
            var btn = e.target.closest('.property-remove-gallery');
            if (!btn) return;
            removeGalleryRow(btn.closest('.gallery-item-row'));
        });
    }

    function showGalleryProgress(current, total) {
        setText(
            'property-gallery-upload-status-text',
            total > 1
                ? ('Uploading image ' + current + ' of ' + total + ', please wait…')
                : 'Uploading image, please wait…'
        );
        showEl('property-gallery-upload-status', true);
    }

    function uploadGalleryFiles(files) {
        var list = Array.isArray(files) ? files.slice() : Array.prototype.slice.call(files || []);
        if (!list.length) return;

        showEl('property-gallery-upload-error', false);
        var total = list.length;
        var failed = 0;
        var chain = Promise.resolve();

        list.forEach(function (file, index) {
            chain = chain.then(function () {
                showGalleryProgress(index + 1, total);
                return uploadImage(file, 'gallery').then(function (data) {
                    appendGalleryRow(data.path, data.url, nextGalleryOrder());
                }).catch(function () {
                    failed++;
                });
            });
        });

        chain.finally(function () {
            showEl('property-gallery-upload-status', false);
            if (failed > 0) {
                var msg = failed === total
                    ? 'Gallery upload failed. Please try again.'
                    : (failed + ' of ' + total + ' images failed to upload. The rest were added.');
                setText('property-gallery-upload-error', msg);
                showEl('property-gallery-upload-error', true);
            }
        });
    }

    if (galleryFile) {
        galleryFile.addEventListener('change', function () {
            var list = snapshotFiles(galleryFile);
            galleryFile.value = '';
            uploadGalleryFiles(list);
        });
    }
})();
