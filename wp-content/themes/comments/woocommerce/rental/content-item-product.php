<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$product 	= wc_get_product( $product_id );

$date_format 	= ovabrw_get_date_format();

$min_adults     = get_post_meta( $product_id, 'ovabrw_adults_min', true );
$min_childrens  = get_post_meta( $product_id, 'ovabrw_childrens_min', true );
if ( !$min_adults ) $min_adults = 1;
if ( !$min_childrens ) $min_childrens = 0;

// thumbnail
$link      	= apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
$param      = array();

global $_POST;
$start_date 	= isset( $_POST['start_date'] ) ? $_POST['start_date'] 	: '';
$adults 		= isset( $_POST['adults'] ) 	? $_POST['adults'] 		: $min_adults;
$childrens 		= isset( $_POST['childrens'] ) 	? $_POST['childrens'] 	: $min_childrens;

$dropoff_date 	= '';

if ( $start_date ) {
	$param['pickup_date'] 	= $start_date;
	$day 					= get_post_meta( $product_id, 'ovabrw_number_days', true );
	$dropoff_date 			= strtotime( $start_date ) + $day*86400;
	$dropoff_date 		  	= wp_date( $date_format, $dropoff_date );
}

if ( $dropoff_date ) {
	$param['dropoff_date']  = $dropoff_date;
}

if ( $adults ) {
	$param['ovabrw_adults'] 	  = $adults;
}

if ( $childrens ) {
	$param['ovabrw_childrens'] 	  = $childrens;
}

if ( $param ) {
	$link = add_query_arg( $param, $link );
}


// tour days
$tour_day  = get_post_meta ( $product_id,'ovabrw_number_days', true );

// location and review
$address        = get_post_meta( $product_id, 'ovabrw_address', true );
$review_count   = $product->get_review_count();
$rating         = $product->get_average_rating();

// Wishlist
$wishlist = do_shortcode('[yith_wcwl_add_to_wishlist]');

// Featured product
$is_featured = $product->is_featured();

// Price
$regular_price      = get_post_meta( $product_id, '_regular_price', true );
if( $product->is_on_sale() ) {
    $sale_price 	= $product->get_sale_price();
}

?>

<div class="ova_head_product">
	<?php if ( $is_featured ): ?>
		<div class="ova-is-featured">
			<?php esc_html_e( 'Featured', 'tripgo' ); ?>
		</div>
	<?php endif; ?>
	<?php if ( '[yith_wcwl_add_to_wishlist]' != $wishlist ): ?>
		<div class="ova-product-wishlist">
			<?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
		</div>
	<?php endif; ?>
	<a href="<?php echo esc_url( $link ); ?>" class="ova-product-thumbnail">
		<?php echo woocommerce_get_product_thumbnail('tripgo_product_slider'); ?>
	</a>
	<?php if( $tour_day ) :?>
		<div class="ova-tour-day">
			<i aria-hidden="true" class="icomoon icomoon-clock"></i>
			<?php echo esc_html( $tour_day ) . ' ' . esc_html__('days','tripgo'); ?>
		</div>
	<?php endif;?>
</div>

<div class="ova_foot_product">
	
	<h2 class="ova-product-title">
		<a href="<?php echo esc_url( $link ); ?>">
	        <?php echo get_the_title(); ?>
	    </a>
	</h2>
	
	<?php if ( $address ): ?>
        <div class="ova-product-location">
            <i aria-hidden="true" class="icomoon icomoon-location"></i>
            <span class="location">
                <?php echo esc_html( $address ); ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if ( wc_review_ratings_enabled() && $rating > 0 ): ?>
        <div class="ova-product-review">
            <div class="star-rating" role="img" aria-label="<?php echo sprintf( __( 'Rated %s out of 5', 'tripgo' ), $rating ); ?>">
                <span class="rating-percent" style="width: <?php echo esc_attr( ( $rating / 5 ) * 100 ).'%'; ?>;"></span>
                <?php if ( $review_count > 0 ): ?>
                    <span class="rating"><?php echo esc_html( $review_count ); ?></span>'
                <?php else: ?>
                    <strong class="rating"><?php echo esc_html( $rating ); ?></strong>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

	<div class="ova-product-wrapper-price">
		<div class="ova-product-price">
			<?php if ( isset( $sale_price ) && $regular_price ): ?>
				<span class="new-product-price"><?php echo wc_price( $sale_price ); ?></span>
				<span class="old-product-price"><?php echo wc_price( $regular_price ); ?></span>
			<?php elseif ( !isset( $sale_price ) && $regular_price ): ?>
				<span class="new-product-price"><?php echo wc_price( $regular_price ); ?></span>
		    <?php else: ?>
		    	<span class="no-product-price"><?php esc_html_e( 'Price Negotiable', 'tripgo' ); ?></span>
			<?php endif; ?>
		</div>
		<a href="<?php echo esc_url( $link ); ?>" class="btn product-btn-book-now">
			<?php  esc_html_e('Explore', 'tripgo'); ?>
		</a>
	</div>

</div>