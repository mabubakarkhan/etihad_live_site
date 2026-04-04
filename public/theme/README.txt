Front theme assets (from html folder).
Used by Laravel front views via asset('theme/...').

Structure:
  css/     - plugins.css, style.css, db-style.css
  js/      - jquery.min.js, plugins.js, scripts.js, map-single.js, map-add.js, db-scripts.js, charts.js
  images/  - favicon, logo, map, avatars, bg, all, clients, etc.
  fonts/   - Font Awesome + custom (lgd641)
  video/   - sample video

In Blade: extend layouts.front or use asset('theme/css/style.css'), etc.
Set window.themeBase before map scripts (layouts/front.blade.php does this).
