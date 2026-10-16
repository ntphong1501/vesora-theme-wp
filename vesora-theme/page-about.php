<?php
/**
 * About Vesora page template.
 *
 * @package Vesora
 */

get_header();

$image_uri = trailingslashit( get_template_directory_uri() ) . 'assets/images/';
?>

<main id="main-content" class="about-page">
	<section class="about-hero" aria-label="<?php esc_attr_e( 'Vesora Hair factory', 'vesora' ); ?>">
		<img
			src="<?php echo esc_url( $image_uri . 'About-Vesora-Banner.png' ); ?>"
			alt="<?php esc_attr_e( 'Vesora Hair factory, No.1 Vietnamese hair extensions', 'vesora' ); ?>"
			width="1891"
			height="831"
			fetchpriority="high"
			decoding="async"
		>
	</section>

	<section class="about-intro">
		<div class="about-intro__inner shell reveal">
			<h1><?php esc_html_e( 'We Make Your Hair Dreams Come True', 'vesora' ); ?></h1>
			<p><?php esc_html_e( 'Vesora Hair supplies Vietnamese hair extensions and wigs to partner salons and hair vendors in more than 100 countries. We help our partners grow with dependable quality, responsive service and sustainable hair supplies.', 'vesora' ); ?></p>
		</div>
	</section>

	<section class="about-story shell" aria-labelledby="about-story-title">
		<div class="about-story__content reveal">
			<h2 id="about-story-title"><?php esc_html_e( 'The Journey of Vesora in the Global Hair Supply Industry', 'vesora' ); ?></h2>
			<p><?php esc_html_e( 'In the early 2000s, a visionary initiative was born out of a genuine desire to deliver the finest Vietnamese hair to customers worldwide. Thus, Vesora was established by delivering excellence and a passion for quality that set it apart from the outset. Despite facing challenges along its journey, Vesora has not only persevered but thrived, carving a distinguished path in the realm of hair supply.', 'vesora' ); ?></p>
			<p><?php esc_html_e( 'Navigating through the ebbs and flows of the industry, Vesora Hair has honed its expertise and fortified its position as a premier purveyor of Vietnamese hair. Drawing from two decades of invaluable experience, Vesora stands as a beacon of innovation and reliability in the dynamic landscape of hair supply.', 'vesora' ); ?></p>
		</div>
		<figure class="about-story__media reveal reveal--delay">
			<img
				src="<?php echo esc_url( $image_uri . 'About-Vesora-2.png' ); ?>"
				alt="<?php esc_attr_e( 'Vesora Vietnamese hair collections prepared for quality inspection', 'vesora' ); ?>"
				width="1586"
				height="992"
				loading="lazy"
				decoding="async"
			>
		</figure>
	</section>

	<section class="about-difference" aria-labelledby="about-difference-title">
		<img
			class="about-difference__image"
			src="<?php echo esc_url( $image_uri . 'banner-tape-in-page-wholesale.png' ); ?>"
			alt=""
			width="2048"
			height="890"
			loading="lazy"
			decoding="async"
			aria-hidden="true"
		>
		<div class="about-difference__content shell reveal">
			<h2 id="about-difference-title"><?php esc_html_e( 'What Makes Vesora Hair Different?', 'vesora' ); ?></h2>
			<p><?php esc_html_e( 'Vesora is committed to excellence, delivering top-grade products alongside exceptional customer service. Our hair extension products are painstakingly crafted using ethically sourced, first-class quality hair obtained from healthy donors. In addition to our superior product offerings, Vesora Hair is renowned for customer service and policies. We pride ourselves on providing round-the-clock support to promptly address any customer inquiries or concerns.', 'vesora' ); ?></p>
		</div>
	</section>

	<section class="about-partners shell" aria-labelledby="about-partners-title">
		<figure class="about-partners__media reveal">
			<img
				src="<?php echo esc_url( $image_uri . 'salon-vesora.png' ); ?>"
				alt="<?php esc_attr_e( 'Vesora hair extensions presented in professional salons', 'vesora' ); ?>"
				width="1254"
				height="1254"
				loading="lazy"
				decoding="async"
			>
		</figure>
		<div class="about-partners__content reveal reveal--delay">
			<h2 id="about-partners-title"><?php esc_html_e( 'Exclusively at Fine Salons and Wholesale Vendors', 'vesora' ); ?></h2>
			<p><?php esc_html_e( 'Vesora Hair is dedicated to quality, ensuring that our products stand out amongst the crowd. We carefully curate our offerings, selecting only the finest ingredients and employing the most advanced techniques to create haircare solutions that exceed expectations.', 'vesora' ); ?></p>
			<p><?php esc_html_e( 'For this reason, salons and wholesale vendors in more than 100 countries have become our long-term partners. When visiting Vesora partner salons or vendors, you can trust that you are receiving nothing but the best.', 'vesora' ); ?></p>
		</div>
	</section>
</main>

<?php get_footer(); ?>
