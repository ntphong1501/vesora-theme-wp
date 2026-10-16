# Vesora — Custom WordPress & WooCommerce Theme

A custom classic WordPress theme for a premium Vietnamese hair showroom. WooCommerce powers a consultation-led product catalog, with WhatsApp and Instagram contact paths in place of public pricing and checkout.

## Project highlights

- Responsive storefront with editorial homepage, product catalog, About and Contact pages, and blog templates.
- Custom catalog presentation and product image synchronization.
- Wholesale inquiry and promotional forms with server-side validation, nonce checks and spam honeypots.
- Private lead management with status tracking and an admin notification badge.
- Admin-managed sales profiles, homepage images and contact settings.
- Custom admin workspace and login presentation.
- CSS design tokens, local fonts, keyboard focus styles and reduced-motion handling.

## Technology

PHP, WordPress, WooCommerce, JavaScript, CSS and `theme.json`. Theme version: **2.25.1**. The theme header declares WordPress 6.4+ and PHP 8.0+; compatibility should be verified on the target installation.

## Installation

1. Set up WordPress with PHP 8.0 or later and install WooCommerce.
2. Copy `vesora-theme/` into `wp-content/themes/`.
3. Activate **Vesora Hair Atelier** under **Appearance → Themes**.
4. Create homepage, Shop, About and Contact pages. Assign the static homepage in **Settings → Reading** and the Shop page in WooCommerce settings.
5. Assign a menu to **Primary navigation**.
6. Configure homepage images and contact destinations in the theme settings. Add products and sales profiles as needed.
7. Configure WordPress mail delivery before testing inquiry notifications.

This repository contains theme assets, without a WordPress database or production configuration. A fresh installation needs its own content and settings.

## Source map

| Path | Responsibility |
| --- | --- |
| `vesora-theme/functions.php` | Theme setup, hooks and form handling |
| `vesora-theme/front-page.php` | Storefront homepage |
| `vesora-theme/woocommerce.php` | WooCommerce content wrapper |
| `vesora-theme/inc/catalog-*.php` | Catalog presentation and synchronization |
| `vesora-theme/inc/leads.php` | Lead management |
| `vesora-theme/inc/sales-team.php` | Sales contact profiles |
| `vesora-theme/inc/admin-workspace.php` | Custom admin workspace |
| `vesora-theme/assets/` | Styles, scripts, fonts and imagery |
| `vesora-theme/theme.json` | Editor design tokens |

## Verification

With PHP installed, syntax-check PHP files before deployment. Verify the homepage and catalog on desktop and mobile, keyboard navigation, contact links, inquiry validation and submissions, lead permissions, and reduced-motion behavior in a local WordPress installation.

## Portfolio description

> Developed a custom WordPress/WooCommerce showroom theme with a responsive product catalog, consultation flows, wholesale inquiry handling, private lead management and an admin workspace for content editors.

## Repository scope

Local credentials, lead exports, deployment notes, temporary previews and release archives are excluded. Product images, fonts and brand assets are included for project presentation; their inclusion does not grant separate reuse rights.
