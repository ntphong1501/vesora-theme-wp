<?php
/**
 * Vesora editorial blog setup and administration.
 *
 * @package Vesora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prepare standard WordPress pages and categories for the editorial section.
 */
function vesora_seed_blog_platform() {
	if ( '1.0.0' === get_option( 'vesora_blog_platform_version' ) ) {
		return;
	}

	$home = get_page_by_path( 'home', OBJECT, 'page' );
	if ( ! $home ) {
		$home_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => __( 'Home', 'vesora' ),
				'post_name'   => 'home',
			)
		);
	} else {
		$home_id = $home->ID;
	}

	$blog = get_page_by_path( 'blog', OBJECT, 'page' );
	if ( ! $blog ) {
		$blog_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => __( 'Blogs', 'vesora' ),
				'post_name'   => 'blog',
			)
		);
	} else {
		$blog_id = $blog->ID;
	}

	if ( ! is_wp_error( $home_id ) && ! is_wp_error( $blog_id ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $home_id );
		update_option( 'page_for_posts', (int) $blog_id );
	}

	$categories = array(
		'Hair Extensions' => 'hair-extensions',
		'Wig Knowledge'   => 'wig-knowledge',
		'Hairstyles'      => 'hairstyles',
		'Hair Knowledge'  => 'hair-knowledge',
	);
	foreach ( $categories as $name => $slug ) {
		if ( ! term_exists( $slug, 'category' ) ) {
			wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
		}
	}

	update_option( 'vesora_blog_platform_version', '1.0.0' );
}
add_action( 'init', 'vesora_seed_blog_platform', 75 );

/**
 * Give content staff a focused role for creating and publishing blog posts.
 */
function vesora_register_blog_writer_role() {
	$role = get_role( 'vesora_blog_writer' );
	if ( ! $role ) {
		$role = add_role(
			'vesora_blog_writer',
			__( 'Vesora Blog Writer', 'vesora' ),
			array(
				'read'                   => true,
				'upload_files'           => true,
				'edit_posts'             => true,
				'edit_published_posts'   => true,
				'publish_posts'          => true,
				'delete_posts'           => true,
				'delete_published_posts' => true,
			)
		);
	}

	if ( $role ) {
		foreach ( array( 'read', 'upload_files', 'edit_posts', 'edit_published_posts', 'publish_posts', 'delete_posts', 'delete_published_posts' ) as $capability ) {
			$role->add_cap( $capability );
		}
	}
}
add_action( 'init', 'vesora_register_blog_writer_role', 20 );

/**
 * Rename the built-in post workspace so the purpose is obvious to staff.
 */
function vesora_relabel_posts_as_blogs() {
	global $wp_post_types;

	if ( empty( $wp_post_types['post'] ) ) {
		return;
	}

	$labels = $wp_post_types['post']->labels;
	$labels->name               = __( 'Blogs', 'vesora' );
	$labels->singular_name      = __( 'Blog post', 'vesora' );
	$labels->menu_name          = __( 'Blogs', 'vesora' );
	$labels->name_admin_bar     = __( 'Blog post', 'vesora' );
	$labels->add_new            = __( 'Add blog', 'vesora' );
	$labels->add_new_item       = __( 'Add new blog post', 'vesora' );
	$labels->edit_item          = __( 'Edit blog post', 'vesora' );
	$labels->new_item           = __( 'New blog post', 'vesora' );
	$labels->view_item          = __( 'View blog post', 'vesora' );
	$labels->search_items       = __( 'Search blogs', 'vesora' );
	$labels->not_found          = __( 'No blog posts found.', 'vesora' );
	$labels->not_found_in_trash = __( 'No blog posts found in Trash.', 'vesora' );
	$labels->featured_image        = __( 'Blog cover image', 'vesora' );
	$labels->set_featured_image    = __( 'Set blog cover image', 'vesora' );
	$labels->remove_featured_image = __( 'Remove blog cover image', 'vesora' );
	$labels->use_featured_image    = __( 'Use as blog cover image', 'vesora' );
}
add_action( 'init', 'vesora_relabel_posts_as_blogs', 100 );

