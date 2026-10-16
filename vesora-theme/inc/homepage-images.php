<?php
/**
 * Easy homepage image management for non-technical administrators.
 *
 * @package Vesora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vesora_homepage_image_groups() {
	return array(
		'categories' => array(
			'label'       => __( 'Danh mục', 'vesora' ),
			'title'       => __( 'Vesora Hair Category', 'vesora' ),
			'description' => __( 'Ảnh đại diện cho các nhóm sản phẩm trên trang chủ.', 'vesora' ),
			'items'       => array(
				'tape-in-hair-extensions' => array( 'name' => __( 'Tape-in Hair', 'vesora' ), 'slug' => 'tape-in-hair-extensions', 'file' => 'categories/tape-in-hair.jpg', 'width' => 1920, 'height' => 2560 ),
				'machine-weft-hair'       => array( 'name' => __( 'Machine Weft Hair', 'vesora' ), 'slug' => 'machine-weft-hair', 'file' => 'categories/machine-weft-hair.jpg', 'width' => 1920, 'height' => 2560 ),
				'keratin-hair'            => array( 'name' => __( 'Keratin Hair', 'vesora' ), 'slug' => 'keratin-hair', 'file' => 'categories/keratin-hair.jpg', 'width' => 1920, 'height' => 2560 ),
				'bulk-hair-extensions'    => array( 'name' => __( 'Bulk Hair', 'vesora' ), 'slug' => 'bulk-hair-extensions', 'file' => 'categories/bulk-hair.jpg', 'width' => 646, 'height' => 1600 ),
				'nano-ring-hair'          => array( 'name' => __( 'Nano-ring Hair', 'vesora' ), 'slug' => 'nano-ring-hair', 'file' => 'categories/nano-ring-hair.jpg', 'width' => 1920, 'height' => 2560 ),
				'genius-weft-hair'        => array( 'name' => __( 'Genius Weft Hair', 'vesora' ), 'slug' => 'genius-weft-hair', 'file' => 'categories/genius-weft-hair.jpg', 'width' => 1920, 'height' => 2560 ),
				'clip-in-hair-extensions' => array( 'name' => __( 'Clip-in Hair', 'vesora' ), 'slug' => 'clip-in-hair-extensions', 'file' => 'categories/clip-in-hair.jpg', 'width' => 1920, 'height' => 2560 ),
				'closure-frontal'         => array( 'name' => __( 'Closure / Frontal', 'vesora' ), 'slug' => 'closure-frontal', 'file' => 'categories/closure-frontal.jpg', 'width' => 1920, 'height' => 2560 ),
				'normal-tape-in'          => array( 'name' => __( 'Normal Tape-in', 'vesora' ), 'slug' => 'normal-tape-in', 'file' => 'categories/long-tape.jpg', 'width' => 1920, 'height' => 2560 ),
				'vietnamese-hair-wigs'    => array( 'name' => __( 'Wig', 'vesora' ), 'slug' => 'vietnamese-hair-wigs', 'file' => 'categories/wig.jpg', 'width' => 720, 'height' => 1280 ),
			),
		),
		'textures' => array(
			'label'       => __( 'Kiểu tóc', 'vesora' ),
			'title'       => __( 'Shop By Texture', 'vesora' ),
			'description' => __( 'Ảnh minh họa cho từng kiểu tóc trong khu vực Shop By Texture.', 'vesora' ),
			'items'       => array(
				'deep-wavy-hair'     => array( 'name' => __( 'Deep Wavy Hair', 'vesora' ), 'file' => 'shop-by-texture/Deep Wavy Hair.jpg', 'width' => 600, 'height' => 800 ),
				'straight-hair'      => array( 'name' => __( 'Straight Hair', 'vesora' ), 'file' => 'shop-by-texture/Straight Hair.jpg', 'width' => 600, 'height' => 800 ),
				'wavy-hair'          => array( 'name' => __( 'Wavy Hair', 'vesora' ), 'file' => 'shop-by-texture/Wavy Hair.jpg', 'width' => 600, 'height' => 800 ),
				'curly-hair'         => array( 'name' => __( 'Curly Hair', 'vesora' ), 'file' => 'shop-by-texture/Curly Hair.jpg', 'width' => 600, 'height' => 800 ),
				'body-wavy-hair'     => array( 'name' => __( 'Body Wavy Hair', 'vesora' ), 'file' => 'shop-by-texture/Body Wavy Hair.jpg', 'width' => 600, 'height' => 800 ),
				'kinky-straight-hair' => array( 'name' => __( 'Kinky Straight Hair', 'vesora' ), 'file' => 'shop-by-texture/KiKinky Straught Hair.jpg', 'width' => 600, 'height' => 800 ),
			),
		),
		'colors' => array(
			'label'       => __( 'Màu tóc', 'vesora' ),
			'title'       => __( 'Shop By Color', 'vesora' ),
			'description' => __( 'Ảnh minh họa cho từng mã màu trong khu vực Shop By Color.', 'vesora' ),
			'items'       => array(
				'natural-black'    => array( 'name' => __( 'Natural Black', 'vesora' ), 'code' => '#1B', 'file' => 'shop-by-color/shop-by-color-1B.jpg', 'width' => 600, 'height' => 800 ),
				'chocolate-brown'  => array( 'name' => __( 'Chocolate Brown', 'vesora' ), 'code' => '#4', 'file' => 'shop-by-color/shop-by-color-4.jpg', 'width' => 600, 'height' => 800 ),
				'medium-brown'     => array( 'name' => __( 'Medium Brown', 'vesora' ), 'code' => '#4Q', 'file' => 'shop-by-color/shop-by-color-4Q.jpg', 'width' => 600, 'height' => 800 ),
				'warm-brown'       => array( 'name' => __( 'Warm Brown', 'vesora' ), 'code' => '#5Q', 'file' => 'shop-by-color/shop-by-color-5Q.jpg', 'width' => 600, 'height' => 800 ),
				'ash-blonde'       => array( 'name' => __( 'Ash Blonde', 'vesora' ), 'code' => '#60ASH', 'file' => 'shop-by-color/shop-by-color-60ASH.jpg', 'width' => 600, 'height' => 800 ),
				'blonde'           => array( 'name' => __( 'Blonde', 'vesora' ), 'code' => '#613', 'file' => 'shop-by-color/shop-by-color-613.jpg', 'width' => 600, 'height' => 800 ),
				'caramel-brown'    => array( 'name' => __( 'Caramel Brown', 'vesora' ), 'code' => '#8', 'file' => 'shop-by-color/shop-by-color-8.jpg', 'width' => 600, 'height' => 800 ),
				'burgundy'         => array( 'name' => __( 'Burgundy', 'vesora' ), 'code' => '#9C', 'file' => 'shop-by-color/shop-by-color-9C.jpg', 'width' => 600, 'height' => 800 ),
			),
		),
	);
}

function vesora_homepage_card_image( $group_key, $item_key ) {
	$groups = vesora_homepage_image_groups();
	if ( empty( $groups[ $group_key ]['items'][ $item_key ] ) ) {
		return array();
	}

	$item       = $groups[ $group_key ]['items'][ $item_key ];
	$saved      = get_option( 'vesora_homepage_images', array() );
	$attachment = isset( $saved[ $group_key ][ $item_key ] ) ? absint( $saved[ $group_key ][ $item_key ] ) : 0;

	if ( $attachment ) {
		$image = wp_get_attachment_image_src( $attachment, 'full' );
		if ( $image ) {
			return array(
				'url'           => $image[0],
				'width'         => $image[1],
				'height'        => $image[2],
				'attachment_id' => $attachment,
				'is_custom'     => true,
			);
		}
	}

	return array(
		'url'           => get_template_directory_uri() . '/assets/images/' . $item['file'],
		'width'         => $item['width'],
		'height'        => $item['height'],
		'attachment_id' => 0,
		'is_custom'     => false,
	);
}

function vesora_homepage_cards( $group_key ) {
	$groups = vesora_homepage_image_groups();
	if ( empty( $groups[ $group_key ]['items'] ) ) {
		return array();
	}

	$cards = array();
	foreach ( $groups[ $group_key ]['items'] as $item_key => $item ) {
		$cards[] = array_merge(
			$item,
			array(
				'key'   => $item_key,
				'image' => vesora_homepage_card_image( $group_key, $item_key ),
			)
		);
	}
	return $cards;
}

/**
 * Return the six homepage banner slots. The first two preserve the bundled art
 * until an administrator replaces them from the Media Library.
 *
 * @return array
 */
