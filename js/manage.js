/**
 * Owner dashboard (/manage/): confirm before cancelling, filters that apply
 * as soon as they change, more payment-date boxes, and a one-off tip about
 * adding the page to the home screen.
 */
(function () {
  'use strict';

  document.querySelectorAll('form.js-confirm').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) {
        e.preventDefault();
      }
    });
  });

  var filters = document.querySelector('.js-filters');
  if (filters) {
    filters.querySelectorAll('select, input[type="checkbox"]').forEach(function (input) {
      input.addEventListener('change', function () { filters.submit(); });
    });
  }

  var add = document.querySelector('.js-add-date');
  var dates = document.querySelector('.js-dates');
  if (add && dates) {
    add.addEventListener('click', function () {
      var input = dates.querySelector('input:last-child').cloneNode();
      input.value = '';
      dates.appendChild(input);
      input.focus();
    });
  }

  // Show the tip on phones until it's dismissed, and never inside the
  // home-screen app itself.
  var tip = document.querySelector('.js-install-tip');
  if (tip) {
    var standalone = window.navigator.standalone || window.matchMedia('(display-mode: standalone)').matches;
    var dismissed = false;
    try { dismissed = window.localStorage.getItem('jjManageTip') === '1'; } catch (err) { /* private mode */ }
    if (!standalone && !dismissed && window.matchMedia('(max-width: 1024px)').matches) {
      tip.hidden = false;
    }
    tip.querySelector('.js-install-tip-close').addEventListener('click', function () {
      tip.hidden = true;
      try { window.localStorage.setItem('jjManageTip', '1'); } catch (err) { /* private mode */ }
    });
  }
})();
