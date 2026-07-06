(function (window) {
    'use strict';

    /**
     * Reserved for marker clustering on front-end maps.
     */
    function ClusterManager() {
        this.clusterer = null;
    }

    ClusterManager.prototype.setMap = function () {};

    ClusterManager.prototype.clear = function () {
        this.clusterer = null;
    };

    window.InteractiveMap = window.InteractiveMap || {};
    window.InteractiveMap.ClusterManager = ClusterManager;
})(window);
