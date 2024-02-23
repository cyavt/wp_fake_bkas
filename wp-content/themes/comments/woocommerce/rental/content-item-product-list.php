<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
 
if ( ! defined( 'ABSPATH' ) ) exit();

$product_id      = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$product 	     = wc_get_product( $product_id );

$date_format 	 = ovabrw_get_date_format();

// gallery
$gallery_ids	 = $product->get_gallery_image_ids();
$number_gallery  = count( $gallery_ids );
$gallery_data    = array();

foreach( $gallery_ids as $k => $gallery_id ) {
	$gallery_url = wp_get_attachment_image_url( $gallery_id, 'tripgo_product_gallery' );
	$gallery_alt = get_post_meta( $gallery_id, '_wp_attachment_image_alt', true );

	if ( ! $gallery_alt ) {
		$gallery_alt = get_the_title( $gallery_id );
	}

	$src = array(
		'src' => $gallery_url,
		'caption' => $gallery_alt,
		'thumb' => $gallery_url,
	);

    array_push( $gallery_data, $src );
}

// video
$embed_url  = get_post_meta( $product_id, 'ovabrw_embed_video', true );
$controls   = apply_filters( 'ft_wc_video_controls', array(
    'autoplay'  => 'yes',
    'mute'      => 'no',
    'loop'      => 'yes',
    'controls'  => 'yes',
    'modest'    => 'yes',
    'rel'       => 'yes',
));

// link to detail
$min_adults     = get_post_meta( $product_id, 'ovabrw_adults_min', true );
$min_childrens  = get_post_meta( $product_id, 'ovabrw_childrens_min', true );
if ( !$min_adults ) $min_adults = 1;
if ( !$min_childrens ) $min_childrens = 0;

// thumbnail
$link      	= apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
$param      = array();

global $_POST;
$start_date 	= isset( $_POST['start_date'] ) 	? $_POST['start_date'] 		: '';
$adults 		= isset( $_POST['adults'] ) 		? $_POST['adults'] 			: $min_adults;
$childrens 		= isset( $_POST['childrens'] ) 		? $_POST['childrens'] 		: $min_childrens;

$dropoff_date 	= '';

if ( $start_date ) {
	$param['pickup_date']  = $start_date;
	$day          		   = get_post_meta( $product_id, 'ovabrw_number_days', true );
	$dropoff_date 		   = strtotime( $start_date ) + $day*86400;
	$dropoff_date 		   = wp_date( $date_format, $dropoff_date );
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

// title, link to detail, featured image
$title 		= get_the_title();

$image_url 	= get_the_post_thumbnail_url( $product_id, 'tripgo_product_gallery' );
$image_id 	= get_post_thumbnail_id();
$image_alt 	= '';

if ( $image_id ) {
	$image_alt 		= get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	if ( empty( $image_alt ) ) {
		$image_alt = get_the_title( $image_id );
	}
}

// Max people
$max_children_number  = get_post_meta( $product_id, 'ovabrw_childrens_max', true );
$max_adult_number     = get_post_meta( $product_id, 'ovabrw_adults_max', true );
$max_people           = $max_children_number + $max_adult_number;

// tour days
$tour_day  = get_post_meta ( $product_id,'ovabrw_number_days', true );

// Wishlist
$wishlist = do_shortcode('[yith_wcwl_add_to_wishlist]');

// Featured product
$is_featured = $product->is_featured();

// location and review
$address        = get_post_meta( $product_id, 'ovabrw_address', true );
$review_count   = $product->get_review_count();
$rating         = $product->get_average_rating();

// short description
$short_description = get_the_excerpt( $product_id );

// Price
$regular_price     = get_post_meta( $product_id, '_regular_price', true );
if( $product->is_on_sale() ) {
    $sale_price    = $product->get_sale_price();
}

?>

<div class="ovabrw-single-product">
	
	<div class="product-img">  
		<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">

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

		<div class="ova-video-gallery">
		    <?php if ( $embed_url ): ?>
		        <div class="btn-video btn-video-gallery" 
		            data-src="<?php echo esc_url( $embed_url ); ?>" 
		            data-controls="<?php echo esc_attr( json_encode( $controls ) ); ?>">
		            <i aria-hidden="true" class="icomoon icomoon-caret-circle-right"></i>
		            <?php esc_html_e( 'View video', 'tripgo' ); ?>
		        </div>
		        <div class="video-container">
		            <div class="modal-container">
		                <div class="modal">
		                    <i class="ovaicon-cancel"></i>
		                    <iframe class="modal-video" allow="autoplay" allowfullscreen></iframe>
		                </div>
		            </div>
		        </div>
		    <?php endif; ?>
		    <?php if ( $gallery_data && is_array( $gallery_data ) ): ?>
		        <div class="btn-gallery btn-video-gallery fancybox" 
		            data-gallery="<?php echo esc_attr( json_encode( $gallery_data ) );?>">
		            <i aria-hidden="true" class="icomoon icomoon-gallery"></i>
		            <?php echo sprintf( esc_html__( '%s photos', 'tripgo' ), count( $gallery_data ) ); ?>
		        </div>
		    <?php endif; ?>
		</div>

	</div>

	<div class="product-container">

		<div class="product-container-left">

			<a href="<?php echo esc_url( $link ); ?>">
				<h2 class="product-title">
					<?php echo esc_html( $title ); ?>
				</h2>
			</a>
            
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
		            </div>
		            <a href="<?php echo esc_url($link); ?>#reviews" class="woo-review-link" rel="nofollow">
		                <?php printf( _n( '%s review', '%s reviews', $review_count, 'tripgo' ), esc_html( $review_count ) ); ?>
		            </a>
		        </div>
		    <?php endif; ?>
            
            <?php if( !empty( $short_description ) ) : ?>
				<div class="product-short-description">
					<?php printf( $short_description ); ?>
				</div>
			<?php endif; ?>

		</div>
        
        <div class="product-container-right">
            
			<?php if( !empty( $max_people ) ) : ?>
				<div class="ova-tour-day ova-max-people">
					<i aria-hidden="true" class="icomoon icomoon-profile-circle"></i>
					<?php echo esc_html( $max_people ) ;?>	
				</div>
			<?php endif;?>

			<?php if( $tour_day ) :?>
				<div class="ova-tour-day">
					<i aria-hidden="true" class="icomoon icomoon-clock"></i>
					<?php echo esc_html( $tour_day ) . ' ' . esc_html__('days','tripgo'); ?>
				</div>
			<?php endif;?>

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
					<?php esc_html_e('Explore', 'tripgo'); ?>
				</a>
			</div>

        </div>	

	</div>

</div>
