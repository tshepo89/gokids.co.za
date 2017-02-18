<?php
/**
 * On Sale WooCommerce hooks
 *
 * @package onsale
 */


/**
 * Header
 * @see  storefront_header_cart()
 */
add_action( 'onsale_header_nav', 'storefront_header_cart', 		60 );


add_action( 'onsale_breadcrumb', 				'woocommerce_breadcrumb', 					10 );
add_action( 'onsale_shop_messages', 			'storefront_shop_messages', 				10 );