<?php
/**
 * Lightweight lead management for the Vesora contact forms.
 *
 * @package Vesora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vesora_lead_statuses() {
	return array(
		'new'       => __( 'New', 'vesora' ),
		'contacted' => __( 'Contacted', 'vesora' ),
		'closed'    => __( 'Closed', 'vesora' ),
		'spam'      => __( 'Spam', 'vesora' ),
	);
}

function vesora_lead_sources() {
	return array(
		'popup'     => __( 'Exclusive Offer Popup', 'vesora' ),
		'wholesale' => __( 'Wholesale Partnership', 'vesora' ),
	);
}

function vesora_new_lead_count() {
	global $wpdb;
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID WHERE p.post_type = %s AND p.post_status = %s AND pm.meta_key = %s AND pm.meta_value = %s",
			'vesora_lead',
			'publish',
			'_vesora_lead_status',
			'new'
		)
	);
}

function vesora_register_lead_post_type() {
	$capabilities = array_fill_keys(
		array(
			'edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts',
			'publish_posts', 'read_private_posts', 'delete_posts', 'delete_private_posts',
			'delete_published_posts', 'delete_others_posts', 'edit_private_posts', 'edit_published_posts',
		),
		'manage_options'
	);
	$capabilities['create_posts'] = 'do_not_allow';

	register_post_type(
		'vesora_lead',
		array(
			'labels' => array(
				'name'          => __( 'Vesora Leads', 'vesora' ),
				'singular_name' => __( 'Vesora Lead', 'vesora' ),
				'menu_name'     => __( 'Vesora Leads', 'vesora' ),
				'all_items'     => __( 'All Leads', 'vesora' ),
				'edit_item'     => __( 'Lead details', 'vesora' ),
				'search_items'  => __( 'Search leads', 'vesora' ),
				'not_found'     => __( 'No leads found.', 'vesora' ),
				'trash'         => __( 'Trash', 'vesora' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 26,
			'supports'            => array( 'title' ),
			'capabilities'        => $capabilities,
			'map_meta_cap'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'vesora_register_lead_post_type', 5 );

/**
 * Add the new-lead badge after WordPress has built the admin menu.
 * Registering HTML in a post-type label causes WordPress to escape it.
 */
function vesora_add_lead_admin_badge() {
	global $menu;
	$count = vesora_new_lead_count();
	if ( $count < 1 || ! is_array( $menu ) ) {
		return;
	}

	foreach ( $menu as &$item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=vesora_lead' === $item[2] ) {
			$item[0] = sprintf(
				'%1$s <span class="awaiting-mod count-%2$d"><span class="pending-count">%3$s</span></span>',
				esc_html__( 'Vesora Leads', 'vesora' ),
				$count,
				esc_html( number_format_i18n( $count ) )
			);
			break;
		}
	}
	unset( $item );
}
add_action( 'admin_menu', 'vesora_add_lead_admin_badge', 99 );

/**
 * Save a validated lead before notification email is attempted.
 *
 * @param array $data Normalized lead values.
 * @return array|WP_Error Array contains the saved ID and duplicate flag.
 */
