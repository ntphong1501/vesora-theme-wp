<?php
/**
 * One-time showroom catalogue importer.
 *
 * Product image filenames in assets/images/products-by-category/vesora use the
 * public Luxshine product slug followed by the original media ID. This importer
 * treats that slug as the stable key, so an image can never be assigned merely
 * because it has a similar title.
 *
 * @package Vesora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vesora_catalog_asset_root() {
	return get_template_directory() . '/assets/images/products-by-category/vesora';
}

function vesora_catalog_asset_rows() {
	$root = vesora_catalog_asset_root();
	if ( ! is_dir( $root ) ) {
		return array();
	}

	$rows  = array();
	$files = array();
	foreach ( array( 'jpg', 'jpeg', 'png', 'webp', 'avif' ) as $extension ) {
		$matches = glob( $root . '/*/*.' . $extension );
		if ( $matches ) {
			$files = array_merge( $files, $matches );
		}
	}
	foreach ( $files as $file ) {
		$filename = basename( $file );
		$stem     = pathinfo( $filename, PATHINFO_FILENAME );
		$slug = preg_replace( '/-[0-9]+$/', '', strtolower( $stem ) );
		if ( ! $slug ) {
			continue;
		}

		$relative      = ltrim( str_replace( '\\', '/', substr( $file, strlen( $root ) ) ), '/' );
		$rows[ $slug ] = array(
			'slug'     => $slug,
			'relative' => $relative,
			'absolute' => $file,
		);
	}

	ksort( $rows, SORT_NATURAL );
	return array_values( $rows );
}

function vesora_catalog_product_title( $slug ) {
	$title = ucwords( str_replace( '-', ' ', $slug ) );
	$replacements = array(
		'I Tip'     => 'I-Tip',
		'U Tip'     => 'U-Tip',
		'V Tip'     => 'V-Tip',
		'Y Tip'     => 'Y-Tip',
		'Tape In'   => 'Tape-in',
		'Clip In'   => 'Clip-in',
		'Hair Wigs' => 'Hair Wigs',
	);

	return strtr( $title, $replacements );
}

function vesora_catalog_best_selling_slugs() {
	return array(
		'butterfly-weft-hair-extensions-natural-color-natural-straight',
		'normal-tape-in-hair-extensions-white-blonde-natural-straight',
		'normal-tape-in-hair-extensions-natural-color-natural-straight',
		'normal-tape-in-hair-extensions-brown-natural-straight',
		'machine-weft-hair-extensions-natural-color-natural-wavy',
		'machine-weft-hair-extensions-natural-color-natural-straight',
		'machine-weft-hair-extensions-light-blonde-natural-straight',
		'genius-weft-hair-extensions-natural-color-natural-straight',
		'bulk-hair-extensions-natural-color-natural-straight',
		'bulk-hair-extensions-light-brown-natural-straight',
		'bulk-hair-extensions-light-blonde-natural-straight',
		'bulk-hair-extensions-brown-natural-straight',
		'flat-tip-hair-extensions-light-blonde-natural-straight',
	);
}

