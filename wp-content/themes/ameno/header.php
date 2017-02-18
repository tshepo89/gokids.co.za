<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ameno
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php endif; ?>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>        
        
<header id="masthead" class="site-header" role="banner">
    
	<?php
        if ( get_header_image() && !('blank' == get_header_textcolor()) ) { 
            echo '<div class="site-branding header-background-image" style="background-image: url(' . get_header_image() . ')">'; 
		}else {
			echo '<div class="site-branding">';
		}
    ?>
    	
        <div id="header-meta"> 
            <div class="logo">
                
                <?php $logoOutput = ameno_get_custom_logo(); 
                if( ($logoOutput != '') ): ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo $logoOutput; ?></a>
                <?php endif; ?>  
                
                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <?php
                $description = get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ) : ?>
                <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
                <?php endif; ?>
                
            </div>
            <div class="search-toggle">
                <i class="fa fa-search"></i>
                <a href="#search-container" class="screen-reader-text"><?php _e( 'Search', 'ameno' ); ?></a>
            </div>
            <?php ameno_social_menu();  ?>
        </div>
        
        <div id="search-container" class="search-box-wrapper clear">
            <div class="search-box clear">
                <?php get_search_form(); ?>
            </div>
        </div> 

    </div><!-- .site-branding -->

    <nav id="site-navigation" class="main-navigation" role="navigation">
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'ameno' ); ?></button>
        <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
    </nav><!-- #site-navigation -->
</header><!-- #masthead -->

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ameno' ); ?></a>

	<div id="content" class="site-content">