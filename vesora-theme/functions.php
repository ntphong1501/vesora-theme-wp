<?php
/**
 * Theme setup and storefront behavior.
 *
 * @package Vesora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VESORA_VERSION', '2.25.1' );

/**
 * Keep the local Docker showroom accessible to incognito and unauthenticated visitors.
 * Production continues to respect WooCommerce's Site visibility setting.
 */
function vesora_local_preview_exclude_coming_soon( $is_excluded ) {
	if ( 'local' === wp_get_environment_type() ) {
		return true;
	}

	return $is_excluded;
}
add_filter( 'woocommerce_coming_soon_exclude', 'vesora_local_preview_exclude_coming_soon', 999 );

require_once get_template_directory() . '/inc/homepage-images.php';
require_once get_template_directory() . '/inc/contact-settings.php';
require_once get_template_directory() . '/inc/sales-team.php';
require_once get_template_directory() . '/inc/catalog-sync.php';
require_once get_template_directory() . '/inc/leads.php';
require_once get_template_directory() . '/inc/blog.php';
require_once get_template_directory() . '/inc/catalog-ui.php';
require_once get_template_directory() . '/inc/admin-workspace.php';
require_once get_template_directory() . '/inc/storefront-layout.php';
require_once get_template_directory() . '/inc/loading.php';

function vesora_setup() {
	load_theme_textdomain( 'vesora', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 96, 'width' => 320, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'vesora' ),
			'footer'  => __( 'Footer navigation', 'vesora' ),
		)
	);
}
add_action( 'after_setup_theme', 'vesora_setup' );

function vesora_assets() {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'flaticon-uicons-brands', 'https://cdn-uicons.flaticon.com/4.0.0/uicons-brands/css/uicons-brands.css', array(), '4.0.0' );
	wp_enqueue_style( 'vesora-theme', get_template_directory_uri() . '/assets/css/theme.css', array(), VESORA_VERSION );
	wp_enqueue_style( 'vesora-catalog', get_template_directory_uri() . '/assets/css/catalog.css', array( 'vesora-theme' ), VESORA_VERSION );
	wp_enqueue_style( 'vesora-storefront-layout', get_template_directory_uri() . '/assets/css/storefront-layout.css', array( 'vesora-catalog' ), VESORA_VERSION );
	wp_enqueue_style( 'vesora-popup-readable', get_template_directory_uri() . '/assets/css/popup-readable.css', array( 'vesora-storefront-layout' ), VESORA_VERSION );
	if ( is_page( 'contact' ) ) {
		wp_enqueue_style( 'flaticon-uicons-regular-rounded', 'https://cdn-uicons.flaticon.com/4.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css', array(), '4.0.0' );
	}
	wp_enqueue_script( 'vesora-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), VESORA_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'vesora_assets' );

function vesora_configure_product_search( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$query->set( 'post_type', 'product' );
	$query->set( 'post_status', 'publish' );
	$query->set( 'posts_per_page', 12 );
}
add_action( 'pre_get_posts', 'vesora_configure_product_search' );

function vesora_normalize_product_search_url() {
	if ( is_admin() || ! is_search() || '' === get_search_query() ) {
		return;
	}

	$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'product' === $post_type ) {
		return;
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				's'         => get_search_query(),
				'post_type' => 'product',
			),
			home_url( '/' )
		)
	);
	exit;
}
add_action( 'template_redirect', 'vesora_normalize_product_search_url', 1 );

function vesora_extend_product_search( $search, $query ) {
	global $wpdb;

	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() || 'product' !== $query->get( 'post_type' ) ) {
		return $search;
	}

	$terms = $query->get( 'search_terms' );
	if ( empty( $terms ) ) {
		$phrase = trim( (string) $query->get( 's' ) );
		$terms  = '' !== $phrase ? preg_split( '/\s+/', $phrase ) : array();
	}

	$terms = array_slice( array_filter( array_map( 'sanitize_text_field', (array) $terms ) ), 0, 10 );
	if ( empty( $terms ) ) {
		return $search;
	}

	$groups = array();
	foreach ( $terms as $term ) {
		$like = '%' . $wpdb->esc_like( $term ) . '%';
		$groups[] = $wpdb->prepare(
			"(
				{$wpdb->posts}.post_title LIKE %s
				OR {$wpdb->posts}.post_excerpt LIKE %s
				OR {$wpdb->posts}.post_content LIKE %s
				OR EXISTS (
					SELECT 1 FROM {$wpdb->postmeta} vesora_search_meta
					WHERE vesora_search_meta.post_id = {$wpdb->posts}.ID
					AND vesora_search_meta.meta_key IN ('_sku', '_product_attributes')
					AND vesora_search_meta.meta_value LIKE %s
				)
				OR EXISTS (
					SELECT 1
					FROM {$wpdb->term_relationships} vesora_search_relationships
					INNER JOIN {$wpdb->term_taxonomy} vesora_search_taxonomy ON vesora_search_taxonomy.term_taxonomy_id = vesora_search_relationships.term_taxonomy_id
					INNER JOIN {$wpdb->terms} vesora_search_terms ON vesora_search_terms.term_id = vesora_search_taxonomy.term_id
					WHERE vesora_search_relationships.object_id = {$wpdb->posts}.ID
					AND (vesora_search_taxonomy.taxonomy IN ('product_cat', 'product_tag') OR vesora_search_taxonomy.taxonomy LIKE 'pa_%%')
					AND (vesora_search_terms.name LIKE %s OR vesora_search_terms.slug LIKE %s)
				)
				OR EXISTS (
					SELECT 1
					FROM {$wpdb->term_relationships} vesora_parent_relationships
					INNER JOIN {$wpdb->term_taxonomy} vesora_child_taxonomy ON vesora_child_taxonomy.term_taxonomy_id = vesora_parent_relationships.term_taxonomy_id
					INNER JOIN {$wpdb->terms} vesora_parent_terms ON vesora_parent_terms.term_id = vesora_child_taxonomy.parent
					WHERE vesora_parent_relationships.object_id = {$wpdb->posts}.ID
					AND vesora_child_taxonomy.taxonomy = 'product_cat'
					AND (vesora_parent_terms.name LIKE %s OR vesora_parent_terms.slug LIKE %s)
				)
			)",
			$like,
			$like,
			$like,
			$like,
			$like,
			$like,
			$like,
			$like
		);
	}

	$search = ' AND (' . implode( ' AND ', $groups ) . ')';
	if ( ! is_user_logged_in() ) {
		$search .= " AND ({$wpdb->posts}.post_password = '')";
	}

	return $search;
}
add_filter( 'posts_search', 'vesora_extend_product_search', 20, 2 );

function vesora_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'vesora_home',
		array(
			'title'    => __( 'Vesora homepage', 'vesora' ),
			'priority' => 30,
		)
	);

	$settings = array(
		'hero_title' => array(
			'label'   => __( 'Hero title', 'vesora' ),
			'default' => __( 'Vietnamese hair, made to scale.', 'vesora' ),
		),
		'hero_copy' => array(
			'label'   => __( 'Hero description', 'vesora' ),
			'default' => __( 'Factory-direct extensions with consistent texture, custom color and dependable worldwide support.', 'vesora' ),
		),
	);

	foreach ( $settings as $key => $config ) {
		$wp_customize->add_setting(
			'vesora_' . $key,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'vesora_' . $key,
			array(
				'label'   => $config['label'],
				'section' => 'vesora_home',
				'type'    => 'textarea',
			)
		);
	}
}
add_action( 'customize_register', 'vesora_customize_register' );

