(function () {
  'use strict';
  let timer, busyForm = null, busyRegion = null;
  const indicator = () => document.getElementById('vesora-loading');
  function hide() {
    clearTimeout(timer);
    const el = indicator();
    if (el) el.hidden = true;
    if (busyRegion) { busyRegion.removeAttribute('aria-busy'); busyRegion = null; }
    if (busyForm) {
      busyForm.removeAttribute('aria-busy');
      busyForm.querySelector('[type="submit"]')?.removeAttribute('aria-disabled');
      busyForm = null;
    }
  }
  function show(label, form) {
    const el = indicator();
    if (!el) return;
    hide();
    el.querySelector('[data-loading-label]').textContent = label || 'Loading, please wait…';
    el.hidden = false;
    const region = document.querySelector('main');
    if (region && !region.hasAttribute('aria-busy')) { busyRegion = region; region.setAttribute('aria-busy', 'true'); }
    if (form) {
      busyForm = form;
      form.setAttribute('aria-busy', 'true');
      form.querySelector('[type="submit"]')?.setAttribute('aria-disabled', 'true');
    }
    // Never leave a failed/cancelled navigation or a slow third-party script blocking the UI.
    timer = setTimeout(hide, 6000);
  }
  window.vesoraLoading = { mount() { if (document.readyState !== 'complete') show(); } };
  window.addEventListener('load', hide, { once: true });
  window.addEventListener('pageshow', hide);
  window.addEventListener('pagehide', hide);
  document.addEventListener('DOMContentLoaded', () => {
    const region = document.querySelector('main');
    if (indicator() && !indicator().hidden && region && !region.hasAttribute('aria-busy')) {
      busyRegion = region;
      region.setAttribute('aria-busy', 'true');
    }
  }, { once: true });
  document.addEventListener('keydown', event => { if (event.key === 'Escape') hide(); });
  document.addEventListener('click', event => {
    if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    const link = event.target.closest('a[href]');
    if (!link || link.hasAttribute('download') || (link.target && link.target !== '_self')) return;
    const url = new URL(link.href, location.href);
    if (!/^https?:$/.test(url.protocol) || url.origin !== location.origin || url.hash || url.searchParams.has('action') || url.pathname.includes('/wp-admin')) return;
    if (url.pathname === location.pathname && url.search === location.search) return;
    // Let other click handlers cancel client-side interactions first.
    queueMicrotask(() => { if (!event.defaultPrevented) show(); });
  });
  document.addEventListener('submit', event => {
    const form = event.target;
    if (form.id !== 'loginform' || event.defaultPrevented) return;
    if (busyForm === form) { event.preventDefault(); return; }
    if (!form.checkValidity() || !form.querySelector('[name="log"]')?.value.trim() || !form.querySelector('[name="pwd"]')?.value) return;
    queueMicrotask(() => { if (!event.defaultPrevented) show('Đang đăng nhập…', form); });
  });
})();
