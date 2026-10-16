<?php
/** Shared wholesale enquiry, preserving field names and lead processing. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
	<section class="section wholesale" id="wholesale" aria-labelledby="wholesale-title">
		<div class="shell wholesale__grid">
			<div class="wholesale__intro reveal">
				<p class="eyebrow"><?php esc_html_e( 'Wholesale partnership', 'vesora' ); ?></p>
				<h2 id="wholesale-title"><?php esc_html_e( 'Build your next collection', 'vesora' ); ?></h2>
				<p><?php esc_html_e( 'Tell us what you need. A product specialist will respond with options for texture, color, volume and custom packaging.', 'vesora' ); ?></p>
				<a class="text-link text-link--light" href="<?php echo esc_url( vesora_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat on WhatsApp', 'vesora' ); ?> ↗</a>
			</div>

			<div class="wholesale__form-wrap reveal">
				<?php $inquiry_status = isset( $_GET['inquiry'] ) ? sanitize_key( wp_unslash( $_GET['inquiry'] ) ) : ''; ?>
				<?php if ( 'sent' === $inquiry_status ) : ?>
					<div class="form-status form-status--success" role="status" tabindex="-1" data-form-status>
						<strong><?php esc_html_e( 'Your request is on its way.', 'vesora' ); ?></strong>
						<p><?php esc_html_e( 'A Vesora specialist will contact you shortly.', 'vesora' ); ?></p>
					</div>
				<?php elseif ( in_array( $inquiry_status, array( 'error', 'invalid' ), true ) ) : ?>
					<div class="form-status form-status--error" role="alert" tabindex="-1" data-form-status>
						<strong><?php esc_html_e( 'We could not send your request.', 'vesora' ); ?></strong>
						<p><?php esc_html_e( 'Check the required fields and try again, or contact us on WhatsApp.', 'vesora' ); ?></p>
					</div>
				<?php endif; ?>

				<form class="inquiry-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-inquiry-form>
					<input type="hidden" name="action" value="vesora_wholesale">
					<?php wp_nonce_field( 'vesora_wholesale', 'vesora_wholesale_nonce' ); ?>
					<div class="honeypot" aria-hidden="true"><label for="company_website"><?php esc_html_e( 'Company website', 'vesora' ); ?></label><input id="company_website" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>
					<div class="field"><label for="name"><?php esc_html_e( 'Name', 'vesora' ); ?> <span aria-hidden="true">*</span></label><input id="name" name="name" type="text" autocomplete="name" required></div>
					<div class="field"><label for="whatsapp"><?php esc_html_e( 'WhatsApp', 'vesora' ); ?> <span aria-hidden="true">*</span></label><input id="whatsapp" name="whatsapp" type="tel" autocomplete="tel" inputmode="tel" required></div>
					<div class="field"><label for="email"><?php esc_html_e( 'Email', 'vesora' ); ?> <span aria-hidden="true">*</span></label><input id="email" name="email" type="email" autocomplete="email" inputmode="email" required></div>
					<div class="field">
						<label for="business_type"><?php esc_html_e( 'Business Type', 'vesora' ); ?> <span aria-hidden="true">*</span></label>
						<select id="business_type" name="business_type" required>
							<option value="" selected disabled><?php esc_html_e( 'Select business type', 'vesora' ); ?></option>
							<?php foreach ( vesora_business_types() as $business_type_value => $business_type_label ) : ?>
								<option value="<?php echo esc_attr( $business_type_value ); ?>"><?php echo esc_html( $business_type_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="field field--message">
						<label for="message"><?php esc_html_e( 'Message', 'vesora' ); ?> <span aria-hidden="true">*</span></label>
						<textarea id="message" name="message" rows="6" maxlength="2000" aria-describedby="message-help" required></textarea>
						<p class="field__help" id="message-help"><?php esc_html_e( 'Tell us about the products, quantity or customization you need.', 'vesora' ); ?></p>
					</div>
					<button class="button button--light" type="submit" data-submit-button><span data-button-label><?php esc_html_e( 'Send request', 'vesora' ); ?></span><span class="button__loading" aria-hidden="true"><?php esc_html_e( 'Sending...', 'vesora' ); ?></span></button>
				</form>
			</div>
		</div>
	</section>
