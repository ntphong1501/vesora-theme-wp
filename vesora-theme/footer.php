<?php
/**
 * Site footer.
 *
 * @package Vesora
 */

$footer_hair_links = array(
	__( 'Tape-in Hair Extensions', 'vesora' ) => vesora_product_category_url( 'tape-in-hair-extensions' ),
	__( 'Keratin Tip Extensions', 'vesora' )  => vesora_product_category_url( 'keratin-hair' ),
	__( 'Nano Ring Extensions', 'vesora' )    => vesora_product_category_url( 'nano-ring-hair' ),
	__( 'Weft Hair Extensions', 'vesora' )    => vesora_product_category_url( 'weft-hair-extensions' ),
	__( 'Clip-in Hair Extensions', 'vesora' ) => vesora_product_category_url( 'clip-in-hair-extensions' ),
	__( 'Ponytail Extensions', 'vesora' )     => vesora_product_category_url( 'ponytail-hair-extensions' ),
	__( 'Bulk Hair', 'vesora' )               => vesora_product_category_url( 'bulk-hair-extensions' ),
);

$footer_resource_links = array(
	__( 'About Vesora', 'vesora' )  => home_url( '/about/' ),
	__( 'Hair Showroom', 'vesora' ) => home_url( '/shop/' ),
	__( 'Best Sellers', 'vesora' )  => vesora_product_category_url( 'best-selling-products' ),
	__( 'Blogs', 'vesora' )         => get_permalink( (int) get_option( 'page_for_posts' ) ),
);

$footer_helpful_links = array(
	__( 'Contact Us', 'vesora' )   => home_url( '/contact/' ),
	__( 'WhatsApp', 'vesora' )     => vesora_whatsapp_url(),
	__( 'Instagram', 'vesora' )    => vesora_contact_value( 'instagram_url' ),
	__( 'Email Vesora', 'vesora' ) => 'mailto:' . sanitize_email( vesora_contact_value( 'email' ) ),
);
?>
<?php get_template_part( 'template-parts/wholesale' ); ?>
<footer id="site-footer" class="site-footer">
	<div class="shell site-footer__grid">
		<div class="site-footer__brand">
			<a class="brand brand--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Vesora Hair home', 'vesora' ); ?>">
				<img class="site-footer__logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-desktop-black.png' ); ?>" alt="" width="2099" height="698" decoding="async">
			</a>

			<ul class="site-footer__contact" aria-label="<?php esc_attr_e( 'Vesora contact information', 'vesora' ); ?>">
				<li>
					<span class="dashicons dashicons-location-alt" aria-hidden="true"></span>
					<p><strong><?php esc_html_e( 'Office:', 'vesora' ); ?></strong> <?php echo nl2br( esc_html( vesora_contact_value( 'address' ) ) ); ?></p>
				</li>
				<?php if ( vesora_contact_value( 'factory_address' ) ) : ?>
				<li>
					<span class="dashicons dashicons-building" aria-hidden="true"></span>
					<p><strong><?php esc_html_e( 'Factory:', 'vesora' ); ?></strong> <?php echo nl2br( esc_html( vesora_contact_value( 'factory_address' ) ) ); ?></p>
				</li>
				<?php endif; ?>
				<li>
					<span class="dashicons dashicons-phone" aria-hidden="true"></span>
					<a href="<?php echo esc_url( vesora_phone_url() ); ?>"><strong><?php esc_html_e( 'Sales & customer care:', 'vesora' ); ?></strong> <?php echo esc_html( vesora_contact_value( 'phone' ) ); ?></a>
				</li>
				<li>
					<span class="dashicons dashicons-email-alt" aria-hidden="true"></span>
					<a href="mailto:<?php echo esc_attr( vesora_contact_value( 'email' ) ); ?>"><?php echo esc_html( vesora_contact_value( 'email' ) ); ?></a>
				</li>
			</ul>

			<div class="site-footer__socials" aria-label="<?php esc_attr_e( 'Vesora social links', 'vesora' ); ?>">
				<a href="<?php echo esc_url( vesora_contact_value( 'instagram_url' ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Vesora on Instagram', 'vesora' ); ?>">
					<span class="dashicons dashicons-instagram" aria-hidden="true"></span>
				</a>
				<a href="<?php echo esc_url( vesora_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat with Vesora on WhatsApp', 'vesora' ); ?>">
					<span class="dashicons dashicons-whatsapp" aria-hidden="true"></span>
				</a>
				<a href="mailto:<?php echo esc_attr( vesora_contact_value( 'email' ) ); ?>" aria-label="<?php esc_attr_e( 'Email Vesora', 'vesora' ); ?>">
					<span class="dashicons dashicons-email-alt" aria-hidden="true"></span>
				</a>
			</div>
		</div>

		<nav class="site-footer__column" aria-labelledby="footer-hair-title">
			<h2 id="footer-hair-title"><?php esc_html_e( 'Hair Extensions', 'vesora' ); ?></h2>
			<ul class="site-footer__links">
				<?php foreach ( $footer_hair_links as $label => $url ) : ?>
					<li><a href="<?php echo esc_url( $url ); ?>"><span aria-hidden="true">›</span><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<nav class="site-footer__column" aria-labelledby="footer-resources-title">
			<h2 id="footer-resources-title"><?php esc_html_e( 'Resources', 'vesora' ); ?></h2>
			<ul class="site-footer__links">
				<?php foreach ( $footer_resource_links as $label => $url ) : ?>
					<li><a href="<?php echo esc_url( $url ); ?>"><span aria-hidden="true">›</span><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<nav class="site-footer__column" aria-labelledby="footer-helpful-title">
			<h2 id="footer-helpful-title"><?php esc_html_e( 'Helpful Links', 'vesora' ); ?></h2>
			<ul class="site-footer__links">
				<?php foreach ( $footer_helpful_links as $label => $url ) : ?>
					<?php $new_tab = in_array( $label, array( __( 'WhatsApp', 'vesora' ), __( 'Instagram', 'vesora' ) ), true ); ?>
					<li><a href="<?php echo esc_url( $url ); ?>"<?php echo $new_tab ? ' target="_blank" rel="noopener"' : ''; ?>><span aria-hidden="true">›</span><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	</div>

	<div class="shell site-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Vesora Hair. <?php esc_html_e( 'All rights reserved.', 'vesora' ); ?></p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
