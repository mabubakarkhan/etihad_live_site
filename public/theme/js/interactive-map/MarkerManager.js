(function (window) {
    'use strict';

    /**
     * Reserved for property markers on front-end maps.
     * Admin overlay editor intentionally does not manage markers.
     */
    function MarkerManager() {
        this.markers = [];
    }

    MarkerManager.prototype.setMap = function () {};

    MarkerManager.prototype.clear = function () {
        this.markers = [];
    };

    MarkerManager.prototype.load = function () {
        return Promise.resolve([]);
    };

    window.InteractiveMap = window.InteractiveMap || {};
    window.InteractiveMap.MarkerManager = MarkerManager;
})(window);
