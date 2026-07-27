(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};
    var loaders = {};
    var activeEditor = null;

    function loadGoogleMaps(apiKey, callbackName) {
        if (window.google && window.google.maps) {
            return Promise.resolve(window.google.maps);
        }

        if (!apiKey) {
            return Promise.reject(new Error('Google Maps API key is not configured. Add GOOGLE_MAPS_API_KEY to your .env file.'));
        }

        var cacheKey = apiKey + ':drawing:' + callbackName;
        if (loaders[cacheKey]) {
            return loaders[cacheKey];
        }

        loaders[cacheKey] = new Promise(function (resolve, reject) {
            window[callbackName] = function () {
                if (window.google && window.google.maps) {
                    resolve(window.google.maps);
                    return;
                }
                reject(new Error('Google Maps failed to load.'));
            };

            var script = document.createElement('script');
            script.async = true;
            script.defer = true;
            script.onerror = function () {
                reject(new Error('Failed to load Google Maps JavaScript API. Check your API key and billing.'));
            };
            script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&libraries=drawing&callback=' + encodeURIComponent(callbackName);
            document.head.appendChild(script);
        });

        return loaders[cacheKey];
    }

    function boundsFromData(data) {
        if (!data) {
            return null;
        }
        var north = parseFloat(data.north);
        var south = parseFloat(data.south);
        var east = parseFloat(data.east);
        var west = parseFloat(data.west);
        if ([north, south, east, west].some(function (v) { return isNaN(v); })) {
            return null;
        }
        return { north: north, south: south, east: east, west: west };
    }

    function centerFromBounds(bounds) {
        if (!bounds) {
            return { lat: 31.5204, lng: 74.3587 };
        }
        return {
            lat: (bounds.north + bounds.south) / 2,
            lng: (bounds.east + bounds.west) / 2,
        };
    }

    function showAlert(message, type) {
        var el = document.getElementById('prototype-alert');
        if (!el) {
            window.alert(message);
            return;
        }
        el.textContent = message;
        el.className = 'prototype-alert prototype-alert--' + (type || 'info') + ' mb-4';
        el.hidden = false;
        clearTimeout(el._hideTimer);
        el._hideTimer = setTimeout(function () {
            el.hidden = true;
        }, 5000);
    }

    function extractError(data) {
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
    }

    function getPageConfig() {
        var page = document.getElementById('prototype-admin-page');
        if (!page) {
            return null;
        }

        return {
            csrf: page.getAttribute('data-csrf') || '',
            storeUrl: page.getAttribute('data-store-url') || '',
            indexUrl: page.getAttribute('data-index-url') || '',
        };
    }

    function destroyActiveEditor() {
        if (!activeEditor) {
            return;
        }
        try {
            if (activeEditor.drawingManager && activeEditor.drawingManager.destroy) {
                activeEditor.drawingManager.destroy();
            }
            if (activeEditor.sectionManager && activeEditor.sectionManager.destroy) {
                activeEditor.sectionManager.destroy();
            }
            if (activeEditor.overlayManager && activeEditor.overlayManager.destroy) {
                activeEditor.overlayManager.destroy();
            }
            if (activeEditor.mapManager && activeEditor.mapManager.destroy) {
                activeEditor.mapManager.destroy();
            }
        } catch (e) {
            // ignore teardown errors
        }
        activeEditor = null;
    }

    /**
     * Soft-navigate without a full page refresh: fetch HTML and swap <main>.
     */
    function softNavigate(url, options) {
        options = options || {};
        showAlert(options.loadingMessage || 'Loading…', 'info');

        return fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            credentials: 'same-origin',
        })
            .then(function (res) {
                if (!res.ok) {
                    throw new Error('Failed to load page.');
                }
                return res.text();
            })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newMain = doc.querySelector('main');
                var curMain = document.querySelector('main');
                if (!newMain || !curMain) {
                    throw new Error('Could not update admin content.');
                }

                destroyActiveEditor();
                curMain.innerHTML = newMain.innerHTML;
                history.pushState({ prototypeSoftNav: true }, '', url);
                bootstrapPrototypeAdmin();

                if (options.successMessage) {
                    showAlert(options.successMessage, 'success');
                }
            })
            .catch(function (err) {
                showAlert(err.message || 'Navigation failed.', 'error');
            });
    }

    function createOverlay(csrf, storeUrl, indexUrl) {
        var title = prompt('Overlay title:', 'New Overlay');
        if (!title || !title.trim()) {
            return;
        }

        if (!storeUrl) {
            showAlert('Create route is not configured.', 'error');
            return;
        }

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ title: title.trim() }),
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok) {
                        throw new Error(extractError(data));
                    }
                    return data;
                });
            })
            .then(function (data) {
                softNavigate(indexUrl + '?overlay=' + data.overlay.id, {
                    loadingMessage: 'Opening overlay…',
                    successMessage: data.message || 'Overlay created.',
                });
            })
            .catch(function (err) {
                showAlert(err.message || 'Create failed.', 'error');
            });
    }

    function bindCreateButtons(csrf, storeUrl, indexUrl) {
        var handler = function () {
            createOverlay(csrf, storeUrl, indexUrl);
        };

        var createBtn = document.getElementById('prototype-create-overlay');
        var createEmptyBtn = document.getElementById('prototype-create-overlay-empty');

        if (createBtn) {
            createBtn.addEventListener('click', handler);
        }
        if (createEmptyBtn) {
            createEmptyBtn.addEventListener('click', handler);
        }
    }

    function PrototypeMapEditor(root) {
        this.root = root;
        this.routes = JSON.parse(root.getAttribute('data-routes') || '{}');
        this.csrf = root.getAttribute('data-csrf');
        this.apiKey = root.getAttribute('data-google-maps-key') || '';
        this.mapId = root.getAttribute('data-google-maps-map-id') || '';
        this.data = JSON.parse(root.getAttribute('data-overlay') || '{}');
        this.mapManager = null;
        this.overlayManager = null;
        this.toolbar = null;
        this.settings = null;
        this.uploader = null;
        this.drawingManager = null;
        this.sectionManager = null;
        this.sectionPanel = null;
        this.callbackName = 'initPrototypeMapEditor_' + (root.getAttribute('data-overlay-id') || '0') + '_' + Date.now();
    }

    PrototypeMapEditor.prototype.init = function () {
        var self = this;
        var canvas = document.getElementById('prototype-map-canvas');
        if (!canvas) {
            return;
        }

        loadGoogleMaps(this.apiKey, this.callbackName)
            .then(function () {
                self.initMap(canvas);
                self.initModules();
                self.bindOverlayList();
                self.bindDeleteOverlay();
            })
            .catch(function (err) {
                showAlert(err.message, 'error');
            });
    };

    PrototypeMapEditor.prototype.initMap = function (canvas) {
        var bounds = boundsFromData(this.data);
        this.mapManager = new PM.MapManager({
            container: canvas,
            mapId: this.mapId,
            minZoom: parseInt(this.data.min_zoom, 10) || 0,
            maxZoom: parseInt(this.data.max_zoom, 10) || 22,
            defaultZoom: parseInt(this.data.default_zoom, 10) || 15,
            center: centerFromBounds(bounds),
        });
        this.mapManager.init();

        if (bounds) {
            this.mapManager.fitBounds(bounds);
            this.mapManager.drawBoundsRectangle(bounds);
        }

        this.triggerMapResize();

        this.overlayManager = new PM.OverlayManager(this.mapManager.getMap());

        if (this.data.overlay_url && bounds) {
            this.overlayManager.show(this.data);
        }

        var self = this;
        this.mapManager.on('zoom_changed', function () {
            if (self.overlayManager && self.overlayManager.overlay_) {
                self.overlayManager.overlay_.draw();
            }
        });
    };

    PrototypeMapEditor.prototype.triggerMapResize = function () {
        var map = this.mapManager && this.mapManager.getMap();
        var g = window.google && window.google.maps;
        if (!map || !g) {
            return;
        }

        var self = this;
        setTimeout(function () {
            g.event.trigger(map, 'resize');
            var bounds = boundsFromData(self.data);
            if (bounds) {
                self.mapManager.fitBounds(bounds);
            } else {
                map.setCenter(centerFromBounds(null));
                map.setZoom(parseInt(self.data.default_zoom, 10) || 15);
            }
        }, 150);
    };

    PrototypeMapEditor.prototype.initModules = function () {
        var self = this;
        var toolbarRoot = this.root.querySelector('[data-prototype-toolbar]');
        var uploadCard = this.root.querySelector('[data-upload-zone]')?.parentElement;
        var settingsRoot = this.root.querySelector('[data-overlay-settings]');

        if (toolbarRoot) {
            this.toolbar = new PM.ToolbarManager(toolbarRoot, this.mapManager);
            this.toolbar.setBounds(boundsFromData(this.data));
        }

        if (uploadCard) {
            this.uploader = new PM.OverlayUploader(uploadCard, {
                uploadUrl: this.routes.upload,
                deleteUrl: this.routes.deleteImage,
                csrf: this.csrf,
                onSuccess: function (data) {
                    self.applyOverlayData(data.overlay);
                    showAlert(data.message, 'success');
                },
                onError: function (msg) {
                    showAlert(msg, 'error');
                },
            });
        }

        if (settingsRoot) {
            this.settings = new PM.OverlaySettings(settingsRoot, {
                updateUrl: this.routes.update,
                csrf: this.csrf,
                data: this.data,
                onLiveUpdate: function (config) {
                    self.applyLiveConfig(config);
                },
                onSuccess: function (data) {
                    self.applyOverlayData(data.overlay);
                    self.syncOverlayListItem(data.overlay);
                    showAlert(data.message, 'success');
                },
                onError: function (msg) {
                    showAlert(msg, 'error');
                },
            });
        }

        this.initGisModules();
    };

    PrototypeMapEditor.prototype.initGisModules = function () {
        var self = this;
        var sectionPanelRoot = this.root.querySelector('[data-section-panel]');
        if (!sectionPanelRoot || !PM.SectionManager || !PM.DrawingManager || !PM.SectionPanel) {
            return;
        }

        try {
            this.drawingManager = new PM.DrawingManager(this.mapManager.getMap(), {
                hintEl: document.getElementById('prototype-draw-hint'),
                onComplete: function (payload) {
                    self.sectionPanel.handleDrawComplete(payload);
                    self.drawingManager.cancel();
                    sectionPanelRoot.querySelectorAll('[data-draw-mode]').forEach(function (b) {
                        b.classList.remove('is-active');
                    });
                },
            });

            if (!this.drawingManager.init()) {
                showAlert('Map drawing tools could not initialize.', 'error');
                return;
            }

            this.sectionManager = new PM.SectionManager(this.mapManager.getMap(), {
                onSelect: function (section) {
                    if (self.sectionPanel && section) {
                        self.sectionPanel.selectSection(section.id);
                    }
                },
            });
            this.sectionManager.load(this.data.sections || []);

            this.sectionPanel = new PM.SectionPanel(sectionPanelRoot, {
                csrf: this.csrf,
                sections: this.data.sections || [],
                routes: this.routes.sections || {},
                onAlert: showAlert,
                onDrawMode: function (mode) {
                    if (!self.drawingManager) {
                        showAlert('Drawing tools are not ready yet.', 'error');
                        return;
                    }
                    self.drawingManager.setStyle(self.sectionPanel.getDrawStyle());
                    self.drawingManager.setMode(mode);
                },
                onDrawCancel: function () {
                    if (self.drawingManager) {
                        self.drawingManager.cancel();
                    }
                },
                onDrawStyleChange: function (style) {
                    if (self.drawingManager) {
                        self.drawingManager.setStyle(style);
                    }
                },
                onSectionSelect: function (section) {
                    self.sectionManager.select(section.id);
                },
                onSectionsChange: function (sections, section, action) {
                    if (action === 'delete') {
                        self.sectionManager.remove(section.id);
                    } else {
                        self.sectionManager.upsert(section);
                    }
                },
            });
        } catch (err) {
            showAlert('GIS drawing tools could not load: ' + err.message, 'error');
        }
    };

    PrototypeMapEditor.prototype.applyLiveConfig = function (config) {
        if (config.bounds && this.toolbar) {
            this.toolbar.setBounds(config.bounds);
            this.mapManager.drawBoundsRectangle(config.bounds);
        }

        if (config.min_zoom !== undefined && config.max_zoom !== undefined) {
            this.mapManager.setZoomLimits(parseInt(config.min_zoom, 10), parseInt(config.max_zoom, 10));
        }

        var overlayConfig = Object.assign({}, this.data, config);
        if (overlayConfig.bounds && overlayConfig.overlay_url) {
            this.overlayManager.show(overlayConfig);
        } else if (overlayConfig.bounds) {
            this.overlayManager.updateLive(overlayConfig);
        }
    };

    PrototypeMapEditor.prototype.applyOverlayData = function (data) {
        this.data = data;
        if (this.settings) {
            this.settings.updateData(data);
        }

        var bounds = boundsFromData(data);
        if (this.toolbar) {
            this.toolbar.setBounds(bounds);
        }
        if (bounds) {
            this.mapManager.drawBoundsRectangle(bounds);
        }

        this.mapManager.setZoomLimits(parseInt(data.min_zoom, 10), parseInt(data.max_zoom, 10));

        if (data.overlay_url && bounds) {
            this.overlayManager.show(data);
        } else {
            this.overlayManager.hide();
        }
    };

    PrototypeMapEditor.prototype.syncOverlayListItem = function (overlay) {
        var btn = document.querySelector('.prototype-overlay-item[data-overlay-id="' + overlay.id + '"]');
        if (!btn) {
            return;
        }
        var titleEl = btn.querySelector('.font-medium');
        var statusEl = btn.querySelector('.text-\\[11px\\]') || btn.querySelector('span:last-child');
        if (titleEl) {
            titleEl.textContent = overlay.title;
        }
        if (statusEl && statusEl !== titleEl) {
            statusEl.textContent = (overlay.status || '').charAt(0).toUpperCase() + (overlay.status || '').slice(1);
        }
    };

    PrototypeMapEditor.prototype.bindOverlayList = function () {
        var self = this;
        var indexUrl = this.routes.index || (getPageConfig() && getPageConfig().indexUrl) || '';

        document.querySelectorAll('.prototype-overlay-item').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-overlay-id');
                if (String(self.root.getAttribute('data-overlay-id')) === String(id)) {
                    return;
                }
                softNavigate(indexUrl + '?overlay=' + id, {
                    loadingMessage: 'Switching overlay…',
                });
            });
        });
    };

    PrototypeMapEditor.prototype.bindDeleteOverlay = function () {
        var self = this;
        var btn = this.root.querySelector('[data-delete-overlay]');
        if (!btn) {
            return;
        }

        btn.addEventListener('click', function () {
            if (!confirm('Delete this overlay permanently?')) {
                return;
            }

            fetch(self.routes.destroy, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': self.csrf,
                    'Accept': 'application/json',
                },
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        if (!res.ok) {
                            throw new Error(data.message || 'Delete failed.');
                        }
                        return data;
                    });
                })
                .then(function (data) {
                    var indexUrl = self.routes.index || (getPageConfig() && getPageConfig().indexUrl) || '';
                    var target = data.next_overlay_id
                        ? (indexUrl + '?overlay=' + data.next_overlay_id)
                        : indexUrl;

                    softNavigate(target, {
                        loadingMessage: 'Updating…',
                        successMessage: data.message || 'Overlay deleted.',
                    });
                })
                .catch(function (err) {
                    showAlert(err.message, 'error');
                });
        });
    };

    function bootstrapPrototypeAdmin() {
        var pageConfig = getPageConfig();
        if (pageConfig) {
            bindCreateButtons(pageConfig.csrf, pageConfig.storeUrl, pageConfig.indexUrl);
        }

        var root = document.getElementById('prototype-map-editor');
        if (root) {
            destroyActiveEditor();
            activeEditor = new PrototypeMapEditor(root);
            activeEditor.init();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        bootstrapPrototypeAdmin();
    });

    window.addEventListener('popstate', function () {
        if (document.getElementById('prototype-admin-page')) {
            softNavigate(window.location.href, { loadingMessage: 'Loading…' });
        }
    });
})(window);