function vesora_homepage_banners() {
	$defaults = array(
		array(
			'enabled'      => 1,
			'desktop_id'   => 0,
			'mobile_id'    => 0,
			'desktop_file' => 'Clip-in-Extensions-A-fresh-new-look-whenever-the-moment-feeels-right-Banner-2.webp',
			'mobile_file'  => 'clip-in-extensions-mobile-square.png',
			'url'          => vesora_product_category_url( 'clip-in-hair-extensions' ),
			'cta_label'    => '',
			'alt'          => __( 'Vesora clip-in extensions for instant length and volume', 'vesora' ),
		),
		array(
			'enabled'      => 1,
			'desktop_id'   => 0,
			'mobile_id'    => 0,
			'desktop_file' => 'Butterfly-weft-Fuller-volume-Flatter-Finish-naturally-seamless-Banner-Desktop.png',
			'mobile_file'  => 'Butterfly-weft-Fuller-volume-Flatter-Finish-naturally-seamless-Banner-Mobile.png',
			'url'          => vesora_product_category_url( 'butterfly-weft-hair-extensions' ),
			'cta_label'    => '',
			'alt'          => __( 'Vesora butterfly weft hair with fuller volume and a naturally seamless finish', 'vesora' ),
		),
	);

	for ( $index = 2; $index < 6; $index++ ) {
		$defaults[] = array( 'enabled' => 0, 'desktop_id' => 0, 'mobile_id' => 0, 'desktop_file' => '', 'mobile_file' => '', 'url' => '', 'cta_label' => '', 'alt' => '' );
	}

	$saved   = get_option( 'vesora_homepage_banners', array() );
	$banners = array();
	foreach ( $defaults as $index => $default ) {
		$banner = wp_parse_args( isset( $saved[ $index ] ) && is_array( $saved[ $index ] ) ? $saved[ $index ] : array(), $default );
		$desktop = $banner['desktop_id'] ? wp_get_attachment_image_src( absint( $banner['desktop_id'] ), 'full' ) : false;
		$mobile  = $banner['mobile_id'] ? wp_get_attachment_image_src( absint( $banner['mobile_id'] ), 'full' ) : false;
		$banner['desktop_url'] = $desktop ? $desktop[0] : ( $banner['desktop_file'] ? get_template_directory_uri() . '/assets/images/' . $banner['desktop_file'] : '' );
		$banner['mobile_url']  = $mobile ? $mobile[0] : ( $banner['mobile_file'] ? get_template_directory_uri() . '/assets/images/' . $banner['mobile_file'] : $banner['desktop_url'] );
		$banners[] = $banner;
	}

	return $banners;
}

