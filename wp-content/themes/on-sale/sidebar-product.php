<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package onsale
 */

if ( is_active_sidebar( 'product-sidebar' ) ) {
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'product-sidebar' ); ?>
</div><!-- #secondary -->

<?php } else { get_sidebar('single'); } ?>