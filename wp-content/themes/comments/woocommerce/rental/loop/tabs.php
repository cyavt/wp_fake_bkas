<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$all_ids = ovabrw_get_all_id_product();

if( isset( $args['id'] ) && $args['id'] != '' ) {

    $product_id     = ( in_array( $args['id'], $all_ids ) == true ) ? $args['id'] : get_the_id();

} elseif( in_array( get_the_id(), $all_ids ) == false ) {

    $product_id     = $all_ids[0];

} else {
    $product_id     = get_the_id();
}

$tour_description    = wpautop( get_post($product_id)->post_content );

$group_tour_included = get_post_meta( $product_id,'ovabrw_group_tour_included',true );
$group_tour_excluded = get_post_meta( $product_id,'ovabrw_group_tour_excluded',true );

$group_tour_plan     = get_post_meta( $product_id,'ovabrw_group_tour_plan',true );
$address             = get_post_meta( $product_id, 'ovabrw_address', true );


?>

<div class="ova-tabs-product">
    <div class="tabs">
        <?php if( !empty($tour_description) ) { ?>
            <div class="item" data-id="#tour-description">
                <?php esc_html_e( 'Description', 'tripgo' ); ?>
            </div>
        <?php } ?>
        <?php if( ! empty( $group_tour_included ) || ! empty( $group_tour_excluded  ) ) {  ?>
            <div class="item" data-id="#tour-included-excluded">
                <?php esc_html_e( 'Included/Excluded', 'tripgo' ); ?>
            </div>
        <?php } ?>
        <?php if( ! empty( $group_tour_plan ) ) {  ?>
            <div class="item" data-id="#tour-plan">
                <?php esc_html_e( 'Tour Plan', 'tripgo' ); ?>
            </div>
        <?php } ?>
        <?php if( ! empty( $address ) ) {  ?>
            <div class="item" data-id="#ova-tour-map">
                <?php esc_html_e( 'Tour Map', 'tripgo' ); ?>
            </div>
        <?php } ?>
        <div class="item" data-id="#ova-tour-review">
            <?php esc_html_e( 'Reviews', 'tripgo' ); ?>
        </div>
    </div>
    <?php
        /* Tab Content */
        wc_get_template( 'rental/loop/content.php', array( 'id' => $product_id ) );
        wc_get_template( 'rental/loop/included-excluded.php', array( 'id' => $product_id ) );
        wc_get_template( 'rental/loop/plan.php', array( 'id' => $product_id ) );
        wc_get_template( 'rental/loop/map.php', array( 'id' => $product_id ) );
        wc_get_template( 'rental/loop/review.php', array( 'id' => $product_id ) );
    ?>
</div>