function vesora_catalog_categories() {
	return array(
		'hair-extensions'                    => array( 'name' => 'Vietnamese Hair Extensions' ),
		'weft-hair-extensions'               => array( 'name' => 'Weft Hair Extensions', 'parent' => 'hair-extensions' ),
		'butterfly-weft-hair-extensions'     => array( 'name' => 'Butterfly Weft Hair Extensions', 'parent' => 'weft-hair-extensions' ),
		'genius-weft-hair'                   => array( 'name' => 'Genius Weft Hair Extensions', 'parent' => 'weft-hair-extensions' ),
		'hand-tied-weft-extensions'          => array( 'name' => 'Hand Tied Weft Extensions', 'parent' => 'weft-hair-extensions' ),
		'machine-weft-hair'                  => array( 'name' => 'Machine Weft Hair Extensions', 'parent' => 'weft-hair-extensions' ),
		'flat-weft-hair-extensions'          => array( 'name' => 'Flat Weft Hair Extensions', 'parent' => 'weft-hair-extensions' ),
		'feather-weft-hair-extensions'       => array( 'name' => 'Feather Weft Hair Extensions', 'parent' => 'weft-hair-extensions' ),
		'single-layer-silky-flat-weft'       => array( 'name' => 'Single Layer Silky Flat Weft', 'parent' => 'weft-hair-extensions' ),
		'double-layer-silky-flat-weft'       => array( 'name' => 'Double Layer Silky Flat Weft', 'parent' => 'weft-hair-extensions' ),
		'keratin-hair'                       => array( 'name' => 'Keratin Hair Extensions', 'parent' => 'hair-extensions' ),
		'i-tip-extensions'                   => array( 'name' => 'I-Tip Extensions', 'parent' => 'keratin-hair' ),
		'u-tip-hair-extensions'              => array( 'name' => 'U Tip Hair Extensions', 'parent' => 'keratin-hair' ),
		'v-tip-hair-extensions'              => array( 'name' => 'V Tip Hair Extensions', 'parent' => 'keratin-hair' ),
		'y-tip-hair-extensions'              => array( 'name' => 'Y-Tip Hair Extensions', 'parent' => 'keratin-hair' ),
		'flat-tip-hair-extensions'           => array( 'name' => 'Flat Tip Hair Extensions', 'parent' => 'keratin-hair' ),
		'micro-ring-hair-extensions'         => array( 'name' => 'Micro Ring Hair Extensions', 'parent' => 'keratin-hair' ),
		'nano-ring-hair'                     => array( 'name' => 'Nano Ring Hair Extensions', 'parent' => 'keratin-hair' ),
		'plastic-nano-ring-hair-extensions'  => array( 'name' => 'Plastic Nano Ring Hair Extensions', 'parent' => 'keratin-hair' ),
		'tape-in-hair-extensions'            => array( 'name' => 'Tape In Hair Extensions', 'parent' => 'hair-extensions' ),
		'normal-tape-in'                     => array( 'name' => 'Normal Tape-in', 'parent' => 'tape-in-hair-extensions' ),
		'invisible-tape-in-extensions'       => array( 'name' => 'Invisible Tape In Extensions', 'parent' => 'tape-in-hair-extensions' ),
		'long-tape'                          => array( 'name' => 'Long Tape Extensions', 'parent' => 'tape-in-hair-extensions' ),
		'bulk-hair-extensions'               => array( 'name' => 'Bulk Hair Extensions', 'parent' => 'hair-extensions' ),
		'clip-in-hair-extensions'            => array( 'name' => 'Clip-in Hair Extensions', 'parent' => 'hair-extensions' ),
		'normal-clip-in'                     => array( 'name' => 'Normal Clip-in', 'parent' => 'clip-in-hair-extensions' ),
		'seamless-clip-in'                   => array( 'name' => 'Seamless Clip-in', 'parent' => 'clip-in-hair-extensions' ),
		'closure-frontal'                    => array( 'name' => 'Lace Closure and Frontal', 'parent' => 'hair-extensions' ),
		'lace-closure'                       => array( 'name' => 'Lace Closure', 'parent' => 'closure-frontal' ),
		'lace-frontal'                       => array( 'name' => 'Lace Frontal', 'parent' => 'closure-frontal' ),
		'ponytail-hair-extensions'           => array( 'name' => 'Ponytail Hair Extensions', 'parent' => 'hair-extensions' ),
		'wrap-ponytail-hair-extensions'      => array( 'name' => 'Wrap Ponytail Hair Extensions', 'parent' => 'ponytail-hair-extensions' ),
		'drawstring-ponytail-hair-extensions' => array( 'name' => 'Drawstring Ponytail Hair Extensions', 'parent' => 'ponytail-hair-extensions' ),
		'halo-hair-extensions'               => array( 'name' => 'Halo Hair Extensions', 'parent' => 'hair-extensions' ),
		'vietnamese-hair-wigs'               => array( 'name' => 'Vietnamese Hair Wigs' ),
		'full-lace-wigs'                     => array( 'name' => 'Full Lace Wigs', 'parent' => 'vietnamese-hair-wigs' ),
		'lace-closure-wigs'                  => array( 'name' => 'Lace Closure Wigs', 'parent' => 'vietnamese-hair-wigs' ),
		'lace-frontal-wigs'                  => array( 'name' => 'Lace Frontal Wigs', 'parent' => 'vietnamese-hair-wigs' ),
		'toupee'                             => array( 'name' => 'Toupee' ),
		'hair-accessories'                   => array( 'name' => 'Hair Accessories' ),
		'best-selling-products'              => array( 'name' => 'Best Selling Products' ),
	);
}

function vesora_ensure_product_categories() {
	if ( ! taxonomy_exists( 'product_cat' ) || '2.2' === get_option( 'vesora_catalog_version' ) ) {
		return;
	}

	$term_ids = array();
	foreach ( vesora_catalog_categories() as $slug => $config ) {
		$name      = $config['name'];
		$parent_id = ! empty( $config['parent'] ) && isset( $term_ids[ $config['parent'] ] ) ? $term_ids[ $config['parent'] ] : 0;
		$existing = term_exists( $slug, 'product_cat' );

		if ( $existing ) {
			$term_id = is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;
			$term    = get_term( $term_id, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) && ( $term->name !== $name || (int) $term->parent !== $parent_id ) ) {
				wp_update_term( $term_id, 'product_cat', array( 'name' => $name, 'parent' => $parent_id ) );
			}
			$term_ids[ $slug ] = $term_id;
			continue;
		}

		$created = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug, 'parent' => $parent_id ) );
		if ( ! is_wp_error( $created ) ) {
			$term_ids[ $slug ] = (int) $created['term_id'];
		}
	}

	update_option( 'vesora_catalog_version', '2.2' );
}
add_action( 'init', 'vesora_ensure_product_categories', 30 );

/**
 * Merge the legacy Mini Tape category into Long Tape once.
 *
 * The old slug is retained as a permanent redirect below so bookmarked and
 * indexed category URLs continue to work after the taxonomy migration.
 */
