# Vesora Hair Atelier

A custom classic WordPress theme for the Vesora Hair showroom. It uses WooCommerce as a visual catalog without public prices or checkout, with direct WhatsApp and Instagram consultation paths.

## Install

1. Copy `vesora-theme` to `wp-content/themes/`.
2. Activate **Vesora Hair Atelier** in Appearance > Themes.
3. Install and activate WooCommerce.
4. Create pages for Shop, About and Contact, then assign the Shop page in WooCommerce settings.
5. Create a menu and assign it to **Primary navigation**.
6. Open Appearance > Customize > Vesora homepage to edit the hero and WhatsApp URL.
7. Set the homepage display to a static page that uses the theme front page.

## Showroom catalog

Version 2.0 creates the Vesora category hierarchy and a starter collection from the supplied Drive assets. WooCommerce remains the catalog engine, while prices, purchasing controls, cart and checkout are disabled on the public site. Every product routes visitors to a tailored WhatsApp consultation, with Instagram as a secondary contact path.

## Wholesale form

The form uses the WordPress `wp_mail()` API and sends to the Administration Email Address. Configure SMTP on production hosting for dependable delivery. The form includes a nonce, server-side sanitization, required-field validation, a honeypot and success/error states.

## Accessibility acceptance checks

- Keyboard focus must remain visible on every interactive element.
- The mobile menu must announce its expanded state.
- Required form fields must be labelled and browser validation must identify invalid values.
- Text and controls must maintain WCAG 2.2 AA contrast.
- Reduced-motion preferences must remove reveal animation.
- Images must retain useful alternative text.

## Theme structure

- `front-page.php`: editorial commerce homepage
- `woocommerce.php`: WooCommerce content wrapper
- `functions.php`: setup, Customizer and wholesale form handler
- `theme.json`: editor tokens and styles
- `assets/css/theme.css`: semantic CSS token system and responsive layout
- `assets/js/theme.js`: menu, form loading and motion behavior

