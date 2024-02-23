<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

global $product;

$all_ids = ovabrw_get_all_id_product();

if( isset( $args['id'] ) && $args['id'] != '' ) {

    $product_id     = ( in_array( $args['id'], $all_ids ) == true ) ? $args['id'] : get_the_id();

} elseif( in_array( get_the_id(), $all_ids ) == false ) {

    $product_id     = $all_ids[0];

} else {
    $product_id     = get_the_id();
}

$embed_url  = get_post_meta( $product_id, 'ovabrw_embed_video', true );
$controls   = apply_filters( 'ft_wc_video_controls', array(
    'autoplay'  => 'yes',
    'mute'      => 'no',
    'loop'      => 'yes',
    'controls'  => 'yes',
    'modest'    => 'yes',
    'rel'       => 'yes',
));

$arr_image_ids = tripgo_get_gallery_ids( $product_id );

$gallery_data = array();

if ( $arr_image_ids && is_array( $arr_image_ids ) ) {
    foreach( $arr_image_ids as $img_id ) {
        $image_url      = wp_get_attachment_image_url( $img_id, 'tripgo_product_gallery' );
        $image_caption  = wp_get_attachment_caption( $img_id );

        if ( !$image_caption ) {
            $image_caption = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
        }

        if ( !$image_caption ) {
            $image_caption = get_the_title( $img_id );
        }

        array_push( $gallery_data, array(
            'src'       => $image_url,
            'caption'   => $image_caption,
            'thumb'     => $image_url,
            'type'      => 'image',
        ));
    }
}

?>

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