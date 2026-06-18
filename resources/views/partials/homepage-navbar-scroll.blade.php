    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var sectionMap = {
          'about-us': '.about',
          'services': '.why',
          'projects': '.dha-showcase--projects'
        };

        document.querySelectorAll('.navbar-navigation__link[data-active]').forEach(function (link) {
          link.addEventListener('click', function (event) {
            event.preventDefault();

            var key = link.getAttribute('data-active');
            var target = document.querySelector(sectionMap[key] || '');
            if (!target) {
              return;
            }

            var menu = document.querySelector('[data-menu]');
            if (menu && menu.classList.contains('--is-active')) {
              menu.classList.remove('--is-active');
              if (window.lenis && typeof window.lenis.start === 'function') {
                window.lenis.start();
              }
            }

            if (window.lenis && typeof window.lenis.scrollTo === 'function') {
              window.lenis.scrollTo(target, { duration: 1.5, offset: 0 });
            } else {
              target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          });
        });
      });
    </script>
