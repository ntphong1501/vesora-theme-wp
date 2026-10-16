<?php
/**
 * Single editorial article.
 *
 * @package Vesora
 */
get_header();
?>
<main id="main-content" class="blog-single">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'blog-article' ); ?>>
			<header class="blog-article__header">
				<div class="shell blog-article__header-inner">
					<nav class="blog-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'vesora' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'vesora' ); ?></a>
						<span aria-hidden="true">›</span>
						<a href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ); ?>"><?php esc_html_e( 'Blogs', 'vesora' ); ?></a>
					</nav>
					<h1><?php the_title(); ?></h1>
					<div class="blog-article__meta">
						<span><?php echo esc_html( get_the_author() ); ?></span>
						<span aria-hidden="true">•</span>
						<span><?php printf( esc_html__( 'Last updated %s', 'vesora' ), esc_html( get_the_modified_date() ) ); ?></span>
					</div>
				</div>
			</header>
			<?php if ( has_excerpt() ) : ?><p class="blog-article__dek"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			<div class="blog-article__content entry-content"><?php the_content(); ?></div>
			<footer class="shell blog-article__footer">
				<?php the_post_navigation( array( 'prev_text' => '<span>' . esc_html__( 'Previous article', 'vesora' ) . '</span><strong>%title</strong>', 'next_text' => '<span>' . esc_html__( 'Next article', 'vesora' ) . '</span><strong>%title</strong>' ) ); ?>
			</footer>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>

