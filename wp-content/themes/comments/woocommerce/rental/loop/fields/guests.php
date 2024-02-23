<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();

$product = wc_get_product( $product_id );

if ( !$product || !$product->is_type('ovabrw_car_rental') ) return;

$adult_price    = tripgo_get_price_product( $product_id );
$children_price = get_post_meta( $product_id, 'ovabrw_children_price', true );

$max_adults     = get_post_meta( $product_id, 'ovabrw_adults_max', true );
$max_childrens  = get_post_meta( $product_id, 'ovabrw_childrens_max', true );

$min_adults     = get_post_meta( $product_id, 'ovabrw_adults_min', true );
$min_childrens  = get_post_meta( $product_id, 'ovabrw_childrens_min', true );

if ( !$max_adults ) $max_adults = 1;
if ( !$max_childrens ) $max_childrens = 0;
if ( !$min_adults ) $min_adults = 1;
if ( !$min_childrens ) $min_childrens = 0;

$number_adults      = isset( $_GET['ovabrw_adults'] ) ? $_GET['ovabrw_adults'] : $min_adults;
$number_childrens   = isset( $_GET['ovabrw_childrens'] ) ? $_GET['ovabrw_childrens'] : $min_childrens;
$gueststotal        = (int)$number_adults + (int)$number_childrens;

?>

<div class="rental_item">
    <label><?php esc_html_e( 'Guests', 'tripgo' ); ?></label>
    <div class="ovabrw-wrapper-guestspicker">
        <div class="ovabrw-guestspicker">
            <div class="guestspicker">
                <span class="gueststotal"><?php echo esc_html( $gueststotal ); ?></span>
            </div>
        </div>
        <div class="ovabrw-guestspicker-content">
            <div class="guests-buttons">
                <div class="description">
                    <label><?php esc_html_e( 'Adults', 'tripgo' ); ?></label>
                    <span class="guests-price adults-price">
                        <?php echo wc_price( $adult_price['regular_price'] ); ?>
                    </span>
                </div>
                <div class="guests-button">
                    <div class="guests-icon minus">
                        <i aria-hidden="true" class="icomoon icomoon-minus"></i>
                    </div>
                    <input 
                        type="text" 
                        name="ovabrw_adults" 
                        class="required ovabrw_adults" 
                        value="<?php echo esc_attr( $number_adults ); ?>" 
                        min="<?php echo esc_attr( $min_adults ); ?>" 
                        max="<?php echo esc_attr( $max_adults ); ?>" 
                        data-error="<?php esc_html_e( 'Adults is required.', 'tripgo' ); ?>" 
                        readonly />
                    <div class="guests-icon plus">
                        <i aria-hidden="true" class="icomoon icomoon-plus"></i>
                    </div>
                </div>
            </div>

            <div class="guests-buttons">
                <div class="description">
                    <label><?php esc_html_e( 'Childrens', 'tripgo' ); ?></label>
                    <span class="guests-price childrens-price">
                        <?php echo wc_price( $children_price ); ?>
                    </span>
                </div>
                <div class="guests-button">
                    <div class="guests-icon minus">
                        <i aria-hidden="true" class="icomoon icomoon-minus"></i>
                    </div>
                    <input 
                        type="text" 
                        name="ovabrw_childrens" 
                        class="ovabrw_childrens" 
                        value="<?php echo esc_attr( $number_childrens ); ?>" 
                        min="<?php echo esc_attr( $min_childrens ); ?>" 
                        max="<?php echo esc_attr( $max_childrens ); ?>" 
                        readonly />
                    <div class="guests-icon plus">
                        <i aria-hidden="true" class="icomoon icomoon-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>