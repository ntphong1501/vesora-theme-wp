<?php
/**
 * Default content template.
 *
 * @package Vesora
 */
get_header();
?>
<main id="main-content" class="content-shell shell">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'entry' ); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<section class="empty-state">
			<h1><?php esc_html_e( 'Nothing here yet', 'vesora' ); ?></h1>
			<p><?php esc_html_e( 'Try a new search or return to the collection.', 'vesora' ); ?></p>
			<a class="button" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Browse hair', 'vesora' ); ?></a>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>

