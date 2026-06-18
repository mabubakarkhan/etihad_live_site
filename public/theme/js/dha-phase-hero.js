(function () {
    function initIcons() {
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }
    initIcons();
    document.addEventListener('DOMContentLoaded', initIcons);
})();
