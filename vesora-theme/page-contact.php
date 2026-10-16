<?php
/** Contact page. Contact details remain managed in the existing admin settings. */
get_header();
?>
<main id="main-content" class="contact-page">
	<section class="contact-page__overview shell">
		<?php while ( have_posts() ) : the_post(); ?>
			<header class="contact-page__heading"><h1><?php the_title(); ?></h1></header>
			<div class="contact-page__content">
				<?php the_content(); ?>
				<?php if ( ! has_shortcode( get_the_content(), 'vesora_contact_details' ) ) { echo vesora_contact_details_shortcode(); } ?>
			</div>
		<?php endwhile; ?>
	</section>
</main>
<?php get_footer(); ?>
