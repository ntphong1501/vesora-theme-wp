<?php
/** Compact category navigation and contact presentation. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vesora_render_desktop_categories() {
	$groups = vesora_mobile_menu_groups();
	$groups['weft']['items']['single-layer-silky-flat-weft'] = __( 'Single Layer Silky Flat Weft', 'vesora' );
	$groups['weft']['items']['double-layer-silky-flat-weft'] = __( 'Double Layer Silky Flat Weft', 'vesora' );
	$groups = array_merge(
		array( 'bulk' => array( 'label' => __( 'Bulk Hair Extensions', 'vesora' ), 'slug' => 'bulk-hair-extensions', 'items' => array() ) ),
		$groups,
		array(
			'halo' => array( 'label' => __( 'Halo Hair Extensions', 'vesora' ), 'slug' => 'halo-hair-extensions', 'items' => array() ),
			'accessories' => array( 'label' => __( 'Hair Accessories', 'vesora' ), 'slug' => 'hair-accessories', 'items' => array() ),
			'wigs' => array( 'label' => __( 'Vietnamese Wigs', 'vesora' ), 'slug' => 'vietnamese-hair-wigs', 'items' => array(
				'full-lace-wigs' => __( 'Full Lace Wigs', 'vesora' ), 'lace-closure-wigs' => __( 'Lace Closure Wigs', 'vesora' ), 'lace-frontal-wigs' => __( 'Lace Frontal Wigs', 'vesora' ),
			) ),
		)
	);
	?>
	<div class="mega-menu category-dropdown" id="hair-extensions-menu" data-submenu>
		<ul class="category-dropdown__list">
			<?php foreach ( $groups as $key => $group ) : ?>
			<li class="category-dropdown__item">
				<div class="category-dropdown__row">
					<a href="<?php echo esc_url( vesora_product_category_url( $group['slug'] ) ); ?>"<?php echo 'bulk' === $key ? ' class="category-dropdown__priority"' : ''; ?>><?php echo esc_html( $group['label'] ); ?></a>
					<?php if ( $group['items'] ) : ?><button type="button" data-category-toggle aria-expanded="false" aria-controls="desktop-category-<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show subcategories: %s', 'vesora' ), $group['label'] ) ); ?>"><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button><?php endif; ?>
				</div>
				<?php if ( $group['items'] ) : ?>
				<ul class="category-dropdown__children" id="desktop-category-<?php echo esc_attr( $key ); ?>" hidden>
					<?php foreach ( $group['items'] as $slug => $label ) : ?>
					<li><a href="<?php echo esc_url( vesora_product_category_url( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

// Rename the existing page without changing its URL or editable content.
add_action( 'init', static function () {
	if ( get_option( 'vesora_contact_title_v224' ) ) { return; }
	$page = get_page_by_path( 'contact', OBJECT, 'page' );
	if ( ! $page ) { return; }
	if ( in_array( strtolower( trim( $page->post_title ) ), array( 'contact', 'contact us' ), true ) ) {
		$result = wp_update_post( array( 'ID' => $page->ID, 'post_title' => 'Contact us' ), true );
		if ( is_wp_error( $result ) ) { return; }
	}
	update_option( 'vesora_contact_title_v224', 1 );
}, 80 );
