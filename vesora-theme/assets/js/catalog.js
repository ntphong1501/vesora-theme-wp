(function () {
  'use strict';
  const filters = document.querySelector('.vesora-filters details');
  if (!filters) return;
  const mobile = window.matchMedia('(max-width: 767px)');
  const setInitialState = () => { filters.open = !mobile.matches || !!filters.querySelector('input:checked'); };
  setInitialState();
  mobile.addEventListener('change', setInitialState);
})();
