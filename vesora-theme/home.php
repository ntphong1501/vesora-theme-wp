<?php
/**
 * Vesora blog index and category archive.
 *
 * @package Vesora
 */
get_header();

$blog_title = is_category() ? single_cat_title( '', false ) : __( 'Vesora Hair Blogs', 'vesora' );
$blog_copy  = is_category() && category_description() ? wp_strip_all_tags( category_description() ) : __( 'Expert guidance for salons, hair professionals and wholesale buyers, from extension methods and care to sourcing Vietnamese human hair.', 'vesora' );
$categories = array_values(
	array_filter(
		get_categories( array( 'hide_empty' => false ) ),
		static function ( $category ) {
			return 'uncategorized' !== $category->slug;
		}
	)
);
?>
<main id="main-content" class="blog-index">
	<header class="blog-hero">
		<div class="shell blog-hero__inner">
			<p class="eyebrow"><?php esc_html_e( 'Knowledge for better hair decisions', 'vesora' ); ?></p>
			<h1><?php echo esc_html( $blog_title ); ?></h1>
			<p><?php echo esc_html( $blog_copy ); ?></p>
		</div>
	</header>

	<nav class="blog-topics" aria-label="<?php esc_attr_e( 'Blog topics', 'vesora' ); ?>">
		<div class="shell blog-topics__track">
			<a<?php echo is_home() ? ' class="is-current" aria-current="page"' : ''; ?> href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ); ?>"><?php esc_html_e( 'All topics', 'vesora' ); ?></a>
			<?php foreach ( $categories as $category ) : ?>
				<a<?php echo is_category( $category->term_id ) ? ' class="is-current" aria-current="page"' : ''; ?> href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
			<?php endforeach; ?>
		</div>
	</nav>

	<section class="shell blog-results" aria-label="<?php esc_attr_e( 'Blog articles', 'vesora' ); ?>">
		<?php if ( have_posts() ) : ?>
			<div class="blog-list">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php $card_cover = vesora_blog_cover_url( get_the_ID(), 'large' ); ?>
					<article <?php post_class( 'blog-list__item' ); ?>>
						<a class="blog-list__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
							<?php if ( $card_cover ) : ?><img src="<?php echo esc_url( $card_cover ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" decoding="async"><?php else : ?><span class="blog-card__placeholder" aria-hidden="true"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-nau.png' ); ?>" alt=""></span><?php endif; ?>
						</a>
						<div class="blog-list__content">
							<p class="blog-list__category"><?php the_category( ', ' ); ?></p>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="blog-list__meta"><?php echo esc_html( get_the_date() ); ?> <span aria-hidden="true">•</span> <?php printf( esc_html__( 'Comments: %s', 'vesora' ), esc_html( get_comments_number() ) ); ?></p>
							<p class="blog-list__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) ); ?></p>
							<a class="blog-list__button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View More', 'vesora' ); ?> <span aria-hidden="true">›</span></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => __( 'Previous', 'vesora' ), 'next_text' => __( 'Next', 'vesora' ) ) ); ?>
		<?php else : ?>
			<div class="blog-empty">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-nau.png' ); ?>" alt="" width="500" height="500">
				<h2><?php esc_html_e( 'The Vesora journal is being prepared', 'vesora' ); ?></h2>
				<p><?php esc_html_e( 'Our specialists will share sourcing guidance, hair knowledge and professional care advice here.', 'vesora' ); ?></p>
			</div>
		<?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>

