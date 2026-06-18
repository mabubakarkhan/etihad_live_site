(function () {
    var form = document.querySelector('[data-homepage-form]');
    if (!form) return;

    var uploadUrl = form.getAttribute('data-upload-url') || '';
    var csrf = (form.querySelector('input[name="_token"]') || {}).value || '';

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

    function setStatus(wrap, text, isError) {
        var status = wrap.querySelector('[data-homepage-media-status]');
        if (!status) return;
        status.textContent = text || '';
        status.classList.toggle('text-rose-600', !!isError);
        status.classList.toggle('dark:text-rose-400', !!isError);
        status.classList.toggle('text-emerald-600', !isError && !!text);
        status.classList.toggle('dark:text-emerald-400', !isError && !!text);
    }

    function uploadFile(file, type) {
        if (!uploadUrl || !file) {
            return Promise.reject(new Error('Upload is not configured.'));
        }

        var body = new FormData();
        body.append('file', file);
        body.append('type', type);
        body.append('_token', csrf);

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

    function updatePreview(wrap, url, kind) {
        var preview = wrap.querySelector('[data-homepage-media-preview]');
        if (!preview) return;

        preview.classList.remove('hidden');
        preview.innerHTML = '';

        if (kind === 'video') {
            var video = document.createElement('video');
            video.src = url;
            video.controls = true;
            video.muted = true;
            video.className = 'max-h-40 rounded-lg border border-slate-200 dark:border-slate-600';
            preview.appendChild(video);
        } else {
            var img = document.createElement('img');
            img.src = url;
            img.alt = 'Preview';
            img.className = wrap.getAttribute('data-preview-class') || 'max-h-48 rounded-lg border border-slate-200 dark:border-slate-600 object-cover';
            preview.appendChild(img);
        }

        var removeName = wrap.getAttribute('data-remove-name');
        if (removeName) {
            var label = document.createElement('label');
            label.className = 'mt-3 inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer';
            label.innerHTML = '<input type="checkbox" name="' + removeName + '" value="1" class="rounded border-slate-400" /> Remove current file';
            preview.appendChild(label);
        }
    }

    form.addEventListener('change', function (event) {
        var input = event.target;
        if (!input || !input.classList || !input.classList.contains('homepage-media-upload')) {
            return;
        }

        var file = input.files && input.files[0];
        if (!file) return;

        var pathName = input.getAttribute('data-path-name');
        var uploadType = input.getAttribute('data-upload-type');
        var wrap = input.closest('[data-homepage-media-wrap]');
        var kind = input.getAttribute('data-media-kind') || 'image';

        if (!pathName || !uploadType || !wrap) {
            return;
        }

        setStatus(wrap, 'Uploading…', false);

        uploadFile(file, uploadType).then(function (data) {
            var pathInput = ensurePathInput(pathName);
            pathInput.value = data.path || '';

            var removeName = wrap.getAttribute('data-remove-name');
            if (removeName) {
                var removeCb = form.querySelector('input[name="' + removeName + '"]');
                if (removeCb) removeCb.checked = false;
            }

            updatePreview(wrap, data.url, kind);
            setStatus(wrap, data.message || 'Uploaded successfully.', false);
            input.value = '';
        }).catch(function (err) {
            setStatus(wrap, err.message || 'Upload failed.', true);
            input.value = '';
        });
    });
})();