function vesora_migrate_mini_tape_to_long_tape() {
	if ( ! taxonomy_exists( 'product_cat' ) || '1.0.0' === get_option( 'vesora_long_tape_migration_version' ) ) {
		return;
	}

	$parent = get_term_by( 'slug', 'tape-in-hair-extensions', 'product_cat' );
	$mini   = get_term_by( 'slug', 'mini-tape-in-extensions', 'product_cat' );
	$long   = get_term_by( 'slug', 'long-tape', 'product_cat' );

	if ( ! $long || is_wp_error( $long ) ) {
		if ( ! $mini || is_wp_error( $mini ) ) {
			return;
		}

		$renamed = wp_update_term(
			$mini->term_id,
			'product_cat',
			array(
				'name'   => 'Long Tape Extensions',
				'slug'   => 'long-tape',
				'parent' => $parent && ! is_wp_error( $parent ) ? (int) $parent->term_id : 0,
			)
		);

		if ( is_wp_error( $renamed ) ) {
			return;
		}

		update_option( 'vesora_long_tape_migration_version', '1.0.0' );
		return;
	}

	$updated = wp_update_term(
		$long->term_id,
		'product_cat',
		array(
			'name'   => 'Long Tape Extensions',
			'parent' => $parent && ! is_wp_error( $parent ) ? (int) $parent->term_id : 0,
		)
	);
	if ( is_wp_error( $updated ) ) {
		return;
	}

	if ( $mini && ! is_wp_error( $mini ) ) {
		$product_ids = get_objects_in_term( (int) $mini->term_id, 'product_cat' );
		if ( is_wp_error( $product_ids ) ) {
			return;
		}

		foreach ( array_map( 'intval', $product_ids ) as $product_id ) {
			$assigned = wp_set_object_terms( $product_id, (int) $long->term_id, 'product_cat', true );
			if ( is_wp_error( $assigned ) ) {
				return;
			}
			wp_remove_object_terms( $product_id, (int) $mini->term_id, 'product_cat' );
		}

		$deleted = wp_delete_term( (int) $mini->term_id, 'product_cat' );
		if ( is_wp_error( $deleted ) || false === $deleted ) {
			return;
		}
	}

	update_option( 'vesora_long_tape_migration_version', '1.0.0' );
}
add_action( 'init', 'vesora_migrate_mini_tape_to_long_tape', 35 );

function vesora_redirect_legacy_mini_tape_category() {
	if ( is_admin() || 'mini-tape-in-extensions' !== get_query_var( 'product_cat' ) ) {
		return;
	}

	wp_safe_redirect( vesora_product_category_url( 'long-tape' ), 301 );
	exit;
}
add_action( 'template_redirect', 'vesora_redirect_legacy_mini_tape_category', 5 );

function vesora_product_category_url( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $term ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}

	return home_url( '/product-category/' . $slug . '/' );
}