function vesora_save_lead( $data ) {
	$source  = isset( $data['source'] ) ? sanitize_key( $data['source'] ) : '';
	$sources = vesora_lead_sources();
	if ( ! isset( $sources[ $source ] ) ) {
		return new WP_Error( 'invalid_source', __( 'Invalid lead source.', 'vesora' ) );
	}

	$email     = sanitize_email( $data['email'] ?? '' );
	$whatsapp  = sanitize_text_field( $data['whatsapp'] ?? '' );
	$dedupe_id = hash( 'sha256', strtolower( $email ) . '|' . preg_replace( '/\D+/', '', $whatsapp ) . '|' . $source );
	$cached_id = (int) get_transient( 'vesora_lead_' . $dedupe_id );
	if ( $cached_id && 'vesora_lead' === get_post_type( $cached_id ) ) {
		return array( 'id' => $cached_id, 'duplicate' => true );
	}

	$name = sanitize_text_field( $data['name'] ?? '' );
	$id   = wp_insert_post(
		array(
			'post_type'   => 'vesora_lead',
			'post_status' => 'publish',
			'post_title'  => $name ?: __( 'Unnamed lead', 'vesora' ),
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		return $id;
	}

	$values = array(
		'_vesora_lead_name'          => $name,
		'_vesora_lead_whatsapp'      => $whatsapp,
		'_vesora_lead_email'         => $email,
		'_vesora_lead_country'       => sanitize_text_field( $data['country'] ?? '' ),
		'_vesora_lead_business_type' => sanitize_text_field( $data['business_type'] ?? '' ),
		'_vesora_lead_message'       => sanitize_textarea_field( $data['message'] ?? '' ),
		'_vesora_lead_source'        => $source,
		'_vesora_lead_status'        => 'new',
		'_vesora_lead_notes'         => '',
		'_vesora_lead_email_sent'    => 'pending',
	);

	foreach ( $values as $key => $value ) {
		update_post_meta( $id, $key, $value );
	}

	set_transient( 'vesora_lead_' . $dedupe_id, $id, 2 * MINUTE_IN_SECONDS );
	return array( 'id' => $id, 'duplicate' => false );
}

function vesora_set_lead_email_status( $lead_id, $sent ) {
	update_post_meta( $lead_id, '_vesora_lead_email_sent', $sent ? 'sent' : 'failed' );
}

/**
 * Send a lead notification through the restricted Cloudflare Email Worker.
 *
 * @param string $subject  Notification subject.
 * @param string $message  Plain-text notification body.
 * @param string $reply_to Customer email used for replies.
 * @return bool Whether the Worker accepted and delivered the notification.
 */
function vesora_send_lead_notification( $subject, $message, $reply_to = '' ) {
	$endpoint = defined( 'VESORA_EMAIL_WORKER_URL' )
		? VESORA_EMAIL_WORKER_URL
		: 'https://vesora-lead-notifier.kumoo.workers.dev';
	if ( ! wp_http_validate_url( $endpoint ) ) {
		return false;
	}

	$response = wp_safe_remote_post(
		$endpoint,
		array(
			'timeout'     => 12,
			'redirection' => 0,
			'headers'     => array( 'Content-Type' => 'application/json' ),
			'body'        => wp_json_encode(
				array(
					'subject' => sanitize_text_field( $subject ),
					'message' => sanitize_textarea_field( $message ),
					'replyTo' => sanitize_email( $reply_to ),
				)
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	return is_array( $body ) && ! empty( $body['ok'] );
}

function vesora_lead_admin_link( $lead_id ) {
	return admin_url( 'post.php?post=' . absint( $lead_id ) . '&action=edit' );
}

function vesora_lead_columns( $columns ) {
	return array(
		'cb'                         => $columns['cb'],
		'title'                      => __( 'Name', 'vesora' ),
		'vesora_lead_status'         => __( 'Status', 'vesora' ),
		'vesora_lead_whatsapp'       => __( 'WhatsApp', 'vesora' ),
		'vesora_lead_email'          => __( 'Email', 'vesora' ),
		'vesora_lead_country'        => __( 'Country', 'vesora' ),
		'vesora_lead_business_type'  => __( 'Business Type', 'vesora' ),
		'vesora_lead_source'         => __( 'Source', 'vesora' ),
		'vesora_lead_email_sent'     => __( 'Notification', 'vesora' ),
		'date'                       => __( 'Submitted', 'vesora' ),
	);
}
add_filter( 'manage_vesora_lead_posts_columns', 'vesora_lead_columns' );

function vesora_render_lead_column( $column, $post_id ) {
	$key   = str_replace( 'vesora_lead_', '_vesora_lead_', $column );
	$value = get_post_meta( $post_id, $key, true );

	if ( 'vesora_lead_status' === $column ) {
		$labels = vesora_lead_statuses();
		echo '<strong>' . esc_html( $labels[ $value ] ?? ucfirst( $value ) ) . '</strong>';
	} elseif ( 'vesora_lead_source' === $column ) {
		$labels = vesora_lead_sources();
		echo esc_html( $labels[ $value ] ?? $value );
	} elseif ( 'vesora_lead_email' === $column ) {
		echo '<a href="mailto:' . esc_attr( $value ) . '">' . esc_html( $value ) . '</a>';
	} elseif ( 'vesora_lead_whatsapp' === $column ) {
		echo esc_html( $value );
	} elseif ( 'vesora_lead_email_sent' === $column ) {
		echo esc_html( 'sent' === $value ? __( 'Sent', 'vesora' ) : ( 'failed' === $value ? __( 'Failed', 'vesora' ) : __( 'Pending', 'vesora' ) ) );
	} elseif ( in_array( $column, array( 'vesora_lead_country', 'vesora_lead_business_type' ), true ) ) {
		echo esc_html( $value ?: '—' );
	}
}
add_action( 'manage_vesora_lead_posts_custom_column', 'vesora_render_lead_column', 10, 2 );

function vesora_lead_admin_filters() {
	$screen = get_current_screen();
	if ( ! $screen || 'edit-vesora_lead' !== $screen->id ) {
		return;
	}

	$current_status = isset( $_GET['lead_status'] ) ? sanitize_key( wp_unslash( $_GET['lead_status'] ) ) : '';
	$current_source = isset( $_GET['lead_source'] ) ? sanitize_key( wp_unslash( $_GET['lead_source'] ) ) : '';

	echo '<select name="lead_status"><option value="">' . esc_html__( 'All statuses', 'vesora' ) . '</option>';
	foreach ( vesora_lead_statuses() as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $current_status, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';

	echo '<select name="lead_source"><option value="">' . esc_html__( 'All sources', 'vesora' ) . '</option>';
	foreach ( vesora_lead_sources() as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $current_source, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';

	$export_url = wp_nonce_url( admin_url( 'admin-post.php?action=vesora_export_leads' ), 'vesora_export_leads' );
	echo '<a class="button" href="' . esc_url( $export_url ) . '">' . esc_html__( 'Export CSV', 'vesora' ) . '</a>';
}
add_action( 'restrict_manage_posts', 'vesora_lead_admin_filters' );

function vesora_filter_lead_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'vesora_lead' !== $query->get( 'post_type' ) ) {
		return;
	}

	$meta_query = array();
	$status     = isset( $_GET['lead_status'] ) ? sanitize_key( wp_unslash( $_GET['lead_status'] ) ) : '';
	$source     = isset( $_GET['lead_source'] ) ? sanitize_key( wp_unslash( $_GET['lead_source'] ) ) : '';
	if ( isset( vesora_lead_statuses()[ $status ] ) ) {
		$meta_query[] = array( 'key' => '_vesora_lead_status', 'value' => $status );
	}
	if ( isset( vesora_lead_sources()[ $source ] ) ) {
		$meta_query[] = array( 'key' => '_vesora_lead_source', 'value' => $source );
	}
	if ( $meta_query ) {
		$query->set( 'meta_query', $meta_query );
	}
}
add_action( 'pre_get_posts', 'vesora_filter_lead_query' );

function vesora_search_lead_meta( $search, $query ) {
	global $wpdb;
	if ( ! is_admin() || ! $query->is_main_query() || 'vesora_lead' !== $query->get( 'post_type' ) || ! $query->get( 's' ) ) {
		return $search;
	}

	$like = '%' . $wpdb->esc_like( $query->get( 's' ) ) . '%';
	return $wpdb->prepare(
		" AND ({$wpdb->posts}.post_title LIKE %s OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} vlm WHERE vlm.post_id = {$wpdb->posts}.ID AND vlm.meta_key IN ('_vesora_lead_email','_vesora_lead_whatsapp') AND vlm.meta_value LIKE %s)) ",
		$like,
		$like
	);
}
add_filter( 'posts_search', 'vesora_search_lead_meta', 10, 2 );

function vesora_lead_details_box() {
	add_meta_box( 'vesora-lead-details', __( 'Customer details', 'vesora' ), 'vesora_render_lead_details_box', 'vesora_lead', 'normal', 'high' );
}
add_action( 'add_meta_boxes_vesora_lead', 'vesora_lead_details_box' );

function vesora_render_lead_details_box( $post ) {
	wp_nonce_field( 'vesora_save_lead_admin', 'vesora_lead_admin_nonce' );
	$fields = array(
		'name' => __( 'Name', 'vesora' ), 'whatsapp' => __( 'WhatsApp', 'vesora' ), 'email' => __( 'Email', 'vesora' ),
		'country' => __( 'Country', 'vesora' ), 'business_type' => __( 'Business Type', 'vesora' ), 'source' => __( 'Source', 'vesora' ),
		'email_sent' => __( 'Notification email', 'vesora' ),
	);
	echo '<table class="widefat striped"><tbody>';
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, '_vesora_lead_' . $key, true );
		if ( 'source' === $key ) {
			$value = vesora_lead_sources()[ $value ] ?? $value;
		}
		echo '<tr><th style="width:180px">' . esc_html( $label ) . '</th><td>' . esc_html( $value ?: '—' ) . '</td></tr>';
	}
	echo '</tbody></table>';

	$message = get_post_meta( $post->ID, '_vesora_lead_message', true );
	$status  = get_post_meta( $post->ID, '_vesora_lead_status', true );
	$notes   = get_post_meta( $post->ID, '_vesora_lead_notes', true );
	echo '<p><strong>' . esc_html__( 'Message', 'vesora' ) . '</strong></p><div style="padding:12px;background:#f6f7f7;white-space:pre-wrap">' . esc_html( $message ) . '</div>';
	echo '<p><label for="vesora-lead-status"><strong>' . esc_html__( 'Status', 'vesora' ) . '</strong></label></p><select id="vesora-lead-status" name="vesora_lead_status">';
	foreach ( vesora_lead_statuses() as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $status, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';
	echo '<p><label for="vesora-lead-notes"><strong>' . esc_html__( 'Internal notes', 'vesora' ) . '</strong></label></p><textarea id="vesora-lead-notes" name="vesora_lead_notes" rows="6" style="width:100%">' . esc_textarea( $notes ) . '</textarea>';
}

function vesora_save_lead_admin_fields( $post_id ) {
	if ( ! isset( $_POST['vesora_lead_admin_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vesora_lead_admin_nonce'] ) ), 'vesora_save_lead_admin' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$status = isset( $_POST['vesora_lead_status'] ) ? sanitize_key( wp_unslash( $_POST['vesora_lead_status'] ) ) : '';
	if ( isset( vesora_lead_statuses()[ $status ] ) ) {
		update_post_meta( $post_id, '_vesora_lead_status', $status );
	}
	$notes = isset( $_POST['vesora_lead_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['vesora_lead_notes'] ) ) : '';
	update_post_meta( $post_id, '_vesora_lead_notes', $notes );
}
add_action( 'save_post_vesora_lead', 'vesora_save_lead_admin_fields' );

function vesora_lead_row_actions( $actions, $post ) {
	if ( 'vesora_lead' !== $post->post_type || 'contacted' === get_post_meta( $post->ID, '_vesora_lead_status', true ) ) {
		return $actions;
	}
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=vesora_mark_lead_contacted&lead_id=' . $post->ID ), 'vesora_mark_lead_contacted_' . $post->ID );
	$actions['vesora_contacted'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Mark contacted', 'vesora' ) . '</a>';
	return $actions;
}
add_filter( 'post_row_actions', 'vesora_lead_row_actions', 10, 2 );

function vesora_mark_lead_contacted() {
	$lead_id = isset( $_GET['lead_id'] ) ? absint( $_GET['lead_id'] ) : 0;
	if ( ! current_user_can( 'manage_options' ) || ! $lead_id ) {
		wp_die( esc_html__( 'You are not allowed to update this lead.', 'vesora' ) );
	}
	check_admin_referer( 'vesora_mark_lead_contacted_' . $lead_id );
	update_post_meta( $lead_id, '_vesora_lead_status', 'contacted' );
	wp_safe_redirect( admin_url( 'edit.php?post_type=vesora_lead' ) );
	exit;
}
add_action( 'admin_post_vesora_mark_lead_contacted', 'vesora_mark_lead_contacted' );

function vesora_export_leads() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to export leads.', 'vesora' ) );
	}
	check_admin_referer( 'vesora_export_leads' );

	$posts = get_posts( array( 'post_type' => 'vesora_lead', 'post_status' => array( 'publish', 'trash' ), 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC' ) );
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=vesora-leads-' . gmdate( 'Y-m-d' ) . '.csv' );
	$output = fopen( 'php://output', 'w' );
	fwrite( $output, "\xEF\xBB\xBF" );
	fputcsv( $output, array( 'Status', 'Name', 'WhatsApp', 'Email', 'Country', 'Business Type', 'Source', 'Message', 'Internal Notes', 'Email Sent', 'Created', 'Updated' ) );
	foreach ( $posts as $post ) {
		$row = array();
		foreach ( array( 'status', 'name', 'whatsapp', 'email', 'country', 'business_type', 'source', 'message', 'notes', 'email_sent' ) as $field ) {
			$row[] = get_post_meta( $post->ID, '_vesora_lead_' . $field, true );
		}
		$row[] = get_date_from_gmt( $post->post_date_gmt, 'Y-m-d H:i:s' );
		$row[] = get_date_from_gmt( $post->post_modified_gmt, 'Y-m-d H:i:s' );
		fputcsv( $output, $row );
	}
	fclose( $output );
	exit;
}
add_action( 'admin_post_vesora_export_leads', 'vesora_export_leads' );
