<?php
/**
 * @package onsale
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="" itemtype="http://schema.org/BlogPosting">

	<?php
		/**
		* onsale_blog_index_thumb hook
		*
		* @hooked onsale_post_thumb - 10
		*/	
		do_action( 'onsale_blog_index_thumb' );
	?>
	<div class="post-content-area">
	<?php
		/**
		* onsale_blog_index_header hook
		*
		* @hooked onsale_post_header - 10
		*/	
		do_action( 'onsale_blog_index_header' );
		/**
		* onsale_blog_index_content hook
		*
		* @hooked onsale_post_content - 10
		*/	
		do_action( 'onsale_blog_index_content' );
	?>
	</div>
	<div class="clearfix"></div>
</article><!-- #post-## -->