function vesora_showroom_products() {
	return array(
		array( 'name' => 'White Blonde Normal Tape-in Hair', 'slug' => 'normal-tape-white-blonde-natural-straight', 'image' => 'normal-tape-white-blonde.jpg', 'categories' => array( 'normal-tape-in', 'best-selling-products' ), 'color' => 'White Blonde', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Natural Color Normal Tape-in Hair', 'slug' => 'normal-tape-natural-color-natural-straight', 'image' => 'normal-tape-natural.jpg', 'categories' => array( 'normal-tape-in', 'best-selling-products' ), 'color' => 'Natural Color', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Brown Normal Tape-in Hair', 'slug' => 'normal-tape-brown-natural-straight', 'image' => 'normal-tape-brown.jpg', 'categories' => array( 'normal-tape-in', 'best-selling-products' ), 'color' => 'Brown', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Natural Wavy Machine Weft Hair', 'slug' => 'machine-weft-natural-color-natural-wavy', 'image' => 'machine-weft-natural-wavy.webp', 'categories' => array( 'machine-weft-hair', 'best-selling-products' ), 'color' => 'Natural Color', 'texture' => 'Natural Wavy' ),
		array( 'name' => 'Natural Straight Machine Weft Hair', 'slug' => 'machine-weft-natural-color-natural-straight', 'image' => 'machine-weft-natural-straight.jpg', 'categories' => array( 'machine-weft-hair', 'best-selling-products' ), 'color' => 'Natural Color', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Light Blonde Machine Weft Hair', 'slug' => 'machine-weft-light-blonde-natural-straight', 'image' => 'machine-weft-light-blonde.jpg', 'categories' => array( 'machine-weft-hair', 'best-selling-products' ), 'color' => 'Light Blonde', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Natural Color Genius Weft Hair', 'slug' => 'genius-weft-natural-color-natural-straight', 'image' => 'genius-weft-natural.webp', 'categories' => array( 'genius-weft-hair', 'best-selling-products' ), 'color' => 'Natural Color', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Light Blonde Flat Tip Hair', 'slug' => 'flat-tip-light-blonde-natural-straight', 'image' => 'flat-tip-light-blonde.webp', 'categories' => array( 'flat-tip-hair-extensions', 'best-selling-products' ), 'color' => 'Light Blonde', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Natural Color Butterfly Weft Hair', 'slug' => 'butterfly-weft-natural-color-natural-straight', 'image' => 'butterfly-weft-natural.webp', 'categories' => array( 'butterfly-weft-hair-extensions', 'best-selling-products' ), 'color' => 'Natural Color', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Natural Color Bulk Hair', 'slug' => 'bulk-natural-color-natural-straight', 'image' => 'bulk-natural.jpg', 'categories' => array( 'bulk-hair-extensions', 'best-selling-products' ), 'color' => 'Natural Color', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Light Brown Bulk Hair', 'slug' => 'bulk-light-brown-natural-straight', 'image' => 'bulk-light-brown.jpg', 'categories' => array( 'bulk-hair-extensions', 'best-selling-products' ), 'color' => 'Light Brown', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Light Blonde Bulk Hair', 'slug' => 'bulk-light-blonde-natural-straight', 'image' => 'bulk-light-blonde.jpg', 'categories' => array( 'bulk-hair-extensions', 'best-selling-products' ), 'color' => 'Light Blonde', 'texture' => 'Natural Straight' ),
		array( 'name' => 'Brown Bulk Hair', 'slug' => 'bulk-brown-natural-straight', 'image' => 'bulk-brown.jpg', 'categories' => array( 'bulk-hair-extensions', 'best-selling-products' ), 'color' => 'Brown', 'texture' => 'Natural Straight' ),
	);
}

function vesora_import_product_image( $filename, $title ) {
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'meta_key'    => '_vesora_source_asset',
			'meta_value'  => $filename,
			'fields'      => 'ids',
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$source = get_template_directory() . '/assets/images/products/' . $filename;
	if ( ! file_exists( $source ) ) {
		return 0;
	}

	$upload = wp_upload_bits( $filename, null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );
	update_post_meta( $attachment_id, '_vesora_source_asset', $filename );
	return (int) $attachment_id;
}

function vesora_seed_showroom_catalog() {
	if ( ! class_exists( 'WC_Product_Simple' ) || '2.1.1' === get_option( 'vesora_showroom_catalog_version' ) ) {
		return;
	}

	$common_lengths   = array( '16 inch', '18 inch', '20 inch', '22 inch', '24 inch', '26 inch', '28 inch' );
	$common_standards = array( 'High Standard Double Drawn', 'Luxury Double Drawn Hair' );

	foreach ( vesora_showroom_products() as $index => $config ) {
		$existing = get_page_by_path( $config['slug'], OBJECT, 'product' );
		$product  = $existing ? wc_get_product( $existing->ID ) : new WC_Product_Simple();

		if ( ! $product ) {
			continue;
		}

		$product->set_name( $config['name'] );
		$product->set_slug( $config['slug'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_regular_price( '' );
		$product->set_sale_price( '' );
		$product->set_price( '' );
		$product->set_featured( true );
		$product->set_short_description( __( '100% Vietnamese human hair made for natural movement, reliable wear and professional customization.', 'vesora' ) );
		$product->set_description( __( 'Available for salon, retailer and wholesale enquiries. Contact Vesora for current color matching, length recommendations, custom packaging and order support.', 'vesora' ) );

		$attributes = array();
		foreach ( array( 'Standard' => $common_standards, 'Length' => $common_lengths, 'Color' => array( $config['color'] ), 'Texture' => array( $config['texture'] ) ) as $name => $options ) {
			$attribute = new WC_Product_Attribute();
			$attribute->set_name( $name );
			$attribute->set_options( $options );
			$attribute->set_visible( true );
			$attribute->set_variation( false );
			$attributes[] = $attribute;
		}
		$product->set_attributes( $attributes );
		$product_id = $product->save();

		wp_set_object_terms( $product_id, $config['categories'], 'product_cat' );
		update_post_meta( $product_id, '_vesora_showroom_product', '1' );
		update_post_meta( $product_id, '_vesora_showroom_order', (string) $index );

		$image_id = vesora_import_product_image( $config['image'], $config['name'] );
		if ( $image_id ) {
			$product->set_image_id( $image_id );
			$product->save();
		}
	}

	update_option( 'vesora_showroom_catalog_version', '2.1.1' );
}
add_action( 'init', 'vesora_seed_showroom_catalog', 50 );

function vesora_product_contact_url( $product = null ) {
	$text = __( 'Hello Vesora Hair, I would like a consultation', 'vesora' );
	if ( $product instanceof WC_Product ) {
		$text .= ': ' . $product->get_name() . ' - ' . get_permalink( $product->get_id() );
	}
	return vesora_whatsapp_url( $text );
}

function vesora_showroom_contact_actions( $product = null, $compact = false ) {
	if ( ! $product && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( get_the_ID() );
	}
	?>
	<div class="showroom-contact<?php echo $compact ? ' showroom-contact--compact' : ''; ?>">
		<a class="button showroom-contact__whatsapp" href="<?php echo esc_url( vesora_product_contact_url( $product ) ); ?>" target="_blank" rel="noopener">
			<span class="dashicons dashicons-whatsapp" aria-hidden="true"></span>
			<span><?php echo esc_html( $compact ? __( 'Ask on WhatsApp', 'vesora' ) : __( 'Get a personal consultation', 'vesora' ) ); ?></span>
		</a>
		<?php if ( ! $compact ) : ?>
			<a class="showroom-contact__instagram" href="<?php echo esc_url( vesora_contact_value( 'instagram_url' ) ); ?>" target="_blank" rel="noopener">
				<span class="dashicons dashicons-instagram" aria-hidden="true"></span>
				<span><?php esc_html_e( 'View Vesora on Instagram', 'vesora' ); ?></span>
			</a>
			<p><?php esc_html_e( 'Pricing is tailored to length, color, volume and order quantity. Our specialist will reply with the best option for you.', 'vesora' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

function vesora_loop_showroom_details() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$terms = array_values( array_filter( $terms, static function ( $term ) { return 'best-selling-products' !== $term->slug; } ) );
		if ( ! empty( $terms ) ) {
			echo '<p class="showroom-card__category">' . esc_html( $terms[0]->name ) . '</p>';
		}
	}
	vesora_showroom_contact_actions( $product, true );
}

function vesora_configure_showroom() {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_after_shop_loop_item', 'vesora_loop_showroom_details', 20 );
	add_action( 'woocommerce_single_product_summary', 'vesora_showroom_contact_actions', 30 );
}
add_action( 'wp', 'vesora_configure_showroom' );
add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

add_filter( 'woocommerce_get_price_html', '__return_empty_string' );
add_filter( 'woocommerce_is_purchasable', '__return_false' );
add_filter( 'woocommerce_variation_is_purchasable', '__return_false' );
add_filter(
	'woocommerce_product_tabs',
	static function ( $tabs ) {
		unset( $tabs['reviews'] );
		if ( isset( $tabs['description'] ) ) {
			$tabs['description']['title'] = __( 'About this hair', 'vesora' );
		}
		if ( isset( $tabs['additional_information'] ) ) {
			$tabs['additional_information']['title'] = __( 'Specifications', 'vesora' );
		}
		return $tabs;
	}
);
add_filter(
	'woocommerce_catalog_orderby',
	static function ( $options ) {
		unset( $options['price'], $options['price-desc'] );
		return $options;
	}
);

function vesora_showroom_page_title( $title ) {
	if ( is_search() ) {
		return sprintf( __( 'Search results for “%s”', 'vesora' ), get_search_query() );
	}
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return __( 'Vesora Hair Showroom', 'vesora' );
	}
	return $title;
}
add_filter( 'woocommerce_page_title', 'vesora_showroom_page_title' );

function vesora_product_category_banners() {
	return array(
		'weft-hair-extensions'           => array( 'file' => 'Butterfly-Weft-Hair-Extensions-Featured-Banner.jpg', 'width' => 1920, 'height' => 500 ),
		'butterfly-weft-hair-extensions' => array( 'file' => 'Butterfly-Weft-Hair-Extensions-Featured-Banner.jpg', 'width' => 1920, 'height' => 500 ),
		'genius-weft-hair'               => array( 'file' => 'Genius-Weft-Hair-Extensions-Featured-Banner.jpg', 'width' => 1920, 'height' => 500 ),
		'hand-tied-weft-extensions'      => array( 'file' => 'Hand-Tied-Weft-Extensions-banner.jpg', 'width' => 1920, 'height' => 500 ),
		'machine-weft-hair'              => array( 'file' => 'Machine-Weft-banner.png', 'width' => 2458, 'height' => 640 ),
		'flat-weft-hair-extensions'      => array( 'file' => 'Flat-Weft-Hair-Extensions-Category-Banner.jpg', 'width' => 1920, 'height' => 501 ),
		'feather-weft-hair-extensions'   => array( 'file' => 'Feather-Weft-Hair-Extensions-Category-Banner.jpg', 'width' => 1920, 'height' => 500 ),
		'keratin-hair'                   => array( 'file' => 'I-Tip-Extensions-banner.jpg', 'width' => 1920, 'height' => 500 ),
		'i-tip-extensions'               => array( 'file' => 'I-Tip-Extensions-banner.jpg', 'width' => 1920, 'height' => 500 ),
		'u-tip-hair-extensions'          => array( 'file' => 'U-Tip-Hair-Extensions-banner.jpg', 'width' => 1920, 'height' => 500 ),
		'v-tip-hair-extensions'          => array( 'file' => 'V-tip-hair.png', 'width' => 2172, 'height' => 724 ),
		'y-tip-hair-extensions'          => array( 'file' => 'Y-tip-hair.png', 'width' => 2103, 'height' => 748 ),
		'flat-tip-hair-extensions'       => array( 'file' => 'Flat-Tip-Hair-Extensions-banner15-04.jpg', 'width' => 1920, 'height' => 500 ),
		'micro-ring-hair-extensions'     => array( 'file' => 'Micro-Ring-Hair-Extensions-Category-Banner.jpg', 'width' => 1920, 'height' => 501 ),
		'nano-ring-hair'                 => array( 'file' => 'Nano-ring-hair.png', 'width' => 2172, 'height' => 724 ),
		'tape-in-hair-extensions'        => array( 'file' => 'Normal-Tape-Hair-Extensions-Category-Banner.jpg', 'width' => 1920, 'height' => 501 ),
		'normal-tape-in'                 => array( 'file' => 'Normal-Tape-Hair-Extensions-Category-Banner.jpg', 'width' => 1920, 'height' => 501 ),
		'invisible-tape-in-extensions'   => array( 'file' => 'Invisible-Tape-In-Hair-Extensions-Featured-Banner.jpg', 'width' => 1920, 'height' => 500 ),
		'long-tape'                      => array( 'file' => 'Long-tape-hair.png', 'width' => 2458, 'height' => 640 ),
		'bulk-hair-extensions'           => array( 'file' => 'bulk-hair-extensions.webp', 'width' => 1920, 'height' => 500 ),
		'clip-in-hair-extensions'        => array( 'file' => 'normal-clip-in-extensions-banner.jpg', 'width' => 2172, 'height' => 724 ),
		'normal-clip-in-hair-extensions' => array( 'file' => 'normal-clip-in-extensions-banner.jpg', 'width' => 2172, 'height' => 724 ),
		'seamless-clip-in-hair-extensions' => array( 'file' => 'seamless-clip-in-hair-extensions-banner.jpg', 'width' => 2172, 'height' => 724 ),
		'closure-frontal'                => array( 'file' => 'Lace-Closure-Category-Banner.jpg', 'width' => 1920, 'height' => 501 ),
		'lace-closure'                   => array( 'file' => 'Lace-Closure-Category-Banner.jpg', 'width' => 1920, 'height' => 501 ),
		'lace-frontal'                   => array( 'file' => 'Lace-Fronal-Category-Banner.jpg', 'width' => 1920, 'height' => 501 ),
		'ponytail-hair-extensions'       => array( 'file' => 'wrap-ponytail-hair-extensions.jpg', 'width' => 2048, 'height' => 768 ),
		'halo-hair-extensions'           => array( 'file' => 'Halo-Hair-Extensions.webp', 'width' => 1920, 'height' => 500 ),
	);
}

function vesora_render_product_category_banner() {
	if ( ! function_exists( 'is_product_category' ) || ! is_product_category() ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$banners = vesora_product_category_banners();
	if ( empty( $banners[ $term->slug ] ) ) {
		return;
	}

	$banner = $banners[ $term->slug ];
	$url    = get_template_directory_uri() . '/assets/images/hair%20ext/' . rawurlencode( $banner['file'] );
	?>
	<figure class="product-category-banner">
		<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s collection banner', 'vesora' ), $term->name ) ); ?>" width="<?php echo esc_attr( $banner['width'] ); ?>" height="<?php echo esc_attr( $banner['height'] ); ?>" fetchpriority="high" decoding="async">
	</figure>
	<?php
}

function vesora_showroom_archive_intro() {
	if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		echo '<div class="showroom-intro"><p>' . esc_html__( 'Explore Vietnamese human hair by construction, texture and finish. Vesora does not publish one-size-fits-all prices: choose a style and our team will advise the right length, color, volume and order option for you.', 'vesora' ) . '</p></div>';
	}
}
add_action( 'woocommerce_archive_description', 'vesora_showroom_archive_intro', 12 );

function vesora_product_search_form() {
	if ( ! is_search() ) {
		return;
	}
	?>
	<div class="search-showroom__intro">
		<form class="search-showroom__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="results-product-search"><?php esc_html_e( 'Search for products', 'vesora' ); ?></label>
			<input id="results-product-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search by product, texture or color', 'vesora' ); ?>" required>
			<input type="hidden" name="post_type" value="product">
			<button class="button" type="submit"><?php esc_html_e( 'Search', 'vesora' ); ?></button>
		</form>
		<p><?php esc_html_e( 'Search by product name, hair type, texture, color, category or SKU.', 'vesora' ); ?></p>
	</div>
	<?php
}
add_action( 'woocommerce_archive_description', 'vesora_product_search_form', 14 );

function vesora_showroom_category_navigation() {
	if ( is_search() || ! ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() ) ) ) {
		return;
	}

	$parent_id = 0;
	if ( is_product_category() ) {
		$current = get_queried_object();
		if ( $current instanceof WP_Term ) {
			$children = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => $current->term_id,
					'hide_empty' => false,
				)
			);
			$parent_id = ( ! is_wp_error( $children ) && ! empty( $children ) ) ? $current->term_id : (int) $current->parent;
		}
	}

	if ( 0 === $parent_id ) {
		$terms = array();
		foreach ( vesora_catalog_categories() as $slug => $config ) {
			if ( ! empty( $config['parent'] ) ) {
				continue;
			}
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term instanceof WP_Term ) {
				$terms[] = $term;
			}
		}
	} else {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => $parent_id,
				'hide_empty' => false,
				'orderby'    => 'menu_order',
				'order'      => 'ASC',
			)
		);
	}
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return;
	}
	?>
	<nav class="showroom-category-nav" aria-label="<?php esc_attr_e( 'Browse hair categories', 'vesora' ); ?>">
		<p><?php esc_html_e( 'Browse by type', 'vesora' ); ?></p>
		<div class="showroom-category-nav__links">
			<?php foreach ( $terms as $term ) : ?>
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"<?php echo is_product_category( $term->slug ) ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( $term->name ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}
add_action( 'woocommerce_before_shop_loop', 'vesora_showroom_category_navigation', 5 );

