(function () {
  'use strict';
  // Only documented promotion notices are grouped. Errors, updates and
  // GTranslate conflict/debug warnings are intentionally left untouched.
  const promotions = Array.from(document.querySelectorAll('.gt-admin-notice')).filter(notice =>
    Array.from(notice.querySelectorAll('a[href]')).some(link => {
      const url = new URL(link.href, location.href);
      return Array.from(url.searchParams).some(([key, value]) => key.endsWith('admin_notice_ignore') && ['two_week_review', 'upgrade_tips'].includes(value));
    })
  );
  promotions.push(...document.querySelectorAll('.ht-ctc-notice-pro-banner[data-db="pro_banner"]'));
  if (!promotions.length) return;
  const group = document.createElement('details');
  group.className = 'vesora-plugin-promotions';
  const label = document.createElement('summary');
  label.textContent = 'Thông tin giới thiệu từ tiện ích (' + promotions.length + ')';
  group.append(label);
  promotions[0].before(group);
  promotions.forEach(notice => group.append(notice));
})();
