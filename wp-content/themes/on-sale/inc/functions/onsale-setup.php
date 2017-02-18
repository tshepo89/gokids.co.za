<?php
/**
 * onsale setup functions
 *
 * @package onsale
 */


function onsale_theme_setup() {
	add_image_size( 'onsale-thumb-400', 400, 400, true );
	add_image_size( 'onsale-slider-600', 600, 600, true );
}

function onsale_google_fonts() {
	wp_enqueue_style('onsale-googleFonts', '//fonts.googleapis.com/css?family=Roboto:400,300,400italic,500,700,900');
}

/**
 * Enqueue scripts and styles.
 * @since  1.0.0
 */
function onsale_scripts() {

	wp_enqueue_style( 'onsale-flexslider-css', get_stylesheet_directory_uri() . '/css/flexslider.min.css', array(), '' );

	wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css' ); //parent style

	if ( class_exists( 'WooCommerce' ) ) { 
		wp_enqueue_script( 'onsale-flexslider-js', get_stylesheet_directory_uri() . '/js/jquery.flexslider-min.js', array(), '', true );
	
		wp_enqueue_script( 'onsale-main-flex', get_stylesheet_directory_uri() . '/js/main-flex.js', array(), '', true );
	}
	wp_enqueue_script( 'onsale-main-nav', get_stylesheet_directory_uri() . '/js/main-nav.js', array(), '', true );
}

//Sidebar Widget
if (function_exists('register_sidebar')) {
    register_sidebar( array(
		'name'          => __( 'Homepage Sidebar', 'onsale' ),
		'id'            => 'homepage-sidebar',
		'description'   => esc_html__( 'Home Page Widget Area', 'onsale' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Product Page Sidebar', 'onsale' ),
		'id'            => 'product-sidebar',
		'description'   => esc_html__( 'Product Page Widget Area', 'onsale' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
			'name'          => __( 'Page Sidebar', 'storefront' ),
			'id'            => 'page-sidebar',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
}

function onsale_remove_widgets(){

	unregister_sidebar( 'header-1' );
}


add_action( 'tgmpa_register', 'onsale_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function onsale_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		array(
			'name'      => 'Homepage Control',
			'slug'      => 'homepage-control',
			'required'  => false,
		),

	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'onsale',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
