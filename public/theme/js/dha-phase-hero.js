(function () {
    function initIcons() {
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }

    function isMobileDevice() {
        try {
            if (window.matchMedia('(max-width: 768px)').matches) {
                return true;
            }
            if (window.matchMedia('(hover: none) and (pointer: coarse)').matches) {
                return true;
            }
        } catch (e) {}
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
            navigator.userAgent || ''
        );
    }

    function initAgentModal() {
        var modal = document.getElementById('dha-agent-modal');
        var triggers = document.querySelectorAll('[data-dha-agent-trigger]');
        if (!modal || !triggers.length) {
            return;
        }

        var lastFocus = null;

        function openModal() {
            lastFocus = document.activeElement;
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('dha-agent-modal-open');
            var closeBtn = modal.querySelector('[data-dha-agent-close]');
            if (closeBtn) {
                closeBtn.focus();
            }
            initIcons();
        }

        function closeModal() {
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
            document.documentElement.classList.remove('dha-agent-modal-open');
            if (lastFocus && typeof lastFocus.focus === 'function') {
                lastFocus.focus();
            }
        }

        triggers.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var tel = btn.getAttribute('data-tel') || '';
                if (isMobileDevice() && tel) {
                    window.location.href = tel;
                    return;
                }
                openModal();
            });
        });

        modal.querySelectorAll('[data-dha-agent-close]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                closeModal();
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });
    }

    initIcons();
    document.addEventListener('DOMContentLoaded', function () {
        initIcons();
        initAgentModal();
    });
})();