function vesora_sanitize_homepage_banners( $input ) {
	$input  = is_array( $input ) ? $input : array();
	$output = array();
	for ( $index = 0; $index < 6; $index++ ) {
		$item       = isset( $input[ $index ] ) && is_array( $input[ $index ] ) ? $input[ $index ] : array();
		$desktop_id = isset( $item['desktop_id'] ) ? absint( $item['desktop_id'] ) : 0;
		$mobile_id  = isset( $item['mobile_id'] ) ? absint( $item['mobile_id'] ) : 0;
		$output[]   = array(
			'enabled'    => ! empty( $item['enabled'] ) ? 1 : 0,
			'desktop_id' => $desktop_id && wp_attachment_is_image( $desktop_id ) ? $desktop_id : 0,
			'mobile_id'  => $mobile_id && wp_attachment_is_image( $mobile_id ) ? $mobile_id : 0,
			'url'        => isset( $item['url'] ) ? esc_url_raw( wp_unslash( $item['url'] ) ) : '',
			'cta_label'  => isset( $item['cta_label'] ) ? sanitize_text_field( wp_unslash( $item['cta_label'] ) ) : '',
			'alt'        => isset( $item['alt'] ) ? sanitize_text_field( wp_unslash( $item['alt'] ) ) : '',
		);
	}
	return $output;
}

/**
 * Built-in customer feedback images bundled with the theme.
 *
 * These files are imported to the Media Library once so administrators can
 * replace, remove and reorder them without editing theme files.
 */
function vesora_feedback_seed_manifest() {
	return array(
		array( 'file' => 'feedback-01.jpg', 'title' => 'Vesora customer feedback 01' ),
		array( 'file' => 'feedback-02.jpg', 'title' => 'Vesora customer feedback 02' ),
		array( 'file' => 'feedback-03.jpg', 'title' => 'Vesora customer feedback 03' ),
		array( 'file' => 'feedback-04.jpg', 'title' => 'Vesora customer feedback 04' ),
		array( 'file' => 'feedback-05.jpg', 'title' => 'Vesora customer feedback 05' ),
		array( 'file' => 'feedback-06.jpg', 'title' => 'Vesora customer feedback 06' ),
		array( 'file' => 'feedback-07.jpg', 'title' => 'Vesora customer feedback 07' ),
		array( 'file' => 'feedback-08.jpg', 'title' => 'Vesora customer feedback 08' ),
		array( 'file' => 'feedback-09.jpg', 'title' => 'Vesora customer feedback 09' ),
	);
}

function vesora_sanitize_feedback_gallery( $input ) {
	$ids = array_filter( array_map( 'absint', is_array( $input ) ? $input : array() ) );
	$ids = array_values( array_unique( $ids ) );

	return array_values(
		array_filter(
			$ids,
			static function ( $attachment_id ) {
				return wp_attachment_is_image( $attachment_id );
			}
		)
	);
}

function vesora_feedback_gallery_images() {
	$images = array();
	foreach ( vesora_sanitize_feedback_gallery( get_option( 'vesora_feedback_gallery', array() ) ) as $attachment_id ) {
		$source = wp_get_attachment_image_src( $attachment_id, 'large' );
		if ( ! $source ) {
			continue;
		}
		$images[] = array(
			'id'        => $attachment_id,
			'url'       => $source[0],
			'width'     => $source[1],
			'height'    => $source[2],
			'alt'       => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
			'title'     => get_the_title( $attachment_id ),
			'landscape' => $source[1] > $source[2],
		);
	}

	return $images;
}

/**
 * Sanitize the administrator-managed homepage introduction video.
 *
 * @param mixed $input Raw option value.
 * @return array
 */
function vesora_sanitize_homepage_video( $input ) {
	$input     = is_array( $input ) ? $input : array();
	$video_id  = isset( $input['video_id'] ) ? absint( $input['video_id'] ) : 0;
	$poster_id = isset( $input['poster_id'] ) ? absint( $input['poster_id'] ) : 0;
	$mime_type = $video_id ? (string) get_post_mime_type( $video_id ) : '';

	if ( ! $video_id || 0 !== strpos( $mime_type, 'video/' ) ) {
		$video_id = 0;
	}

	if ( ! $poster_id || ! wp_attachment_is_image( $poster_id ) ) {
		$poster_id = 0;
	}

	return array(
		'enabled'     => ! empty( $input['enabled'] ) ? 1 : 0,
		'video_id'    => $video_id,
		'poster_id'   => $poster_id,
		'youtube_url' => isset( $input['youtube_url'] ) ? esc_url_raw( wp_unslash( $input['youtube_url'] ) ) : '',
		'title'       => isset( $input['title'] ) ? sanitize_text_field( wp_unslash( $input['title'] ) ) : '',
		'copy'        => isset( $input['copy'] ) ? wp_kses_post( wp_unslash( $input['copy'] ) ) : '',
		'cta_label'   => isset( $input['cta_label'] ) ? sanitize_text_field( wp_unslash( $input['cta_label'] ) ) : '',
		'cta_url'     => isset( $input['cta_url'] ) ? esc_url_raw( wp_unslash( $input['cta_url'] ) ) : '',
	);
}

