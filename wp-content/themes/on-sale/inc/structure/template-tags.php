<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package onsale
 */

if ( ! function_exists( 'storefront_product_categories' ) ) {
	/**
	 * Display Product Categories
	 * Hooked into the `homepage` action in the homepage template
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_product_categories( $args ) {

		if ( is_woocommerce_activated() ) {

			?>
			<section class="storefront-product-section storefront-product-categories">
				<div class="woocommerce columns-3">
				<?php
					do_action( 'storefront_homepage_before_product_categories' );

					$args = array(
					'number'			=> 3,
					'child_categories' 	=> 0,
					'orderby' 			=> 'name',
					);

					$product_categories = get_terms( 'product_cat', $args );
					$count = count($product_categories);
					if ( $count > 0 ){ ?>

					<ul class='products'>

					<?php foreach ( $product_categories as $product_category ) {

				    // get the thumbnail id user the term_id
				    $thumbnail_id = get_woocommerce_term_meta( $product_category->term_id, 'thumbnail_id', true ); 
				    // get the image URL
				    $image = wp_get_attachment_url( $thumbnail_id ); 


					?>
					<?php if($image): ?>
					<li class="product-category product" style="background-image:url(<?php echo $image; ?>);">
					<?php else: ?>
					<li class="product-category product" style="background-image:url(<?php echo woocommerce_placeholder_img_src(); ?>);">
					<?php endif; ?>
						<a href="<?php echo get_term_link( $product_category ); ?>" class="cat-details">
							<h3><?php echo  $product_category->name; ?></h3>
							<?php echo $product_category->description; ?>
						</a>
						<a href="<?php echo get_term_link( $product_category ); ?>"><span class="overlay"></span></a>
					</li>
					
					<?php
					}
					echo "</ul>";
					}

					do_action( 'storefront_homepage_after_product_categories' );
				?>
				</div>
			</section>
			<?php
		}
	}
}

if ( ! function_exists( 'storefront_recent_products' ) ) {
	/**
	 * Display Recent Products
	 * Hooked into the `homepage` action in the homepage template
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_recent_products( $args ) {

		if ( is_woocommerce_activated() ) {

			$args = apply_filters( 'storefront_recent_products_args', array(
				'limit' 			=> 6,
				'columns' 			=> 3,
				'title'				=> __( 'Recent Products', 'storefront' ),
				) );

			echo '<section class="storefront-product-section storefront-recent-products">';

				do_action( 'storefront_homepage_before_recent_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				echo storefront_do_shortcode( 'recent_products',
					array(
						'per_page' 	=> intval( $args['limit'] ),
						'columns'	=> intval( $args['columns'] ),
						) );

				do_action( 'storefront_homepage_after_recent_products' );

			echo '</section>';
		}
	}
}

if ( ! function_exists( 'storefront_featured_products' ) ) {
	/**
	 * Display Featured Products
	 * Hooked into the `homepage` action in the homepage template
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_featured_products( $args ) {

		if ( is_woocommerce_activated() ) {

			$args = apply_filters( 'storefront_featured_products_args', array(
				'limit' 			=> 6,
				'columns' 			=> 3,
				'orderby'			=> 'date',
				'order'				=> 'desc',
				'title'				=> __( 'Featured Products', 'storefront' ),
				) );

			echo '<section class="storefront-product-section storefront-featured-products">';

				do_action( 'storefront_homepage_before_featured_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				echo storefront_do_shortcode( 'featured_products',
					array(
						'per_page' 	=> intval( $args['limit'] ),
						'columns'	=> intval( $args['columns'] ),
						'orderby'	=> esc_attr( $args['orderby'] ),
						'order'		=> esc_attr( $args['order'] ),
						) );

				do_action( 'storefront_homepage_after_featured_products' );

			echo '</section>';

		}
	}
}

if ( ! function_exists( 'storefront_popular_products' ) ) {
	/**
	 * Display Popular Products
	 * Hooked into the `homepage` action in the homepage template
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_popular_products( $args ) {

		if ( is_woocommerce_activated() ) {

			$args = apply_filters( 'storefront_popular_products_args', array(
				'limit' 			=> 6,
				'columns' 			=> 3,
				'title'				=> __( 'Top Rated Products', 'storefront' ),
				) );

			echo '<section class="storefront-product-section storefront-popular-products">';

				do_action( 'storefront_homepage_before_popular_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				echo storefront_do_shortcode( 'top_rated_products',
					array(
						'per_page' 	=> intval( $args['limit'] ),
						'columns'	=> intval( $args['columns'] ),
						) );

				do_action( 'storefront_homepage_after_popular_products' );

			echo '</section>';

		}
	}
}

if ( ! function_exists( 'storefront_on_sale_products' ) ) {
	/**
	 * Display On Sale Products
	 * Hooked into the `homepage` action in the homepage template
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_on_sale_products( $args ) {

		if ( is_woocommerce_activated() ) {

			$args = apply_filters( 'storefront_on_sale_products_args', array(
				'limit' 			=> 6,
				'columns' 			=> 3,
				'title'				=> __( 'On Sale', 'storefront' ),
				) );

			echo '<section class="storefront-product-section storefront-on-sale-products">';

				do_action( 'storefront_homepage_before_on_sale_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				echo storefront_do_shortcode( 'sale_products',
					array(
						'per_page' 	=> intval( $args['limit'] ),
						'columns'	=> intval( $args['columns'] ),
						) );

				do_action( 'storefront_homepage_after_on_sale_products' );

			echo '</section>';

		}
	}
}