/** Keep Blogs reliable while the production proxy rejects block-editor REST nonces. */
function vesora_blog_use_classic_editor( $use_block_editor, $post_type ) {
	return 'post' === $post_type ? false : $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'vesora_blog_use_classic_editor', 20, 2 );

/** Use the featured image, or the article's first image for older/imported posts. */
function vesora_blog_cover_url( $post_id, $size = 'large' ) {
	$cover = get_the_post_thumbnail_url( $post_id, $size );
	if ( $cover ) {
		return $cover;
	}

	$content = (string) get_post_field( 'post_content', $post_id );
	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches ) ) {
		return esc_url_raw( html_entity_decode( $matches[1] ) );
	}

	return '';
}

function vesora_blog_admin_menu_labels() {
	global $menu, $submenu;

	if ( isset( $menu[5][0] ) ) {
		$menu[5][0] = __( 'Blogs', 'vesora' );
	}
	if ( isset( $submenu['edit.php'][5][0] ) ) {
		$submenu['edit.php'][5][0] = __( 'All blogs', 'vesora' );
	}
	if ( isset( $submenu['edit.php'][10][0] ) ) {
		$submenu['edit.php'][10][0] = __( 'Add blog', 'vesora' );
	}
}
add_action( 'admin_menu', 'vesora_blog_admin_menu_labels', 100 );

/**
 * Keep publishing guidance next to the editor without replacing Gutenberg.
 */
function vesora_blog_add_editor_guide() {
	add_meta_box(
		'vesora-blog-guide',
		__( 'Vesora blog checklist', 'vesora' ),
		'vesora_blog_editor_guide',
		'post',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes_post', 'vesora_blog_add_editor_guide' );

function vesora_blog_editor_guide() {
	?>
	<ol style="margin:0 0 0 1.2em;line-height:1.65">
		<li><?php esc_html_e( 'Write a clear title for salons and wholesale buyers.', 'vesora' ); ?></li>
		<li><?php esc_html_e( 'Choose one primary category.', 'vesora' ); ?></li>
		<li><?php esc_html_e( 'Set a landscape Blog cover image (recommended 1600 × 900).', 'vesora' ); ?></li>
		<li><?php esc_html_e( 'Write a short excerpt for the blog listing.', 'vesora' ); ?></li>
		<li><?php esc_html_e( 'Preview on desktop and mobile before publishing.', 'vesora' ); ?></li>
	</ol>
	<?php
}

function vesora_blog_admin_columns( $columns ) {
	$updated = array();
	foreach ( $columns as $key => $label ) {
		$updated[ $key ] = $label;
		if ( 'cb' === $key ) {
			$updated['vesora_blog_cover'] = __( 'Cover', 'vesora' );
		}
	}
	return $updated;
}
add_filter( 'manage_post_posts_columns', 'vesora_blog_admin_columns' );

function vesora_blog_admin_column_content( $column, $post_id ) {
	if ( 'vesora_blog_cover' === $column ) {
		$cover = vesora_blog_cover_url( $post_id, 'thumbnail' );
		if ( $cover ) {
			echo '<img src="' . esc_url( $cover ) . '" alt="" style="width:72px;height:48px;object-fit:cover;border-radius:3px;">';
		} else {
			echo '<span aria-hidden="true">—</span>';
		}
	}
}
add_action( 'manage_post_posts_custom_column', 'vesora_blog_admin_column_content', 10, 2 );

function vesora_blog_title_placeholder( $title, $post ) {
	if ( $post && 'post' === $post->post_type ) {
		return __( 'Blog title', 'vesora' );
	}
	return $title;
}
add_filter( 'enter_title_here', 'vesora_blog_title_placeholder', 10, 2 );

function vesora_blog_thumbnail_guidance( $content, $post_id ) {
	if ( 'post' !== get_post_type( $post_id ) ) {
		return $content;
	}

	$hint = '<p style="margin:0 0 10px;color:#646970">' . esc_html__( 'Recommended: landscape JPG, PNG or WebP, 1600 × 900 px and under 2 MB.', 'vesora' ) . '</p>';
	return $hint . $content;
}
add_filter( 'admin_post_thumbnail_html', 'vesora_blog_thumbnail_guidance', 10, 2 );

