/**
 * Light/dark toggle. No stored choice = follow the system; a click stores
 * the opposite of whatever is currently showing. The early no-flash init
 * lives inline in html.html.twig; icon swapping is pure CSS via --night.
 */
(function () {
  'use strict';

  var button = document.querySelector('.js-theme-toggle');
  if (!button) {
    return;
  }

  var systemDark = window.matchMedia('(prefers-color-scheme: dark)');

  function resolved() {
    return document.documentElement.dataset.theme
      || (systemDark.matches ? 'dark' : 'light');
  }

  function updateLabel() {
    button.setAttribute('aria-label',
      resolved() === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
  }

  button.addEventListener('click', function () {
    var next = resolved() === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = next;
    try {
      localStorage.setItem('theme', next);
    } catch (e) {
      // Embedded previews can block storage – the toggle still works for
      // the current page, the choice just won't persist.
    }
    updateLabel();
  });

  systemDark.addEventListener('change', updateLabel);
  updateLabel();
})();
