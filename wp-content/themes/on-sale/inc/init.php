<?php
/**
 * onsale engine room
 *
 * @package onsale
 */


/*
 * Structure.
 * Template functions used throughout the theme.
 */
require get_stylesheet_directory() . '/inc/structure/hooks.php';
require get_stylesheet_directory() . '/inc/structure/header.php';
require get_stylesheet_directory() . '/inc/structure/post.php';
require get_stylesheet_directory() . '/inc/structure/template-tags.php';
require get_stylesheet_directory() . '/inc/structure/footer.php';

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_stylesheet_directory() . '/inc/tgm/class-tgm-plugin-activation.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_stylesheet_directory() . '/inc/functions/extras.php';
require get_stylesheet_directory() . '/inc/functions/onsale-setup.php';

/**
 * Customizer additions.
 */

	/*require get_stylesheet_directory() . '/inc/customizer/hooks.php';
	require get_stylesheet_directory() . '/inc/customizer/controls.php';
	require get_stylesheet_directory() . '/inc/customizer/display.php';
	require get_stylesheet_directory() . '/inc/customizer/functions.php';
	require get_stylesheet_directory() . '/inc/customizer/colors.php';*/
	require get_stylesheet_directory() . '/inc/customizer/class-onsale-customizer.php';
	/*require get_stylesheet_directory() . '/inc/customizer/custom-header.php';*/


/**
 * Load WooCommerce compatibility files.
 */

	require get_stylesheet_directory() . '/inc/woocommerce/hooks.php';
