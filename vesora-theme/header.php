<?php
/**
 * Site header.
 *
 * @package Vesora
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-KJ4DXXR3');</script>
	<!-- End Google Tag Manager -->
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-Z8BX403S5E"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'G-Z8BX403S5E');
	</script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KJ4DXXR3" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'vesora' ); ?></a>

<div class="announcement" role="region" aria-label="<?php esc_attr_e( 'Current offer', 'vesora' ); ?>">
	<p><?php esc_html_e( 'Trusted Vietnamese Hair Factory', 'vesora' ); ?></p>
</div>

<header class="site-header" data-site-header>
	<div class="shell site-header__inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Vesora Hair home', 'vesora' ); ?>">
			<picture class="brand__logo" aria-hidden="true">
				<source media="(min-width: 768px)" srcset="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-desktop-black.png' ); ?>">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-mobile-black.png' ); ?>" alt="" width="866" height="288" fetchpriority="high" decoding="async">
			</picture>
		</a>

		<nav class="site-nav" id="site-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'vesora' ); ?>" data-site-nav>
			<div class="mobile-nav__header">
				<form class="mobile-nav__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="mobile-site-search"><?php esc_html_e( 'Search for products', 'vesora' ); ?></label>
					<input id="mobile-site-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search for products', 'vesora' ); ?>" required>
					<input type="hidden" name="post_type" value="product">
					<button type="submit" aria-label="<?php esc_attr_e( 'Submit search', 'vesora' ); ?>"><span class="dashicons dashicons-search" aria-hidden="true"></span></button>
				</form>
				<button class="mobile-nav__close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'vesora' ); ?>" data-menu-close>
					<span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
				</button>
			</div>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__list site-nav__desktop-list',
					'fallback_cb'    => 'vesora_menu_fallback',
				)
			);
			?>
			<?php vesora_render_mobile_navigation(); ?>
			<div class="mobile-language" data-mobile-language>
				<span class="mobile-language__label"><?php esc_html_e( 'Language', 'vesora' ); ?></span>
				<div class="mobile-language__widget" data-language-widget></div>
			</div>
		</nav>
		<button class="mobile-menu-backdrop" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'vesora' ); ?>" data-menu-backdrop></button>

		<div class="header-actions">
			<button class="icon-link icon-link--search" type="button" aria-label="<?php esc_attr_e( 'Search products', 'vesora' ); ?>" aria-haspopup="dialog" aria-expanded="false" data-search-open><span class="dashicons dashicons-search" aria-hidden="true"></span></button>
			<a class="icon-link icon-link--instagram" href="<?php echo esc_url( vesora_contact_value( 'instagram_url' ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Vesora Hair on Instagram', 'vesora' ); ?>">
				<span class="dashicons dashicons-instagram" aria-hidden="true"></span>
			</a>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" data-menu-toggle>
				<span></span><span></span><span class="screen-reader-text"><?php esc_html_e( 'Toggle menu', 'vesora' ); ?></span>
			</button>
		</div>
	</div>
</header>

<div class="site-search-overlay" data-search-overlay hidden>
	<button class="site-search-overlay__backdrop" type="button" tabindex="-1" aria-label="<?php esc_attr_e( 'Close search', 'vesora' ); ?>" data-search-close></button>
	<div class="site-search-dialog" role="dialog" aria-modal="true" aria-labelledby="site-search-title" data-search-dialog>
		<div class="site-search-dialog__header">
			<h2 id="site-search-title"><?php esc_html_e( 'Search the hair showroom', 'vesora' ); ?></h2>
			<button class="site-search-dialog__close" type="button" aria-label="<?php esc_attr_e( 'Close search', 'vesora' ); ?>" data-search-close><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
		</div>
		<form class="site-search-dialog__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="desktop-product-search"><?php esc_html_e( 'Search for products', 'vesora' ); ?></label>
			<input id="desktop-product-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Try Tape-in, Brown, Blonde or Wavy', 'vesora' ); ?>" autocomplete="off" required data-search-input>
			<input type="hidden" name="post_type" value="product">
			<button type="submit"><?php esc_html_e( 'Search', 'vesora' ); ?><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>
		</form>
		<p class="site-search-dialog__hint"><?php esc_html_e( 'Search by product name, hair type, texture or color.', 'vesora' ); ?></p>
	</div>
</div>
