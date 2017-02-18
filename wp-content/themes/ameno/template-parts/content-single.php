<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ameno
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	

		<?php 
        if (has_post_thumbnail()) {
            echo '<div class="single-post-thumbnail clear">';
            echo '<div class="image-shifter">';
            echo the_post_thumbnail('large-thumb');
            echo '</div>';
            echo '</div>';
        }
        ?>
                                
    <header class="entry-header">
      
        <?php
            /* translators: used between list items, there is a space after the comma */
            $category_list = get_the_category_list( __( ', ', 'ameno' ) );
        
            if ( ameno_categorized_blog() ) {
                echo '<div class="category-list">' . $category_list . '</div>';
            }
        ?>
        
		<h1 class="entry-title"><?php the_title(); ?></h1>

		<div class="entry-meta">
			<?php ameno_posted_on(); ?>
                        
            <?php 
                if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) { 
                    echo '<span class="comments-link">';
                    comments_popup_link( __( 'Leave a comment', 'ameno' ), __( '1 Comment', 'ameno' ), __( '% Comments', 'ameno' ) );
                    echo '</span>';
                }
            ?>
                            
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'ameno' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		
		<?php
            ameno_entry_footer();
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->