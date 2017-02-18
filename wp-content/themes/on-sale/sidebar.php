<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package onsale
 */

if ( ! is_active_sidebar( 'page-sidebar' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'page-sidebar' ); ?>
</div><!-- #secondary -->
