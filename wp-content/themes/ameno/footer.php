<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ameno
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		
        <?php get_sidebar( 'footer' ); ?>
        
        <div class="site-info">
            <?php
			printf( esc_html__( 'Theme: %1$s by %2$s', 'ameno' ), esc_attr( 'Ameno', 'ameno' ),
				'<a href="'.esc_url( __( 'http://bgwebagency.com', 'ameno' ) ).'">' . esc_attr__( 'BG Web Agency', 'ameno' ) . '</a>' ); ?>
		</div><!-- .site-info -->
        
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
