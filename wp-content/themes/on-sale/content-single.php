<?php
/**
 * @package onsale
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="" itemtype="http://schema.org/BlogPosting">
	<div class="post-meta"><?php storefront_posted_on(); ?></div>
	<?php
	/**
	 * @hooked storefront_post_meta - 20
	 * @hooked storefront_post_content - 30
	 */
	do_action( 'onsale_single_post' );

	/**
	 * Functions hooked in to storefront_single_post_after action
	 *
	 * @hooked storefront_post_nav         - 10
	 * @hooked storefront_display_comments - 20
	 */
	do_action( 'storefront_single_post_bottom' );
	?>

</article><!-- #post-## -->