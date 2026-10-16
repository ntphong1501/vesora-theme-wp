(function () {
  'use strict';

  const toggle = document.querySelector('[data-menu-toggle]');
  const nav = document.querySelector('[data-site-nav]');
  const closeButton = document.querySelector('[data-menu-close]');
  const backdrop = document.querySelector('[data-menu-backdrop]');
  const submenuToggles = Array.from(document.querySelectorAll('[data-submenu-toggle]'));
  const mobileDrilldown = document.querySelector('[data-mobile-drilldown]');
  const mobilePanels = mobileDrilldown ? Array.from(mobileDrilldown.querySelectorAll('[data-mobile-panel]')) : [];

  function showMobilePanel(panelName, moveFocus) {
    if (!mobilePanels.length) {
      return;
    }

    mobilePanels.forEach(function (panel) {
      const active = panel.dataset.mobilePanel === panelName;
      panel.classList.toggle('is-active', active);
      panel.setAttribute('aria-hidden', String(!active));
      if (active && moveFocus) {
        const firstControl = panel.querySelector('button, a');
        if (firstControl) {
          window.setTimeout(function () { firstControl.focus(); }, 180);
        }
      }
    });
  }

  if (mobileDrilldown) {
    mobileDrilldown.addEventListener('click', function (event) {
      const forward = event.target.closest('[data-mobile-panel-open]');
      const back = event.target.closest('[data-mobile-panel-back]');
      if (!forward && !back) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      showMobilePanel(
        forward ? forward.dataset.mobilePanelOpen : back.dataset.mobilePanelBack,
        true
      );
    });
  }

  function setSubmenuState(submenuToggle, open) {
    const item = submenuToggle.closest('.site-nav__item--mega');
    if (!item) {
      return;
    }

    submenuToggle.setAttribute('aria-expanded', String(open));
    item.classList.toggle('is-open', open);
    if (!open) {
      item.querySelectorAll('[data-category-toggle]').forEach(function (button) {
        button.setAttribute('aria-expanded', 'false');
        button.closest('.category-dropdown__item').classList.remove('is-active');
        const panel = document.getElementById(button.getAttribute('aria-controls'));
        if (panel) panel.hidden = true;
      });
    }
  }

  function closeAllSubmenus(exceptItem) {
    submenuToggles.forEach(function (submenuToggle) {
      const item = submenuToggle.closest('.site-nav__item--mega');
      if (item && item !== exceptItem) {
        setSubmenuState(submenuToggle, false);
      }
    });
  }

  function suppressHoverUntilLeave(item) {
    item.classList.add('is-hover-suppressed');

    if (!item.matches(':hover')) {
      item.classList.remove('is-hover-suppressed');
      return;
    }

    item.addEventListener('pointerleave', function clearHoverSuppression() {
      item.classList.remove('is-hover-suppressed');
    }, { once: true });
  }

  if (toggle && nav) {
    function closeMenu(restoreFocus) {
      toggle.setAttribute('aria-expanded', 'false');
      nav.removeAttribute('data-open');
      document.body.classList.remove('menu-is-open');
      closeAllSubmenus();
      showMobilePanel('root', false);
      if (restoreFocus) {
        toggle.focus();
      }
    }

    toggle.addEventListener('click', function () {
      const open = toggle.getAttribute('aria-expanded') === 'true';
      if (open) {
        closeMenu(false);
      } else {
		showMobilePanel('root', false);
        toggle.setAttribute('aria-expanded', 'true');
        nav.setAttribute('data-open', '');
        document.body.classList.add('menu-is-open');
        window.setTimeout(function () {
          const search = nav.querySelector('input[type="search"]');
          if (search) {
            search.focus();
          }
        }, 250);
      }
    });

    if (closeButton) {
      closeButton.addEventListener('click', function () { closeMenu(true); });
    }

    if (backdrop) {
      backdrop.addEventListener('click', function () { closeMenu(true); });
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && nav.hasAttribute('data-open')) {
        const activePanel = mobileDrilldown ? mobileDrilldown.querySelector('[data-mobile-panel].is-active') : null;
        const backControl = activePanel ? activePanel.querySelector('[data-mobile-panel-back]') : null;
        if (backControl) {
          showMobilePanel(backControl.dataset.mobilePanelBack, true);
        } else {
          closeMenu(true);
        }
      }
    });

    nav.addEventListener('click', function (event) {
      if (event.target.closest('[data-mobile-language]')) {
        return;
      }

      if (event.target.closest('a')) {
        closeMenu(false);
      }
    });
  }

  submenuToggles.forEach(function (submenuToggle) {
    const item = submenuToggle.closest('.site-nav__item--mega');
    if (!item) {
      return;
    }

    submenuToggle.addEventListener('click', function () {
      const open = submenuToggle.getAttribute('aria-expanded') === 'true';
      closeAllSubmenus(item);
      if (open) {
        suppressHoverUntilLeave(item);
      } else {
        item.classList.remove('is-hover-suppressed');
      }
      setSubmenuState(submenuToggle, !open);
    });

    item.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        event.stopPropagation();
        suppressHoverUntilLeave(item);
        setSubmenuState(submenuToggle, false);
        submenuToggle.focus();
      }
    });
  });

  document.addEventListener('pointerdown', function (event) {
    if (!event.target.closest('.site-nav__item--mega')) {
      closeAllSubmenus();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      const openToggle = submenuToggles.find(function (submenuToggle) {
        return submenuToggle.getAttribute('aria-expanded') === 'true';
      });

      if (openToggle) {
        const item = openToggle.closest('.site-nav__item--mega');
        if (item) {
          suppressHoverUntilLeave(item);
        }
        setSubmenuState(openToggle, false);
        openToggle.focus();
      }
    }
  });

  document.querySelectorAll('.category-dropdown').forEach(function (dropdown) {
    const rows = Array.from(dropdown.querySelectorAll('.category-dropdown__item'));
    const root = dropdown.closest('.site-nav__item--mega');
    function activate(row, open) {
      dropdown.classList.remove('opens-left');
      const bounds = dropdown.getBoundingClientRect();
      dropdown.classList.toggle('opens-left', bounds.right + 300 > window.innerWidth - 16);
      rows.forEach(function (entry) {
        const button = entry.querySelector('[data-category-toggle]');
        const panel = entry.querySelector('.category-dropdown__children');
        const active = entry === row && open;
        entry.classList.toggle('is-active', active);
        if (button) button.setAttribute('aria-expanded', String(active));
        if (panel) panel.hidden = !active;
      });
    }
    rows.forEach(function (row) {
      const button = row.querySelector('[data-category-toggle]');
      row.querySelector('.category-dropdown__row').addEventListener('pointerenter', function (event) {
        if (event.pointerType === 'mouse') activate(row, !!button);
      });
      if (!button) return;
      button.addEventListener('click', function () { activate(row, button.getAttribute('aria-expanded') !== 'true'); });
      row.addEventListener('keydown', function (event) {
        const panel = row.querySelector('.category-dropdown__children');
        if (event.key === 'ArrowRight') {
          event.preventDefault(); activate(row, true); panel.querySelector('a').focus();
        } else if ((event.key === 'Escape' || event.key === 'ArrowLeft') && !panel.hidden) {
          event.preventDefault(); event.stopPropagation(); activate(row, false); button.focus();
        }
      });
    });
    root.addEventListener('pointerleave', function (event) {
      if (event.pointerType === 'mouse' && !root.contains(document.activeElement)) {
        activate(null, false);
        setSubmenuState(root.querySelector('[data-submenu-toggle]'), false);
      }
    });
    root.addEventListener('focusout', function () {
      window.setTimeout(function () {
        if (!root.contains(document.activeElement) && !root.matches(':hover')) {
          activate(null, false);
          setSubmenuState(root.querySelector('[data-submenu-toggle]'), false);
        }
      }, 0);
    });
  });

  const searchOpen = document.querySelector('[data-search-open]');
  const searchOverlay = document.querySelector('[data-search-overlay]');
  const searchDialog = document.querySelector('[data-search-dialog]');
  const searchInput = document.querySelector('[data-search-input]');
  const searchCloseButtons = Array.from(document.querySelectorAll('[data-search-close]'));
  let searchLastFocus = null;

  function closeProductSearch(restoreFocus) {
    if (!searchOverlay || searchOverlay.hidden) {
      return;
    }

    searchOverlay.classList.remove('is-open');
    document.body.classList.remove('search-is-open');
    if (searchOpen) {
      searchOpen.setAttribute('aria-expanded', 'false');
    }

    window.setTimeout(function () {
      searchOverlay.hidden = true;
      if (restoreFocus && searchLastFocus) {
        searchLastFocus.focus();
      }
    }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220);
  }

  if (searchOpen && searchOverlay && searchDialog && searchInput) {
    searchOpen.addEventListener('click', function () {
      searchLastFocus = document.activeElement;
      searchOverlay.hidden = false;
      document.body.classList.add('search-is-open');
      searchOpen.setAttribute('aria-expanded', 'true');

      window.requestAnimationFrame(function () {
        searchOverlay.classList.add('is-open');
        searchInput.focus();
        searchInput.select();
      });
    });

    searchCloseButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        closeProductSearch(true);
      });
    });

    searchOverlay.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        event.preventDefault();
        closeProductSearch(true);
        return;
      }

      if (event.key !== 'Tab') {
        return;
      }

      const focusable = Array.from(searchDialog.querySelectorAll('input, button, a[href]')).filter(function (element) {
        return !element.disabled && element.offsetParent !== null;
      });
      if (!focusable.length) {
        return;
      }

      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });
  }

  document.querySelectorAll('[data-hero-carousel]').forEach(function (carousel) {
    const slides = Array.from(carousel.querySelectorAll('[data-hero-slide]'));
    const previousButton = carousel.querySelector('[data-hero-previous]');
    const nextButton = carousel.querySelector('[data-hero-next]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let activeIndex = 0;
    let autoTimer = null;

    if (slides.length < 2) {
      return;
    }

    function stopAutoRotation() {
      if (autoTimer) {
        window.clearInterval(autoTimer);
        autoTimer = null;
      }
    }

    function startAutoRotation() {
      stopAutoRotation();
      if (reduceMotion || document.hidden) {
        return;
      }
      autoTimer = window.setInterval(function () {
        showSlide(activeIndex + 1);
      }, 4000);
    }

    function showSlide(index) {
      activeIndex = (index + slides.length) % slides.length;
      slides.forEach(function (slide, slideIndex) {
        const active = slideIndex === activeIndex;
        const link = slide.querySelector('a');
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', String(!active));
        slide.inert = !active;
        if (link) {
          link.setAttribute('tabindex', active ? '0' : '-1');
        }
      });
    }

    function navigateTo(index) {
      showSlide(index);
      startAutoRotation();
    }

    if (previousButton) {
      previousButton.addEventListener('click', function () {
        navigateTo(activeIndex - 1);
      });
    }

    if (nextButton) {
      nextButton.addEventListener('click', function () {
        navigateTo(activeIndex + 1);
      });
    }

    carousel.addEventListener('pointerenter', function (event) {
      if (event.pointerType === 'mouse') {
        stopAutoRotation();
      }
    });
    carousel.addEventListener('pointerleave', function (event) {
      if (event.pointerType === 'mouse') {
        startAutoRotation();
      }
    });
    carousel.addEventListener('focusin', stopAutoRotation);
    carousel.addEventListener('focusout', function () {
      window.setTimeout(function () {
        if (!carousel.contains(document.activeElement)) {
          startAutoRotation();
        }
      }, 0);
    });
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        stopAutoRotation();
      } else {
        startAutoRotation();
      }
    });

    showSlide(0);
    startAutoRotation();
  });

  const form = document.querySelector('[data-inquiry-form]');
  if (form) {
    form.addEventListener('submit', function () {
      const button = form.querySelector('[data-submit-button]');
      if (button && form.checkValidity()) {
        button.disabled = true;
        button.classList.add('is-loading');
        button.setAttribute('aria-busy', 'true');
      }
    });
  }

  const status = document.querySelector('[data-form-status]');
  if (status) {
    status.focus();
  }

  function fitGTranslateToTopBar() {
    const announcement = document.querySelector('.announcement');
    const switcher = document.querySelector('#gt_float_wrapper');
    const mobileTarget = document.querySelector('[data-language-widget]');
    if (!announcement || !switcher) {
      return false;
    }

    const widgetHost = switcher.closest('.gtranslate_wrapper') || switcher.parentElement;

    function placeLanguageSwitcher() {
      const useMobileMenu = window.matchMedia('(max-width: 1024px)').matches;

      if (useMobileMenu && mobileTarget && widgetHost.parentElement !== mobileTarget) {
        mobileTarget.appendChild(widgetHost);
      } else if (!useMobileMenu && widgetHost.parentElement !== document.body) {
        document.body.appendChild(widgetHost);
      }
    }

    placeLanguageSwitcher();

    if (!switcher.hasAttribute('data-responsive-ready')) {
      const mobileQuery = window.matchMedia('(max-width: 1024px)');
      mobileQuery.addEventListener('change', placeLanguageSwitcher);
      switcher.setAttribute('data-responsive-ready', 'true');
    }

    const trigger = switcher.querySelector('.gt-selected');
    if (trigger && !trigger.hasAttribute('data-keyboard-ready')) {
      trigger.setAttribute('role', 'button');
      trigger.setAttribute('tabindex', '0');
      trigger.setAttribute('aria-label', 'Choose language');
      trigger.setAttribute('data-keyboard-ready', 'true');
      trigger.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          trigger.click();
        }
      });
    }

    return true;
  }

  if (!fitGTranslateToTopBar()) {
    const translateObserver = new MutationObserver(function () {
      if (fitGTranslateToTopBar()) {
        translateObserver.disconnect();
      }
    });
    translateObserver.observe(document.body, { childList: true, subtree: true });
    window.setTimeout(function () { translateObserver.disconnect(); }, 10000);
  }

  const counters = document.querySelectorAll('[data-counter-target]');
  if (counters.length) {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function animateCounter(counter) {
      const target = Number(counter.getAttribute('data-counter-target'));
      if (!Number.isFinite(target) || reduceMotion) {
        counter.textContent = String(target);
        return;
      }

      const duration = 1300;
      const startedAt = performance.now();
      counter.textContent = '0';

      function updateCounter(now) {
        const progress = Math.min((now - startedAt) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        counter.textContent = String(Math.round(target * eased));
        if (progress < 1) {
          window.requestAnimationFrame(updateCounter);
        }
      }

      window.requestAnimationFrame(updateCounter);
    }

    if ('IntersectionObserver' in window && !reduceMotion) {
      const counterObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.querySelectorAll('[data-counter-target]').forEach(animateCounter);
            counterObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.3 });

      document.querySelectorAll('[data-counter-section]').forEach(function (section) {
        counterObserver.observe(section);
      });
    } else {
      counters.forEach(animateCounter);
    }
  }

  document.querySelectorAll('[data-shop-discovery]').forEach(function (shopDiscovery) {
    const tabs = Array.from(shopDiscovery.querySelectorAll('[data-shop-tab]'));
    const panels = Array.from(shopDiscovery.querySelectorAll('[data-shop-panel]'));
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let autoTimer = null;
    let interactionTimer = null;

    function stopAutoRotation() {
      if (autoTimer) {
        window.clearInterval(autoTimer);
        autoTimer = null;
      }
    }

    function getCardDistance(track) {
      const firstCard = track.querySelector('.shop-discovery__card');
      const gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
      return firstCard ? firstCard.getBoundingClientRect().width + gap : track.clientWidth * 0.8;
    }

    function advancePanel(panel) {
      const track = panel.querySelector('[data-shop-track]');
      if (!track) {
        return;
      }
      const distance = getCardDistance(track);
      const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      if (track.scrollLeft >= maxScroll - (distance * 0.25)) {
        track.scrollTo({ left: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      } else {
        track.scrollBy({ left: distance, behavior: reduceMotion ? 'auto' : 'smooth' });
      }
    }

    function startAutoRotation() {
      stopAutoRotation();
      if (reduceMotion || document.hidden) {
        return;
      }
      autoTimer = window.setInterval(function () {
        const activePanel = panels.find(function (panel) { return !panel.hidden; });
        if (activePanel) {
          advancePanel(activePanel);
        }
      }, 3200);
    }

    function pauseAfterInteraction() {
      stopAutoRotation();
      if (interactionTimer) {
        window.clearTimeout(interactionTimer);
      }
      interactionTimer = window.setTimeout(startAutoRotation, 5000);
    }

    function updateArrows(panel) {
      const track = panel.querySelector('[data-shop-track]');
      const previous = panel.querySelector('[data-shop-previous]');
      const next = panel.querySelector('[data-shop-next]');
      if (!track || !previous || !next) {
        return;
      }

      const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      previous.disabled = track.scrollLeft <= 4;
      next.disabled = track.scrollLeft >= maxScroll - 4;
    }

    function activateTab(tab, moveFocus) {
      const target = tab.getAttribute('data-shop-tab');
      tabs.forEach(function (candidate) {
        const active = candidate === tab;
        candidate.classList.toggle('is-active', active);
        candidate.setAttribute('aria-selected', String(active));
        candidate.setAttribute('tabindex', active ? '0' : '-1');
      });
      panels.forEach(function (panel) {
        const active = panel.getAttribute('data-shop-panel') === target;
        panel.hidden = !active;
        if (active) {
          window.requestAnimationFrame(function () { updateArrows(panel); });
        }
      });
      if (moveFocus) {
        tab.focus();
      }
      startAutoRotation();
    }

    tabs.forEach(function (tab, index) {
      tab.addEventListener('click', function () { activateTab(tab, false); });
      tab.addEventListener('keydown', function (event) {
        let nextIndex = null;
        if (event.key === 'ArrowRight') {
          nextIndex = (index + 1) % tabs.length;
        } else if (event.key === 'ArrowLeft') {
          nextIndex = (index - 1 + tabs.length) % tabs.length;
        } else if (event.key === 'Home') {
          nextIndex = 0;
        } else if (event.key === 'End') {
          nextIndex = tabs.length - 1;
        }
        if (nextIndex !== null) {
          event.preventDefault();
          activateTab(tabs[nextIndex], true);
        }
      });
    });

    panels.forEach(function (panel) {
      const track = panel.querySelector('[data-shop-track]');
      const previous = panel.querySelector('[data-shop-previous]');
      const next = panel.querySelector('[data-shop-next]');
      if (!track || !previous || !next) {
        return;
      }

      function scrollCards(direction) {
        const distance = getCardDistance(track);
        track.scrollBy({ left: direction * distance, behavior: reduceMotion ? 'auto' : 'smooth' });
      }

      previous.addEventListener('click', function () { pauseAfterInteraction(); scrollCards(-1); });
      next.addEventListener('click', function () { pauseAfterInteraction(); scrollCards(1); });
      track.addEventListener('scroll', function () { window.requestAnimationFrame(function () { updateArrows(panel); }); }, { passive: true });
      track.addEventListener('pointerdown', pauseAfterInteraction, { passive: true });
      track.addEventListener('wheel', pauseAfterInteraction, { passive: true });
      updateArrows(panel);
    });

    shopDiscovery.addEventListener('pointerenter', function (event) {
      if (event.pointerType === 'mouse') {
        stopAutoRotation();
      }
    });
    shopDiscovery.addEventListener('pointerleave', function (event) {
      if (event.pointerType === 'mouse') {
        startAutoRotation();
      }
    });
    shopDiscovery.addEventListener('focusin', stopAutoRotation);
    shopDiscovery.addEventListener('focusout', function () {
      window.setTimeout(function () {
        if (!shopDiscovery.contains(document.activeElement)) {
          startAutoRotation();
        }
      }, 0);
    });
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        stopAutoRotation();
      } else {
        startAutoRotation();
      }
    });

    window.addEventListener('resize', function () {
      panels.forEach(function (panel) { if (!panel.hidden) { updateArrows(panel); } });
    });
    startAutoRotation();
  });

  document.querySelectorAll('[data-product-carousel]').forEach(function (carousel) {
    const track = carousel.querySelector('ul.products');
    const previous = carousel.querySelector('[data-product-previous]');
    const next = carousel.querySelector('[data-product-next]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let autoTimer = null;
    let interactionTimer = null;

    if (!track || !previous || !next) {
      return;
    }

    function cardDistance() {
      const firstCard = track.querySelector('li.product');
      const gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
      return firstCard ? firstCard.getBoundingClientRect().width + gap : track.clientWidth * 0.8;
    }

    function updateArrows() {
      const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      previous.disabled = track.scrollLeft <= 4;
      next.disabled = track.scrollLeft >= maxScroll - 4;
    }

    function stopAutoRotation() {
      if (autoTimer) {
        window.clearInterval(autoTimer);
        autoTimer = null;
      }
    }

    function advance() {
      const distance = cardDistance();
      const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      if (track.scrollLeft >= maxScroll - (distance * 0.25)) {
        track.scrollTo({ left: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      } else {
        track.scrollBy({ left: distance, behavior: reduceMotion ? 'auto' : 'smooth' });
      }
    }

    function startAutoRotation() {
      stopAutoRotation();
      if (reduceMotion || document.hidden || track.scrollWidth <= track.clientWidth + 4) {
        return;
      }
      autoTimer = window.setInterval(advance, 3400);
    }

    function pauseAfterInteraction() {
      stopAutoRotation();
      if (interactionTimer) {
        window.clearTimeout(interactionTimer);
      }
      interactionTimer = window.setTimeout(startAutoRotation, 5000);
    }

    previous.addEventListener('click', function () {
      pauseAfterInteraction();
      track.scrollBy({ left: -cardDistance(), behavior: reduceMotion ? 'auto' : 'smooth' });
    });
    next.addEventListener('click', function () {
      pauseAfterInteraction();
      track.scrollBy({ left: cardDistance(), behavior: reduceMotion ? 'auto' : 'smooth' });
    });
    track.addEventListener('scroll', function () { window.requestAnimationFrame(updateArrows); }, { passive: true });
    track.addEventListener('pointerdown', pauseAfterInteraction, { passive: true });
    track.addEventListener('wheel', pauseAfterInteraction, { passive: true });
    carousel.addEventListener('pointerenter', function (event) { if (event.pointerType === 'mouse') { stopAutoRotation(); } });
    carousel.addEventListener('pointerleave', function (event) { if (event.pointerType === 'mouse') { startAutoRotation(); } });
    carousel.addEventListener('focusin', stopAutoRotation);
    carousel.addEventListener('focusout', function () {
      window.setTimeout(function () { if (!carousel.contains(document.activeElement)) { startAutoRotation(); } }, 0);
    });
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        stopAutoRotation();
      } else {
        startAutoRotation();
      }
    });
    window.addEventListener('resize', function () { updateArrows(); startAutoRotation(); });
    updateArrows();
    startAutoRotation();
  });

  document.querySelectorAll('[data-feedback-carousel]').forEach(function (carousel) {
    const track = carousel.querySelector('[data-feedback-track]');
    const previous = carousel.querySelector('[data-feedback-previous]');
    const next = carousel.querySelector('[data-feedback-next]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let autoTimer = null;
    let interactionTimer = null;

    if (!track || !previous || !next || track.children.length < 2) {
      return;
    }

    function cardDistance() {
      const firstCard = track.querySelector('.customer-feedback__item');
      const gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
      return firstCard ? firstCard.getBoundingClientRect().width + gap : track.clientWidth * 0.82;
    }

    function updateArrows() {
      const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      previous.disabled = track.scrollLeft <= 4;
      next.disabled = track.scrollLeft >= maxScroll - 4;
    }

    function stopAutoRotation() {
      if (autoTimer) {
        window.clearInterval(autoTimer);
        autoTimer = null;
      }
    }

    function advance() {
      const distance = cardDistance();
      const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      if (track.scrollLeft >= maxScroll - (distance * 0.25)) {
        track.scrollTo({ left: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      } else {
        track.scrollBy({ left: distance, behavior: reduceMotion ? 'auto' : 'smooth' });
      }
    }

    function startAutoRotation() {
      stopAutoRotation();
      if (reduceMotion || document.hidden || track.scrollWidth <= track.clientWidth + 4) {
        return;
      }
      autoTimer = window.setInterval(advance, 5200);
    }

    function pauseAfterInteraction() {
      stopAutoRotation();
      if (interactionTimer) {
        window.clearTimeout(interactionTimer);
      }
      interactionTimer = window.setTimeout(startAutoRotation, 6500);
    }

    previous.addEventListener('click', function () {
      pauseAfterInteraction();
      track.scrollBy({ left: -cardDistance(), behavior: reduceMotion ? 'auto' : 'smooth' });
    });
    next.addEventListener('click', function () {
      pauseAfterInteraction();
      track.scrollBy({ left: cardDistance(), behavior: reduceMotion ? 'auto' : 'smooth' });
    });
    track.addEventListener('scroll', function () { window.requestAnimationFrame(updateArrows); }, { passive: true });
    track.addEventListener('pointerdown', pauseAfterInteraction, { passive: true });
    track.addEventListener('wheel', pauseAfterInteraction, { passive: true });
    carousel.addEventListener('pointerenter', function (event) { if (event.pointerType === 'mouse') { stopAutoRotation(); } });
    carousel.addEventListener('pointerleave', function (event) { if (event.pointerType === 'mouse') { startAutoRotation(); } });
    carousel.addEventListener('focusin', stopAutoRotation);
    carousel.addEventListener('focusout', function () {
      window.setTimeout(function () { if (!carousel.contains(document.activeElement)) { startAutoRotation(); } }, 0);
    });
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        stopAutoRotation();
      } else {
        startAutoRotation();
      }
    });
    window.addEventListener('resize', function () { updateArrows(); startAutoRotation(); });
    updateArrows();
    startAutoRotation();
  });

  document.querySelectorAll('[data-specialist-carousel]').forEach(function (carousel) {
    const track = carousel.querySelector('.vesora-sales-team__grid');
    const previous = carousel.querySelector('[data-specialist-previous]');
    const next = carousel.querySelector('[data-specialist-next]');
    if (!track || !previous || !next || track.children.length < 2) return;

    const originals = Array.from(track.children);
    const count = originals.length;
    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    // Pad both ends by at least one desktop viewport. The central set is canonical.
    const paddingCount = Math.ceil(4 / count) * count;
    let distance = 0, autoTimer = null, settleTimer = null, resumeTimer = null;
    let moving = false, hovered = false, inView = false, lastInteraction = 0;

    originals.forEach(function (card, index) { card.dataset.specialistIndex = index; });
    function copy(index) {
      const card = originals[index % count].cloneNode(true);
      card.dataset.specialistCopy = '1';
      card.removeAttribute('id');
      card.querySelectorAll('[id]').forEach(function (node) { node.removeAttribute('id'); });
      return card;
    }
    const before = document.createDocumentFragment();
    const after = document.createDocumentFragment();
    for (let i = 0; i < paddingCount; i++) {
      before.appendChild(copy(i));
      after.appendChild(copy(i));
    }
    track.prepend(before);
    track.append(after);
    const cards = Array.from(track.children);
    const modulo = value => ((value % count) + count) % count;

    function measure() {
      return originals[0].getBoundingClientRect().width + (parseFloat(getComputedStyle(track).columnGap) || 0);
    }
    function jump(left) {
      // Override the track's smooth CSS only for the visually identical loop reset.
      const behavior = track.style.scrollBehavior;
      track.style.scrollBehavior = 'auto';
      track.scrollTo({ left: left, behavior: 'instant' });
      track.style.scrollBehavior = behavior;
    }
    function syncAccessibility() {
      const rect = track.getBoundingClientRect();
      cards.forEach(function (card) {
        const box = card.getBoundingClientRect();
        const visible = box.right > rect.left + 2 && box.left < rect.right - 2;
        const focused = card.contains(document.activeElement);
        card.toggleAttribute('aria-hidden', !visible && !focused);
        if (!visible && !focused) card.setAttribute('aria-hidden', 'true');
        card.querySelectorAll('a,button').forEach(function (control) {
          if (visible || focused) control.removeAttribute('tabindex');
          else control.setAttribute('tabindex', '-1');
        });
      });
    }
    function settle() {
      clearTimeout(settleTimer);
      const index = Math.round(track.scrollLeft / distance);
      const canonical = paddingCount + modulo(index - paddingCount);
      if (index < paddingCount || index >= paddingCount + count) {
        const active = document.activeElement;
        const focusedCard = active && active.closest('.vesora-sales-card');
        const focusedIndex = focusedCard?.dataset.specialistIndex;
        const controlIndex = focusedCard ? Array.from(focusedCard.querySelectorAll('a,button')).indexOf(active) : -1;
        jump(canonical * distance);
        if (controlIndex >= 0) {
          const visibleMatch = cards.slice(canonical, canonical + 4).find(card => card.dataset.specialistIndex === focusedIndex);
          visibleMatch?.querySelectorAll('a,button')[controlIndex]?.focus({ preventScroll: true });
        }
      }
      moving = false;
      syncAccessibility();
    }
    function move(direction) {
      if (moving || !distance) return;
      moving = true;
      const target = (Math.round(track.scrollLeft / distance) + direction) * distance;
      if (motion.matches) {
        jump(target);
        settle();
      } else {
        track.scrollTo({ left: target, behavior: 'smooth' });
        clearTimeout(settleTimer);
        settleTimer = setTimeout(settle, 1000);
      }
    }
    function stop() { clearInterval(autoTimer); autoTimer = null; }
    function start() {
      stop();
      if (motion.matches || document.hidden || !inView || hovered || carousel.contains(document.activeElement) || Date.now() - lastInteraction < 6500) return;
      autoTimer = setInterval(function () { move(1); }, 5200);
    }
    function interact() {
      lastInteraction = Date.now();
      stop();
      clearTimeout(resumeTimer);
      resumeTimer = setTimeout(start, 6550);
    }

    previous.addEventListener('click', function () { interact(); move(-1); });
    next.addEventListener('click', function () { interact(); move(1); });
    track.addEventListener('scroll', function () {
      moving = true;
      clearTimeout(settleTimer);
      settleTimer = setTimeout(settle, 140);
    }, { passive: true });
    track.addEventListener('pointerdown', interact, { passive: true });
    track.addEventListener('wheel', interact, { passive: true });
    carousel.addEventListener('pointerenter', function (event) { if (event.pointerType === 'mouse') { hovered = true; stop(); } });
    carousel.addEventListener('pointerleave', function (event) { if (event.pointerType === 'mouse') { hovered = false; start(); } });
    carousel.addEventListener('focusin', stop);
    carousel.addEventListener('focusout', function () { setTimeout(start, 0); });
    document.addEventListener('visibilitychange', start);
    motion.addEventListener('change', function () { settle(); start(); });
    window.addEventListener('resize', function () {
      const index = distance ? modulo(Math.round(track.scrollLeft / distance) - paddingCount) : 0;
      distance = measure();
      jump((paddingCount + index) * distance);
      syncAccessibility();
      start();
    });
    distance = measure();
    jump(paddingCount * distance);
    syncAccessibility();
    carousel.dataset.specialistLoopReady = '1';
    new IntersectionObserver(function (entries) {
      inView = entries[0].isIntersecting;
      start();
    }, { threshold: 0.15 }).observe(carousel);
  });

  const offerPopup = document.querySelector('[data-offer-popup]');
  if (offerPopup) {
    const sessionKey = 'vesora-exclusive-offer-shown';
    const dialog = offerPopup.querySelector('[data-offer-dialog]');
    const closeOfferButton = offerPopup.querySelector('[data-offer-close]');
    const offerForm = offerPopup.querySelector('[data-offer-form]');
    const offerStatus = offerPopup.querySelector('[data-offer-status]');
    const offerSubmit = offerPopup.querySelector('[data-offer-submit]');
    const offerSubmitLabel = offerPopup.querySelector('[data-offer-submit-label]');
    const offerLoadingLabel = offerPopup.querySelector('[data-offer-loading]');
    let autoCloseTimer = null;
    let closeTransitionTimer = null;
    let previousFocus = null;

    function sessionHasSeenOffer() {
      try {
        return window.sessionStorage.getItem(sessionKey) === '1';
      } catch (error) {
        return offerPopup.dataset.sessionShown === '1';
      }
    }

    function rememberOfferShown() {
      offerPopup.dataset.sessionShown = '1';
      try {
        window.sessionStorage.setItem(sessionKey, '1');
      } catch (error) {
        // The in-memory data attribute remains the fallback for restricted storage.
      }
    }

    function stopOfferAutoClose() {
      if (autoCloseTimer) {
        window.clearTimeout(autoCloseTimer);
        autoCloseTimer = null;
      }
    }

    function removeOfferInteractionListeners() {
      offerPopup.removeEventListener('pointerdown', stopOfferAutoClose);
      offerPopup.removeEventListener('touchstart', stopOfferAutoClose);
      offerPopup.removeEventListener('input', stopOfferAutoClose);
      offerPopup.removeEventListener('change', stopOfferAutoClose);
      document.removeEventListener('keydown', handleOfferKeydown);
    }

    function closeOfferPopup(restoreFocus) {
      if (offerPopup.hidden) {
        return;
      }

      stopOfferAutoClose();
      removeOfferInteractionListeners();
      offerPopup.classList.remove('is-visible');
      document.body.classList.remove('offer-popup-is-open');

      closeTransitionTimer = window.setTimeout(function () {
        offerPopup.hidden = true;
        if (restoreFocus && previousFocus && typeof previousFocus.focus === 'function') {
          previousFocus.focus({ preventScroll: true });
        }
      }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 300);
    }

    function focusableOfferElements() {
      return Array.from(dialog.querySelectorAll('button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), a[href]'))
        .filter(function (element) { return element.offsetParent !== null; });
    }

    function handleOfferKeydown(event) {
      stopOfferAutoClose();

      if (event.key === 'Escape') {
        event.preventDefault();
        closeOfferPopup(true);
        return;
      }

      if (event.key !== 'Tab') {
        return;
      }

      const focusable = focusableOfferElements();
      if (!focusable.length) {
        event.preventDefault();
        return;
      }

      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }

    function addOfferInteractionListeners() {
      offerPopup.addEventListener('pointerdown', stopOfferAutoClose, { passive: true });
      offerPopup.addEventListener('touchstart', stopOfferAutoClose, { passive: true });
      offerPopup.addEventListener('input', stopOfferAutoClose, { passive: true });
      offerPopup.addEventListener('change', stopOfferAutoClose, { passive: true });
      document.addEventListener('keydown', handleOfferKeydown);
    }

    function openOfferPopup() {
      if (sessionHasSeenOffer()) {
        return;
      }

      if (closeTransitionTimer) {
        window.clearTimeout(closeTransitionTimer);
      }

      previousFocus = document.activeElement;
      rememberOfferShown();
      offerPopup.hidden = false;
      document.body.classList.add('offer-popup-is-open');
      addOfferInteractionListeners();

      window.requestAnimationFrame(function () {
        offerPopup.classList.add('is-visible');
        window.setTimeout(function () { closeOfferButton.focus({ preventScroll: true }); }, 50);
      });

      autoCloseTimer = window.setTimeout(function () { closeOfferPopup(false); }, 7000);
    }

    closeOfferButton.addEventListener('click', function () { closeOfferPopup(true); });

    if (offerForm) {
      offerForm.addEventListener('submit', function (event) {
        event.preventDefault();
        stopOfferAutoClose();

        if (!offerForm.checkValidity()) {
          offerForm.classList.add('was-validated');
          offerForm.reportValidity();
          offerStatus.textContent = 'Please complete every required field.';
          offerStatus.classList.remove('is-success');
          return;
        }

        const formData = new FormData(offerForm);
        formData.append('vesora_offer_ajax', '1');
        offerSubmit.disabled = true;
        offerSubmit.setAttribute('aria-busy', 'true');
        offerSubmitLabel.hidden = true;
        offerLoadingLabel.hidden = false;
        offerStatus.textContent = '';
        offerStatus.classList.remove('is-success');

        const offerEndpoint = offerForm.dataset.offerEndpoint || offerForm.getAttribute('action');
        window.fetch(offerEndpoint, {
          method: 'POST',
          body: formData,
          credentials: 'same-origin',
          headers: { Accept: 'application/json' }
        })
          .then(function (response) {
            return response.json().then(function (payload) {
              if (!response.ok || !payload.success) {
                const requestError = new Error(payload.data && payload.data.message ? payload.data.message : 'We could not save your request.');
                requestError.isVesoraMessage = true;
                throw requestError;
              }
              return payload;
            });
          })
          .then(function (payload) {
            offerForm.reset();
            offerForm.classList.remove('was-validated');
            offerStatus.textContent = payload.data.message;
            offerStatus.classList.add('is-success');
            offerSubmit.hidden = true;
            window.setTimeout(function () { closeOfferPopup(false); }, 3200);
          })
          .catch(function (error) {
            offerStatus.textContent = error.isVesoraMessage ? error.message : 'We could not save your request. Please check your connection and try again.';
            offerStatus.classList.remove('is-success');
            offerSubmit.disabled = false;
            offerSubmit.removeAttribute('aria-busy');
            offerSubmitLabel.hidden = false;
            offerLoadingLabel.hidden = true;
          });
      });
    }

    openOfferPopup();
  }

  document.querySelectorAll('[data-homepage-video]').forEach(function (frame) {
    const player = frame.querySelector('[data-homepage-video-player]');
    const playButton = frame.querySelector('[data-homepage-video-play]');
    const source = player ? player.querySelector('source[data-src]') : null;

    if (!player || !playButton || !source) {
      return;
    }

    function prepareVideo() {
      if (!source.getAttribute('src')) {
        source.src = source.dataset.src;
        player.load();
      }
      player.controls = true;
    }

    playButton.addEventListener('click', function () {
      prepareVideo();
      playButton.disabled = true;

      const playback = player.play();
      if (playback && typeof playback.catch === 'function') {
        playback.catch(function () {
          playButton.disabled = false;
          player.controls = true;
        });
      }
    });

    player.addEventListener('playing', function () {
      frame.classList.add('is-playing');
      playButton.disabled = false;
    });

    player.addEventListener('pause', function () {
      if (player.currentTime === 0 || player.ended) {
        frame.classList.remove('is-playing');
      }
    });

    player.addEventListener('error', function () {
      frame.classList.remove('is-playing');
      playButton.disabled = true;
      playButton.lastElementChild.textContent = 'Video unavailable';
    });
  });

  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    document.querySelectorAll('.reveal').forEach(function (element) {
      observer.observe(element);
    });
  } else {
    document.querySelectorAll('.reveal').forEach(function (element) {
      element.classList.add('is-visible');
    });
  }
}());