function vesora_catalog_product_categories( $slug, $relative ) {
	$folder = strtolower( strtok( str_replace( '\\', '/', $relative ), '/' ) );
	if ( 'hair-accessories' === $folder ) {
		return array( 'hair-accessories' );
	}

	if ( preg_match( '/(?:full-lace-wig|full-lace-wigs)/', $slug ) ) {
		$categories = array( 'full-lace-wigs', 'vietnamese-hair-wigs' );
	} elseif ( preg_match( '/lace-closure-wig/', $slug ) ) {
		$categories = array( 'lace-closure-wigs', 'vietnamese-hair-wigs' );
	} elseif ( preg_match( '/lace-(?:front|frontal)-wig/', $slug ) ) {
		$categories = array( 'lace-frontal-wigs', 'vietnamese-hair-wigs' );
	} elseif ( 'vietnamese-hair-wigs' === $folder ) {
		$categories = array( 'vietnamese-hair-wigs' );
	} else {
		$categories = array( 'hair-extensions' );
		$rules      = array(
			'butterfly-weft'       => array( 'butterfly-weft-hair-extensions', 'weft-hair-extensions' ),
			'genius-weft'          => array( 'genius-weft-hair', 'weft-hair-extensions' ),
			'hand-tied-weft'       => array( 'hand-tied-weft-extensions', 'weft-hair-extensions' ),
			'machine-weft'         => array( 'machine-weft-hair', 'weft-hair-extensions' ),
			'feather-weft'         => array( 'feather-weft-hair-extensions', 'weft-hair-extensions' ),
			'single-layer-silky'   => array( 'single-layer-silky-flat-weft', 'weft-hair-extensions' ),
			'double-layer-silky'   => array( 'double-layer-silky-flat-weft', 'weft-hair-extensions' ),
			'flat-weft'            => array( 'flat-weft-hair-extensions', 'weft-hair-extensions' ),
			'plastic-nano-ring'    => array( 'plastic-nano-ring-hair-extensions', 'keratin-hair' ),
			'nano-ring'            => array( 'nano-ring-hair', 'keratin-hair' ),
			'micro-ring'           => array( 'micro-ring-hair-extensions', 'keratin-hair' ),
			'flat-tip'             => array( 'flat-tip-hair-extensions', 'keratin-hair' ),
			'i-tip'                => array( 'i-tip-extensions', 'keratin-hair' ),
			'u-tip'                => array( 'u-tip-hair-extensions', 'keratin-hair' ),
			'v-tip'                => array( 'v-tip-hair-extensions', 'keratin-hair' ),
			'y-tip'                => array( 'y-tip-hair-extensions', 'keratin-hair' ),
			'invisible-tape'       => array( 'invisible-tape-in-extensions', 'tape-in-hair-extensions' ),
			'mini-tape'            => array( 'long-tape', 'tape-in-hair-extensions' ),
			'normal-tape'          => array( 'normal-tape-in', 'tape-in-hair-extensions' ),
			'seamless-clip-in'     => array( 'seamless-clip-in', 'clip-in-hair-extensions' ),
			'normal-clip-in'       => array( 'normal-clip-in', 'clip-in-hair-extensions' ),
			'wrap-ponytail'        => array( 'wrap-ponytail-hair-extensions', 'ponytail-hair-extensions' ),
			'drawstring-ponytail'  => array( 'drawstring-ponytail-hair-extensions', 'ponytail-hair-extensions' ),
			'halo-hair'            => array( 'halo-hair-extensions' ),
			'bulk-hair'            => array( 'bulk-hair-extensions' ),
			'lace-closure'         => array( 'lace-closure', 'closure-frontal' ),
			'lace-frontal'         => array( 'lace-frontal', 'closure-frontal' ),
		);

		$matched = false;
		foreach ( $rules as $needle => $terms ) {
			if ( false !== strpos( $slug, $needle ) ) {
				$categories = array_merge( $terms, $categories );
				$matched    = true;
				break;
			}
		}
		if ( ! $matched && false !== strpos( $slug, 'weft' ) ) {
			array_unshift( $categories, 'weft-hair-extensions' );
		}
	}

	if ( in_array( $slug, vesora_catalog_best_selling_slugs(), true ) ) {
		array_unshift( $categories, 'best-selling-products' );
	}

	return array_values( array_unique( $categories ) );
}

function vesora_catalog_detect_value( $slug, $values ) {
	foreach ( $values as $value ) {
		if ( false !== strpos( $slug, sanitize_title( $value ) ) ) {
			return $value;
		}
	}

	return '';
}

