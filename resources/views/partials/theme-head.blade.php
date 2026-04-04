{{--
    Include in any front view that uses the Rentstate theme.
    Use @include('partials.theme-head') in <head> or extend layouts.front.

    Asset paths (use in Blade):
    - CSS:  asset('theme/css/plugins.css'), asset('theme/css/style.css'), asset('theme/css/db-style.css')
    - JS:   asset('theme/js/jquery.min.js'), asset('theme/js/plugins.js'), asset('theme/js/scripts.js')
    - Maps: asset('theme/js/map-single.js'), asset('theme/js/map-add.js')
    - Dashboard: asset('theme/js/db-scripts.js'), asset('theme/js/charts.js')
    - Images: asset('theme/images/...')  e.g. asset('theme/images/logo.png')
    - Fonts:  asset('theme/fonts/...')
    - Video:  asset('theme/video/...')

    Set window.themeBase = "{{ asset('theme') }}" before map scripts so marker icon resolves to /theme/images/marker.png
--}}
