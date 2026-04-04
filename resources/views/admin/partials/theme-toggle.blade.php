{{-- Theme toggle button: place in header with other right-aligned buttons --}}
<button type="button"
        id="admin-theme-toggle"
        aria-label="Toggle dark/light theme"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition shadow-sm">
    <span id="admin-theme-icon" class="inline-block w-4 h-4" aria-hidden="true">
        {{-- Sun icon (show in dark mode = click to switch to light) --}}
        <svg id="admin-theme-icon-dark" class="hidden dark:block w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
        </svg>
        {{-- Moon icon (show in light mode = click to switch to dark) --}}
        <svg id="admin-theme-icon-light" class="block dark:hidden w-4 h-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
        </svg>
    </span>
    <span id="admin-theme-label" class="hidden sm:inline">Dark</span>
</button>
<script>
(function() {
    var COOKIE = 'admin_theme';
    var COOKIE_DAYS = 365;

    function getTheme() {
        var m = document.cookie.match(new RegExp(COOKIE + '=([^;]+)'));
        return (m && m[1]) ? m[1] : 'dark';
    }
    function setTheme(theme) {
        theme = theme === 'light' ? 'light' : 'dark';
        var d = new Date();
        d.setTime(d.getTime() + COOKIE_DAYS * 24 * 60 * 60 * 1000);
        document.cookie = COOKIE + '=' + theme + ';path=/;expires=' + d.toUTCString() + ';SameSite=Lax';
        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.setAttribute('data-theme', theme);
        var label = document.getElementById('admin-theme-label');
        if (label) label.textContent = theme === 'dark' ? 'Dark' : 'Light';
    }
    function toggleTheme() {
        setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
    }
    var btn = document.getElementById('admin-theme-toggle');
    if (btn) btn.addEventListener('click', toggleTheme);
    setTheme(getTheme());
})();
</script>