function vesora_search_no_products_found() {
	if ( ! is_search() ) {
		wc_no_products_found();
		return;
	}
	?>
	<section class="empty-state search-showroom__empty">
		<h2><?php esc_html_e( 'No matching hair found', 'vesora' ); ?></h2>
		<p><?php esc_html_e( 'Try a shorter term such as Tape-in, Brown, Blonde, Wavy or Bulk.', 'vesora' ); ?></p>
		<a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse all hair', 'vesora' ); ?></a>
	</section>
	<?php
}

function vesora_replace_no_products_message() {
	remove_action( 'woocommerce_no_products_found', 'wc_no_products_found', 10 );
	add_action( 'woocommerce_no_products_found', 'vesora_search_no_products_found', 10 );
}
add_action( 'wp', 'vesora_replace_no_products_message' );

function vesora_disable_checkout_routes() {
	if ( is_admin() || wp_doing_ajax() || ! function_exists( 'is_cart' ) ) {
		return;
	}
	if ( is_cart() || is_checkout() ) {
		wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
		exit;
	}
}
add_action( 'template_redirect', 'vesora_disable_checkout_routes' );

function vesora_seed_core_pages() {
	if ( '1.0.0' === get_option( 'vesora_core_pages_version' ) ) {
		return;
	}

	$pages = array(
		'about'   => array(
			'title'   => __( 'About us', 'vesora' ),
			'content' => __( 'Vesora Hair is a Vietnamese human hair showroom connecting salons, retailers and wholesale partners with carefully selected hair, dependable customization and direct specialist support.', 'vesora' ),
		),
		'contact' => array(
			'title'   => __( 'Contact', 'vesora' ),
			'content' => __( 'Talk with a Vesora specialist about textures, colors, lengths, samples, private label packaging and wholesale orders.', 'vesora' ) . "\n\n[vesora_contact_details]",
		),
	);

	foreach ( $pages as $slug => $page ) {
		if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => $page['content'],
			)
		);
	}

	update_option( 'vesora_core_pages_version', '1.0.0' );
}
add_action( 'init', 'vesora_seed_core_pages', 40 );

