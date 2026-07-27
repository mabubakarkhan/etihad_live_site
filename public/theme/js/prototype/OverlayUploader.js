(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};

    function OverlayUploader(root, options) {
        this.root = root;
        this.uploadUrl = options.uploadUrl;
        this.deleteUrl = options.deleteUrl;
        this.csrf = options.csrf;
        this.onSuccess = options.onSuccess || function () {};
        this.onError = options.onError || function () {};
        this.fileInput = root.querySelector('[data-overlay-input]');
        this.previewImg = root.querySelector('[data-overlay-preview-img]');
        this.previewEmpty = root.querySelector('[data-overlay-empty]');
        this.deleteBtn = root.querySelector('[data-overlay-delete]');
        this.uploadZone = root.querySelector('[data-upload-zone]');
        this.uploadTrigger = root.querySelector('[data-upload-trigger]');
        this.bindEvents();
    }

    OverlayUploader.prototype.bindEvents = function () {
        var self = this;

        if (this.uploadTrigger && this.fileInput) {
            this.uploadTrigger.addEventListener('click', function () {
                self.fileInput.click();
            });
        }

        if (this.fileInput) {
            this.fileInput.addEventListener('change', function (e) {
                var file = e.target.files && e.target.files[0];
                if (file) {
                    self.upload(file);
                }
                e.target.value = '';
            });
        }

        if (this.uploadZone) {
            this.uploadZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                self.uploadZone.classList.add('is-dragover');
            });
            this.uploadZone.addEventListener('dragleave', function () {
                self.uploadZone.classList.remove('is-dragover');
            });
            this.uploadZone.addEventListener('drop', function (e) {
                e.preventDefault();
                self.uploadZone.classList.remove('is-dragover');
                var file = e.dataTransfer.files && e.dataTransfer.files[0];
                if (file) {
                    self.upload(file);
                }
            });
        }

        if (this.deleteBtn) {
            this.deleteBtn.addEventListener('click', function () {
                self.deleteImage();
            });
        }
    };

    OverlayUploader.prototype.upload = function (file) {
        var self = this;

        if (file.type !== 'image/png') {
            this.onError('Only PNG images are supported.');
            return;
        }

        var formData = new FormData();
        formData.append('overlay_image', file);

        fetch(this.uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
            },
            body: formData,
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok) {
                        throw new Error(self.extractError(data));
                    }
                    return data;
                });
            })
            .then(function (data) {
                self.updatePreview(data.overlay);
                self.onSuccess(data);
            })
            .catch(function (err) {
                self.onError(err.message || 'Upload failed.');
            });
    };

    OverlayUploader.prototype.deleteImage = function () {
        var self = this;

        if (!confirm('Remove this overlay image?')) {
            return;
        }

        fetch(this.deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
            },
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok) {
                        throw new Error(self.extractError(data));
                    }
                    return data;
                });
            })
            .then(function (data) {
                self.updatePreview(data.overlay);
                self.onSuccess(data);
            })
            .catch(function (err) {
                self.onError(err.message || 'Delete failed.');
            });
    };

    OverlayUploader.prototype.updatePreview = function (overlay) {
        if (!overlay) {
            return;
        }

        var hasImage = !!overlay.overlay_url;

        if (this.previewImg) {
            if (hasImage) {
                this.previewImg.src = overlay.overlay_url + (overlay.overlay_url.indexOf('?') > -1 ? '&' : '?') + 't=' + Date.now();
                this.previewImg.classList.remove('hidden');
            } else {
                this.previewImg.src = '';
                this.previewImg.classList.add('hidden');
            }
        }

        if (this.previewEmpty) {
            this.previewEmpty.classList.toggle('hidden', hasImage);
        }

        if (this.deleteBtn) {
            this.deleteBtn.classList.toggle('hidden', !hasImage);
        }
    };

    OverlayUploader.prototype.extractError = function (data) {
        if (data && data.message) {
            return data.message;
        }
        if (data && data.errors) {
            var first = Object.keys(data.errors)[0];
            if (first && data.errors[first][0]) {
                return data.errors[first][0];
            }
        }
        return 'Request failed.';
    };

    PM.OverlayUploader = OverlayUploader;
})(window);
