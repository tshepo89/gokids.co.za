<?php
/**
 * Onsale Customizer Class
 *
 * @author   WooThemes
 * @package  storefront
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

error_reporting(0);

if ( ! class_exists( 'Onsale_Customizer' ) ) :

	/**
	 * The Onsale Customizer class
	 */
	class Onsale_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'customize_preview_init',          array( $this, 'customize_preview_js' ), 10 );
			add_action( 'customize_register',              array( $this, 'customize_register' ), 10 );
			add_filter( 'storefront_setting_default_values', array( $this, 'get_onsale_defaults' ) );
			add_action( 'wp_enqueue_scripts',              array( $this, 'add_customizer_css' ), 1000 );
      		add_action( 'customize_register',	array( $this, 'edit_default_customizer_settings' ),			99 );
			add_action( 'init',					array( $this, 'default_theme_mod_values' )					);
		}

    /**
  	 * Returns an array of the desired default Storefront options
  	 * @return array
  	 */
     public function get_onsale_defaults() {
   		return apply_filters( 'onsale_default_settings', $args = array(
         'onsale_header_top_background_color'    => '#242424',
         'onsale_header_top_text_color'          => '#ffffff',
         'storefront_header_background_color'  => '#242424',
         'storefront_header_link_color'        => '#ffffff',
         'storefront_header_text_color'        => '#dd3333',
         'storefront_footer_background_color'  => '#242424',
         'storefront_footer_link_color'        => '#ffffff',
         'storefront_footer_heading_color'     => '#ffffff',
         'storefront_footer_text_color'        => '#ffffff',
         'storefront_text_color'               => '#242424',
         'storefront_heading_color'            => '#242424',
         'storefront_button_background_color'  => '#028468',
         'storefront_button_text_color'        => '#ffffff',
         'storefront_button_alt_background_color' => '#dd3333',
         'storefront_button_alt_text_color'       => '#ffffff',
 				 'storefront_accent_color'                => '#028468',
         'onsale_alt_accent_color'              => '#dd3333',
 
   		) );
   	}

    /**
	 * Set default Customizer settings based on Storechild design.
	 * @uses get_onsale_defaults()
	 * @return void
	 */
	public function edit_default_customizer_settings( $wp_customize ) {
		foreach ( Onsale_Customizer::get_onsale_defaults() as $mod => $val ) {
			$setting = $wp_customize->get_setting( $mod );

			if ( is_object( $setting ) ) {
				$setting->default = $val;
			}
		}
	}

	/**
	 * Returns a default theme_mod value if there is none set.
	 * @uses get_onsale_defaults()
	 * @return void
	 */
	public function default_theme_mod_values() {
		foreach ( Onsale_Customizer::get_onsale_defaults() as $mod => $val ) {
			add_filter( 'theme_mod_' . $mod, function( $setting ) use ( $val ) {
				return $setting ? $setting : $val;
			});
		}
	}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer along with several other settings.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since  1.0.0
		 */
		public function customize_register( $wp_customize ) {

      /**
		 * Header Top Background
		 */
		$wp_customize->add_setting( 'onsale_header_top_background_color', array(
			'default'           => apply_filters( 'onsale_default_header_top_background_color', '#242424' ),
			'sanitize_callback' => 'storefront_sanitize_hex_color',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'onsale_header_top_background_color', array(
			'label'	   => __( 'Top Background color', 'onsale' ),
			'section'  => 'header_image',
			'settings' => 'onsale_header_top_background_color',
			'priority' => 10,
		) ) );

		/**
		 * Header Top text color
		 */
		$wp_customize->add_setting( 'onsale_header_top_text_color', array(
			'default'           => apply_filters( 'onsale_default_header_top_text_color', '#ffffff' ),
			'sanitize_callback' => 'storefront_sanitize_hex_color',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'onsale_header_top_text_color', array(
			'label'	   => __( 'Top Text color', 'onsale' ),
			'section'  => 'header_image',
			'settings' => 'onsale_header_top_text_color',
			'priority' => 12,
		) ) );

		$wp_customize->add_section( 'onsale_slider_section' , array(
	      'title'       => __( 'Slider Options', 'onsale' ),
	      'priority'    => 33,
	      'description' => __( 'Product Slider', 'onsale' ),
	    ) );
	    
	    $wp_customize->add_setting( 'onsale_slider_area', array(
	      'default' => 'recent',
	      'sanitize_callback' => 'sanitize_text_field',
	    ));
	    
	    $wp_customize->add_control( 'effect_select_box', array(
	      'settings' => 'onsale_slider_area',
	      'label' => __( 'What products to show:', 'onsale' ),
	      'section' => 'onsale_slider_section',
	      'type' => 'select',
	      'choices' => array(
	        'featured' => 'Featured Products',
	        'total_sales' => 'Best Selling Products',
	        'recent' => 'Recent Products',
	        'top_rated' => 'Top Rated Products',
	        'sale' => 'On Sale Products',
	      ),
	      'priority' => 12,
	    ));

	    $wp_customize->add_setting( 'onsale_slider_num_show', array (
	    	'default' => 5,
      		'sanitize_callback' => 'onsale_check_number',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_slider_num_show', array(
	      'label'    => __( 'Products show at most', 'onsale' ),
	      'section'  => 'onsale_slider_section',
	      'settings' => 'onsale_slider_num_show',
	      'priority'    => 10,
	    ) ) );


		/**
		 * Footer Background
		 */
		$wp_customize->add_setting( 'onsale_footer_credits_background_color', array(
			'default'           => apply_filters( 'onsale_default_footer_credits_background_color', '#242424' ),
			'sanitize_callback' => 'storefront_sanitize_hex_color',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'onsale_footer_credits_background_color', array(
			'label'	   => __( 'Top Background color', 'onsale' ),
			'section'  => 'storefront_footer',
			'settings' => 'onsale_footer_credits_background_color',
			'priority' => 50,
		) ) );

		/**
		 * Social Media Icons
		 */
	    $wp_customize->add_section( 'onsale_social_section' , array(
	      'title'       => __( 'Social Media Icons', 'onsale' ),
	      'priority'    => 42,
	      'description' => __( 'Optional media icons in the header', 'onsale' ),
	    ) );
	    
	    $wp_customize->add_setting( 'onsale_facebook', array (
      		'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_facebook', array(
	      'label'    => __( 'Enter your Facebook url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_facebook',
	      'priority'    => 101,
	    ) ) );
	  
	    $wp_customize->add_setting( 'onsale_twitter', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_twitter', array(
	      'label'    => __( 'Enter your Twitter url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_twitter',
	      'priority'    => 102,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_google', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_google', array(
	      'label'    => __( 'Enter your Google+ url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_google',
	      'priority'    => 103,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_pinterest', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_pinterest', array(
	      'label'    => __( 'Enter your Pinterest url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_pinterest',
	      'priority'    => 104,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_linkedin', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_linkedin', array(
	      'label'    => __( 'Enter your Linkedin url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_linkedin',
	      'priority'    => 105,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_youtube', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_youtube', array(
	      'label'    => __( 'Enter your Youtube url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_youtube',
	      'priority'    => 106,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_tumblr', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_tumblr', array(
	      'label'    => __( 'Enter your Tumblr url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_tumblr',
	      'priority'    => 107,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_instagram', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_instagram', array(
	      'label'    => __( 'Enter your Instagram url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_instagram',
	      'priority'    => 108,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_flickr', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_flickr', array(
	      'label'    => __( 'Enter your Flickr url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_flickr',
	      'priority'    => 109,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_vimeo', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_vimeo', array(
	      'label'    => __( 'Enter your Vimeo url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_vimeo',
	      'priority'    => 110,
	    ) ) );
	    
	    $wp_customize->add_setting( 'onsale_rss', array (
	      'sanitize_callback' => 'esc_url_raw',
	    ) );
	    
	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'onsale_rss', array(
	      'label'    => __( 'Enter your RSS url', 'onsale' ),
	      'section'  => 'onsale_social_section',
	      'settings' => 'onsale_rss',
	      'priority'    => 112,
	    ) ) );
      
		}



		/**
		 * Add CSS in <head> for styles handled by the theme customizer
		 * If the Customizer is active pull in the raw css. Otherwise pull in the prepared theme_mods.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_customizer_css() {
    $accent_color 					= get_theme_mod( 'storefront_accent_color' );
		$header_top_background_color 	= get_theme_mod( 'onsale_header_top_background_color' );
		$header_top_text_color 			= get_theme_mod( 'onsale_header_top_text_color' );
		$header_background_color 		= get_theme_mod( 'storefront_header_background_color' );
		$header_link_color 				= get_theme_mod( 'storefront_header_link_color' );
		$header_text_color 				= get_theme_mod( 'storefront_header_text_color' );

		$footer_background_color 		= get_theme_mod( 'storefront_footer_background_color' );
		$footer_link_color 				= get_theme_mod( 'storefront_footer_link_color' );
		$footer_heading_color 			= get_theme_mod( 'storefront_footer_heading_color' );
		$footer_text_color 				= get_theme_mod( 'storefront_footer_text_color' );
		$credits_background_color 		= get_theme_mod( 'onsale_footer_credits_background_color' );
    $credits_text_color           = get_theme_mod( 'onsale_footer_credits_text_color' );

		$text_color 					= get_theme_mod( 'storefront_text_color' );
		$heading_color 					= get_theme_mod( 'storefront_heading_color' );
		$button_background_color 		= get_theme_mod( 'storefront_button_background_color' );
		$button_text_color 				= get_theme_mod( 'storefront_button_text_color' );
		$button_alt_background_color 	= get_theme_mod( 'storefront_button_alt_background_color' );
		$button_alt_text_color 			= get_theme_mod( 'storefront_button_alt_text_color' );
    $alt_accent_color           = get_theme_mod( 'onsale_alt_accent_color' );

		$brighten_factor 				= 25;
		$darken_factor 					= -25;

    $style 							= '
		header .top-area{
			background-color: ' . $header_top_background_color . ';
			color: ' . $header_top_text_color . ';
		}
		header .social-media .social-tw{
			color: ' . $header_top_text_color . ';
		}
		
		ul.products li.product .price,
		.title-holder .inner-title h1,
		.cart-collaterals h2,
		.site-main .columns-3 ul.products li.product:hover .cat-details h3{
			color: ' . $accent_color . '!important;
		}
		.site-header{
			color: ' . $text_color . '!important;
		}
		#banner-area .product-slider .banner-product-details a{
			color: ' . $text_color . '!important;
			border: 2px solid;
		}
		#banner-area .flex-control-paging li a.flex-active,
		ul.products li.product .onsale,
		.woocommerce-info, 
		.woocommerce-noreviews, 
		p.no-comments,
		article .post-content-area .more-link,
		.woocommerce-error, 
		.woocommerce-info, 
		.woocommerce-message, 
		.woocommerce-noreviews, 
		p.no-comments,
		.site-header-cart .cart-contents,
		.flex-control-paging li a.flex-active,
		.main-nav,
		.widget h2.widget-title{
			background-color: ' . $accent_color . '!important;
		}
		.site-footer .credits-area{
			background-color: ' . $credits_background_color . ';
		}

		.main-navigation ul li a,
		.site-title a,
		ul.menu li a,
		.site-branding p.site-title a,
		ul.menu li.current-menu-item > a  {
			color: ' . $header_link_color . ';
		}

		.main-navigation ul li a:hover,
		.site-title a:hover {
			color: ' . storefront_adjust_color_brightness( $header_link_color, $darken_factor ) . ';
		}

		.site-header,
		.main-navigation ul ul,
		.secondary-navigation ul ul,
		.main-navigation ul.menu > li.menu-item-has-children:after,
		.secondary-navigation ul.menu ul,
		.main-navigation ul.menu ul,
		.main-navigation ul.nav-menu ul {
			background-color: ' . $header_background_color . ';
		}
		h1, h2, h3, h4, h5, h6 {
			color: ' . $heading_color . ';
		}

		.hentry .entry-header {
			border-color: ' . $heading_color . ';
		}

		.widget h1 {
			border-bottom-color: ' . $heading_color . ';
		}

		body,
		.secondary-navigation a,
		.widget-area .widget a,
		#comments .comment-list .reply a,
		.pagination .page-numbers li .page-numbers:not(.current), .woocommerce-pagination .page-numbers li .page-numbers:not(.current),
		.storefront-product-section .section-title {
			color: ' . $text_color . ';
		}

		a  {
			color: ' . $accent_color . ';
		}

		a:focus{
			outline-color: ' . $accent_color . ';
		}
		.navigation-area #searchform input[type="text"]:focus{
			border: 4px solid ' . $accent_color . ';
		}
		.navigation-area #searchform button:focus{
			background-color: ' . storefront_adjust_color_brightness( $button_background_color, $darken_factor ) . ';
			border-color: ' . storefront_adjust_color_brightness( $button_background_color, $darken_factor ) . ';
			color: ' . $button_text_color . ';
		}
		.button:focus,
		.button.alt:focus,
		.button.added_to_cart:focus,
		.button.wc-forward:focus,
		button:focus,
		input[type="button"]:focus,
		input[type="reset"]:focus,
		input[type="submit"]:focus {
			box-shadow: 0 4px 3px ' . storefront_adjust_color_brightness( $button_background_color, $brighten_factor ) . ';
			background-color: ' . storefront_adjust_color_brightness( $button_background_color, $darken_factor ) . ';
			border-color: ' . storefront_adjust_color_brightness( $button_background_color, $darken_factor ) . ';
			color: ' . $button_text_color . ';
		}

		button, input[type="button"], input[type="reset"], input[type="submit"], .button, .added_to_cart, .widget-area .widget a.button, .site-header-cart .widget_shopping_cart a.button {
			background-color: ' . $button_background_color . ';
			border-color: ' . $button_background_color . ';
			color: ' . $button_text_color . ';
		}

		button:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover, .button:hover, .added_to_cart:hover, .widget-area .widget a.button:hover, .site-header-cart .widget_shopping_cart a.button:hover {
			background-color: ' . storefront_adjust_color_brightness( $button_background_color, $darken_factor ) . ';
			border-color: ' . storefront_adjust_color_brightness( $button_background_color, $darken_factor ) . ';
			color: ' . $button_text_color . ';
		}

		button.alt, input[type="button"].alt, input[type="reset"].alt, input[type="submit"].alt, .button.alt, .added_to_cart.alt, .widget-area .widget a.button.alt, .added_to_cart, .pagination .page-numbers li .page-numbers.current, .woocommerce-pagination .page-numbers li .page-numbers.current {
			background-color: ' . $button_alt_background_color . ';
			border-color: ' . $button_alt_background_color . ';
			color: ' . $button_alt_text_color . ';
		}

		button.alt:hover, input[type="button"].alt:hover, input[type="reset"].alt:hover, input[type="submit"].alt:hover, .button.alt:hover, .added_to_cart.alt:hover, .widget-area .widget a.button.alt:hover, .added_to_cart:hover {
			background-color: ' . storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ) . ';
			border-color: ' . storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ) . ';
			color: ' . $button_alt_text_color . ';
		}

		.site-footer {
			background-color: ' . $footer_background_color . ';
			color: ' . $footer_text_color . ';
		}

		.site-footer a:not(.button) {
			color: ' . $footer_link_color . ';
		}

		.site-footer h1, .site-footer h2, .site-footer h3, .site-footer h4, .site-footer h5, .site-footer h6 {
			color: ' . $footer_heading_color . ';
		}

		@media screen and ( min-width: 768px ) {
			.main-navigation ul.menu > li > ul {
				border-top-color: ' . $header_background_color . '}
			}

			.secondary-navigation ul.menu a:hover {
				color: ' . storefront_adjust_color_brightness( $header_text_color, $brighten_factor ) . ';
			}

			.main-navigation ul.menu ul {
				background-color: ' . $header_background_color . ';
			}

			.secondary-navigation ul.menu a {
				color: ' . $header_text_color . ';
			}
		}';

		$woocommerce_style 							= '
		a.cart-contents,
		.site-header-cart .widget_shopping_cart a {
			color: ' . $header_link_color . ';
		}

		a.cart-contents:hover,
		.site-header-cart .widget_shopping_cart a:hover {
			color: ' . storefront_adjust_color_brightness( $header_link_color, $darken_factor ) . ';
		}

		.site-header-cart .widget_shopping_cart {
			background-color: ' . $header_background_color . ';
		}

		.widget-area .widget a:hover,
		.product_list_widget a:hover,
		.quantity .plus, .quantity .minus,
		p.stars a:hover:after,
		p.stars a:after
		 {
			color: ' . $accent_color . ';
		}

		.widget_price_filter .ui-slider .ui-slider-range,
		.widget_price_filter .ui-slider .ui-slider-handle,
		.onsale {
			background-color: ' . $accent_color . ';
		}

		#order_review_heading, #order_review {
			border-color: ' . $accent_color . ';
		}

		';

		wp_add_inline_style( 'storefront-child-style', $style );
    wp_add_inline_style( 'storefront-woocommerce-style', $woocommerce_style  );
		}

		/**
		 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 *
		 * @since  1.0.0
		 */
		public function customize_preview_js() {
			wp_enqueue_script( 'onsale-customizer', get_stylesheet_directory_uri() . '/inc/customizer/js/customizer.min.js', array( 'customize-preview' ), '1.16', true );
		}

	}

endif;

return new Onsale_Customizer();