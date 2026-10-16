<?php
/**
 * Front page.
 *
 * @package Vesora
 */
get_header();

$asset_uri = get_template_directory_uri() . '/assets/images/';
$categories    = vesora_homepage_cards( 'categories' );
$texture_cards = vesora_homepage_cards( 'textures' );
$color_cards   = vesora_homepage_cards( 'colors' );
$feedback_images = vesora_feedback_gallery_images();
$homepage_video = vesora_homepage_video();
$homepage_banners = array_values( array_filter( vesora_homepage_banners(), static function ( $banner ) { return ! empty( $banner['enabled'] ) && ! empty( $banner['desktop_url'] ); } ) );
$sales_profiles = function_exists( 'vesora_get_sales_profiles' ) ? vesora_get_sales_profiles() : array();
$social_images = array(
	array( 'file' => 'social/vesora-social-01.jpg', 'width' => 1024, 'height' => 1536, 'alt' => __( 'Vesora Hair wedding season styling inspiration', 'vesora' ) ),
	array( 'file' => 'social/vesora-social-02.jpg', 'width' => 1024, 'height' => 1536, 'alt' => __( 'Every Vesora Hair order has a story behind it', 'vesora' ) ),
	array( 'file' => 'social/vesora-social-03.jpg', 'width' => 1254, 'height' => 1254, 'alt' => __( 'Vesora listens, creates, verifies and delivers', 'vesora' ) ),
	array( 'file' => 'social/vesora-social-04.jpg', 'width' => 1122, 'height' => 1402, 'alt' => __( 'Vesora Vietnamese hair factory mission', 'vesora' ) ),
	array( 'file' => 'social/vesora-social-05.jpg', 'width' => 1122, 'height' => 1402, 'alt' => __( 'Vesora Hair trusted by salons across Latin America', 'vesora' ) ),
);
?>
<main id="main-content">
	<section class="hero-banner" role="region" aria-labelledby="hero-title" aria-roledescription="carousel" data-hero-carousel>
		<h1 id="hero-title" class="screen-reader-text"><?php esc_html_e( 'Featured Vesora hair collections', 'vesora' ); ?></h1>
		<div class="hero-banner__slides">
			<?php foreach ( $homepage_banners as $index => $banner ) : ?>
				<div class="hero-banner__slide<?php echo 0 === $index ? ' is-active' : ''; ?>" aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>" data-hero-slide>
					<a class="hero-banner__link" href="<?php echo esc_url( $banner['url'] ? $banner['url'] : '#' ); ?>" aria-label="<?php echo esc_attr( $banner['alt'] ? $banner['alt'] : sprintf( __( 'Vesora banner %d', 'vesora' ), $index + 1 ) ); ?>"<?php echo 0 === $index ? '' : ' tabindex="-1"'; ?>>
						<picture class="hero-banner__picture">
							<source media="(max-width: 767px)" srcset="<?php echo esc_url( $banner['mobile_url'] ); ?>">
							<img class="hero-banner__image" src="<?php echo esc_url( $banner['desktop_url'] ); ?>" alt="<?php echo esc_attr( $banner['alt'] ); ?>" width="2033" height="773" <?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy" fetchpriority="low"'; ?> decoding="async">
						</picture>
						<?php if ( $banner['cta_label'] ) : ?><span class="hero-banner__cta"><?php echo esc_html( $banner['cta_label'] ); ?> <span aria-hidden="true">›</span></span><?php endif; ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( count( $homepage_banners ) > 1 ) : ?><div class="hero-banner__navigation" aria-label="<?php esc_attr_e( 'Banner navigation', 'vesora' ); ?>">
			<button class="hero-banner__arrow hero-banner__arrow--previous" type="button" data-hero-previous>
				<span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Previous banner', 'vesora' ); ?></span>
			</button>
			<button class="hero-banner__arrow hero-banner__arrow--next" type="button" data-hero-next>
				<span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Next banner', 'vesora' ); ?></span>
			</button>
		</div><?php endif; ?>
	</section>

	<div
		class="brand-marquee"
		role="region"
		aria-label="<?php esc_attr_e( 'Factory Direct - Consistent Quality - Strong Capability. Your Trusted Hair Supply Partner. Real Vietnamese Hair, Real Beauty.', 'vesora' ); ?>"
	>
		<div class="brand-marquee__track" aria-hidden="true">
			<div class="brand-marquee__group">
				<span><?php esc_html_e( 'Factory Direct - Consistent Quality - Strong Capability', 'vesora' ); ?></span>
				<span class="brand-marquee__separator">•</span>
				<span><?php esc_html_e( 'Your Trusted Hair Supply Partner', 'vesora' ); ?></span>
				<span class="brand-marquee__separator">•</span>
				<span><?php esc_html_e( 'Real Vietnamese Hair, Real Beauty', 'vesora' ); ?></span>
				<span class="brand-marquee__separator">•</span>
			</div>
			<div class="brand-marquee__group">
				<span><?php esc_html_e( 'Factory Direct - Consistent Quality - Strong Capability', 'vesora' ); ?></span>
				<span class="brand-marquee__separator">•</span>
				<span><?php esc_html_e( 'Your Trusted Hair Supply Partner', 'vesora' ); ?></span>
				<span class="brand-marquee__separator">•</span>
				<span><?php esc_html_e( 'Real Vietnamese Hair, Real Beauty', 'vesora' ); ?></span>
				<span class="brand-marquee__separator">•</span>
			</div>
		</div>
	</div>

	<figure id="counter-banner" class="counter-banner">
		<img src="<?php echo esc_url( $asset_uri . 'Luxury-in-every-strand-banner-20-09-2026.png' ); ?>" alt="<?php esc_attr_e( 'Vesora Hair, luxury in every strand', 'vesora' ); ?>" width="2172" height="724" loading="lazy" decoding="async">
	</figure>

	<?php if ( ! empty( $homepage_video['enabled'] ) && ( ! empty( $homepage_video['youtube_embed_url'] ) || ! empty( $homepage_video['video_url'] ) ) ) : ?>
		<section class="homepage-video section" aria-labelledby="homepage-video-title">
			<div class="shell homepage-video__layout">
				<div class="homepage-video__frame reveal"<?php echo $homepage_video['youtube_embed_url'] ? '' : ' data-homepage-video'; ?>>
					<?php if ( $homepage_video['youtube_embed_url'] ) : ?>
						<iframe src="<?php echo esc_url( $homepage_video['youtube_embed_url'] ); ?>" title="<?php esc_attr_e( 'Vesora Hair introduction video', 'vesora' ); ?>" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
					<?php else : ?>
					<video class="homepage-video__player" preload="none" playsinline data-homepage-video-player<?php echo $homepage_video['poster_url'] ? ' poster="' . esc_url( $homepage_video['poster_url'] ) . '"' : ''; ?>>
						<source data-src="<?php echo esc_url( $homepage_video['video_url'] ); ?>" type="<?php echo esc_attr( $homepage_video['video_type'] ); ?>">
					</video>
					<button class="homepage-video__play" type="button" data-homepage-video-play aria-label="<?php esc_attr_e( 'Play Vesora Hair introduction video', 'vesora' ); ?>">
						<span class="dashicons dashicons-controls-play" aria-hidden="true"></span>
						<span><?php esc_html_e( 'Play video', 'vesora' ); ?></span>
					</button>
					<noscript>
						<video class="homepage-video__player homepage-video__player--fallback" controls preload="metadata" playsinline src="<?php echo esc_url( $homepage_video['video_url'] ); ?>"<?php echo $homepage_video['poster_url'] ? ' poster="' . esc_url( $homepage_video['poster_url'] ) . '"' : ''; ?>></video>
					</noscript>
					<?php endif; ?>
				</div>
				<div class="homepage-video__content reveal reveal--delay">
					<h2 id="homepage-video-title"><?php echo esc_html( $homepage_video['title'] ); ?></h2>
					<?php if ( $homepage_video['copy'] ) : ?><div class="homepage-video__copy"><?php echo wp_kses_post( wpautop( $homepage_video['copy'] ) ); ?></div><?php endif; ?>
					<?php if ( $homepage_video['cta_label'] && $homepage_video['cta_url'] ) : ?><a class="homepage-video__cta" href="<?php echo esc_url( $homepage_video['cta_url'] ); ?>"><?php echo esc_html( $homepage_video['cta_label'] ); ?> <span aria-hidden="true">›</span></a><?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="wholesale-reasons section" aria-labelledby="wholesale-reasons-title">
		<div class="shell">
			<div class="brand-divider wholesale-reasons__divider" aria-hidden="true">
				<span class="brand-divider__line"></span>
				<span class="brand-divider__mark">
					<img src="<?php echo esc_url( $asset_uri . 'logo-nau.png' ); ?>" alt="" width="500" height="500" loading="lazy" decoding="async">
				</span>
				<span class="brand-divider__line"></span>
			</div>
			<header class="wholesale-reasons__header">
				<h2 id="wholesale-reasons-title"><?php esc_html_e( 'Why Wholesale Buyers Choose Vesora Hair as Their Vietnam Hair Factory', 'vesora' ); ?></h2>
				<p><?php esc_html_e( 'We always appreciate the quality of products and services provided to customers. These products can be customized according to your wishes.', 'vesora' ); ?></p>
			</header>

			<div class="wholesale-reasons__grid">
				<article class="wholesale-reasons__item">
					<span class="dashicons dashicons-money-alt" aria-hidden="true"></span>
					<div>
						<h3><?php esc_html_e( 'Factory-Direct Wholesale Pricing', 'vesora' ); ?></h3>
						<p><?php esc_html_e( 'Work directly with Vesora Hair to receive clear wholesale quotations tailored to order volume, product type and customization requirements.', 'vesora' ); ?></p>
					</div>
				</article>
				<article class="wholesale-reasons__item">
					<span class="dashicons dashicons-clock" aria-hidden="true"></span>
					<div>
						<h3><?php esc_html_e( 'Responsive Order Processing', 'vesora' ); ?></h3>
						<p><?php esc_html_e( 'An organized production and order-handling process helps salons, retailers and distributors restock with greater predictability.', 'vesora' ); ?></p>
					</div>
				</article>
				<article class="wholesale-reasons__item">
					<span class="dashicons dashicons-awards" aria-hidden="true"></span>
					<div>
						<h3><?php esc_html_e( 'Premium Vietnamese Human Hair', 'vesora' ); ?></h3>
						<p><?php esc_html_e( 'Our extensions, wigs and bulk hair are made from carefully selected Vietnamese human hair and checked for texture, color, weight and finish.', 'vesora' ); ?></p>
					</div>
				</article>
				<article class="wholesale-reasons__item">
					<span class="dashicons dashicons-art" aria-hidden="true"></span>
					<div>
						<h3><?php esc_html_e( 'Private Label & Customization', 'vesora' ); ?></h3>
						<p><?php esc_html_e( 'Customize lengths, colors, textures, construction and packaging to build a collection that fits your market and brand.', 'vesora' ); ?></p>
					</div>
				</article>
				<article class="wholesale-reasons__item">
					<span class="dashicons dashicons-airplane" aria-hidden="true"></span>
					<div>
						<h3><?php esc_html_e( 'Reliable Global Shipping', 'vesora' ); ?></h3>
						<p><?php esc_html_e( 'Orders are prepared for international delivery with suitable packaging and coordinated shipping options for wholesale customers.', 'vesora' ); ?></p>
					</div>
				</article>
				<article class="wholesale-reasons__item">
					<span class="dashicons dashicons-groups" aria-hidden="true"></span>
					<div>
						<h3><?php esc_html_e( 'Dedicated Business Support', 'vesora' ); ?></h3>
						<p><?php esc_html_e( 'A Vesora specialist supports product selection, quotations, order updates and after-sales questions through each stage of the partnership.', 'vesora' ); ?></p>
					</div>
				</article>
			</div>
		</div>
	</section>

	<section class="proof" aria-label="<?php esc_attr_e( 'Vesora at a glance', 'vesora' ); ?>">
		<div class="shell proof__grid" data-counter-section>
			<div class="proof__item">
				<span class="proof__icon dashicons dashicons-awards" aria-hidden="true"></span>
				<p class="proof__number"><span class="elementor-counter-number" data-counter-target="20">20</span><span class="proof__suffix">+</span></p>
				<p class="proof__label"><?php esc_html_e( 'Years of experience', 'vesora' ); ?></p>
			</div>
			<div class="proof__item">
				<span class="proof__icon dashicons dashicons-heart" aria-hidden="true"></span>
				<p class="proof__number"><span class="elementor-counter-number" data-counter-target="100">100</span><span class="proof__suffix">%</span></p>
				<p class="proof__label"><?php esc_html_e( 'Human hair', 'vesora' ); ?></p>
			</div>
			<div class="proof__item">
				<span class="proof__icon dashicons dashicons-groups" aria-hidden="true"></span>
				<p class="proof__number"><span class="elementor-counter-number" data-counter-target="85">85</span><span class="proof__suffix">K+</span></p>
				<p class="proof__label"><?php esc_html_e( 'Customers served', 'vesora' ); ?></p>
			</div>
			<div class="proof__item">
				<span class="proof__icon dashicons dashicons-store" aria-hidden="true"></span>
				<p class="proof__number"><span class="elementor-counter-number" data-counter-target="800">800</span><span class="proof__suffix">+</span></p>
				<p class="proof__label"><?php esc_html_e( 'Salon partners', 'vesora' ); ?></p>
			</div>
		</div>
	</section>

	<section class="shop-discovery" aria-labelledby="shop-discovery-title" data-shop-discovery>
		<div class="shell">
			<h2 id="shop-discovery-title" class="screen-reader-text"><?php esc_html_e( 'Shop Vesora hair by texture or color', 'vesora' ); ?></h2>
			<div class="brand-divider reveal" aria-hidden="true">
				<span class="brand-divider__line"></span>
				<span class="brand-divider__mark">
					<img src="<?php echo esc_url( $asset_uri . 'logo-nau.png' ); ?>" alt="" width="500" height="500" decoding="async">
				</span>
				<span class="brand-divider__line"></span>
			</div>

			<div class="shop-discovery__tabs reveal" role="tablist" aria-label="<?php esc_attr_e( 'Choose how to browse hair', 'vesora' ); ?>">
				<button id="shop-texture-tab" class="shop-discovery__tab is-active" type="button" role="tab" aria-selected="true" aria-controls="shop-texture-panel" data-shop-tab="texture"><?php esc_html_e( 'Shop By Texture', 'vesora' ); ?></button>
				<button id="shop-color-tab" class="shop-discovery__tab" type="button" role="tab" aria-selected="false" aria-controls="shop-color-panel" tabindex="-1" data-shop-tab="color"><?php esc_html_e( 'Shop By Color', 'vesora' ); ?></button>
			</div>

			<div id="shop-texture-panel" class="shop-discovery__panel" role="tabpanel" aria-labelledby="shop-texture-tab" data-shop-panel="texture">
				<button class="shop-discovery__arrow shop-discovery__arrow--previous" type="button" aria-label="<?php esc_attr_e( 'Previous textures', 'vesora' ); ?>" data-shop-previous><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>
				<div class="shop-discovery__track" data-shop-track>
					<?php foreach ( $texture_cards as $card ) : ?>
						<article class="shop-discovery__card">
							<img src="<?php echo esc_url( $card['image']['url'] ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s texture', 'vesora' ), $card['name'] ) ); ?>" width="<?php echo esc_attr( $card['image']['width'] ); ?>" height="<?php echo esc_attr( $card['image']['height'] ); ?>" loading="lazy" decoding="async">
							<span class="shop-discovery__card-copy"><strong><?php echo esc_html( $card['name'] ); ?></strong><span><?php esc_html_e( 'Explore texture', 'vesora' ); ?></span></span>
						</article>
					<?php endforeach; ?>
				</div>
				<button class="shop-discovery__arrow shop-discovery__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next textures', 'vesora' ); ?>" data-shop-next><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>
			</div>

			<div id="shop-color-panel" class="shop-discovery__panel" role="tabpanel" aria-labelledby="shop-color-tab" data-shop-panel="color" hidden>
				<button class="shop-discovery__arrow shop-discovery__arrow--previous" type="button" aria-label="<?php esc_attr_e( 'Previous colors', 'vesora' ); ?>" data-shop-previous><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>
				<div class="shop-discovery__track" data-shop-track>
					<?php foreach ( $color_cards as $card ) : ?>
						<article class="shop-discovery__card">
							<img src="<?php echo esc_url( $card['image']['url'] ); ?>" alt="<?php echo esc_attr( sprintf( __( '%1$s hair color %2$s', 'vesora' ), $card['name'], $card['code'] ) ); ?>" width="<?php echo esc_attr( $card['image']['width'] ); ?>" height="<?php echo esc_attr( $card['image']['height'] ); ?>" loading="lazy" decoding="async">
							<span class="shop-discovery__card-copy"><strong><?php echo esc_html( $card['name'] ); ?></strong><span><?php echo esc_html( $card['code'] ); ?></span></span>
						</article>
					<?php endforeach; ?>
				</div>
				<button class="shop-discovery__arrow shop-discovery__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next colors', 'vesora' ); ?>" data-shop-next><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>
			</div>
		</div>
	</section>

	<section class="category-showcase" aria-labelledby="collections-title">
		<div class="shell">
			<header class="category-showcase__heading reveal">
				<h2 id="collections-title"><span><?php esc_html_e( 'Vesora Hair', 'vesora' ); ?></span> <?php esc_html_e( 'Category', 'vesora' ); ?></h2>
			</header>
			<div class="category-accordion reveal">
				<?php foreach ( array_chunk( $categories, 5 ) as $row ) : ?>
					<div class="category-accordion__row">
						<?php foreach ( $row as $category ) : ?>
							<a class="category-card" href="<?php echo esc_url( vesora_product_category_url( $category['slug'] ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s category', 'vesora' ), $category['name'] ) ); ?>">
								<img src="<?php echo esc_url( $category['image']['url'] ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s Vietnamese human hair', 'vesora' ), $category['name'] ) ); ?>" width="<?php echo esc_attr( $category['image']['width'] ); ?>" height="<?php echo esc_attr( $category['image']['height'] ); ?>" loading="lazy" decoding="async">
								<span class="category-card__content">
									<strong><?php echo esc_html( $category['name'] ); ?></strong>
									<span><?php esc_html_e( 'See more', 'vesora' ); ?></span>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section best-sellers" aria-labelledby="best-sellers-title">
		<div class="shell">
			<div class="brand-divider reveal" aria-hidden="true">
				<span class="brand-divider__line"></span>
				<span class="brand-divider__mark">
					<img src="<?php echo esc_url( $asset_uri . 'logo-nau.png' ); ?>" alt="" width="500" height="500" decoding="async">
				</span>
				<span class="brand-divider__line"></span>
			</div>
			<header class="section-heading reveal">
				<h2 id="best-sellers-title"><span><?php esc_html_e( 'Vesora Hair', 'vesora' ); ?></span> <?php esc_html_e( 'Best Selling', 'vesora' ); ?></h2>
			</header>
			<?php $has_products = class_exists( 'WooCommerce' ) && ! empty( wc_get_products( array( 'limit' => 1, 'status' => 'publish', 'return' => 'ids' ) ) ); ?>
			<?php if ( $has_products ) : ?>
				<div class="best-sellers__carousel" data-product-carousel>
					<button class="shop-discovery__arrow shop-discovery__arrow--previous" type="button" aria-label="<?php esc_attr_e( 'Previous best sellers', 'vesora' ); ?>" data-product-previous><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>
					<?php echo do_shortcode( '[products limit="8" columns="4" category="best-selling-products" orderby="date" order="ASC" visibility="visible"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<button class="shop-discovery__arrow shop-discovery__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next best sellers', 'vesora' ); ?>" data-product-next><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>
				</div>
			<?php else : ?>
				<div class="empty-state">
					<h3><?php esc_html_e( 'The collection is being prepared', 'vesora' ); ?></h3>
					<p><?php esc_html_e( 'Ask our team for the current catalog, colors and wholesale options.', 'vesora' ); ?></p>
					<a class="button button--secondary" href="#wholesale"><?php esc_html_e( 'Request wholesale', 'vesora' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( $feedback_images ) : ?>
		<div class="customer-feedback-divider" aria-hidden="true">
			<div class="shell">
				<div class="brand-divider reveal">
					<span class="brand-divider__line"></span>
					<span class="brand-divider__mark">
						<img src="<?php echo esc_url( $asset_uri . 'logo-nau.png' ); ?>" alt="" width="500" height="500" loading="lazy" decoding="async">
					</span>
					<span class="brand-divider__line"></span>
				</div>
			</div>
		</div>
		<section class="section customer-feedback" id="customer-feedback" aria-labelledby="customer-feedback-title">
			<div class="shell">
				<header class="customer-feedback__heading reveal">
					<p class="eyebrow"><?php esc_html_e( 'Feedback from our partners', 'vesora' ); ?></p>
					<h2 id="customer-feedback-title"><?php esc_html_e( 'Happy', 'vesora' ); ?> <span><?php esc_html_e( 'Customers', 'vesora' ); ?></span></h2>
				</header>
				<div class="customer-feedback__carousel reveal" role="region" aria-label="<?php esc_attr_e( 'Customer feedback gallery', 'vesora' ); ?>" data-feedback-carousel>
					<button class="customer-feedback__arrow customer-feedback__arrow--previous" type="button" aria-label="<?php esc_attr_e( 'Previous feedback', 'vesora' ); ?>" data-feedback-previous><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>
					<div class="customer-feedback__track" data-feedback-track>
						<?php foreach ( $feedback_images as $image ) : ?>
							<figure class="customer-feedback__item<?php echo $image['landscape'] ? ' is-landscape' : ''; ?>">
								<?php echo wp_get_attachment_image( $image['id'], 'large', false, array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => $image['alt'] ? $image['alt'] : __( 'Customer feedback for Vesora Hair', 'vesora' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</figure>
						<?php endforeach; ?>
					</div>
					<button class="customer-feedback__arrow customer-feedback__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next feedback', 'vesora' ); ?>" data-feedback-next><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="vesora-experience" id="vesora-experience" aria-labelledby="vesora-experience-title">
		<div class="vesora-experience__intro">
			<div class="shell vesora-experience__intro-grid">
				<figure class="vesora-experience__team-photo reveal">
					<img src="<?php echo esc_url( $asset_uri . 'vesorahair-new-12102026.webp' ); ?>" alt="<?php esc_attr_e( 'Vesora Hair experience specialists with Vietnamese hair extension collections', 'vesora' ); ?>" width="1536" height="1024" loading="lazy" decoding="async">
				</figure>

				<header class="vesora-experience__header reveal">
					<h2 id="vesora-experience-title"><span><?php esc_html_e( 'VESORA HAIR', 'vesora' ); ?></span> <?php esc_html_e( 'Experience Specialist', 'vesora' ); ?></h2>
					<p><?php esc_html_e( 'Our sales team boasts extensive expertise in the hair extension industry, accumulated over many years. We are dedicated to providing customer support, available round-the-clock, to ensure our customers consistently receive exceptional service and top-quality products. Should any concerns arise, either before or after purchase, our sales team stands ready to provide prompt and effective assistance!', 'vesora' ); ?></p>
				</header>
			</div>
		</div>

		<?php if ( $sales_profiles && function_exists( 'vesora_sales_team_cards' ) ) : ?>
			<div class="shell vesora-experience__profiles reveal" role="region" aria-label="<?php esc_attr_e( 'Vesora Hair sales specialists', 'vesora' ); ?>" data-specialist-carousel>
				<button class="vesora-experience__arrow vesora-experience__arrow--previous" type="button" aria-label="<?php esc_attr_e( 'Previous sales specialist', 'vesora' ); ?>" data-specialist-previous><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>
				<div class="vesora-experience__viewport" data-specialist-viewport>
					<?php echo vesora_sales_team_cards( $sales_profiles ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<button class="vesora-experience__arrow vesora-experience__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next sales specialist', 'vesora' ); ?>" data-specialist-next><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>
			</div>
		<?php endif; ?>
	</section>

	<section class="section certifications" id="vesora-certifications" aria-labelledby="vesora-certifications-title">
		<div class="shell">
			<div class="brand-divider certifications__divider" aria-hidden="true">
				<span class="brand-divider__line"></span>
				<span class="brand-divider__mark">
					<img src="<?php echo esc_url( $asset_uri . 'logo-nau.png' ); ?>" alt="" width="500" height="500" loading="lazy" decoding="async">
				</span>
				<span class="brand-divider__line"></span>
			</div>

			<header class="certifications__heading reveal">
				<h2 id="vesora-certifications-title"><?php esc_html_e( "VESORA's International Certifications", 'vesora' ); ?></h2>
				<h3><?php esc_html_e( 'Quality Standards Verified for Manufacturing and Trading Hairs at VESORA', 'vesora' ); ?></h3>
				<p><?php esc_html_e( "Trust VESORA's internationally certified Manufacturing and Trading Hairs with ISO 9001:2015 Quality Management and SA 8000:2014 Social Accountability standards. These certifications guarantee consistent quality and ethical sourcing for every wholesale order.", 'vesora' ); ?></p>
			</header>

			<div class="certifications__grid reveal" role="list" aria-label="<?php esc_attr_e( 'Vesora Hair international certificates', 'vesora' ); ?>">
				<figure class="certifications__item" role="listitem">
					<img src="<?php echo esc_url( $asset_uri . 'certifications/vesora-sa-8000-2014.jpg' ); ?>" alt="<?php esc_attr_e( 'Vesora Hair SA 8000:2014 Social Accountability Management System certificate', 'vesora' ); ?>" width="600" height="849" loading="lazy" decoding="async">
				</figure>
				<figure class="certifications__item" role="listitem">
					<img src="<?php echo esc_url( $asset_uri . 'certifications/vesora-iso-9001-2015-en.jpg' ); ?>" alt="<?php esc_attr_e( 'Vesora Hair ISO 9001:2015 Quality Management System certificate in English', 'vesora' ); ?>" width="600" height="849" loading="lazy" decoding="async">
				</figure>
				<figure class="certifications__item" role="listitem">
					<img src="<?php echo esc_url( $asset_uri . 'certifications/vesora-iso-9001-2015-vi.jpg' ); ?>" alt="<?php esc_attr_e( 'Vesora Hair ISO 9001:2015 Quality Management System certificate in Vietnamese', 'vesora' ); ?>" width="600" height="849" loading="lazy" decoding="async">
				</figure>
			</div>
		</div>
	</section>

	<section class="section social-gallery" id="vesora-social" aria-labelledby="vesora-social-title">
		<div class="shell">
			<div class="brand-divider social-gallery__divider" aria-hidden="true">
				<span class="brand-divider__line"></span>
				<span class="brand-divider__mark">
					<img src="<?php echo esc_url( $asset_uri . 'logo-nau.png' ); ?>" alt="" width="500" height="500" loading="lazy" decoding="async">
				</span>
				<span class="brand-divider__line"></span>
			</div>
			<header class="social-gallery__heading reveal">
				<h2 id="vesora-social-title"><span class="social-gallery__title-brand"><?php esc_html_e( "Vesora Hair's", 'vesora' ); ?></span> <span class="social-gallery__title-label"><?php esc_html_e( 'Social', 'vesora' ); ?></span></h2>
			</header>
			<div class="social-gallery__viewport" role="region" aria-label="<?php esc_attr_e( 'Vesora Hair social gallery', 'vesora' ); ?>">
				<div class="social-gallery__track">
					<?php foreach ( $social_images as $index => $image ) : ?>
						<figure class="social-gallery__item social-gallery__item--<?php echo esc_attr( $index + 1 ); ?>">
							<img src="<?php echo esc_url( $asset_uri . $image['file'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>" loading="lazy" decoding="async">
						</figure>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

</main>
<?php get_footer(); ?>