function vesora_youtube_embed_url( $url ) {
	if ( ! $url ) {
		return '';
	}
	$parts = wp_parse_url( $url );
	$host  = isset( $parts['host'] ) ? strtolower( $parts['host'] ) : '';
	$path  = isset( $parts['path'] ) ? trim( $parts['path'], '/' ) : '';
	parse_str( isset( $parts['query'] ) ? $parts['query'] : '', $query );

	if ( ! in_array( $host, array( 'youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be' ), true ) ) {
		return '';
	}
	if ( ! empty( $query['list'] ) ) {
		return 'https://www.youtube-nocookie.com/embed/videoseries?list=' . rawurlencode( sanitize_text_field( $query['list'] ) );
	}
	if ( 'youtu.be' === $host && $path ) {
		return 'https://www.youtube-nocookie.com/embed/' . rawurlencode( sanitize_text_field( $path ) );
	}
	if ( ! empty( $query['v'] ) ) {
		return 'https://www.youtube-nocookie.com/embed/' . rawurlencode( sanitize_text_field( $query['v'] ) );
	}
	if ( 0 === strpos( $path, 'shorts/' ) || 0 === strpos( $path, 'embed/' ) ) {
		$segments = explode( '/', $path );
		return isset( $segments[1] ) ? 'https://www.youtube-nocookie.com/embed/' . rawurlencode( sanitize_text_field( $segments[1] ) ) : '';
	}
	if ( false !== stripos( $path, '@VesoraHairExtensionFactory' ) || 'channel/UCcLs-OojLGNpF9J7UYBhDkg' === $path ) {
		return 'https://www.youtube-nocookie.com/embed/videoseries?list=UUcLs-OojLGNpF9J7UYBhDkg';
	}
	return '';
}

/**
 * Return normalized homepage video data for the front end and admin preview.
 *
 * @return array
 */
function vesora_homepage_video() {
	$defaults = array(
		'enabled'     => 1,
		'video_id'    => 0,
		'poster_id'   => 0,
		'youtube_url' => 'https://www.youtube.com/@VesoraHairExtensionFactory',
		'title'       => __( 'VESORA HAIR #1 TRUSTED VIETNAMESE HAIR VENDOR', 'vesora' ),
		'copy'        => __( 'Established <strong>in 2011</strong> and developed to the present, <strong>Vesora hair</strong> has been famous as the largest-scale <strong>Vietnamese hair factory</strong>. Specializing in high-quality <strong>Vietnamese hair extensions</strong>, we provide wholesale hair solutions with ethically sourced, natural, and durable products. We have extended business relationships with over <strong>10,000</strong> wholesalers, retailers and hairstylists worldwide thanks to our excellent reputation as well as our high-quality goods and services.', 'vesora' ),
		'cta_label'   => __( 'See more', 'vesora' ),
		'cta_url'     => home_url( '/about/' ),
	);
	$settings  = wp_parse_args( get_option( 'vesora_homepage_video', array() ), $defaults );
	$video_id  = absint( $settings['video_id'] );
	$poster_id = absint( $settings['poster_id'] );

	$settings['video_id']   = $video_id;
	$settings['poster_id']  = $poster_id;
	$settings['video_url']  = $video_id ? wp_get_attachment_url( $video_id ) : '';
	$settings['video_type'] = $video_id ? (string) get_post_mime_type( $video_id ) : '';
	$settings['video_name'] = $video_id ? get_the_title( $video_id ) : '';
	$settings['poster_url'] = $poster_id ? wp_get_attachment_image_url( $poster_id, 'full' ) : '';
	$settings['youtube_embed_url'] = vesora_youtube_embed_url( $settings['youtube_url'] );

	return $settings;
}

function vesora_maybe_upgrade_homepage_video() {
	if ( '1' === get_option( 'vesora_homepage_video_youtube_upgrade' ) ) {
		return;
	}
	$current = get_option( 'vesora_homepage_video', array() );
	if ( empty( $current['youtube_url'] ) && empty( $current['video_id'] ) ) {
		$defaults = vesora_homepage_video();
		update_option(
			'vesora_homepage_video',
			array_intersect_key( $defaults, array_flip( array( 'enabled', 'video_id', 'poster_id', 'youtube_url', 'title', 'copy', 'cta_label', 'cta_url' ) ) ),
			false
		);
	}
	update_option( 'vesora_homepage_video_youtube_upgrade', '1', false );
}
add_action( 'init', 'vesora_maybe_upgrade_homepage_video', 35 );

function vesora_import_feedback_asset( $item ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_vesora_feedback_source',
			'meta_value'     => $item['file'],
		)
	);
	if ( $existing ) {
		return absint( $existing[0] );
	}

	$path = get_template_directory() . '/assets/images/feedback/' . $item['file'];
	if ( ! is_readable( $path ) ) {
		return 0;
	}

	$contents = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $contents ) {
		return 0;
	}

	$upload = wp_upload_bits( $item['file'], null, $contents );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$filetype      = wp_check_filetype( $upload['file'] );
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $item['title'],
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $item['title'] );
	update_post_meta( $attachment_id, '_vesora_feedback_source', $item['file'] );

	return absint( $attachment_id );
}

function vesora_maybe_seed_feedback_gallery() {
	$seed_version = '2';
	if ( $seed_version === get_option( 'vesora_feedback_gallery_seeded' ) ) {
		return;
	}

	$ids = array();
	$manifest = vesora_feedback_seed_manifest();
	foreach ( $manifest as $item ) {
		$attachment_id = vesora_import_feedback_asset( $item );
		if ( $attachment_id ) {
			$ids[] = $attachment_id;
		}
	}

	if ( count( $ids ) !== count( $manifest ) ) {
		return;
	}

	$old_attachment_ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_vesora_feedback_source',
		)
	);

	update_option( 'vesora_feedback_gallery', $ids, false );
	update_option( 'vesora_feedback_gallery_seeded', $seed_version, false );

	foreach ( array_diff( array_map( 'absint', $old_attachment_ids ), $ids ) as $old_attachment_id ) {
		wp_delete_attachment( $old_attachment_id, true );
	}
}
add_action( 'init', 'vesora_maybe_seed_feedback_gallery', 30 );

