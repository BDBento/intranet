(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.intranetMsNavigation = {
    attach(context) {
      once('intranet-ms-navigation', '.menu-toggle', context).forEach((toggle) => {
        const navigationId = toggle.getAttribute('aria-controls');
        const navigation = document.getElementById(navigationId);

        if (!navigation) {
          return;
        }

        toggle.addEventListener('click', () => {
          const expanded = toggle.getAttribute('aria-expanded') === 'true';
          toggle.setAttribute('aria-expanded', String(!expanded));
          navigation.classList.toggle('is-open', !expanded);
        });

        window.matchMedia('(min-width: 48rem)').addEventListener('change', (event) => {
          if (event.matches) {
            toggle.setAttribute('aria-expanded', 'false');
            navigation.classList.remove('is-open');
          }
        });
      });
    },
  };
})(Drupal, once);
