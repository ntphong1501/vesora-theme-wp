<?php
/**
 * WooCommerce wrapper.
 *
 * @package Vesora
 */
get_header();
?>
<main id="main-content" class="content-shell shell woocommerce-shell">
	<?php if ( is_singular( 'product' ) ) : ?>
		<?php woocommerce_breadcrumb(); woocommerce_content(); ?>
	<?php else : ?>
		<?php woocommerce_breadcrumb(); ?>
		<header class="vesora-catalog-heading"><h1><?php woocommerce_page_title(); ?></h1></header>
		<div class="vesora-catalog-toolbar">
			<?php woocommerce_result_count(); ?>
			<?php vesora_catalog_ordering(); ?>
		</div>
		<div class="vesora-catalog-layout">
			<?php vesora_catalog_filters(); ?>
			<div class="vesora-catalog-results">
				<?php vesora_render_product_category_banner(); ?>
				<div class="vesora-catalog-intro"><?php do_action( 'woocommerce_archive_description' ); ?></div>
				<?php
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
				if ( woocommerce_product_loop() ) {
					do_action( 'woocommerce_before_shop_loop' );
					woocommerce_product_loop_start();
					while ( have_posts() ) {
						the_post();
						do_action( 'woocommerce_shop_loop' );
						wc_get_template_part( 'content', 'product' );
					}
					woocommerce_product_loop_end();
					do_action( 'woocommerce_after_shop_loop' );
				} else {
					do_action( 'woocommerce_no_products_found' );
				}
				vesora_catalog_long_description();
				?>
			</div>
		</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>

