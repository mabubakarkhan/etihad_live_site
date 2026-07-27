(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};

    function ToolbarManager(root, mapManager) {
        this.root = root;
        this.mapManager = mapManager;
        this.boundsVisible = true;
        this.currentBounds = null;
        this.handlers_ = [];
        this.bindEvents();
    }

    ToolbarManager.prototype.bindEvents = function () {
        var self = this;
        var buttons = this.root.querySelectorAll('[data-tool]');

        buttons.forEach(function (btn) {
            var handler = function () {
                self.handleTool(btn.getAttribute('data-tool'), btn);
            };
            btn.addEventListener('click', handler);
            self.handlers_.push({ el: btn, handler: handler });
        });
    };

    ToolbarManager.prototype.setBounds = function (boundsLiteral) {
        this.currentBounds = boundsLiteral;
    };

    ToolbarManager.prototype.handleTool = function (tool, btn) {
        var map = this.mapManager.getMap();
        if (!map) {
            return;
        }

        var mapTypeButtons = this.root.querySelectorAll('[data-tool="roadmap"], [data-tool="satellite"], [data-tool="hybrid"], [data-tool="terrain"]');

        switch (tool) {
            case 'fit-bounds':
                if (this.currentBounds) {
                    this.mapManager.fitBounds(this.currentBounds);
                }
                break;
            case 'roadmap':
                this.mapManager.setMapType('roadmap');
                this.setActiveMapType(mapTypeButtons, btn);
                break;
            case 'satellite':
                this.mapManager.setMapType('satellite');
                this.setActiveMapType(mapTypeButtons, btn);
                break;
            case 'hybrid':
                this.mapManager.setMapType('hybrid');
                this.setActiveMapType(mapTypeButtons, btn);
                break;
            case 'terrain':
                this.mapManager.setMapType('terrain');
                this.setActiveMapType(mapTypeButtons, btn);
                break;
            case 'toggle-bounds':
                this.boundsVisible = !this.boundsVisible;
                this.mapManager.toggleBoundsRectangle(this.boundsVisible);
                btn.classList.toggle('is-active', this.boundsVisible);
                break;
        }
    };

    ToolbarManager.prototype.setActiveMapType = function (buttons, activeBtn) {
        buttons.forEach(function (b) {
            b.classList.toggle('is-active', b === activeBtn);
        });
    };

    ToolbarManager.prototype.destroy = function () {
        this.handlers_.forEach(function (item) {
            item.el.removeEventListener('click', item.handler);
        });
        this.handlers_ = [];
    };

    PM.ToolbarManager = ToolbarManager;
})(window);
