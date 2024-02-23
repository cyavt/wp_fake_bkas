<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$product    = wc_get_product( $product_id );
$title      = $product->get_title();

if ( !$product || !$product->is_type('ovabrw_car_rental') ) return;

// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
    $first_day = 0;
}

// Total Day
$total_day = get_post_meta( $product_id, 'ovabrw_number_days', true );
if ( !$total_day ) {
    $total_day = 1;
}

// Get booked time
$statuses   = brw_list_order_status();
$order_time = ovabrw_get_disabled_dates( $product_id, $statuses );

$placeholder_date   = ovabrw_get_placeholder_date();
$label_time         = esc_html__( 'Choose time', 'tripgo' );
$label_check_in     = esc_html__( 'Check in', 'tripgo' );
$label_check_out    = esc_html__( 'Check out', 'tripgo' );

$check_in_default   = ovabrw_get_current_date_from_search( 'pickup_date', $product_id );
$check_out_default  = ovabrw_get_current_date_from_search( 'dropoff_date', $product_id );

$fixed_time_check_in    = get_post_meta( $product_id, 'ovabrw_fixed_time_check_in', true );
$fixed_time_check_out   = get_post_meta( $product_id, 'ovabrw_fixed_time_check_out', true );

$readonly = $check_in_class = '';
if ( ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) ) {
    $readonly = 'readonly';
    $check_in_class = ' ovabrw_readonly';
}

?>

<div class="ova-request-form" id="request-form">
    <form 
        class="form request-form" 
        action="<?php echo home_url('/'); ?>" 
        method="post" 
        enctype="multipart/form-data">

        <div class="ovabrw-form-container">
            <div class="rental_item"> 
                <label>
                    <?php esc_html_e( 'Name *', 'tripgo' ); ?>
                </label>
                <input 
                    type="text" 
                    class="required" 
                    name="name" 
                    placeholder="<?php esc_html_e( 'Your name', 'tripgo' ); ?> "
                    data-error="<?php esc_html_e( 'Name is required.', 'tripgo' ); ?>" />
            </div>
            <div class="rental_item"> 
                <label>
                    <?php esc_html_e( 'Email *', 'tripgo' ); ?>
                </label>
                <input 
                    type="text" 
                    class="required" 
                    name="email" 
                    placeholder="<?php esc_html_e( 'example@gmail.com', 'tripgo' ); ?> "
                    data-error="<?php esc_html_e( 'Email is required.', 'tripgo' ); ?>" />
            </div>
            <?php if ( 'yes' === ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_number', 'yes') ) ): ?>
                <div class="rental_item"> 
                    <label>
                        <?php esc_html_e( 'Phone *', 'tripgo' ); ?>
                    </label>
                    <input 
                        type="text" 
                        class="required" 
                        name="phone" 
                        placeholder="<?php esc_html_e( '(229) 555-2872', 'tripgo' ); ?>" 
                        data-error="<?php esc_html_e( 'Phone is required.', 'tripgo' ); ?>" />
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_address', 'yes') ) ): ?>
                <div class="rental_item"> 
                    <label>
                        <?php esc_html_e( 'Address *', 'tripgo' ); ?>
                    </label>
                    <input 
                        type="text" 
                        class="required" 
                        name="address" 
                        placeholder="<?php esc_html_e( 'Your address', 'tripgo' ); ?>" 
                        data-error="<?php esc_html_e( 'Address is required.', 'tripgo' ); ?>" />
                </div>
            <?php endif; ?>
            <?php if ( ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) ): ?>
                <div class="rental_item">
                    <label>
                        <?php echo esc_html( $label_time ); ?>
                    </label>
                    <select name="ovabrw_fixed_time" class="ovabrw_fixed_time">
                        <?php foreach( $fixed_time_check_in as $k => $check_in ):
                            if ( $check_in && isset( $fixed_time_check_out[$k] ) && $fixed_time_check_out[$k] ):
                                $txt_time = sprintf( esc_html__( 'From %s to %s', 'tripgo' ), $check_in, $fixed_time_check_out[$k] );
                        ?>
                            <option value="<?php esc_html_e( $check_in.'|'.$fixed_time_check_out[$k] ); ?>">
                                <?php esc_html_e( $txt_time ); ?>
                            </option>
                        <?php endif; endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_dates', 'yes' ) ) ): ?>
                <div class="rental_item"> 
                    <label>
                        <?php esc_html_e( 'Check in *', 'tripgo' ); ?>
                    </label>
                    <input 
                        type="text" 
                        name="ovabrw_request_pickup_date"  
                        class="required ovabrw_datetimepicker ovabrw_start_date<?php esc_attr_e( $check_in_class ); ?>" 
                        placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
                        autocomplete="off" 
                        value="<?php echo esc_attr( $check_in_default ); ?>" 
                        data-firstday="<?php echo esc_attr( $first_day ); ?>" 
                        data-total-day="<?php echo esc_attr( $total_day ); ?>" 
                        data-order-time='<?php echo esc_attr( $order_time ); ?>' 
                        data-error="<?php esc_html_e( 'Check-in is required.', 'tripgo' ); ?>" 
                        data-readonly="<?php esc_attr_e( $readonly ); ?>"
                        <?php esc_html_e( $readonly ); ?>/>
                </div>
                <div class="rental_item"> 
                    <label>
                        <?php esc_html_e( 'Check out *', 'tripgo' ); ?>
                    </label>
                    <input 
                        type="text" 
                        name="ovabrw_request_pickoff_date"  
                        class="required ovabrw_end_date" 
                        placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
                        autocomplete="off" 
                        value="<?php echo esc_attr( $check_in_default ); ?>" 
                        data-firstday="<?php echo esc_attr( $first_day ); ?>" 
                        data-total-day="<?php echo esc_attr( $total_day ); ?>" 
                        data-order-time='<?php echo esc_attr( $order_time ); ?>' 
                        data-error="<?php esc_html_e( 'Check-out is required.', 'tripgo' ); ?>" 
                        readonly />
                    <span class="ovabrw-date-loading">
                        <i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
                    </span>
                </div>
            <?php endif; ?>
            <?php wc_get_template( 'rental/loop/fields/guests.php', array( 'id' => $product_id ) ); ?>
            <?php wc_get_template( 'rental/loop/fields/extra_fields.php', array( 'id' => $product_id ) ); ?>
            <?php wc_get_template( 'rental/loop/fields/resources.php', array( 'id' => $product_id, 'form' => 'request' ) ); ?>
            <?php wc_get_template( 'rental/loop/fields/services.php', array( 'id' => $product_id ) ); ?>
            <?php if ( 'yes' === ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) ) ): ?>
                <div class="rental_item">
                    <textarea name="extra" cols="50" rows="5" placeholder="<?php esc_html_e( 'Extra Information', 'tripgo' ); ?>"></textarea>
                </div>
            <?php endif; ?>
        </div>
        <div class="ajax-error"></div>
        <button type="submit" class="request-form-submit">
            <?php esc_html_e( 'Send Now', 'tripgo' ); ?>
        </button>
        <input type="hidden" name="product_name" value="<?php echo esc_html( $title ); ?>" />
        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
        <input type="hidden" name="request_booking" value="request_booking" />
        <input type="hidden" name="quantity" value="1" />
    </form>
</div>