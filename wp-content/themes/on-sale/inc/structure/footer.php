<?php
/**
 * Template functions used for the site footer.
 *
 * @package onsale
 */

if ( ! function_exists( 'onsale_credit' ) ) {
	/**
	 * Display the theme credit
	 * @since  1.0.0
	 * @return void
	 */
	function onsale_credit() {
		?>
		<div class="site-info">
			<?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?>
			<?php if ( apply_filters( 'storefront_credit_link', true ) && is_home() || is_front_page()) { ?>
			<?php printf( __( '| %1$s by %2$s and %3$s.', 'onsale' ), 'Powered','<a href="https://wordpress.org/" alt="Blog Tool, Publishing Platform, and CMS" title="Blog Tool, Publishing Platform, and CMS">WordPress</a>','<a href="http://ecommercethemes.org/" alt="Responsive eCommerce Themes" title="Responsive eCommerce Themes">EcommerceThemes.org</a>' ); ?>
			<?php } ?>
		</div><!-- .site-info -->
		<?php
	}
}