function vesora_update_primary_menu_items( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	foreach ( $items as $item ) {
		$title = strtolower( trim( wp_strip_all_tags( $item->title ) ) );
		$path  = wp_parse_url( $item->url, PHP_URL_PATH );
		$fragment = wp_parse_url( $item->url, PHP_URL_FRAGMENT );
		if ( '/contact/' === trailingslashit( (string) $path ) ) {
			$item->title = __( 'Contact us', 'vesora' );
		}

		if ( 'our factory' === $title || '/about/' === trailingslashit( (string) $path ) ) {
			$item->title = __( 'About us', 'vesora' );
			$item->url   = home_url( '/about/' );
		}

		if ( in_array( $title, array( 'journal', 'blogs', 'blog' ), true ) || false !== strpos( $title, 'wholesale' ) || '/blog/' === trailingslashit( (string) $path ) || 'wholesale' === $fragment ) {
			$item->title = __( 'Blogs', 'vesora' );
			$item->url   = home_url( '/blog/' );
		}
	}

	/* Reorder complete top-level branches without separating nested children. */
	$by_id = array();
	foreach ( $items as $item ) {
		$by_id[ (int) $item->ID ] = $item;
	}
	$branches = array();
	$order    = array();
	foreach ( $items as $item ) {
		$root = $item;
		$seen = array();
		while ( ! empty( $root->menu_item_parent ) && isset( $by_id[ (int) $root->menu_item_parent ] ) && empty( $seen[ (int) $root->ID ] ) ) {
			$seen[ (int) $root->ID ] = true;
			$root = $by_id[ (int) $root->menu_item_parent ];
		}
		$root_id = (int) $root->ID;
		if ( ! isset( $branches[ $root_id ] ) ) {
			$branches[ $root_id ] = array();
			$order[] = $root_id;
		}
		$branches[ $root_id ][] = $item;
	}
	$priority = static function ( $branch ) {
		$title = strtolower( trim( wp_strip_all_tags( $branch[0]->title ) ) );
		if ( 'about us' === $title ) { return 10; }
		if ( false !== strpos( $title, 'hair extension' ) ) { return 20; }
		if ( false !== strpos( $title, 'showroom' ) ) { return 30; }
		if ( false !== strpos( $title, 'best seller' ) ) { return 40; }
		if ( 'blogs' === $title || 'blog' === $title ) { return 50; }
		if ( in_array( $title, array( 'contact', 'contact us' ), true ) ) { return 60; }
		return 45;
	};
	usort( $order, static function ( $left, $right ) use ( $branches, $priority ) { return $priority( $branches[ $left ] ) <=> $priority( $branches[ $right ] ); } );
	$items = array();
	foreach ( $order as $root_id ) {
		$items = array_merge( $items, $branches[ $root_id ] );
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'vesora_update_primary_menu_items', 10, 2 );

function vesora_menu_fallback() {
	?>
	<ul class="site-nav__list site-nav__desktop-list">
		<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-info-outline" aria-hidden="true"></span><span><?php esc_html_e( 'About us', 'vesora' ); ?></span></a></li>
		<li class="site-nav__item site-nav__item--mega">
			<button class="site-nav__mega-toggle" type="button" aria-expanded="false" aria-controls="hair-extensions-menu" data-submenu-toggle>
				<span class="mobile-nav-icon dashicons dashicons-editor-ol" aria-hidden="true"></span>
				<span class="site-nav__label"><?php esc_html_e( 'Hair extensions', 'vesora' ); ?></span>
				<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
			</button>
			<?php vesora_render_desktop_categories(); ?>
		</li>
		<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-products" aria-hidden="true"></span><span><?php esc_html_e( 'Hair showroom', 'vesora' ); ?></span></a></li>
		<li><a href="<?php echo esc_url( home_url( '/product-category/best-selling-products/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-awards" aria-hidden="true"></span><span><?php esc_html_e( 'Best sellers', 'vesora' ); ?></span></a></li>
		<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-edit-page" aria-hidden="true"></span><span><?php esc_html_e( 'Blogs', 'vesora' ); ?></span></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-email-alt" aria-hidden="true"></span><span><?php esc_html_e( 'Contact us', 'vesora' ); ?></span></a></li>
	</ul>
	<?php
}

function vesora_mobile_menu_groups() {
	return array(
		'weft' => array(
			'label' => __( 'Weft Hair Extensions', 'vesora' ), 'slug' => 'weft-hair-extensions',
			'items' => array(
				'butterfly-weft-hair-extensions' => __( 'Butterfly Weft Hair Extensions', 'vesora' ),
				'genius-weft-hair' => __( 'Genius Weft Hair Extensions', 'vesora' ),
				'hand-tied-weft-extensions' => __( 'Hand Tied Weft Extensions', 'vesora' ),
				'machine-weft-hair' => __( 'Machine Weft', 'vesora' ),
				'flat-weft-hair-extensions' => __( 'Flat Weft Hair Extensions', 'vesora' ),
				'feather-weft-hair-extensions' => __( 'Feather Weft Hair Extensions', 'vesora' ),
			),
		),
		'keratin' => array(
			'label' => __( 'Keratin Hair Extensions', 'vesora' ), 'slug' => 'keratin-hair',
			'items' => array(
				'i-tip-extensions' => __( 'I-Tip Extensions', 'vesora' ), 'u-tip-hair-extensions' => __( 'U Tip Hair', 'vesora' ),
				'v-tip-hair-extensions' => __( 'V Tip Hair', 'vesora' ), 'y-tip-hair-extensions' => __( 'Y-Tip Hair', 'vesora' ),
				'flat-tip-hair-extensions' => __( 'Flat Tip Hair', 'vesora' ), 'micro-ring-hair-extensions' => __( 'Micro Ring Hair', 'vesora' ),
				'nano-ring-hair' => __( 'Nano-ring Hair', 'vesora' ), 'plastic-nano-ring-hair-extensions' => __( 'Plastic Nano Ring Hair', 'vesora' ),
			),
		),
		'tape' => array(
			'label' => __( 'Tape-in Hair Extensions', 'vesora' ), 'slug' => 'tape-in-hair-extensions',
			'items' => array(
				'normal-tape-in' => __( 'Normal Tape-in', 'vesora' ), 'long-tape' => __( 'Long Tape', 'vesora' ),
				'invisible-tape-in-extensions' => __( 'Invisible Tape In', 'vesora' ),
			),
		),
		'clip' => array(
			'label' => __( 'Clip-in Hair Extensions', 'vesora' ), 'slug' => 'clip-in-hair-extensions',
			'items' => array( 'normal-clip-in' => __( 'Normal Clip-in', 'vesora' ), 'seamless-clip-in' => __( 'Seamless Clip-in', 'vesora' ) ),
		),
		'ponytail' => array(
			'label' => __( 'Ponytail Hair Extensions', 'vesora' ), 'slug' => 'ponytail-hair-extensions',
			'items' => array(
				'wrap-ponytail-hair-extensions' => __( 'Wrap Ponytail', 'vesora' ),
				'drawstring-ponytail-hair-extensions' => __( 'Drawstring Ponytail', 'vesora' ),
			),
		),
		'lace' => array(
			'label' => __( 'Lace Closure and Frontal', 'vesora' ), 'slug' => 'closure-frontal',
			'items' => array( 'lace-closure' => __( 'Lace Closure', 'vesora' ), 'lace-frontal' => __( 'Lace Frontal', 'vesora' ) ),
		),
	);
}

function vesora_render_mobile_navigation() {
	$groups = vesora_mobile_menu_groups();
	?>
	<div class="mobile-drilldown" data-mobile-drilldown>
		<section class="mobile-nav-panel is-active" data-mobile-panel="root" aria-label="<?php esc_attr_e( 'Main menu', 'vesora' ); ?>">
			<ul class="mobile-nav-list">
				<li><a class="mobile-nav-row" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-info-outline" aria-hidden="true"></span><span><?php esc_html_e( 'About us', 'vesora' ); ?></span></a></li>
				<li><button type="button" class="mobile-nav-row" data-mobile-panel-open="extensions" aria-controls="mobile-panel-extensions"><span class="mobile-nav-icon dashicons dashicons-editor-ol" aria-hidden="true"></span><span><?php esc_html_e( 'Hair extensions', 'vesora' ); ?></span><span class="mobile-nav-chevron" aria-hidden="true">›</span></button></li>
				<li><a class="mobile-nav-row" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-products" aria-hidden="true"></span><span><?php esc_html_e( 'Hair showroom', 'vesora' ); ?></span></a></li>
				<li><a class="mobile-nav-row" href="<?php echo esc_url( home_url( '/product-category/best-selling-products/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-awards" aria-hidden="true"></span><span><?php esc_html_e( 'Best sellers', 'vesora' ); ?></span></a></li>
				<li><a class="mobile-nav-row" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-email-alt" aria-hidden="true"></span><span><?php esc_html_e( 'Contact us', 'vesora' ); ?></span></a></li>
				<li><a class="mobile-nav-row" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><span class="mobile-nav-icon dashicons dashicons-edit-page" aria-hidden="true"></span><span><?php esc_html_e( 'Blogs', 'vesora' ); ?></span></a></li>
			</ul>
		</section>

		<section class="mobile-nav-panel" id="mobile-panel-extensions" data-mobile-panel="extensions" aria-label="<?php esc_attr_e( 'Hair extensions', 'vesora' ); ?>" aria-hidden="true">
			<button type="button" class="mobile-nav-back" data-mobile-panel-back="root"><span aria-hidden="true">‹</span> <?php esc_html_e( 'Hair extensions', 'vesora' ); ?></button>
			<ul class="mobile-nav-list mobile-nav-list--categories">
				<li><a class="mobile-nav-row mobile-nav-row--priority" href="<?php echo esc_url( vesora_product_category_url( 'bulk-hair-extensions' ) ); ?>"><span><?php esc_html_e( 'Bulk Hair Extensions', 'vesora' ); ?></span></a></li>
				<?php foreach ( $groups as $key => $group ) : ?>
					<li><button type="button" class="mobile-nav-row" data-mobile-panel-open="<?php echo esc_attr( $key ); ?>" aria-controls="mobile-panel-<?php echo esc_attr( $key ); ?>"><span><?php echo esc_html( $group['label'] ); ?></span><span class="mobile-nav-chevron" aria-hidden="true">›</span></button></li>
				<?php endforeach; ?>
				<li><a class="mobile-nav-row" href="<?php echo esc_url( vesora_product_category_url( 'halo-hair-extensions' ) ); ?>"><span><?php esc_html_e( 'Halo Hair Extensions', 'vesora' ); ?></span></a></li>
				<li><a class="mobile-nav-row" href="<?php echo esc_url( vesora_product_category_url( 'hair-accessories' ) ); ?>"><span><?php esc_html_e( 'Hair Accessories', 'vesora' ); ?></span></a></li>
			</ul>
		</section>

		<?php foreach ( $groups as $key => $group ) : ?>
			<section class="mobile-nav-panel" id="mobile-panel-<?php echo esc_attr( $key ); ?>" data-mobile-panel="<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( $group['label'] ); ?>" aria-hidden="true">
				<button type="button" class="mobile-nav-back" data-mobile-panel-back="extensions"><span aria-hidden="true">‹</span> <?php echo esc_html( $group['label'] ); ?></button>
				<ul class="mobile-nav-list mobile-nav-list--children">
					<li><a class="mobile-nav-row mobile-nav-row--all" href="<?php echo esc_url( vesora_product_category_url( $group['slug'] ) ); ?>"><?php esc_html_e( 'View all', 'vesora' ); ?> <?php echo esc_html( $group['label'] ); ?></a></li>
					<?php foreach ( $group['items'] as $slug => $label ) : ?>
						<li><a class="mobile-nav-row" href="<?php echo esc_url( vesora_product_category_url( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endforeach; ?>
	</div>
	<?php
}

function vesora_business_types() {
	return array(
		'salon-stylist'          => __( 'Salon or Stylist', 'vesora' ),
		'hair-brand'             => __( 'Hair Brand', 'vesora' ),
		'distributor-wholesaler' => __( 'Distributor or Wholesaler', 'vesora' ),
		'retailer'               => __( 'Retailer', 'vesora' ),
		'online-store'           => __( 'Online Store', 'vesora' ),
		'other'                  => __( 'Other', 'vesora' ),
	);
}

function vesora_wholesale_return_url() {
	$fallback = home_url( '/' );
	$referer = wp_get_referer();
	// wp_nonce_field supplies a relative referer. Resolve it against the site's origin.
	$home = wp_parse_url( $fallback );
	if ( is_string( $referer ) && 0 === strpos( $referer, '/' ) && 0 !== strpos( $referer, '//' ) ) {
		$referer = $home['scheme'] . '://' . $home['host'] . ( isset( $home['port'] ) ? ':' . $home['port'] : '' ) . $referer;
	}
	$url = wp_validate_redirect( $referer ?: $fallback, $fallback );
	$parts = wp_parse_url( $url );
	// Do not let modified form data redirect to other origins or admin/login endpoints.
	if ( ! $parts || ( $parts['host'] ?? '' ) !== $home['host'] || ( $parts['scheme'] ?? '' ) !== $home['scheme'] || ( $parts['port'] ?? null ) !== ( $home['port'] ?? null ) || isset( $parts['user'] ) || preg_match( '~/(?:wp-admin|wp-login\.php)(?:/|$)~i', $parts['path'] ?? '' ) ) {
		$url = $fallback;
	}
	return remove_query_arg( 'inquiry', preg_replace( '/#.*$/', '', $url ) ) . '#wholesale';
}

function vesora_handle_wholesale_form() {
	$redirect = vesora_wholesale_return_url();

	if ( ! isset( $_POST['vesora_wholesale_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vesora_wholesale_nonce'] ) ), 'vesora_wholesale' ) ) {
		wp_safe_redirect( add_query_arg( 'inquiry', 'error', $redirect ) );
		exit;
	}

	if ( ! empty( $_POST['company_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'inquiry', 'sent', $redirect ) );
		exit;
	}

	$name             = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$whatsapp         = isset( $_POST['whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) : '';
	$email            = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$business_type    = isset( $_POST['business_type'] ) ? sanitize_key( wp_unslash( $_POST['business_type'] ) ) : '';
	$customer_message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$business_types   = vesora_business_types();

	if ( empty( $name ) || empty( $whatsapp ) || ! is_email( $email ) || ! isset( $business_types[ $business_type ] ) || empty( $customer_message ) || strlen( $customer_message ) > 4000 ) {
		wp_safe_redirect( add_query_arg( 'inquiry', 'invalid', $redirect ) );
		exit;
	}

	$lead_result = vesora_save_lead(
		array(
			'name'          => $name,
			'whatsapp'      => $whatsapp,
			'email'         => $email,
			'business_type' => $business_types[ $business_type ],
			'message'       => $customer_message,
			'source'        => 'wholesale',
		)
	);
	if ( is_wp_error( $lead_result ) ) {
		wp_safe_redirect( add_query_arg( 'inquiry', 'error', $redirect ) );
		exit;
	}
	$lead_id = (int) $lead_result['id'];
	if ( ! empty( $lead_result['duplicate'] ) ) {
		wp_safe_redirect( add_query_arg( 'inquiry', 'sent', $redirect ) );
		exit;
	}

	$subject = sprintf( __( 'Wholesale inquiry from %s', 'vesora' ), $name );
	$message = "Name: {$name}\nWhatsApp: {$whatsapp}\nEmail: {$email}\nBusiness Type: {$business_types[ $business_type ]}\n\nMessage:\n{$customer_message}\n\nLead: " . vesora_lead_admin_link( $lead_id );
	$sent    = vesora_send_lead_notification( $subject, $message, $email );
	vesora_set_lead_email_status( $lead_id, $sent );

	wp_safe_redirect( add_query_arg( 'inquiry', 'sent', $redirect ) );
	exit;
}
add_action( 'admin_post_nopriv_vesora_wholesale', 'vesora_handle_wholesale_form' );
add_action( 'admin_post_vesora_wholesale', 'vesora_handle_wholesale_form' );

function vesora_offer_business_types() {
	return array(
		'salon'                 => __( 'Salon', 'vesora' ),
		'personal-use'          => __( 'Personal use', 'vesora' ),
		'start-business-retail' => __( 'Start Business / Retail', 'vesora' ),
		'hair-shop-wholesaler'  => __( 'Hair Shop / Wholesaler', 'vesora' ),
		'hair-company'          => __( 'Hair Company', 'vesora' ),
	);
}

function vesora_offer_countries() {
	return array(
		'Australia', 'Belgium', 'Brazil', 'Canada', 'Chile', 'Colombia', 'Denmark', 'Finland', 'France', 'Germany',
		'Ireland', 'Italy', 'Japan', 'Mexico', 'Netherlands', 'New Zealand', 'Nigeria', 'Norway', 'Poland', 'Portugal',
		'Saudi Arabia', 'South Africa', 'South Korea', 'Spain', 'Sweden', 'Switzerland', 'United Arab Emirates',
		'United Kingdom', 'United States', 'Vietnam', 'Other',
	);
}

function vesora_offer_form_response( $success, $message, $status_code = 200 ) {
	if ( ! empty( $_POST['vesora_offer_ajax'] ) ) {
		if ( $success ) {
			wp_send_json_success( array( 'message' => $message ), $status_code );
		}

		wp_send_json_error( array( 'message' => $message ), $status_code );
	}

	$redirect = wp_get_referer();
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	wp_safe_redirect( add_query_arg( 'offer', $success ? 'sent' : 'error', $redirect ) );
	exit;
}

function vesora_handle_offer_form() {
	if ( ! isset( $_POST['vesora_offer_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vesora_offer_nonce'] ) ), 'vesora_offer' ) ) {
		vesora_offer_form_response( false, __( 'Your session expired. Please refresh the page and try again.', 'vesora' ), 403 );
	}

	if ( ! empty( $_POST['company_website'] ) ) {
		vesora_offer_form_response( true, __( 'Thank you. Your request has been received.', 'vesora' ) );
	}

	$name             = isset( $_POST['offer_name'] ) ? sanitize_text_field( wp_unslash( $_POST['offer_name'] ) ) : '';
	$whatsapp         = isset( $_POST['offer_whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['offer_whatsapp'] ) ) : '';
	$country          = isset( $_POST['offer_country'] ) ? sanitize_text_field( wp_unslash( $_POST['offer_country'] ) ) : '';
	$email            = isset( $_POST['offer_email'] ) ? sanitize_email( wp_unslash( $_POST['offer_email'] ) ) : '';
	$business_type    = isset( $_POST['offer_business_type'] ) ? sanitize_key( wp_unslash( $_POST['offer_business_type'] ) ) : '';
	$customer_message = isset( $_POST['offer_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['offer_message'] ) ) : '';
	$business_types   = vesora_offer_business_types();
	$countries        = vesora_offer_countries();

	if ( empty( $name ) || empty( $whatsapp ) || ! is_email( $email ) || ! in_array( $country, $countries, true ) || ! isset( $business_types[ $business_type ] ) || empty( $customer_message ) || strlen( $customer_message ) > 2000 ) {
		vesora_offer_form_response( false, __( 'Please complete every required field with valid information.', 'vesora' ), 422 );
	}

	$lead_result = vesora_save_lead(
		array(
			'name'          => $name,
			'whatsapp'      => $whatsapp,
			'country'       => $country,
			'email'         => $email,
			'business_type' => $business_types[ $business_type ],
			'message'       => $customer_message,
			'source'        => 'popup',
		)
	);
	if ( is_wp_error( $lead_result ) ) {
		vesora_offer_form_response( false, __( 'We could not safely save your request. Please try again.', 'vesora' ), 500 );
	}
	$lead_id = (int) $lead_result['id'];
	if ( ! empty( $lead_result['duplicate'] ) ) {
		vesora_offer_form_response( true, __( 'Thank you. Your request has already been received.', 'vesora' ) );
	}

	$subject = sprintf( __( 'Exclusive offer request from %s', 'vesora' ), $name );
	$message = "Name: {$name}\nWhatsApp: {$whatsapp}\nCountry: {$country}\nEmail: {$email}\nBusiness Type: {$business_types[ $business_type ]}\n\nMessage:\n{$customer_message}\n\nLead: " . vesora_lead_admin_link( $lead_id );
	$sent    = vesora_send_lead_notification( $subject, $message, $email );
	vesora_set_lead_email_status( $lead_id, $sent );

	vesora_offer_form_response( true, __( 'Thank you. A Vesora specialist will contact you shortly.', 'vesora' ) );
}
add_action( 'admin_post_nopriv_vesora_offer', 'vesora_handle_offer_form' );
add_action( 'admin_post_vesora_offer', 'vesora_handle_offer_form' );

function vesora_render_offer_popup() {
	?>
	<div class="offer-popup" data-offer-popup hidden>
		<div class="offer-popup__backdrop" aria-hidden="true"></div>
		<section class="offer-popup__dialog" role="dialog" aria-modal="true" aria-labelledby="offer-popup-title" aria-describedby="offer-popup-description" data-offer-dialog>
			<button class="offer-popup__close" type="button" aria-label="<?php esc_attr_e( 'Close exclusive offer form', 'vesora' ); ?>" data-offer-close>&times;</button>

			<div class="offer-popup__visual">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Get-Exclusive-Offers-Popup-Vesora.png' ); ?>" alt="<?php esc_attr_e( 'Get exclusive offers from Vesora Hair', 'vesora' ); ?>" width="800" height="925" decoding="async">
			</div>

			<div class="offer-popup__content">
				<header class="offer-popup__header">
					<h2 id="offer-popup-title"><?php esc_html_e( 'Register to receive exclusive offers', 'vesora' ); ?></h2>
					<p id="offer-popup-description"><?php esc_html_e( 'Tell us what you need and a Vesora specialist will contact you.', 'vesora' ); ?></p>
				</header>

				<form class="offer-popup__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-offer-form data-offer-endpoint="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
					<input type="hidden" name="action" value="vesora_offer">
					<?php wp_nonce_field( 'vesora_offer', 'vesora_offer_nonce' ); ?>
					<div class="honeypot" aria-hidden="true"><label for="offer-company-website"><?php esc_html_e( 'Company website', 'vesora' ); ?></label><input id="offer-company-website" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>

					<div class="offer-popup__row">
						<div class="field"><label for="offer-name"><?php esc_html_e( 'Name', 'vesora' ); ?> <span aria-hidden="true">*</span></label><input id="offer-name" name="offer_name" type="text" autocomplete="name" required></div>
						<div class="field"><label for="offer-whatsapp"><?php esc_html_e( 'WhatsApp', 'vesora' ); ?> <span aria-hidden="true">*</span></label><input id="offer-whatsapp" name="offer_whatsapp" type="tel" autocomplete="tel" inputmode="tel" required></div>
					</div>

					<div class="offer-popup__row offer-popup__row--details">
						<div class="field">
							<label for="offer-country"><?php esc_html_e( 'Country', 'vesora' ); ?> <span aria-hidden="true">*</span></label>
							<select id="offer-country" name="offer_country" required>
								<option value="" selected disabled><?php esc_html_e( 'Select your country', 'vesora' ); ?></option>
								<?php foreach ( vesora_offer_countries() as $country ) : ?>
									<option value="<?php echo esc_attr( $country ); ?>"><?php echo esc_html( $country ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="field">
							<label for="offer-business-type"><?php esc_html_e( 'Business Type', 'vesora' ); ?> <span aria-hidden="true">*</span></label>
							<select id="offer-business-type" name="offer_business_type" required>
								<option value="" selected disabled><?php esc_html_e( 'Select business type', 'vesora' ); ?></option>
								<?php foreach ( vesora_offer_business_types() as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="field"><label for="offer-email"><?php esc_html_e( 'Email', 'vesora' ); ?> <span aria-hidden="true">*</span></label><input id="offer-email" name="offer_email" type="email" autocomplete="email" inputmode="email" required></div>

					<div class="field"><label for="offer-message"><?php esc_html_e( 'Message', 'vesora' ); ?> <span aria-hidden="true">*</span></label><textarea id="offer-message" name="offer_message" rows="4" maxlength="2000" required></textarea></div>
					<p class="offer-popup__status" role="status" aria-live="polite" data-offer-status></p>
					<button class="offer-popup__submit" type="submit" data-offer-submit><span data-offer-submit-label><?php esc_html_e( 'Submit', 'vesora' ); ?></span><span data-offer-loading hidden><?php esc_html_e( 'Sending...', 'vesora' ); ?></span></button>
				</form>
			</div>
		</section>
	</div>
	<?php
}
add_action( 'wp_footer', 'vesora_render_offer_popup', 5 );

function vesora_body_classes( $classes ) {
	$classes[] = 'vesora-site';
	return $classes;
}
add_filter( 'body_class', 'vesora_body_classes' );
