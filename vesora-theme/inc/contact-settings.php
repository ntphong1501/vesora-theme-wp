<?php
/**
 * Central contact settings for the Vesora storefront.
 *
 * @package Vesora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vesora_contact_defaults() {
	return array(
		'whatsapp_number' => '19128888386',
		'instagram_url'   => 'https://www.instagram.com/vesorahair/',
		'email'           => 'info@vesorahair.com',
		'inquiry_email'   => get_option( 'admin_email' ),
		'phone'           => '+1 912 888 8386',
		'address'         => '3A Building, 82 Duy Tan Street, Cau Giay, Hanoi, Vietnam',
		'factory_address' => 'Phu Luong, Tuyen Quang Province, Vietnam.',
	);
}

function vesora_whatsapp_number_from_url( $url ) {
	$url = (string) $url;
	if ( '' === $url ) {
		return '';
	}

	$query = wp_parse_url( $url, PHP_URL_QUERY );
	if ( $query ) {
		parse_str( $query, $parameters );
		if ( ! empty( $parameters['phone'] ) ) {
			return preg_replace( '/\D+/', '', (string) $parameters['phone'] );
		}
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	return preg_replace( '/\D+/', '', (string) $path );
}

function vesora_contact_settings() {
	$defaults = vesora_contact_defaults();
	$saved    = get_option( 'vesora_contact_settings', null );

	if ( null === $saved ) {
		$legacy_whatsapp = get_theme_mod( 'vesora_whatsapp_url', '' );
		$legacy_instagram = get_theme_mod( 'vesora_instagram_url', '' );
		$legacy_number = vesora_whatsapp_number_from_url( $legacy_whatsapp );

		if ( $legacy_number ) {
			$defaults['whatsapp_number'] = $legacy_number;
		}
		if ( $legacy_instagram ) {
			$defaults['instagram_url'] = $legacy_instagram;
		}

		return $defaults;
	}

	return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
}

function vesora_contact_value( $key ) {
	$settings = vesora_contact_settings();
	return isset( $settings[ $key ] ) ? $settings[ $key ] : '';
}

function vesora_whatsapp_url( $message = '' ) {
	$number = preg_replace( '/\D+/', '', (string) vesora_contact_value( 'whatsapp_number' ) );
	$url    = 'https://wa.me/' . $number;

	if ( '' !== $message ) {
		$url = add_query_arg( 'text', $message, $url );
	}

	return $url;
}

function vesora_phone_url() {
	$phone = preg_replace( '/[^0-9+]/', '', (string) vesora_contact_value( 'phone' ) );
	return 'tel:' . $phone;
}

function vesora_normalize_instagram_url( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	if ( 0 === strpos( $value, '@' ) || false === strpos( $value, '://' ) ) {
		$username = trim( $value, "@/ \t\n\r\0\x0B" );
		if ( preg_match( '/^[A-Za-z0-9._]+$/', $username ) ) {
			return 'https://www.instagram.com/' . $username . '/';
		}
	}

	return esc_url_raw( $value, array( 'http', 'https' ) );
}

function vesora_sanitize_contact_settings( $input ) {
	$current = vesora_contact_settings();
	$input   = is_array( $input ) ? $input : array();
	$output  = $current;

	$whatsapp = isset( $input['whatsapp_number'] ) ? preg_replace( '/\D+/', '', wp_unslash( $input['whatsapp_number'] ) ) : '';
	if ( strlen( $whatsapp ) >= 7 && strlen( $whatsapp ) <= 15 ) {
		$output['whatsapp_number'] = $whatsapp;
	} else {
		add_settings_error( 'vesora_contact_settings', 'invalid_whatsapp', __( 'Số WhatsApp chưa hợp lệ. Hãy nhập mã quốc gia và số điện thoại, chỉ dùng chữ số.', 'vesora' ) );
	}

	$instagram = isset( $input['instagram_url'] ) ? vesora_normalize_instagram_url( wp_unslash( $input['instagram_url'] ) ) : '';
	if ( $instagram && wp_http_validate_url( $instagram ) ) {
		$output['instagram_url'] = $instagram;
	} else {
		add_settings_error( 'vesora_contact_settings', 'invalid_instagram', __( 'Instagram chưa hợp lệ. Hãy nhập tên tài khoản hoặc đường dẫn đầy đủ.', 'vesora' ) );
	}

	$email = isset( $input['email'] ) ? sanitize_email( wp_unslash( $input['email'] ) ) : '';
	if ( is_email( $email ) ) {
		$output['email'] = $email;
	} else {
		add_settings_error( 'vesora_contact_settings', 'invalid_email', __( 'Email hiển thị chưa hợp lệ.', 'vesora' ) );
	}

	$inquiry_email = isset( $input['inquiry_email'] ) ? sanitize_email( wp_unslash( $input['inquiry_email'] ) ) : '';
	if ( is_email( $inquiry_email ) ) {
		$output['inquiry_email'] = $inquiry_email;
	} else {
		add_settings_error( 'vesora_contact_settings', 'invalid_inquiry_email', __( 'Email nhận yêu cầu chưa hợp lệ.', 'vesora' ) );
	}

	$phone = isset( $input['phone'] ) ? sanitize_text_field( wp_unslash( $input['phone'] ) ) : '';
	if ( '' !== $phone ) {
		$output['phone'] = $phone;
	} else {
		add_settings_error( 'vesora_contact_settings', 'invalid_phone', __( 'Số điện thoại không được để trống.', 'vesora' ) );
	}

	$address = isset( $input['address'] ) ? sanitize_textarea_field( wp_unslash( $input['address'] ) ) : '';
	if ( '' !== $address ) {
		$output['address'] = $address;
	} else {
		add_settings_error( 'vesora_contact_settings', 'invalid_address', __( 'Địa chỉ không được để trống.', 'vesora' ) );
	}

	if ( isset( $input['factory_address'] ) && is_string( $input['factory_address'] ) ) {
		$output['factory_address'] = sanitize_textarea_field( wp_unslash( $input['factory_address'] ) );
	}
	return $output;
}

function vesora_register_contact_settings() {
	register_setting(
		'vesora_contact_settings_group',
		'vesora_contact_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'vesora_sanitize_contact_settings',
			'default'           => vesora_contact_defaults(),
		)
	);
}
add_action( 'admin_init', 'vesora_register_contact_settings' );

function vesora_add_contact_settings_menu() {
	add_menu_page(
		__( 'Thông tin liên hệ Vesora', 'vesora' ),
		__( 'Thông tin liên hệ', 'vesora' ),
		'edit_theme_options',
		'vesora-contact-settings',
		'vesora_contact_settings_admin_page',
		'dashicons-phone',
		60
	);
}
add_action( 'admin_menu', 'vesora_add_contact_settings_menu' );

function vesora_contact_settings_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_vesora-contact-settings' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style( 'vesora-contact-settings-admin', get_template_directory_uri() . '/assets/css/admin-contact-settings.css', array(), VESORA_VERSION );
}
add_action( 'admin_enqueue_scripts', 'vesora_contact_settings_admin_assets' );

function vesora_contact_settings_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$settings = vesora_contact_settings();
	?>
	<div class="wrap vesora-contact-admin">
		<header class="vesora-contact-admin__intro">
			<h1><?php esc_html_e( 'Thông tin liên hệ Vesora', 'vesora' ); ?></h1>
			<p><?php esc_html_e( 'Cập nhật một lần để thay đổi các nút liên hệ, trang sản phẩm, footer và nơi nhận biểu mẫu.', 'vesora' ); ?></p>
		</header>

		<?php settings_errors(); ?>

		<form action="options.php" method="post">
			<?php settings_fields( 'vesora_contact_settings_group' ); ?>

			<div class="vesora-contact-admin__layout">
				<section class="vesora-contact-admin__section" aria-labelledby="vesora-social-title">
					<h2 id="vesora-social-title"><?php esc_html_e( 'Tin nhắn và mạng xã hội', 'vesora' ); ?></h2>

					<div class="vesora-contact-field">
						<label for="vesora-whatsapp-number"><?php esc_html_e( 'Số WhatsApp', 'vesora' ); ?></label>
						<input class="regular-text" id="vesora-whatsapp-number" name="vesora_contact_settings[whatsapp_number]" type="tel" inputmode="numeric" value="<?php echo esc_attr( $settings['whatsapp_number'] ); ?>" required aria-describedby="vesora-whatsapp-help">
						<p id="vesora-whatsapp-help"><?php esc_html_e( 'Nhập mã quốc gia và số điện thoại, không nhập dấu + hoặc khoảng trắng. Ví dụ: 84912345678.', 'vesora' ); ?></p>
					</div>

					<div class="vesora-contact-field">
						<label for="vesora-instagram-url"><?php esc_html_e( 'Instagram', 'vesora' ); ?></label>
						<input class="regular-text" id="vesora-instagram-url" name="vesora_contact_settings[instagram_url]" type="text" value="<?php echo esc_attr( $settings['instagram_url'] ); ?>" required aria-describedby="vesora-instagram-help">
						<p id="vesora-instagram-help"><?php esc_html_e( 'Có thể nhập @vesorahair hoặc đường dẫn Instagram đầy đủ.', 'vesora' ); ?></p>
					</div>
				</section>

				<section class="vesora-contact-admin__section" aria-labelledby="vesora-email-title">
					<h2 id="vesora-email-title"><?php esc_html_e( 'Email và điện thoại', 'vesora' ); ?></h2>

					<div class="vesora-contact-field">
						<label for="vesora-contact-email"><?php esc_html_e( 'Email hiển thị', 'vesora' ); ?></label>
						<input class="regular-text" id="vesora-contact-email" name="vesora_contact_settings[email]" type="email" value="<?php echo esc_attr( $settings['email'] ); ?>" required>
					</div>

					<div class="vesora-contact-field">
						<label for="vesora-inquiry-email"><?php esc_html_e( 'Email nhận biểu mẫu', 'vesora' ); ?></label>
						<input class="regular-text" id="vesora-inquiry-email" name="vesora_contact_settings[inquiry_email]" type="email" value="<?php echo esc_attr( $settings['inquiry_email'] ); ?>" required aria-describedby="vesora-inquiry-help">
						<p id="vesora-inquiry-help"><?php esc_html_e( 'Yêu cầu từ biểu mẫu Wholesale sẽ được gửi đến email này.', 'vesora' ); ?></p>
					</div>

					<div class="vesora-contact-field">
						<label for="vesora-phone"><?php esc_html_e( 'Số điện thoại hiển thị', 'vesora' ); ?></label>
						<input class="regular-text" id="vesora-phone" name="vesora_contact_settings[phone]" type="tel" value="<?php echo esc_attr( $settings['phone'] ); ?>" required>
					</div>
				</section>

				<section class="vesora-contact-admin__section vesora-contact-admin__section--wide" aria-labelledby="vesora-address-title">
					<h2 id="vesora-address-title"><?php esc_html_e( 'Địa chỉ', 'vesora' ); ?></h2>
					<div class="vesora-contact-field">
						<label for="vesora-address"><?php esc_html_e( 'Địa chỉ hiển thị tại footer và trang Contact', 'vesora' ); ?></label>
						<textarea class="large-text" id="vesora-address" name="vesora_contact_settings[address]" rows="4" required><?php echo esc_textarea( $settings['address'] ); ?></textarea>
					</div>
					<div class="vesora-contact-field">
						<label for="vesora-factory-address"><?php esc_html_e( 'Địa chỉ nhà máy (Factory)', 'vesora' ); ?></label>
						<textarea class="large-text" id="vesora-factory-address" name="vesora_contact_settings[factory_address]" rows="2"><?php echo esc_textarea( $settings['factory_address'] ); ?></textarea>
						<p><?php esc_html_e( 'Hiển thị tại footer và trang Contact. Để trống nếu muốn ẩn.', 'vesora' ); ?></p>
					</div>
				</section>
			</div>

			<div class="vesora-contact-admin__plugin-note">
				<span class="dashicons dashicons-info-outline" aria-hidden="true"></span>
				<div>
					<strong><?php esc_html_e( 'Nút WhatsApp nổi', 'vesora' ); ?></strong>
					<p><?php esc_html_e( 'Nút nổi ở góc màn hình do plugin quản lý. Khi đổi số WhatsApp tại đây, hãy cập nhật cùng số trong cài đặt của plugin.', 'vesora' ); ?></p>
				</div>
			</div>

			<div class="vesora-contact-admin__save">
				<?php submit_button( __( 'Lưu thông tin liên hệ', 'vesora' ), 'primary', 'submit', false ); ?>
				<span><?php esc_html_e( 'Các thay đổi áp dụng ngay sau khi lưu.', 'vesora' ); ?></span>
			</div>
		</form>
	</div>
	<?php
}

function vesora_contact_details_shortcode() {
	$email     = vesora_contact_value( 'email' );
	$phone     = vesora_contact_value( 'phone' );
	$address   = vesora_contact_value( 'address' );
	$instagram = vesora_contact_value( 'instagram_url' );

	ob_start();
	?>
	<div class="vesora-contact-details">
		<div class="vesora-contact-details__item">
			<i class="fi fi-rr-envelope" aria-hidden="true"></i><span><?php esc_html_e( 'Email', 'vesora' ); ?></span>
			<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
		</div>
		<div class="vesora-contact-details__item">
			<i class="fi fi-rr-phone-call" aria-hidden="true"></i><span><?php esc_html_e( 'Phone', 'vesora' ); ?></span>
			<a href="<?php echo esc_url( vesora_phone_url() ); ?>"><?php echo esc_html( $phone ); ?></a>
		</div>
		<div class="vesora-contact-details__item">
			<i class="fi fi-brands-whatsapp" aria-hidden="true"></i><span><?php esc_html_e( 'WhatsApp', 'vesora' ); ?></span>
			<a href="<?php echo esc_url( vesora_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat with Vesora', 'vesora' ); ?></a>
		</div>
		<div class="vesora-contact-details__item">
			<i class="fi fi-brands-instagram" aria-hidden="true"></i><span><?php esc_html_e( 'Instagram', 'vesora' ); ?></span>
			<a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View Instagram', 'vesora' ); ?></a>
		</div>
		<div class="vesora-contact-details__item vesora-contact-details__item--address">
			<i class="fi fi-rr-marker" aria-hidden="true"></i><span><?php esc_html_e( 'Office', 'vesora' ); ?></span>
			<p><?php echo nl2br( esc_html( $address ) ); ?></p>
		</div>
		<?php if ( vesora_contact_value( 'factory_address' ) ) : ?>
		<div class="vesora-contact-details__item vesora-contact-details__item--address">
			<i class="fi fi-rr-building" aria-hidden="true"></i><span><?php esc_html_e( 'Factory', 'vesora' ); ?></span>
			<p><?php echo nl2br( esc_html( vesora_contact_value( 'factory_address' ) ) ); ?></p>
		</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'vesora_contact_details', 'vesora_contact_details_shortcode' );

function vesora_migrate_contact_page_content() {
	if ( '1.0.0' === get_option( 'vesora_contact_page_dynamic_version' ) ) {
		return;
	}

	$page = get_page_by_path( 'contact', OBJECT, 'page' );
	if ( ! $page ) {
		return;
	}

	$content = (string) $page->post_content;
	if ( ! has_shortcode( $content, 'vesora_contact_details' ) ) {
		$legacy = 'Talk with a Vesora specialist about textures, colors, lengths, samples, private label packaging and wholesale orders. Email info@vesorahair.com or message us through WhatsApp and Instagram.';
		$intro  = __( 'Talk with a Vesora specialist about textures, colors, lengths, samples, private label packaging and wholesale orders.', 'vesora' );
		$content = trim( $content ) === $legacy ? $intro : trim( $content );
		$content = trim( $content . "\n\n[vesora_contact_details]" );
		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => $content,
			)
		);
	}

	update_option( 'vesora_contact_page_dynamic_version', '1.0.0' );
}
add_action( 'init', 'vesora_migrate_contact_page_content', 65 );