function vesora_sanitize_homepage_images( $input ) {
	$output = array();
	$groups = vesora_homepage_image_groups();
	$input  = is_array( $input ) ? $input : array();

	foreach ( $groups as $group_key => $group ) {
		$output[ $group_key ] = array();
		foreach ( $group['items'] as $item_key => $item ) {
			$attachment = isset( $input[ $group_key ][ $item_key ] ) ? absint( $input[ $group_key ][ $item_key ] ) : 0;
			$output[ $group_key ][ $item_key ] = $attachment && wp_attachment_is_image( $attachment ) ? $attachment : 0;
		}
	}

	return $output;
}

function vesora_register_homepage_image_setting() {
	register_setting(
		'vesora_homepage_images_group',
		'vesora_homepage_banners',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'vesora_sanitize_homepage_banners',
			'default'           => array(),
		)
	);
	register_setting(
		'vesora_homepage_images_group',
		'vesora_homepage_images',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'vesora_sanitize_homepage_images',
			'default'           => array(),
		)
	);
	register_setting(
		'vesora_homepage_images_group',
		'vesora_feedback_gallery',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'vesora_sanitize_feedback_gallery',
			'default'           => array(),
		)
	);
	register_setting(
		'vesora_homepage_images_group',
		'vesora_homepage_video',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'vesora_sanitize_homepage_video',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'vesora_register_homepage_image_setting' );

function vesora_add_homepage_images_menu() {
	add_menu_page(
		__( 'Ảnh trang chủ Vesora', 'vesora' ),
		__( 'Ảnh trang chủ', 'vesora' ),
		'edit_theme_options',
		'vesora-homepage-images',
		'vesora_homepage_images_admin_page',
		'dashicons-format-gallery',
		59
	);
}
add_action( 'admin_menu', 'vesora_add_homepage_images_menu' );

