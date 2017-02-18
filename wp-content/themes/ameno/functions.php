<?php
/**
 * ameno functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ameno
 */

if ( ! function_exists( 'ameno_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function ameno_setup() {
	
	// This theme styles the visual editor to resemble the theme style.
	$font_url = 'http://fonts.googleapis.com/css?family=Lato:300,400,400italic,700,900,900italic|PT+Serif:400,700,400italic,700italic';
	add_editor_style( array( 'inc/editor-style.css', str_replace( ',', '%2C', $font_url ) ) );
	
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on ameno, use a find and replace
	 * to change 'ameno' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'ameno', get_template_directory() . '/languages' );

	// Add theme support for custom logo as it is required for wordPress 4.5 onwards.
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-width' => true,
	) );
	
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size('ameno-large-thumb', 1060, 650, true);
	add_image_size('ameno-index-thumb', 780, 250, true);
	add_image_size('ameno-square-thumb', 500, 500, true);
	add_image_size('ameno-side-gallery-thumb', 300, 450, true);

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'ameno' ),
		'social' => __( 'Social Menu', 'ameno'),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'ameno_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif;
add_action( 'after_setup_theme', 'ameno_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function ameno_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'ameno_content_width', 600 );
}
add_action( 'after_setup_theme', 'ameno_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function ameno_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'ameno' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Widgets', 'ameno' ),
		'description'   => __( 'Footer widgets area appears in the footer of the site.', 'ameno' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'ameno_widgets_init' );

function ameno_fonts_url() {
        $fonts_url = '';
        $fonts     = array();
        $subsets   = 'latin,latin-ext';

        /* translators: If there are characters in your language that are not supported by Lato, translate this to 'off'. Do not translate into your own language. */
        if ( 'off' !== _x( 'on', 'Lato font: on or off', 'ameno' ) ) {
                $fonts[] = 'Lato:100,300,400,400italic,700,900,900italic';
        }

        /* translators: If there are characters in your language that are not supported by PT Serif, translate this to 'off'. Do not translate into your own language. */
        if ( 'off' !== _x( 'on', 'PT Serif font: on or off', 'ameno' ) ) {
                $fonts[] = 'PT Serif:400,700,400italic,700italic';
        }

        if ( $fonts ) {
                $fonts_url = add_query_arg( array(
                        'family' => urlencode( implode( '|', $fonts ) ),
                        'subset' => urlencode( $subsets ),
                ), 'https://fonts.googleapis.com/css' );
        }

        return $fonts_url;
}

/**
 * Ameno Custom Logo
 */
function ameno_get_custom_logo() {
	
	if ( function_exists( 'get_custom_logo' ) ) {
		return get_custom_logo();
	}

}

/**
 * Enqueue scripts and styles.
 */
function ameno_scripts() {
	wp_enqueue_style( 'ameno-style', get_stylesheet_uri() );
	
	if (is_page_template('page-templates/page-nosidebar.php')) {
		wp_enqueue_style( 'ameno-layout-style' , get_template_directory_uri() . '/layouts/no-sidebar.css');
	} elseif(is_page_template('page-templates/page-leftsidebar.php')) {
		wp_enqueue_style( 'ameno-layout-style' , get_template_directory_uri() . '/layouts/sidebar-content.css');
	} else {
		wp_enqueue_style( 'ameno-layout-style' , get_template_directory_uri() . '/layouts/content-sidebar.css');
	}
	
	wp_enqueue_style( 'ameno-layout-style' , get_template_directory_uri() . '/layouts/content-sidebar.css');
	
	wp_enqueue_style( 'google-fonts', ameno_fonts_url() );
                    
	// FontAwesome
	wp_enqueue_style('fontawesome', get_template_directory_uri() .  '/font-awesome/css/font-awesome.min.css');
		
	wp_enqueue_script( 'superfish', get_template_directory_uri() . '/js/superfish.min.js', array('jquery'), '20140328', true );
	
	wp_enqueue_script( 'superfish-settings', get_template_directory_uri() . '/js/superfish-settings.js', array('ameno-superfish'), '20140328', true );
                
	wp_enqueue_script( 'ameno-hide-search', get_template_directory_uri() . '/js/hide-search.js', array(), '20140404', true );
	
	wp_enqueue_script( 'navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	wp_enqueue_script( 'masonry', get_template_directory_uri() . '/js/masonry-settings.js', array('masonry'), '20140401', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ameno_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
