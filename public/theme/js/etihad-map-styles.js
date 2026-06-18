(function (window) {
    var BRAND = '#a9823d';
    var BRAND_DARK = '#8a6a32';
    var BRAND_LIGHT = '#d4bc8a';

    function getStyles() {
        return [
            { featureType: 'all', elementType: 'geometry', stylers: [{ color: '#f7f4ef' }] },
            { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#dfe8ef' }] },
            { featureType: 'landscape', elementType: 'geometry', stylers: [{ color: '#f5f0e8' }] },
            { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
            { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: BRAND_LIGHT }] },
            { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#faf7f2' }] },
            { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: BRAND }] },
            { featureType: 'road.arterial', elementType: 'labels.text.fill', stylers: [{ color: BRAND_DARK }] },
            { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
            { featureType: 'transit', stylers: [{ visibility: 'off' }] },
            { featureType: 'administrative.locality', elementType: 'labels.text.fill', stylers: [{ color: BRAND_DARK }] }
        ];
    }

    function getMinimalStyles() {
        return [
            { featureType: 'poi', stylers: [{ visibility: 'off' }] },
            { featureType: 'transit', stylers: [{ visibility: 'off' }] },
            { featureType: 'landscape', stylers: [{ visibility: 'off' }] },
            { featureType: 'water', stylers: [{ visibility: 'off' }] },
            { featureType: 'administrative', stylers: [{ visibility: 'off' }] },
            { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: BRAND }] },
            { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: BRAND }] }
        ];
    }

    function getMarkerIcon(googleMaps) {
        var g = googleMaps || (window.google && window.google.maps);
        if (!g) return null;
        return {
            path: 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
            fillColor: BRAND,
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 1.5,
            scale: 1.6,
            anchor: new g.Point(12, 22)
        };
    }

    function applyToMapOptions(opts, variant) {
        opts = opts || {};
        var mapId = opts.mapId;
        if (!mapId || mapId === 'DEMO_MAP_ID') {
            delete opts.mapId;
            if (!opts.styles) {
                opts.styles = variant === 'minimal' ? getMinimalStyles() : getStyles();
            }
        }
        return opts;
    }

    function createMarker(options) {
        options = options || {};
        var g = window.google && window.google.maps;
        if (!g) return null;
        var markerOpts = {
            position: options.position,
            map: options.map,
            title: options.title || ''
        };
        var icon = getMarkerIcon(g);
        if (icon) markerOpts.icon = icon;
        return new g.Marker(markerOpts);
    }

    window.EtihadMap = {
        brandColor: BRAND,
        getStyles: getStyles,
        getMinimalStyles: getMinimalStyles,
        getMarkerIcon: getMarkerIcon,
        applyToMapOptions: applyToMapOptions,
        createMarker: createMarker
    };
})(window);