function vesora_catalog_import_image( $row, $title ) {
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'meta_key'    => '_vesora_catalog_source',
			'meta_value'  => $row['relative'],
			'fields'      => 'ids',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$contents = file_get_contents( $row['absolute'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $contents ) {
		return 0;
	}

	$upload = wp_upload_bits( basename( $row['absolute'] ), null, $contents );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$filetype      = wp_check_filetype( $upload['file'] );
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
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
	update_post_meta( $attachment_id, '_vesora_catalog_source', $row['relative'] );
	return (int) $attachment_id;
}

function vesora_catalog_sync_product( $row, $order = 0 ) {
	$existing_ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'any',
			'name'           => $row['slug'],
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	$product = $existing_ids ? wc_get_product( (int) $existing_ids[0] ) : new WC_Product_Simple();
	if ( ! $product ) {
		return new WP_Error( 'invalid_product', 'Could not create or load product.' );
	}

	$title      = vesora_catalog_product_title( $row['slug'] );
	$categories = vesora_catalog_product_categories( $row['slug'], $row['relative'] );
	$color      = vesora_catalog_detect_value( $row['slug'], array( 'Natural Color', 'White Blonde', 'Light Blonde', 'Light Brown', 'Dark Brown', 'Cool Color', 'Brown', 'Black', 'Red' ) );
	$texture    = vesora_catalog_detect_value( $row['slug'], array( 'Water Body Wavy', 'Romantic Curly', 'Kinky Straight', 'Natural Straight', 'Natural Wavy', 'Bouncy Wavy', 'Body Wavy', 'Deep Curly', 'Deep Wavy', 'Yaki Straight', 'Straight' ) );

	$product->set_name( $title );
	$product->set_slug( $row['slug'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_regular_price( '' );
	$product->set_sale_price( '' );
	$product->set_price( '' );
	$product->set_featured( in_array( 'best-selling-products', $categories, true ) );
	$product->set_menu_order( $order );
	$product->set_short_description( __( '100% Vietnamese human hair made for natural movement, reliable wear and professional customization.', 'vesora' ) );
	$product->set_description( __( 'Available for salon, retailer and wholesale enquiries. Contact Vesora for current color matching, length recommendations, custom packaging and order support.', 'vesora' ) );

	$attribute_values = array(
		'Standard' => array( 'High Standard Double Drawn', 'Luxury Double Drawn Hair' ),
		'Length'   => array( '16 inch', '18 inch', '20 inch', '22 inch', '24 inch', '26 inch', '28 inch' ),
	);
	if ( $color ) {
		$attribute_values['Color'] = array( $color );
	}
	if ( $texture ) {
		$attribute_values['Texture'] = array( $texture );
	}
	$attributes = array();
	foreach ( $attribute_values as $name => $options ) {
		$attribute = new WC_Product_Attribute();
		$attribute->set_name( $name );
		$attribute->set_options( $options );
		$attribute->set_visible( true );
		$attributes[] = $attribute;
	}
	$product->set_attributes( $attributes );
	$product_id = $product->save();

	wp_set_object_terms( $product_id, $categories, 'product_cat' );
	update_post_meta( $product_id, '_vesora_showroom_product', '1' );
	update_post_meta( $product_id, '_vesora_catalog_source', $row['relative'] );

	$image_id = vesora_catalog_import_image( $row, $title );
	if ( $image_id ) {
		$product->set_image_id( $image_id );
		$product->save();
	}

	return $product_id;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	class Vesora_Catalog_CLI_Command {
		public function sync( $args, $assoc_args ) {
			if ( ! class_exists( 'WC_Product_Simple' ) ) {
				WP_CLI::error( 'WooCommerce is not active.' );
			}

			vesora_ensure_product_categories();
			$rows   = vesora_catalog_asset_rows();
			$offset = isset( $assoc_args['offset'] ) ? max( 0, (int) $assoc_args['offset'] ) : 0;
			$limit  = isset( $assoc_args['limit'] ) ? max( 1, (int) $assoc_args['limit'] ) : count( $rows );
			$rows   = array_slice( $rows, $offset, $limit );

			$created = 0;
			$failed  = 0;
			foreach ( $rows as $index => $row ) {
				$result = vesora_catalog_sync_product( $row, $offset + $index );
				if ( is_wp_error( $result ) ) {
					++$failed;
					WP_CLI::warning( $row['slug'] . ': ' . $result->get_error_message() );
				} else {
					++$created;
				}
				if ( 0 === ( $index + 1 ) % 25 ) {
					WP_CLI::log( sprintf( 'Processed %d products...', $index + 1 ) );
				}
			}

			update_option( 'vesora_full_catalog_sync', gmdate( 'c' ) );
			WP_CLI::success( sprintf( 'Synchronized %d products; %d failed.', $created, $failed ) );
		}

		public function cleanup_placeholders() {
			$ids = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'   => '_vesora_showroom_product',
							'value' => '1',
						),
						array(
							'key'     => '_vesora_catalog_source',
							'compare' => 'NOT EXISTS',
						),
					),
				)
			);

			foreach ( $ids as $product_id ) {
				wp_update_post( array( 'ID' => $product_id, 'post_status' => 'draft' ) );
			}

			WP_CLI::success( sprintf( 'Moved %d legacy showroom placeholders to Draft.', count( $ids ) ) );
		}

		public function report_unmanaged() {
			$ids = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_query'     => array(
						array(
							'key'     => '_vesora_catalog_source',
							'compare' => 'NOT EXISTS',
						),
					),
				)
			);
			foreach ( $ids as $product_id ) {
				WP_CLI::log( $product_id . ' :: ' . get_post_field( 'post_name', $product_id ) . ' :: ' . get_the_title( $product_id ) );
			}
			WP_CLI::success( sprintf( '%d unmanaged published products.', count( $ids ) ) );
		}

		public function verify() {
			$ids = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_key'       => '_vesora_catalog_source',
				)
			);
			$errors = array();
			foreach ( $ids as $product_id ) {
				$source   = get_post_meta( $product_id, '_vesora_catalog_source', true );
				$image_id = get_post_thumbnail_id( $product_id );
				if ( ! $image_id ) {
					$errors[] = $product_id . ' has no featured image';
					continue;
				}
				if ( $source !== get_post_meta( $image_id, '_vesora_catalog_source', true ) ) {
					$errors[] = $product_id . ' source does not match its featured image';
				}
				if ( ! file_exists( get_attached_file( $image_id ) ) ) {
					$errors[] = $product_id . ' featured image file is missing';
				}
				$terms = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
				if ( ! $terms || is_wp_error( $terms ) ) {
					$errors[] = $product_id . ' has no product category';
				}
			}

			foreach ( array_slice( $errors, 0, 20 ) as $error ) {
				WP_CLI::warning( $error );
			}
			if ( $errors ) {
				WP_CLI::error( sprintf( '%d validation errors across %d catalog products.', count( $errors ), count( $ids ) ) );
			}
			WP_CLI::success( sprintf( 'Verified %d catalog products: image source, file and category all valid.', count( $ids ) ) );
		}
	}

	WP_CLI::add_command( 'vesora catalog', 'Vesora_Catalog_CLI_Command' );
}
