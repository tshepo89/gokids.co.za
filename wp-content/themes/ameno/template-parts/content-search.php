<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ameno
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="index-box">
        <header class="entry-header">
            <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
    
            <?php if ( 'post' === get_post_type() ) : ?>
            <div class="entry-meta">
				  <?php ameno_posted_on(); ?>
                  <?php 
                  if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) { 
                      echo '<span class="comments-link">';
                      comments_popup_link( __( 'Leave a comment', 'ameno' ), __( '1 Comment', 'ameno' ), __( '% Comments', 'ameno' ) );
                      echo '</span>';
                  }
                  ?>
                  <?php edit_post_link( __( ' | Edit', 'ameno' ), '<span class="edit-link">', '</span>' ); ?>
            </div><!-- .entry-meta -->
            <?php endif; ?>
        </header><!-- .entry-header -->
    
        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div><!-- .entry-summary -->
    
        <footer class="entry-footer read-more">
            <?php echo '<a href="' . esc_url( get_permalink() ). '" title="' . esc_html__('Read more about : ', 'ameno') . get_the_title() . '" rel="bookmark">'. esc_html__('Read more', 'ameno') .'</a>'; ?>
        </footer><!-- .entry-footer -->
    </div>
</article><!-- #post-## -->