function vesora_homepage_images_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_vesora-homepage-images' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'vesora-homepage-images-admin', get_template_directory_uri() . '/assets/css/admin-homepage-images.css', array(), VESORA_VERSION );
	wp_enqueue_script( 'vesora-homepage-images-admin', get_template_directory_uri() . '/assets/js/admin-homepage-images.js', array( 'jquery' ), VESORA_VERSION, true );
	wp_localize_script(
		'vesora-homepage-images-admin',
		'vesoraHomepageImages',
		array(
			'frameTitle'   => __( 'Chọn ảnh cho trang chủ', 'vesora' ),
			'frameButton'  => __( 'Dùng ảnh này', 'vesora' ),
			'chooseLabel'  => __( 'Chọn ảnh', 'vesora' ),
			'changeLabel'  => __( 'Thay ảnh', 'vesora' ),
			'defaultState' => __( 'Đang dùng ảnh mặc định', 'vesora' ),
			'customState'  => __( 'Đã chọn ảnh từ Media Library', 'vesora' ),
			'videoFrameTitle'   => __( 'Chọn video giới thiệu', 'vesora' ),
			'videoFrameButton'  => __( 'Dùng video này', 'vesora' ),
			'posterFrameTitle'  => __( 'Chọn ảnh bìa video', 'vesora' ),
			'posterFrameButton' => __( 'Dùng ảnh này', 'vesora' ),
			'videoEmpty'        => __( 'Chưa chọn video', 'vesora' ),
			'videoChooseLabel'  => __( 'Chọn video', 'vesora' ),
			'videoChangeLabel'  => __( 'Thay video', 'vesora' ),
			'posterChooseLabel' => __( 'Chọn ảnh bìa', 'vesora' ),
			'posterChangeLabel' => __( 'Thay ảnh bìa', 'vesora' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'vesora_homepage_images_admin_assets' );

function vesora_homepage_images_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$groups          = vesora_homepage_image_groups();
	$homepage_banners = vesora_homepage_banners();
	$feedback_images = vesora_feedback_gallery_images();
	$homepage_video  = vesora_homepage_video();
	?>
	<div class="wrap vesora-home-images">
		<header class="vesora-home-images__intro">
			<div>
				<h1><?php esc_html_e( 'Ảnh trang chủ Vesora', 'vesora' ); ?></h1>
				<p><?php esc_html_e( 'Chọn ảnh từ Media Library. Ảnh mới sẽ xuất hiện trên trang chủ ngay sau khi lưu.', 'vesora' ); ?></p>
			</div>
		</header>

		<?php settings_errors(); ?>

		<form action="options.php" method="post">
			<?php settings_fields( 'vesora_homepage_images_group' ); ?>

			<nav class="nav-tab-wrapper vesora-home-images__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Nhóm ảnh trang chủ', 'vesora' ); ?>">
				<button class="nav-tab nav-tab-active" type="button" role="tab" id="vesora-tab-banners" aria-controls="vesora-panel-banners" aria-selected="true" data-vesora-tab="banners"><?php esc_html_e( 'Banner trang chủ', 'vesora' ); ?></button>
				<?php $first_tab = true; ?>
				<?php foreach ( $groups as $group_key => $group ) : ?>
					<button class="nav-tab" type="button" role="tab" id="vesora-tab-<?php echo esc_attr( $group_key ); ?>" aria-controls="vesora-panel-<?php echo esc_attr( $group_key ); ?>" aria-selected="false" tabindex="-1" data-vesora-tab="<?php echo esc_attr( $group_key ); ?>"><?php echo esc_html( $group['label'] ); ?></button>
					<?php $first_tab = false; ?>
				<?php endforeach; ?>
				<button class="nav-tab" type="button" role="tab" id="vesora-tab-feedback" aria-controls="vesora-panel-feedback" aria-selected="false" tabindex="-1" data-vesora-tab="feedback"><?php esc_html_e( 'Feedback khách hàng', 'vesora' ); ?></button>
				<button class="nav-tab" type="button" role="tab" id="vesora-tab-video" aria-controls="vesora-panel-video" aria-selected="false" tabindex="-1" data-vesora-tab="video"><?php esc_html_e( 'Video giới thiệu', 'vesora' ); ?></button>
			</nav>

			<section class="vesora-home-images__panel" id="vesora-panel-banners" role="tabpanel" aria-labelledby="vesora-tab-banners" data-vesora-panel="banners">
				<div class="vesora-home-images__section-heading">
					<h2><?php esc_html_e( 'Banner trang chủ', 'vesora' ); ?></h2>
					<p><?php esc_html_e( 'Quản lý tối đa 6 banner. Toàn bộ banner có thể bấm; CTA là nhãn nút hiển thị thêm trên ảnh.', 'vesora' ); ?></p>
					<p class="vesora-home-images__recommendation"><?php esc_html_e( 'Khuyên dùng: desktop 2033 × 773 px, mobile ảnh vuông 1254 × 1254 px.', 'vesora' ); ?></p>
				</div>
				<div class="vesora-banner-admin">
					<?php foreach ( $homepage_banners as $index => $banner ) : ?>
						<article class="vesora-banner-card" data-vesora-banner-card>
							<h3><?php printf( esc_html__( 'Banner %d', 'vesora' ), $index + 1 ); ?></h3>
							<label><input type="hidden" name="vesora_homepage_banners[<?php echo esc_attr( $index ); ?>][enabled]" value="0"><input type="checkbox" name="vesora_homepage_banners[<?php echo esc_attr( $index ); ?>][enabled]" value="1" <?php checked( ! empty( $banner['enabled'] ) ); ?>> <?php esc_html_e( 'Hiển thị banner', 'vesora' ); ?></label>
							<div class="vesora-banner-card__media-grid">
								<div><strong><?php esc_html_e( 'Ảnh desktop', 'vesora' ); ?></strong><div class="vesora-banner-card__preview"><img src="<?php echo esc_url( $banner['desktop_url'] ); ?>" alt="" data-vesora-banner-preview="desktop"></div><input type="hidden" name="vesora_homepage_banners[<?php echo esc_attr( $index ); ?>][desktop_id]" value="<?php echo esc_attr( $banner['desktop_id'] ); ?>" data-vesora-banner-id="desktop"><button class="button" type="button" data-vesora-banner-select="desktop"><?php esc_html_e( 'Chọn ảnh desktop', 'vesora' ); ?></button></div>
								<div><strong><?php esc_html_e( 'Ảnh mobile', 'vesora' ); ?></strong><div class="vesora-banner-card__preview vesora-banner-card__preview--mobile"><img src="<?php echo esc_url( $banner['mobile_url'] ); ?>" alt="" data-vesora-banner-preview="mobile"></div><input type="hidden" name="vesora_homepage_banners[<?php echo esc_attr( $index ); ?>][mobile_id]" value="<?php echo esc_attr( $banner['mobile_id'] ); ?>" data-vesora-banner-id="mobile"><button class="button" type="button" data-vesora-banner-select="mobile"><?php esc_html_e( 'Chọn ảnh mobile', 'vesora' ); ?></button></div>
							</div>
							<label><?php esc_html_e( 'Liên kết khi bấm banner', 'vesora' ); ?><input class="widefat" type="url" name="vesora_homepage_banners[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $banner['url'] ); ?>" placeholder="https://"></label>
							<label><?php esc_html_e( 'Nhãn CTA (không bắt buộc)', 'vesora' ); ?><input class="widefat" type="text" name="vesora_homepage_banners[<?php echo esc_attr( $index ); ?>][cta_label]" value="<?php echo esc_attr( $banner['cta_label'] ); ?>" maxlength="50"></label>
							<label><?php esc_html_e( 'Mô tả ảnh', 'vesora' ); ?><input class="widefat" type="text" name="vesora_homepage_banners[<?php echo esc_attr( $index ); ?>][alt]" value="<?php echo esc_attr( $banner['alt'] ); ?>" maxlength="160"></label>
						</article>
					<?php endforeach; ?>
				</div>
			</section>

			<?php $first_panel = false; ?>
			<?php foreach ( $groups as $group_key => $group ) : ?>
				<section class="vesora-home-images__panel" id="vesora-panel-<?php echo esc_attr( $group_key ); ?>" role="tabpanel" aria-labelledby="vesora-tab-<?php echo esc_attr( $group_key ); ?>" data-vesora-panel="<?php echo esc_attr( $group_key ); ?>" hidden>
					<div class="vesora-home-images__section-heading">
						<h2><?php echo esc_html( $group['title'] ); ?></h2>
						<p><?php echo esc_html( $group['description'] ); ?></p>
						<p class="vesora-home-images__recommendation"><?php esc_html_e( 'Khuyên dùng: ảnh dọc, tỉ lệ 3:4, rộng tối thiểu 1200 px.', 'vesora' ); ?></p>
					</div>

					<div class="vesora-home-images__grid">
						<?php foreach ( $group['items'] as $item_key => $item ) : ?>
							<?php $image = vesora_homepage_card_image( $group_key, $item_key ); ?>
							<article class="vesora-home-image-card" data-vesora-image-card data-fallback-url="<?php echo esc_url( get_template_directory_uri() . '/assets/images/' . $item['file'] ); ?>">
								<div class="vesora-home-image-card__preview">
									<img src="<?php echo esc_url( $image['url'] ); ?>" alt="" data-vesora-preview>
								</div>
								<div class="vesora-home-image-card__body">
									<h3><?php echo esc_html( $item['name'] ); ?></h3>
									<?php if ( ! empty( $item['code'] ) ) : ?>
										<p class="vesora-home-image-card__code"><?php echo esc_html( $item['code'] ); ?></p>
									<?php endif; ?>
									<p class="vesora-home-image-card__status" data-vesora-status aria-live="polite"><?php echo esc_html( $image['is_custom'] ? __( 'Đã chọn ảnh từ Media Library', 'vesora' ) : __( 'Đang dùng ảnh mặc định', 'vesora' ) ); ?></p>
									<input type="hidden" name="vesora_homepage_images[<?php echo esc_attr( $group_key ); ?>][<?php echo esc_attr( $item_key ); ?>]" value="<?php echo esc_attr( $image['attachment_id'] ); ?>" data-vesora-image-id>
									<div class="vesora-home-image-card__actions">
										<button class="button button-primary" type="button" data-vesora-select><?php echo esc_html( $image['is_custom'] ? __( 'Thay ảnh', 'vesora' ) : __( 'Chọn ảnh', 'vesora' ) ); ?></button>
										<button class="button button-link-delete" type="button" data-vesora-reset<?php echo $image['is_custom'] ? '' : ' hidden'; ?>><?php esc_html_e( 'Dùng ảnh mặc định', 'vesora' ); ?></button>
									</div>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>

			<section class="vesora-home-images__panel" id="vesora-panel-feedback" role="tabpanel" aria-labelledby="vesora-tab-feedback" data-vesora-panel="feedback" hidden>
				<div class="vesora-home-images__section-heading vesora-home-images__section-heading--with-action">
					<div>
						<h2><?php esc_html_e( 'Feedback khách hàng', 'vesora' ); ?></h2>
						<p><?php esc_html_e( 'Các ảnh sẽ xuất hiện theo đúng thứ tự bên dưới. Bạn có thể thêm nhiều ảnh cùng lúc, thay ảnh, xóa ảnh hoặc đổi vị trí.', 'vesora' ); ?></p>
						<p class="vesora-home-images__recommendation"><?php esc_html_e( 'Khuyên dùng: ảnh rõ chữ, không để lộ số điện thoại hoặc thông tin riêng tư của khách hàng. Dung lượng tối đa 32 MB mỗi ảnh.', 'vesora' ); ?></p>
					</div>
					<button class="button button-primary" type="button" data-vesora-feedback-add><?php esc_html_e( 'Thêm ảnh feedback', 'vesora' ); ?></button>
				</div>

				<p class="vesora-feedback-admin__empty" data-vesora-feedback-empty<?php echo $feedback_images ? ' hidden' : ''; ?>><?php esc_html_e( 'Chưa có ảnh feedback. Bấm nút thêm ảnh để bắt đầu.', 'vesora' ); ?></p>
				<input type="hidden" name="vesora_feedback_gallery[]" value="">
				<div class="vesora-feedback-admin" data-vesora-feedback-list data-change-label="<?php esc_attr_e( 'Thay ảnh', 'vesora' ); ?>" data-remove-label="<?php esc_attr_e( 'Xóa', 'vesora' ); ?>" data-move-up-label="<?php esc_attr_e( 'Chuyển ảnh lên trước', 'vesora' ); ?>" data-move-down-label="<?php esc_attr_e( 'Chuyển ảnh xuống sau', 'vesora' ); ?>">
					<?php foreach ( $feedback_images as $index => $image ) : ?>
						<article class="vesora-feedback-admin__card" data-vesora-feedback-card>
							<div class="vesora-feedback-admin__position" data-vesora-feedback-position><?php echo esc_html( $index + 1 ); ?></div>
							<div class="vesora-feedback-admin__preview"><img src="<?php echo esc_url( $image['url'] ); ?>" alt="" data-vesora-feedback-preview></div>
							<input type="hidden" name="vesora_feedback_gallery[]" value="<?php echo esc_attr( $image['id'] ); ?>" data-vesora-feedback-id>
							<div class="vesora-feedback-admin__actions">
								<button class="button" type="button" data-vesora-feedback-change><?php esc_html_e( 'Thay ảnh', 'vesora' ); ?></button>
								<button class="button" type="button" data-vesora-feedback-up aria-label="<?php esc_attr_e( 'Chuyển ảnh lên trước', 'vesora' ); ?>"><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>
								<button class="button" type="button" data-vesora-feedback-down aria-label="<?php esc_attr_e( 'Chuyển ảnh xuống sau', 'vesora' ); ?>"><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>
								<button class="button button-link-delete" type="button" data-vesora-feedback-remove><?php esc_html_e( 'Xóa', 'vesora' ); ?></button>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="vesora-home-images__panel" id="vesora-panel-video" role="tabpanel" aria-labelledby="vesora-tab-video" data-vesora-panel="video" hidden>
				<div class="vesora-home-images__section-heading">
					<h2><?php esc_html_e( 'Video giới thiệu Vesora', 'vesora' ); ?></h2>
					<p><?php esc_html_e( 'Video và nội dung sẽ xuất hiện phía trên phần lý do chọn Vesora Hair. Có thể dùng YouTube hoặc video MP4 tải lên.', 'vesora' ); ?></p>
					<p class="vesora-home-images__recommendation"><?php esc_html_e( 'YouTube hỗ trợ liên kết video, playlist hoặc kênh Vesora. Video tải lên nên là MP4 H.264 tỷ lệ 16:9.', 'vesora' ); ?></p>
				</div>

				<div class="vesora-video-admin">
					<div class="vesora-video-admin__media">
						<div class="vesora-video-admin__preview" data-vesora-video-preview-wrap<?php echo $homepage_video['video_url'] ? '' : ' hidden'; ?>>
							<video controls preload="metadata" data-vesora-video-preview<?php echo $homepage_video['poster_url'] ? ' poster="' . esc_url( $homepage_video['poster_url'] ) . '"' : ''; ?>>
								<?php if ( $homepage_video['video_url'] ) : ?>
									<source src="<?php echo esc_url( $homepage_video['video_url'] ); ?>" type="<?php echo esc_attr( $homepage_video['video_type'] ); ?>">
								<?php endif; ?>
							</video>
						</div>
						<div class="vesora-video-admin__empty" data-vesora-video-empty<?php echo $homepage_video['video_url'] ? ' hidden' : ''; ?>>
							<span class="dashicons dashicons-video-alt3" aria-hidden="true"></span>
							<p><?php esc_html_e( 'Chưa chọn video giới thiệu', 'vesora' ); ?></p>
						</div>
						<input type="hidden" name="vesora_homepage_video[video_id]" value="<?php echo esc_attr( $homepage_video['video_id'] ); ?>" data-vesora-video-id>
						<p class="vesora-video-admin__filename" data-vesora-video-name><?php echo esc_html( $homepage_video['video_name'] ? $homepage_video['video_name'] : __( 'Chưa chọn video', 'vesora' ) ); ?></p>
						<div class="vesora-video-admin__actions">
							<button class="button button-primary" type="button" data-vesora-video-select><?php echo esc_html( $homepage_video['video_id'] ? __( 'Thay video', 'vesora' ) : __( 'Chọn video', 'vesora' ) ); ?></button>
							<button class="button button-link-delete" type="button" data-vesora-video-remove<?php echo $homepage_video['video_id'] ? '' : ' hidden'; ?>><?php esc_html_e( 'Xóa video', 'vesora' ); ?></button>
						</div>
					</div>

					<div class="vesora-video-admin__settings">
						<label class="vesora-video-admin__toggle">
							<input type="hidden" name="vesora_homepage_video[enabled]" value="0">
							<input type="checkbox" name="vesora_homepage_video[enabled]" value="1"<?php checked( ! empty( $homepage_video['enabled'] ) ); ?> data-vesora-video-enabled>
							<span><?php esc_html_e( 'Hiển thị video trên trang chủ', 'vesora' ); ?></span>
						</label>

						<label for="vesora-home-youtube-url"><?php esc_html_e( 'URL YouTube', 'vesora' ); ?></label>
						<input class="large-text" id="vesora-home-youtube-url" type="url" name="vesora_homepage_video[youtube_url]" value="<?php echo esc_attr( $homepage_video['youtube_url'] ); ?>" placeholder="https://www.youtube.com/watch?v=...">

						<label for="vesora-home-video-title"><?php esc_html_e( 'Tiêu đề', 'vesora' ); ?></label>
						<input class="regular-text" id="vesora-home-video-title" type="text" name="vesora_homepage_video[title]" value="<?php echo esc_attr( $homepage_video['title'] ); ?>" maxlength="100">

						<label for="vesora-home-video-copy"><?php esc_html_e( 'Mô tả ngắn', 'vesora' ); ?></label>
						<textarea class="large-text" id="vesora-home-video-copy" name="vesora_homepage_video[copy]" rows="8" maxlength="1400"><?php echo esc_textarea( $homepage_video['copy'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Có thể dùng thẻ <strong> để in đậm cụm từ quan trọng.', 'vesora' ); ?></p>

						<label for="vesora-home-video-cta-label"><?php esc_html_e( 'Nhãn CTA', 'vesora' ); ?></label>
						<input class="regular-text" id="vesora-home-video-cta-label" type="text" name="vesora_homepage_video[cta_label]" value="<?php echo esc_attr( $homepage_video['cta_label'] ); ?>" maxlength="50">

						<label for="vesora-home-video-cta-url"><?php esc_html_e( 'Liên kết CTA', 'vesora' ); ?></label>
						<input class="large-text" id="vesora-home-video-cta-url" type="url" name="vesora_homepage_video[cta_url]" value="<?php echo esc_attr( $homepage_video['cta_url'] ); ?>" placeholder="https://">

						<div class="vesora-video-admin__poster">
							<p class="vesora-video-admin__label"><?php esc_html_e( 'Ảnh bìa video', 'vesora' ); ?></p>
							<div class="vesora-video-admin__poster-preview" data-vesora-poster-preview-wrap<?php echo $homepage_video['poster_url'] ? '' : ' hidden'; ?>>
								<img src="<?php echo esc_url( $homepage_video['poster_url'] ); ?>" alt="" data-vesora-poster-preview>
							</div>
							<input type="hidden" name="vesora_homepage_video[poster_id]" value="<?php echo esc_attr( $homepage_video['poster_id'] ); ?>" data-vesora-poster-id>
							<div class="vesora-video-admin__actions">
								<button class="button" type="button" data-vesora-poster-select><?php echo esc_html( $homepage_video['poster_id'] ? __( 'Thay ảnh bìa', 'vesora' ) : __( 'Chọn ảnh bìa', 'vesora' ) ); ?></button>
								<button class="button button-link-delete" type="button" data-vesora-poster-remove<?php echo $homepage_video['poster_id'] ? '' : ' hidden'; ?>><?php esc_html_e( 'Xóa ảnh bìa', 'vesora' ); ?></button>
							</div>
						</div>
					</div>
				</div>
			</section>

			<div class="vesora-home-images__save">
				<?php submit_button( __( 'Lưu thay đổi', 'vesora' ), 'primary', 'submit', false ); ?>
				<span><?php esc_html_e( 'Ảnh chỉ thay đổi sau khi bạn bấm lưu.', 'vesora' ); ?></span>
			</div>
		</form>
	</div>
	<?php
}
