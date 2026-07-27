{{-- Prototype pages: internal development only — never index. --}}
<meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
<meta name="googlebot" content="noindex,nofollow,noarchive,nosnippet,noimageindex">

<script>
    (function() {
        var m = document.cookie.match(/admin_theme=([^;]+)/);
        var theme = (m && m[1]) ? m[1] : 'dark';
        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>
