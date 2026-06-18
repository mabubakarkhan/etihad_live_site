{{-- Block search engines from indexing any admin URL (login, dashboard, CRUD, previews). --}}
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

{{-- Theme: runs first so no flash. Cookie: admin_theme = "light" | "dark". Default: dark. --}}
<script>
    (function() {
        var m = document.cookie.match(/admin_theme=([^;]+)/);
        var theme = (m && m[1]) ? m[1] : 'dark';
        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>
