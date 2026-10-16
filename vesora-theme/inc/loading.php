<?php
/** Shared, progressive-enhancement page and login loading feedback. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vesora_loading_head() {
	// Inline critical assets so the indicator never waits for an extra request.
	echo '<style id="vesora-loading-css">';
	readfile( get_template_directory() . '/assets/css/loading.css' );
	echo '</style><script id="vesora-loading-js">';
	readfile( get_template_directory() . '/assets/js/loading.js' );
	echo '</script>';
}
add_action( 'wp_head', 'vesora_loading_head', 1 );
add_action( 'login_head', 'vesora_loading_head', 1 );

function vesora_loading_markup() {
	?>
	<div id="vesora-loading" class="vesora-loading" hidden role="status" aria-live="polite" aria-label="<?php esc_attr_e( 'Loading, please wait', 'vesora' ); ?>">
		<div class="vesora-loading__brand" aria-hidden="true">
			<?php readfile( get_template_directory() . '/assets/images/vesora-loader.svg' ); ?>
			<div class="vesora-loading__track"><span></span></div>
		</div>
		<p class="vesora-loading__label" data-loading-label><?php esc_html_e( 'Loading, please wait…', 'vesora' ); ?></p>
	</div>
	<script>if(window.vesoraLoading){window.vesoraLoading.mount();}</script>
	<?php
}
add_action( 'wp_body_open', 'vesora_loading_markup', 0 );
add_action( 'login_header', 'vesora_loading_markup', 0 );
