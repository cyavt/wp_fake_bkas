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

$arr_price  = tripgo_get_price_product( $product_id );

?>

<div class="ova-forms-product">
    <div class="forms-wrapper">
        <div class="price-product">
            <div class="label">
                <i aria-hidden="true" class="icomoon icomoon-tag"></i>
                <span><?php esc_html_e( 'From', 'tripgo' ); ?></span>
            </div>
            <div class="price">
                <span class="regular-price">
                    <?php echo wc_price( $arr_price['regular_price'] ); ?>
                </span>
                <?php if ( $arr_price['sale_price'] ): ?>
                    <span class="sale-price">
                        <?php echo wc_price( $arr_price['sale_price'] ); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="logo">
            <span class="line"></span>
            <i aria-hidden="true" class="icomoon icomoon-flig-outline"></i>
        </div>
        <div class="tabs">
            <?php if ( get_option( 'ova_brw_template_show_booking_form', 'yes' ) == 'yes' ): ?>
                <div class="item" data-id="#booking-form">
                    <?php esc_html_e( 'Booking Form', 'tripgo' ); ?>
                </div>
            <?php endif; ?>
            <?php if ( get_option( 'ova_brw_template_show_request_booking', 'yes' ) == 'yes' ): ?>
                <div class="item" data-id="#request-form">
                    <?php esc_html_e( 'Enquiry From', 'tripgo' ); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
            /* Booking Form */
            wc_get_template( 'rental/loop/booking-form.php', array( 'id' => $product_id ) );
            wc_get_template( 'rental/loop/request-form.php', array( 'id' => $product_id ) );
        ?>
    </div>
</div>