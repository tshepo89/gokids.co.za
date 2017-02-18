<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ameno
 */

?>

<section class="<?php if ( is_404() ) { echo 'error-404'; } else { echo 'no-results'; } ?> not-found">
    <div class="index-box">
	<header class="entry-header">
		<h1 class="entry-title">
			<?php 
            if ( is_404() ) { esc_html_e( 'Page not available', 'ameno' ); }
            else if ( is_search() ) { printf( wp_kses( __( 'Nothing found for <em>', 'ameno') . get_search_query() . '</em>', array( 'em' => array() ) ) ); }
            else { esc_html_e( 'Nothing Found', 'ameno' );}
            ?>
        </h1>
	</header>

	<div class="entry-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
            
            <p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'ameno' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>
                        
                <?php elseif ( is_404() ) : ?>
                        
                        <p><?php esc_html_e( 'You seem to be lost. To find what you are looking for check out the most recent articles below or try a search:', 'ameno' ); ?></p>
                        <?php get_search_form(); ?>
                        
		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Nothing matched your search terms. Check out the most recent articles below or try searching for something else:', 'ameno' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'ameno' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!-- .entry-content -->
    </div><!-- .index-box -->
    
    <?php
    if ( is_404() || is_search() ) {
        
        ?>
    <header class="page-header"><h1 class="page-title"><?php esc_html_e( 'Most recent posts:', 'ameno' ); ?></h1></header>
    <?php
        // Get the 6 latest posts
        $args = array(
            'posts_per_page' => 6
        );

        $latest_posts_query = new WP_Query( $args );

        // The Loop
        if ( $latest_posts_query->have_posts() ) {
                while ( $latest_posts_query->have_posts() ) {

                    $latest_posts_query->the_post();
                    // Get the standard index page content
                    get_template_part( 'template-parts/content', get_post_format() );

                }
        }
        /* Restore original Post Data */
        wp_reset_postdata();

    }
    ?>
    
</section><!-- .no-results -->