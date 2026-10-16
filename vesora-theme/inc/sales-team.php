<?php
/**
 * Sales team profiles and Contact page presentation.
 *
 * @package Vesora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the admin-managed sales profile type.
 */
function vesora_register_sales_profiles() {
	$labels = array(
		'name'                  => __( 'Sales Team', 'vesora' ),
		'singular_name'         => __( 'Sales contact', 'vesora' ),
		'menu_name'             => __( 'Sales Team', 'vesora' ),
		'name_admin_bar'        => __( 'Sales contact', 'vesora' ),
		'add_new'               => __( 'Add contact', 'vesora' ),
		'add_new_item'          => __( 'Add sales contact', 'vesora' ),
		'edit_item'             => __( 'Edit sales contact', 'vesora' ),
		'new_item'              => __( 'New sales contact', 'vesora' ),
		'view_item'             => __( 'View sales contact', 'vesora' ),
		'search_items'          => __( 'Search sales contacts', 'vesora' ),
		'not_found'             => __( 'No sales contacts found.', 'vesora' ),
		'not_found_in_trash'    => __( 'No sales contacts found in Trash.', 'vesora' ),
		'featured_image'        => __( 'Profile photo', 'vesora' ),
		'set_featured_image'    => __( 'Choose profile photo', 'vesora' ),
		'remove_featured_image' => __( 'Remove profile photo', 'vesora' ),
		'use_featured_image'    => __( 'Use as profile photo', 'vesora' ),
	);

	register_post_type(
		'vesora_sales',
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'menu_position'       => 59,
			'menu_icon'           => 'dashicons-businessperson',
			'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'vesora_register_sales_profiles' );

/**
 * Scope the wider sales grid to the Contact page only.
 */
function vesora_sales_contact_body_class( $classes ) {
	if ( is_page( 'contact' ) ) {
		$classes[] = 'vesora-contact-page';
	}

	return $classes;
}
add_filter( 'body_class', 'vesora_sales_contact_body_class' );

/**
 * Replace the generic title placeholder with an instruction.
 */
function vesora_sales_title_placeholder( $title, $post ) {
	if ( $post && 'vesora_sales' === $post->post_type ) {
		return __( 'Salesperson name', 'vesora' );
	}

	return $title;
}
add_filter( 'enter_title_here', 'vesora_sales_title_placeholder', 10, 2 );

/**
 * Add contact fields to the sales profile editor.
 */
function vesora_sales_add_meta_boxes() {
	add_meta_box(
		'vesora-sales-details',
		__( 'Contact details', 'vesora' ),
		'vesora_sales_details_meta_box',
		'vesora_sales',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'vesora_sales_add_meta_boxes' );

function vesora_sales_details_meta_box( $post ) {
	$role      = get_post_meta( $post->ID, '_vesora_sales_role', true );
	$whatsapp  = get_post_meta( $post->ID, '_vesora_sales_whatsapp', true );
	$instagram = get_post_meta( $post->ID, '_vesora_sales_instagram', true );

	wp_nonce_field( 'vesora_save_sales_profile', 'vesora_sales_nonce' );
	?>
	<style>
		.vesora-sales-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 24px; }
		.vesora-sales-fields label { display: block; margin-bottom: 7px; font-weight: 600; }
		.vesora-sales-fields input { width: 100%; }
		.vesora-sales-fields p { grid-column: 1 / -1; margin: 0; color: #646970; }
		@media (max-width: 782px) { .vesora-sales-fields { grid-template-columns: 1fr; } .vesora-sales-fields p { grid-column: auto; } }
	</style>
	<div class="vesora-sales-fields">
		<div>
			<label for="vesora-sales-role"><?php esc_html_e( 'Role', 'vesora' ); ?></label>
			<input id="vesora-sales-role" name="vesora_sales_role" type="text" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'Sales specialist', 'vesora' ); ?>">
		</div>
		<div>
			<label for="vesora-sales-whatsapp"><?php esc_html_e( 'WhatsApp number', 'vesora' ); ?></label>
			<input id="vesora-sales-whatsapp" name="vesora_sales_whatsapp" type="tel" inputmode="numeric" value="<?php echo esc_attr( $whatsapp ); ?>" placeholder="84912345678" required>
		</div>
		<div>
			<label for="vesora-sales-instagram"><?php esc_html_e( 'Instagram', 'vesora' ); ?></label>
			<input id="vesora-sales-instagram" name="vesora_sales_instagram" type="text" value="<?php echo esc_attr( $instagram ); ?>" placeholder="@vesorahair">
		</div>
		<p><?php esc_html_e( 'Use a square Featured Image for the profile photo. Enter only digits for WhatsApp, including the country code. Instagram accepts either @username or a full URL. Use the Order field to control display order.', 'vesora' ); ?></p>
	</div>
	<?php
}

/**
 * Save a sales profile safely.
 */
function vesora_sales_save_profile( $post_id ) {
	if ( ! isset( $_POST['vesora_sales_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vesora_sales_nonce'] ) ), 'vesora_save_sales_profile' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'_vesora_sales_role'      => isset( $_POST['vesora_sales_role'] ) ? sanitize_text_field( wp_unslash( $_POST['vesora_sales_role'] ) ) : '',
		'_vesora_sales_whatsapp'  => isset( $_POST['vesora_sales_whatsapp'] ) ? preg_replace( '/\D+/', '', wp_unslash( $_POST['vesora_sales_whatsapp'] ) ) : '',
		'_vesora_sales_instagram' => isset( $_POST['vesora_sales_instagram'] ) && function_exists( 'vesora_normalize_instagram_url' ) ? vesora_normalize_instagram_url( wp_unslash( $_POST['vesora_sales_instagram'] ) ) : '',
	);

	foreach ( $fields as $key => $value ) {
		if ( '' !== $value ) {
			update_post_meta( $post_id, $key, $value );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
}
add_action( 'save_post_vesora_sales', 'vesora_sales_save_profile' );

/**
 * Useful columns for non-technical administrators.
 */
function vesora_sales_admin_columns( $columns ) {
	return array(
		'cb'                => isset( $columns['cb'] ) ? $columns['cb'] : '<input type="checkbox">',
		'vesora_photo'      => __( 'Photo', 'vesora' ),
		'title'             => __( 'Name', 'vesora' ),
		'vesora_role'       => __( 'Role', 'vesora' ),
		'vesora_whatsapp'   => __( 'WhatsApp', 'vesora' ),
		'vesora_instagram'  => __( 'Instagram', 'vesora' ),
		'vesora_order'      => __( 'Order', 'vesora' ),
		'date'              => isset( $columns['date'] ) ? $columns['date'] : __( 'Date', 'vesora' ),
	);
}
add_filter( 'manage_vesora_sales_posts_columns', 'vesora_sales_admin_columns' );

function vesora_sales_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'vesora_photo':
			echo get_the_post_thumbnail( $post_id, array( 52, 52 ), array( 'style' => 'width:52px;height:52px;object-fit:cover;border-radius:3px;' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;
		case 'vesora_role':
			echo esc_html( get_post_meta( $post_id, '_vesora_sales_role', true ) );
			break;
		case 'vesora_whatsapp':
			echo esc_html( get_post_meta( $post_id, '_vesora_sales_whatsapp', true ) );
			break;
		case 'vesora_instagram':
			$instagram = get_post_meta( $post_id, '_vesora_sales_instagram', true );
			if ( $instagram ) {
				echo '<a href="' . esc_url( $instagram ) . '" target="_blank" rel="noopener">' . esc_html__( 'View profile', 'vesora' ) . '</a>';
			} else {
				echo '&mdash;';
			}
			break;
		case 'vesora_order':
			echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
			break;
	}
}
add_action( 'manage_vesora_sales_posts_custom_column', 'vesora_sales_admin_column_content', 10, 2 );

/**
 * Get published Vesora sales profiles in their admin-defined order.
 *
 * @return WP_Post[]
 */
function vesora_get_sales_profiles() {
	return get_posts(
		array(
			'post_type'      => 'vesora_sales',
			'post_status'    => 'publish',
			'posts_per_page' => 40,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

/**
 * Build the reusable sales profile cards.
 *
 * @param WP_Post[]|null $profiles Optional profiles. Defaults to all published profiles.
 * @return string
 */
function vesora_sales_team_cards( $profiles = null ) {
	if ( null === $profiles ) {
		$profiles = vesora_get_sales_profiles();
	}

	if ( ! $profiles ) {
		return '';
	}

	ob_start();
	?>
	<div class="vesora-sales-team__grid">
			<?php foreach ( $profiles as $profile ) : ?>
				<?php
				$role       = get_post_meta( $profile->ID, '_vesora_sales_role', true );
				$whatsapp   = preg_replace( '/\D+/', '', (string) get_post_meta( $profile->ID, '_vesora_sales_whatsapp', true ) );
				$instagram = esc_url( get_post_meta( $profile->ID, '_vesora_sales_instagram', true ) );
				if ( ! $instagram && function_exists( 'vesora_contact_value' ) ) {
					$instagram = esc_url( vesora_contact_value( 'instagram_url' ) );
				}
				$message    = sprintf( __( 'Hello %s, I would like to learn more about Vesora Hair products.', 'vesora' ), get_the_title( $profile ) );
				$whatsapp_url = $whatsapp ? add_query_arg( 'text', $message, 'https://wa.me/' . $whatsapp ) : '';
				?>
				<article class="vesora-sales-card">
					<div class="vesora-sales-card__media">
						<?php if ( has_post_thumbnail( $profile ) ) : ?>
							<?php echo get_the_post_thumbnail( $profile, 'large', array( 'loading' => 'lazy', 'alt' => get_the_title( $profile ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<div class="vesora-sales-card__placeholder" aria-hidden="true">
								<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-nau.png' ); ?>" alt="">
							</div>
						<?php endif; ?>
					</div>
					<div class="vesora-sales-card__body">
						<h3><?php echo esc_html( get_the_title( $profile ) ); ?></h3>
						<?php if ( $role ) : ?><p class="vesora-sales-card__role"><?php echo esc_html( $role ); ?></p><?php endif; ?>
						<div class="vesora-sales-card__actions">
							<?php if ( $whatsapp_url ) : ?>
								<a class="vesora-sales-card__social vesora-sales-card__whatsapp" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( sprintf( __( 'Chat with %s on WhatsApp', 'vesora' ), get_the_title( $profile ) ) ); ?>"><i class="fi fi-brands-whatsapp" aria-hidden="true"></i><span><?php esc_html_e( 'WhatsApp', 'vesora' ); ?></span></a>
							<?php endif; ?>
							<?php if ( $instagram ) : ?>
								<a class="vesora-sales-card__social vesora-sales-card__instagram" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( sprintf( __( 'View %s on Instagram', 'vesora' ), get_the_title( $profile ) ) ); ?>"><i class="fi fi-brands-instagram" aria-hidden="true"></i><span><?php esc_html_e( 'Instagram', 'vesora' ); ?></span></a>
							<?php endif; ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Build the standalone public Sales Team section when the shortcode is used.
 */
function vesora_sales_team_shortcode() {
	$cards = vesora_sales_team_cards();
	if ( ! $cards ) {
		return '';
	}

	ob_start();
	?>
	<section class="vesora-sales-team" aria-labelledby="vesora-sales-team-title">
		<header class="vesora-sales-team__header">
			<p class="eyebrow"><?php esc_html_e( 'Direct support', 'vesora' ); ?></p>
			<h2 id="vesora-sales-team-title"><?php esc_html_e( 'Meet your Vesora sales team', 'vesora' ); ?></h2>
			<p><?php esc_html_e( 'Choose the specialist who best matches your market and start a direct WhatsApp conversation.', 'vesora' ); ?></p>
		</header>
		<?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'vesora_sales_team', 'vesora_sales_team_shortcode' );

/**
 * Remove the former Sales Team shortcode from Contact after moving the team home.
 */
function vesora_migrate_contact_sales_team() {
	if ( '2.0.0' === get_option( 'vesora_contact_sales_team_version' ) ) {
		return;
	}

	$page = get_page_by_path( 'contact', OBJECT, 'page' );
	if ( ! $page ) {
		return;
	}

	$content = (string) $page->post_content;
	$updated = preg_replace( '/\s*\[vesora_sales_team(?:\s[^\]]*)?\]\s*/i', "\n\n", $content );
	if ( is_string( $updated ) && $updated !== $content ) {
		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => trim( $updated ),
			)
		);
	}

	update_option( 'vesora_contact_sales_team_version', '2.0.0' );
}
add_action( 'init', 'vesora_migrate_contact_sales_team', 70 );
