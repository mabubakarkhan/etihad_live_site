(function (window) {
    'use strict';

    function SearchManager(options) {
        this.map = options.map;
        this.input = options.input;
        this.placesProxy = options.placesProxy || null;
        this.onPlaceSelected = options.onPlaceSelected || null;
        this.binding = null;
    }

    SearchManager.prototype.init = function () {
        if (!this.map || !this.input || !window.EtihadPlacesAutocomplete) {
            return false;
        }

        var self = this;
        var handlers = {
            onPlaceChanged: function (point) {
                if (typeof self.onPlaceSelected === 'function') {
                    self.onPlaceSelected({
                        name: point.name || '',
                        address: point.address || '',
                        location: {
                            lat: point.lat,
                            lng: point.lng,
                        },
                    });
                }
            },
        };

        if (this.placesProxy && typeof window.EtihadPlacesAutocomplete.bindMapSearchWithPlacesProxy === 'function') {
            this.binding = window.EtihadPlacesAutocomplete.bindMapSearchWithPlacesProxy(
                this.input,
                this.map,
                this.placesProxy,
                handlers
            );
        } else {
            this.binding = window.EtihadPlacesAutocomplete.bindMapSearchAutocomplete(this.input, this.map, handlers);
        }

        return !!this.binding;
    };

    SearchManager.prototype.clear = function () {
        if (this.binding && typeof this.binding.clearMarker === 'function') {
            this.binding.clearMarker();
        }
    };

    SearchManager.prototype.destroy = function () {
        if (this.binding && typeof this.binding.destroy === 'function') {
            this.binding.destroy();
        }
        this.binding = null;
    };

    window.InteractiveMap = window.InteractiveMap || {};
    window.InteractiveMap.SearchManager = SearchManager;
})(window);
