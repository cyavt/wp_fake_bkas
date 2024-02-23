<?php
defined( 'ABSPATH' ) || exit();

// 1: Validate Booking Form And Rent Time
add_filter( 'woocommerce_add_to_cart_validation', 'ovabrw_validation_booking_form', 10, 3 );
if ( ! function_exists( 'ovabrw_validation_booking_form' ) ) {
    function ovabrw_validation_booking_form( $passed, $product_id, $quantity ) {
        if ( !$product_id ) {
            $product_id = sanitize_text_field( filter_input( INPUT_POST, 'product_id' ) );
        }

        // Check product type
        $product = wc_get_product( $product_id );

        if ( !$product || !$product->is_type('ovabrw_car_rental') ) return $passed;

        $data = $_POST;
        $date_format    = ovabrw_get_date_format();

        // Check-in
        $ovabrw_pickup_date = '';
        if ( ovabrw_check_array( $data, 'ovabrw_pickup_date' ) ) {
            $ovabrw_pickup_date = strtotime( $data['ovabrw_pickup_date'] );
        } else {
            $ovabrw_pickup_date = strtotime( sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickup_date' ) ) );
        }

        // Check-out
        $ovabrw_pickoff_date = '';
        if ( ovabrw_check_array( $data, 'ovabrw_pickoff_date' ) ) {
            $ovabrw_pickoff_date = strtotime( $data['ovabrw_pickoff_date'] );
        } else {
            $ovabrw_pickoff_date = strtotime( sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickoff_date' ) ) );
        }

        // Guests
        $ovabrw_adults = 1;
        if ( ovabrw_check_array( $data, 'ovabrw_adults' ) ) {
            $ovabrw_adults = absint( $data['ovabrw_adults'] );
        } else {
            $ovabrw_adults = absint( sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_adults' ) ) );
        }

        $ovabrw_childrens = 0;
        if ( ovabrw_check_array( $data, 'ovabrw_childrens' ) ) {
            $ovabrw_childrens = absint( $data['ovabrw_childrens'] );
        } else {
            $ovabrw_childrens = absint( sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_childrens' ) ) );
        }

        // Min, Max adults, childrens
        $min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
        $min_childrens  = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
        $max_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_max', true ) );
        $max_childrens  = absint( get_post_meta( $product_id, 'ovabrw_childrens_max', true ) );
        
        // Quantity
        if ( ovabrw_check_array( $data, 'ovabrw_quantity' ) ) {
            $quantity = absint( $data['ovabrw_quantity'] );
        } else {
            $ovabrw_quantity = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_quantity' ) );
            $quantity = !empty( $ovabrw_quantity ) ? absint( $ovabrw_quantity ) : 1;
        }

        // Set Pick-up, Drop-off Date again
        $new_input_date = ovabrw_new_input_date( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $date_format );

        $pickup_date_new    = $new_input_date['pickup_date_new'];
        $pickoff_date_new   = $new_input_date['pickoff_date_new'];

        // Error empty Pick Up Date or Pick Off Date
        if ( empty( $pickup_date_new ) ) {
            wc_clear_notices();
            echo wc_add_notice( __("Insert Pick-up date", 'ova-brw'), 'error');
            return false;
        }

        // Error Pick Up Date < Current Time
        if ( $pickup_date_new < current_time('timestamp') ){
            wc_clear_notices();
            echo wc_add_notice( __("Pick-up Date must be greater than Current Time", 'ova-brw'), 'error');   
            return false;
        }

        // Error Pick Up Date > Pick Off Date
        if ( $pickup_date_new >  $pickoff_date_new){
            wc_clear_notices();
            echo wc_add_notice( __("Drop-off Date must be greater than Pick-up Date", 'ova-brw'), 'error');
            return false;
        }

        // Error Quantity
        if ( $quantity < 1 ){
            wc_clear_notices();
            echo wc_add_notice( __("Please choose quantity greater 0", 'ova-brw'), 'error');   
            return false;
        }

        // Error Adults
        if ( $ovabrw_adults > $max_adults ) {
            wc_clear_notices();
            echo wc_add_notice( sprintf( esc_html__( 'Please choose the number of adults less than or equals %d', 'ova-brw' ), $max_adults ), 'error');
            return false;
        }

        if ( $ovabrw_adults < $min_adults ){
            wc_clear_notices();
            echo wc_add_notice( sprintf( esc_html__( 'Please choose the number of adults larger %d', 'ova-brw' ), $min_adults ), 'error'); 
            return false;
        }

        // Error Childrens
        if ( $ovabrw_childrens > $max_childrens ) {
            wc_clear_notices();
            echo wc_add_notice( sprintf( esc_html__( 'Please choose the number of childrens less than or equals %d', 'ova-brw' ), $max_childrens ), 'error');
            return false;
        }

        if ( $ovabrw_childrens < $min_childrens ){
            wc_clear_notices();
            echo wc_add_notice( sprintf( esc_html__( 'Please choose the number of childrens larger %d', 'ova-brw' ), $min_childrens ), 'error'); 
            return false;
        }

        // Check service
        if ( ovabrw_check_array( $data, 'ovabrw_service' ) ) {
            $ovabrw_service = $data['ovabrw_service'];
            $ovabrw_service_required = get_post_meta( $product_id, 'ovabrw_service_required', true );
            if ( $ovabrw_service_required ) {
                foreach( $ovabrw_service_required as $key => $value ) {
                    if ( 'yes' === $value ) {
                        if ( !( isset( $ovabrw_service[$key] ) && $ovabrw_service[$key] ) ) {
                            wc_clear_notices();
                            echo wc_add_notice( __("Please choose Service", 'ova-brw'), 'error');   
                            return false;
                            break;
                        }
                    }
                }
            }
        }
        
        $validate_manage_store = ova_validate_manage_store( $product_id, $pickup_date_new, $pickoff_date_new, $passed, $validate = 'cart', $quantity );
        
        if ( !empty( $validate_manage_store ) ) {
            return $validate_manage_store['status'];
        }

        return false;
    }
}

// 2: Add Extra Data To Cart Item
add_filter( 'woocommerce_add_cart_item_data', 'ovabrw_add_extra_data_to_cart_item',10, 4 );
if ( ! function_exists( 'ovabrw_add_extra_data_to_cart_item' ) ) {
    function ovabrw_add_extra_data_to_cart_item( $cart_item_data, $product_id, $variation_id, $quantity ) {

        // Check product type: rental
        $product = wc_get_product( $product_id );

        if ( !$product || !$product->is_type('ovabrw_car_rental') ) return $cart_item_data;

        $data = $_POST;
        $date_format = ovabrw_get_date_format();

        $ovabrw_pickup_date = $ovabrw_pickoff_date = '';
        // Check-in
        if ( ovabrw_check_array( $data, 'ovabrw_pickup_date' ) ) {
            $ovabrw_pickup_date = $data['ovabrw_pickup_date'];
        } else {
            $ovabrw_pickup_date = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickup_date' ) );
        }
        $cart_item_data['ovabrw_pickup_date'] = $ovabrw_pickup_date;

        // Check-out
        if ( ovabrw_check_array( $data, 'ovabrw_pickoff_date' ) ) {
            $ovabrw_pickoff_date = $data['ovabrw_pickoff_date'];
        } else {
            $ovabrw_pickoff_date = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickoff_date' ) );
        }
        $cart_item_data['ovabrw_pickoff_date'] = $ovabrw_pickoff_date;

        // If Check-in & Check-out empty
        if ( empty( $ovabrw_pickup_date ) && empty( $ovabrw_pickoff_date ) ) {
            return $cart_item_data;
        }

        // Adults
        $ovabrw_adults = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
        if ( ovabrw_check_array( $data, 'ovabrw_adults' ) ) {
            $ovabrw_adults = $data['ovabrw_adults'];
        } else {
            $ovabrw_adults = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_adults' ) );
        }
        $cart_item_data['ovabrw_adults'] = $ovabrw_adults;

        // Childrens
        $ovabrw_childrens  = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
        if ( ovabrw_check_array( $data, 'ovabrw_childrens' ) ) {
            $ovabrw_childrens = $data['ovabrw_childrens'];
        } else {
            $ovabrw_childrens = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_childrens' ) );
        }
        $cart_item_data['ovabrw_childrens'] = $ovabrw_childrens;
        
        // Quantity
        if ( ovabrw_check_array( $data, 'ovabrw_quantity' ) ) {
            $quantity = absint( $data['ovabrw_quantity'] );
        } else {
            $ovabrw_quantity = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_quantity' ) );
            $quantity = !empty( $ovabrw_quantity ) ? absint( $ovabrw_quantity ) : 1;
        }
        $cart_item_data['ovabrw_quantity'] = $quantity > 0 ? $quantity : 1;

        // Amount Insurance
        $ovabrw_amount_insurance = get_post_meta( $product_id, 'ovabrw_amount_insurance', true );
        if ( $ovabrw_amount_insurance ){
            $cart_item_data['ovabrw_amount_insurance'] = $cart_item_data['ovabrw_quantity'] * $ovabrw_amount_insurance * ( $ovabrw_adults + $ovabrw_childrens );
        }

        // Deposit
        $ova_type_deposit = '';
        if ( ovabrw_check_array( $data, 'ova_type_deposit' ) ) {
            $ova_type_deposit = $data['ova_type_deposit'];
        } else {
            $ova_type_deposit = sanitize_text_field( filter_input( INPUT_POST, 'ova_type_deposit' ) );
        }
        $ova_type_deposit = trim( $ova_type_deposit ) === 'deposit' ? 'deposit' : 'full';
        $cart_item_data['ova_type_deposit'] = $ova_type_deposit;

        $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
        $cart_item_data['ova_enable_deposit'] = $deposit_enable;

        // Custom Checkout Fields
        $list_fields = get_option( 'ovabrw_booking_form', array() );
        if ( !empty( $list_fields ) && is_array( $list_fields ) ) {
            foreach( $list_fields as $key => $field ) {
                if( $field['enabled'] == 'on' ) {
                    $cart_item_data[$key] = sanitize_text_field( filter_input( INPUT_POST, $key ) );
                }
            }
        }

        // Resources
        $ovabrw_resource_checkboxs = array();
        if ( ovabrw_check_array( $data, 'ovabrw_rs_checkboxs' ) ) {
            $ovabrw_resource_checkboxs = recursive_array_replace( '\\', '', $data['ovabrw_rs_checkboxs'] );
        }
        $cart_item_data['ovabrw_resources'] = $ovabrw_resource_checkboxs;

        // Services
        $ovabrw_services = array();
        if ( ovabrw_check_array( $data, 'ovabrw_service' ) ) {
            $ovabrw_services = recursive_array_replace( '\\', '', $data['ovabrw_service'] );
        }
        $cart_item_data['ovabrw_services'] = $ovabrw_services;

        return $cart_item_data;
    }
}

// 3: Display Extra Data in the Cart
add_filter( 'woocommerce_get_item_data', 'ovabrw_display_extra_data_cart', 10, 2 );
if ( ! function_exists( 'ovabrw_display_extra_data_cart' ) ) {
    function ovabrw_display_extra_data_cart( $item_data, $cart_item ) {
        // Check product type: rental
        if( !$cart_item['data']->is_type('ovabrw_car_rental') ) return $item_data;

        if ( $item_data ) {
            unset( $item_data );
        }

        if ( empty( $cart_item['ovabrw_pickup_date'] ) && empty( $cart_item['ovabrw_pickoff_date'] ) ) {
            wc_clear_notices();
            wc_add_notice( __('Insert full data in booking form', 'ova-brw'), 'notice');

            return false;
        }

        $date_format = ovabrw_get_date_format();

        // Check in
        if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
            $ovabrw_pickup_date = wp_date( $date_format, strtotime( $cart_item['ovabrw_pickup_date'] ) );

            $item_data[] = array(
                'key'     => esc_html__( 'Check in', 'ova-brw' ),
                'value'   => wc_clean( $ovabrw_pickup_date ),
                'display' => '',
            );
        }

        // Check out
        if ( ovabrw_check_array( $cart_item, 'ovabrw_pickoff_date' ) ) {
            $ovabrw_pickoff_date = wp_date( $date_format, strtotime( $cart_item['ovabrw_pickoff_date'] ) );

            $item_data[] = array(
                'key'     => esc_html__( 'Check out', 'ova-brw' ),
                'value'   => wc_clean( $ovabrw_pickoff_date ),
                'display' => '',
            );
        }

        // Adults
        if ( ovabrw_check_array( $cart_item, 'ovabrw_adults' ) ) {
            $item_data[] = array(
                'key'     => esc_html__( 'Adults', 'ova-brw' ),
                'value'   => wc_clean( $cart_item['ovabrw_adults'] ),
                'display' => '',
            );
        }

        // Childrens
        if ( ovabrw_check_array( $cart_item, 'ovabrw_childrens' ) ) {
            $item_data[] = array(
                'key'     => esc_html__( 'Childrens', 'ova-brw' ),
                'value'   => wc_clean( $cart_item['ovabrw_childrens'] ),
                'display' => '',
            );
        }

        // Quantity
        $ovabrw_quantity = 1;
        if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
            $ovabrw_quantity = absint( $cart_item['ovabrw_quantity'] );
        }
        
        $item_data[] = array(
            'key'     => esc_html__( 'Quantity', 'ova-brw' ),
            'value'   => wc_clean( $ovabrw_quantity ),
            'display' => '',
        );

        // Custom checkout fields
        $list_fields = get_option( 'ovabrw_booking_form', array() );
        if ( !empty( $list_fields ) && is_array( $list_fields ) ) {
            foreach( $list_fields as $key => $field ) {
                $value = array_key_exists( $key, $cart_item ) ? $cart_item[$key] : '';
                if( ! empty( $value ) && $field['enabled'] == 'on' ) {
                    $item_data[] = array(
                        'key'     => $field['label'],
                        'value'   => wc_clean( $value ),
                        'display' => '',
                    );
                }
            }
        }

        // Amount insurance
        if ( ovabrw_check_array( $cart_item, 'ovabrw_amount_insurance' ) ) {
            $item_data[] = array(
                'key'     => esc_html__( 'Amount Of Insurance', 'ova-brw' ),
                'value'   => wc_price( $cart_item['ovabrw_amount_insurance'] ),
                'display' => '',
            );
        }

        // Services
        if ( ovabrw_check_array( $cart_item, 'ovabrw_services' ) ) {
            $label_service      = get_post_meta( $cart_item['product_id'], 'ovabrw_label_service', true ); 
            $service_id         = get_post_meta( $cart_item['product_id'], 'ovabrw_service_id', true ); 
            $service_name       = get_post_meta( $cart_item['product_id'], 'ovabrw_service_name', true );
            $ovabrw_services    = $cart_item['ovabrw_services'];

            if ( is_array( $ovabrw_services ) ) {
                foreach( $ovabrw_services as $ser_id ) {
                    if( ! empty( $service_id ) && is_array( $service_id ) ) {
                        foreach( $service_id as $key => $value ) {
                            if ( !empty( $value ) && is_array( $value ) ) {
                                foreach( $value as $k => $val ) {
                                    if ( !empty( $val ) && $ser_id == $val ) {
                                        $s_label  = $label_service[$key];
                                        $s_name   = $service_name[$key][$k];
                                        
                                        $item_data[] = array(
                                            'key'     => $s_label,
                                            'value'   => wc_clean( $s_name ),
                                            'display' => '',
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Resources
        if ( ovabrw_check_array( $cart_item, 'ovabrw_resources' ) ) {
            $resource_arr = array();
            foreach( $cart_item['ovabrw_resources'] as $r_key => $r_value ) {
                if ( !in_array( $r_value, $resource_arr ) ) {
                    array_push( $resource_arr, $r_value );
                }
            }

            if ( !empty( $resource_arr ) ) {
                $item_data[] = array(
                    'key'     => esc_html__( 'Resource', 'ova-brw' ),
                    'value'   => wc_clean( join( ', ', $resource_arr ) ),
                    'display' => '',
                ); 
            }
        }

        return $item_data;
    }
}

// 4: Checkout Validate
add_action('woocommerce_after_checkout_validation', 'ovabrw_after_checkout_validation', 10, 2);
if ( ! function_exists( 'ovabrw_after_checkout_validation' ) ) {
    function ovabrw_after_checkout_validation( $data, $errors ) {
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $product = $cart_item['data'];

            $pickup_date = '';
            if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
                $pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
            }

            $pickoff_date = '';
            if ( ovabrw_check_array( $cart_item, 'ovabrw_pickoff_date' ) ) {
                $pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );
            }

            $stock_quantity = 1;
            if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
                $stock_quantity = absint( $cart_item['ovabrw_quantity'] );
            }

            if ( !empty( $product ) && $product->is_type( 'ovabrw_car_rental' ) ) {
                $product_id = $product->get_id();

                // Set Pick-up, Drop-off Date again
                $new_input_date     = ovabrw_new_input_date( $product_id, $pickup_date, $pickoff_date );
                $pickup_date_new    = $new_input_date['pickup_date_new'];
                $pickoff_date_new   = $new_input_date['pickoff_date_new'];

                $validate_manage_store = ova_validate_manage_store( $product_id, $pickup_date_new, $pickoff_date_new, $passed = true, $validate = 'checkout', $stock_quantity );

                if ( !empty( $validate_manage_store ) ) {
                    return $validate_manage_store['status'];
                } else {
                    $errors->add( 'validation', sprintf( __('%s isn\'t available for this time, Please book other time.', 'ova-brw'), $product->name ) );
                }
            }
        }

    }
}

// 5: Save to Order
add_action( 'woocommerce_checkout_create_order_line_item', 'ovabrw_add_extra_data_to_order_items', 10, 4 );
if ( ! function_exists( 'ovabrw_add_extra_data_to_order_items' ) ) {
    function ovabrw_add_extra_data_to_order_items( $item, $cart_item_key, $values, $order ) {
        $product_id = $item->get_product_id();

        // Check product type: rental
        $product = wc_get_product( $product_id );
        if ( !empty( $product ) && !$product->is_type('ovabrw_car_rental') ) return;

        if ( empty( $values['ovabrw_pickup_date'] ) && empty( $values['ovabrw_pickoff_date'] ) ) {
            return;
        }

        // Check in & Check out
        $item->add_meta_data( 'ovabrw_pickup_date', $values['ovabrw_pickup_date']);
        $item->add_meta_data( 'ovabrw_pickoff_date', $values['ovabrw_pickoff_date'] );

        // Guests
        if ( ovabrw_check_array( $values, 'ovabrw_adults' ) ) {
            $item->add_meta_data( 'ovabrw_adults', $values['ovabrw_adults']);
        } else {
            $item->add_meta_data( 'ovabrw_adults', 1 );
        }

        if ( ovabrw_check_array( $values, 'ovabrw_childrens' ) ) {
            $item->add_meta_data( 'ovabrw_childrens', $values['ovabrw_childrens']);
        } else {
            $item->add_meta_data( 'ovabrw_childrens', 0 );
        }

        // Quantity
        if ( ovabrw_check_array( $values, 'ovabrw_quantity' ) ) {
            $item->add_meta_data( 'ovabrw_quantity', $values['ovabrw_quantity'] );
        } else {
            $item->add_meta_data( 'ovabrw_quantity', 1 );
        }

        // Custom Checkout Fields
        $list_fields = get_option( 'ovabrw_booking_form', array() );
        if ( !empty( $list_fields ) && is_array( $list_fields ) ) {
            foreach( $list_fields as $key => $field ) {
                $value = array_key_exists( $key, $values ) ? $values[$key] : '';
                if( ! empty( $value ) && $field['enabled'] == 'on' ) {
                    $item->add_meta_data( $key, $value );
                }
            }
        }  

        // Services
        if ( ovabrw_check_array( $values, 'ovabrw_services' ) ) {
            $label_service      = get_post_meta( $product_id, 'ovabrw_label_service', true ); 
            $service_id         = get_post_meta( $product_id, 'ovabrw_service_id', true ); 
            $service_name       = get_post_meta( $product_id, 'ovabrw_service_name', true );
            $ovabrw_services    = $values['ovabrw_services'];

            $item->add_meta_data( 'ovabrw_services', $ovabrw_services );

            if ( is_array( $ovabrw_services ) ) {
                foreach( $ovabrw_services as $ser_id ) {
                    if( ! empty( $service_id ) && is_array( $service_id ) ) {
                        foreach( $service_id as $key => $value ) {
                            if ( !empty( $value ) && is_array( $value ) ) {
                                foreach( $value as $k => $val ) {
                                    if ( !empty( $val ) && $ser_id == $val ) {
                                        $s_label  = $label_service[$key];
                                        $s_name   = $service_name[$key][$k];
                                        $item->add_meta_data( $s_label, $s_name );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Resouces
        if ( ovabrw_check_array( $values, 'ovabrw_resources' ) ) {
            $resource_arr = array();
            foreach( $values['ovabrw_resources'] as $r_key => $r_value ) {
                if ( !in_array( $r_value, $resource_arr ) ) {
                    array_push( $resource_arr, $r_value );
                }
            }

            if ( !empty( $resource_arr ) ) {
                $item->add_meta_data( esc_html__( 'Resource', 'ova-brw' ), join( ', ', $resource_arr ) );
            }

            $item->add_meta_data( 'ovabrw_resources', $values['ovabrw_resources'] ); 
        }

        // Amount insurance
        if ( ovabrw_check_array( $values, 'ovabrw_amount_insurance' ) ) {
            $item->add_meta_data( 'ovabrw_amount_insurance', $values['ovabrw_amount_insurance'] );
        }
       
        $deposit_enable     = get_post_meta ($product_id, 'ovabrw_enable_deposit', true );
        $remaining_amount   = ova_calculate_deposit_remaining_amount( $values );

        $total      = round( $item->get_total(), wc_get_price_decimals() );
        $subtotal   = round( $item->get_subtotal(), wc_get_price_decimals() );
        $item->set_total( $total );
        $item->set_subtotal( $subtotal );

        /* Get totol include tax */
        if ( wc_tax_enabled() && wc_prices_include_tax() ) {
            $total += round( $values['line_tax'], wc_get_price_decimals() );
        }

        if( $remaining_amount['ova_type_deposit'] === 'full' ) {
            $deposit_amount     = $total;
            $remaining_amount   = 0;
        } else {
            $deposit_amount     = $remaining_amount['deposit_amount'];
            $remaining_amount   = $remaining_amount['remaining_amount'];
        }

        if ( 'yes' === $deposit_enable ) {
            $item->add_meta_data( 'ovabrw_remaining_amount', $remaining_amount );
            $item->add_meta_data( 'ovabrw_deposit_amount', $deposit_amount );
            $item->add_meta_data( 'ovabrw_deposit_full_amount', $deposit_amount + $remaining_amount );
        }
    }
}

// Return array deposit info
if ( ! function_exists( 'ova_calculate_deposit_remaining_amount' ) ) {
    function ova_calculate_deposit_remaining_amount ( $cart_item ) {
        $remaining_amount = $deposit_amount = $total_amount_insurance = $remaining_tax_total = 0;
        $product_id       = $cart_item['product_id'];

        // Quantity
        $quantity = 1;
        if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
            $quantity = absint( $cart_item['ovabrw_quantity'] );
        }

        // Check in
        $ovabrw_pickup_date = '';
        if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
            $ovabrw_pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
        }

        // Check out
        $ovabrw_pickoff_date = '';
        if ( ovabrw_check_array( $cart_item, 'ovabrw_pickoff_date' ) ) {
            $ovabrw_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );
        }

        $line_total = get_price_by_guests( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $cart_item );
        $line_total = round( $line_total, wc_get_price_decimals() );

        $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
        $value_deposit  = get_post_meta ( $product_id, 'ovabrw_amount_deposit', true );
        $value_deposit  = $value_deposit ? floatval( $value_deposit ) : 0;
        $type_deposit   = get_post_meta ( $product_id, 'ovabrw_type_deposit', true );

        $sub_remaining_amount = 0;
        $sub_deposit_amount   = $line_total;

        $cart_type_deposit = 'full';
        if ( ovabrw_check_array( $cart_item, 'ova_type_deposit' ) ) {
            $cart_type_deposit = $cart_item['ova_type_deposit'];
        }

        if ( 'yes' === $deposit_enable && $cart_type_deposit ) {
            $has_deposit = true;
            if ( 'full' === $cart_type_deposit ) {
                $sub_deposit_amount     = $line_total;
                $sub_remaining_amount   = 0;
            } else if ( 'deposit' === $cart_type_deposit ) {
                if ( 'percent' === $type_deposit ) {
                    $sub_deposit_amount     = ($line_total * $value_deposit) / 100;
                    $sub_remaining_amount   = $line_total - $sub_deposit_amount;
                } else if ( 'value' === $type_deposit ) {
                    $sub_deposit_amount     = $value_deposit;
                    $sub_remaining_amount   = $line_total - $sub_deposit_amount;
                }
            }
        }

        $remaining_amount       += ovabrw_get_price_tax( $sub_remaining_amount, $cart_item );
        $deposit_amount         += $sub_deposit_amount;

        $deposit_remaining_amount                       = [];
        $deposit_remaining_amount['deposit_amount']     = round( ovabrw_get_price_tax( $deposit_amount, $cart_item ), wc_get_price_decimals() );
        $deposit_remaining_amount['remaining_amount']   = round( ovabrw_get_price_tax( $remaining_amount, $cart_item ), wc_get_price_decimals() );
        $deposit_remaining_amount['ova_type_deposit']   = $cart_item['ova_type_deposit'];
        $deposit_remaining_amount['pay_total']          = round( $deposit_remaining_amount['deposit_amount'] + $deposit_remaining_amount['remaining_amount'] );

        return $deposit_remaining_amount;
    }
}

/**
 * [ova_validate_manage_store check product available]
 * @param  [number]  $product_id - ID product
 * @param  [strtotime]  $pickup_date - the date has been filtered via function
 * @param  [strtotime]  $pickoff_date - the date has been filtered via function  
 * @param  [string] $passed - true, false
 * @param  string  $validate - cart, checkout, empty
 * @param  integer $quantity - quantity in cart
 * @return [array] status
 */
if ( ! function_exists( 'ova_validate_manage_store' ) ) {
    function ova_validate_manage_store( $product_id, $pickup_date, $pickoff_date, $passed,  $validate = 'cart', $quantity = 1 ) {
        $quantity = absint( $quantity );

        // Get all Order ID by Product ID
        $statuses   = brw_list_order_status();
        $orders_ids = ovabrw_get_orders_by_product_id( $product_id, $statuses );

        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $product_id );

        // Error: Unvailable time for renting
        $untime_startdate = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $untime_enddate   = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

        if ( $untime_startdate ) {
            foreach( $untime_startdate as $key => $value ) {
                if ( isset( $untime_enddate[$key] ) && $untime_enddate[$key] && $untime_startdate[$key] ) {
                    $stt_untime_startdate   = strtotime( $untime_startdate[$key] );
                    $stt_untime_enddate     = strtotime( $untime_enddate[$key] );

                    if ( !( $pickup_date > $stt_untime_enddate || $pickoff_date < $stt_untime_startdate ) ) {
                        if ( $validate != 'search' ) {
                            wc_clear_notices();
                            echo wc_add_notice( esc_html__( 'This time is not available for booking', 'ova-brw' ), 'error');
                        }
                        return false;
                    }
                }
            }
        }

        // Error: Unavailable Date for booking in settings
        $disable_week_day      = get_option( 'ova_brw_calendar_disable_week_day', '' );
        $data_disable_week_day = $disable_week_day != '' ? explode( ',', $disable_week_day ) : '';

        if ( $data_disable_week_day && $pickup_date && $pickoff_date && apply_filters( 'ovabrw_disable_week_day', true ) ) {

            $datediff       = absint( $pickoff_date ) - absint( $pickup_date );
            $total_datediff = round( $datediff / (60 * 60 * 24), wc_get_price_decimals() ) + 1;

            // get number day
            $pickup_date_of_week   = date('w', $pickup_date );

            $pickup_date_timestamp = $pickup_date;
            
            $i = 0;

            while ( $i <= $total_datediff ) {
                if ( in_array( $pickup_date_of_week, $data_disable_week_day ) ) {
                    if ( $validate == 'search' ) {
                        return array( 'status' => false, 'vehicle_availables' => [] );
                    }
                    wc_clear_notices();
                    echo wc_add_notice( esc_html__( 'This time is not available for booking', 'ova-brw' ), 'error');
                    
                    return false;
                }

                $pickup_date_of_week  = date('w', $pickup_date_timestamp );
                
                $pickup_date_timestamp = strtotime('+1 day', $pickup_date_timestamp);

                $i++;
            }
        }

        // Check Count Product in Order
        $store_quantity = ovabrw_quantity_available_in_order( $product_id, $pickup_date, $pickoff_date );
        
        // Check Count Product in Cart
        $cart_quantity  = ovabrw_quantity_available_in_cart( $product_id, $validate, $pickup_date, $pickoff_date );
        
        // Data Quantity Available
        $data_quantity = ovabrw_get_quantity_available( $product_id, $store_quantity, $cart_quantity, $quantity, $passed, $validate );
        
        if ( $data_quantity && $data_quantity['quantity_available'] > 0 ) {
            return array( 'status' => $data_quantity['passed'], 'quantity_available' => $data_quantity['quantity_available'] );
        }

        return false;
    }
}

/**
 * Check quantity available in order
 */
if ( ! function_exists( 'ovabrw_quantity_available_in_order' ) ) {
    function ovabrw_quantity_available_in_order( $product_id, $pickup_date, $pickoff_date ) {
        $quantity = $qty_order = 0;

        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $product_id );

        // Get all Order ID by Product ID
        $statuses   = brw_list_order_status();
        $orders_ids = ovabrw_get_orders_by_product_id( $product_id, $statuses );

        if ( $orders_ids ) {
            foreach( $orders_ids as $key => $value ) {
                // Get Order Detail by Order ID
                $order = wc_get_order($value);

                // Get Meta Data type line_item of Order
                $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
                
                // For Meta Data
                foreach( $order_line_items as $item_id => $item ) {

                    $pickup_date_store = $pickoff_date_store = $id_vehicle_rented = '';

                    // Get product
                    $product_id = $item->get_product_id();

                    // Check Line Item have item ID is Car_ID
                    if ( in_array( $product_id , $array_product_ids ) ) {
                        
                        // Get value of pickup date, pickoff date
                        foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                            
                            if ( $meta->key == 'ovabrw_pickup_date' ) {
                                $pickup_date_store = strtotime( $meta->value );
                            }

                            if ( $meta->key == 'ovabrw_pickoff_date' ) {
                                $pickoff_date_store = strtotime( $meta->value );
                            }

                            if ( $meta->key == 'ovabrw_quantity' ) {
                                $qty_order = trim( $meta->value );
                            }
                        }

                        // Only compare date when "PickOff Date in Store" > "Current Time" becaue "PickOff Date Rent" have to > "Current Time"
                        if ( $pickoff_date_store >= current_time( 'timestamp' ) ) {
                            if ( !( $pickup_date > $pickoff_date_store || $pickoff_date < $pickup_date_store ) ){
                                $quantity += $qty_order;
                            }
                        }  
                    }
                }
            }
        }

        return $quantity;
    }
}

/**
 * Check quantity available in cart
 */
if ( ! function_exists( 'ovabrw_quantity_available_in_cart' ) ) {
    function ovabrw_quantity_available_in_cart( $product_id, $validate, $pickup_date, $pickoff_date ) {
        $quantity = 0;

        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $product_id );

        if ( $validate == 'cart' ) {
            foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ( in_array( $product_id, $array_product_ids ) ) {
                    $cart_pickup_date = '';
                    if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
                        $cart_pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
                    }

                    $cart_pickoff_date = '';
                    if ( ovabrw_check_array( $cart_item, 'ovabrw_pickoff_date' ) ) {
                        $cart_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );
                    }

                    if ( $cart_pickup_date && $cart_pickoff_date ) {
                        if ( !( $pickup_date >= $cart_pickoff_date || $pickoff_date <= $cart_pickup_date ) ) {
                            $quantity += $cart_item['ovabrw_quantity'];
                        }
                    }
                }
            }
        }

        return $quantity;
    }
}

/**
 * Get quantity available store
 */
if ( !function_exists( 'ovabrw_get_quantity_available' ) ) {
    function ovabrw_get_quantity_available( $product_id, $store_quantity, $cart_quantity, $quantity = 1, $passed, $validate ) {
        // Get stock quantity of product
        $stock_quantity = absint( get_post_meta( $product_id, 'ovabrw_stock_quantity', true ) );

        $quantity_available = (int)( $stock_quantity - $store_quantity - $cart_quantity );

        if ( $quantity_available > 0 && $quantity_available >= $quantity ) {
            $passed = true;
        } else {
            if ( $validate != 'search' && !wp_doing_ajax() ) {
                if ( $quantity > $quantity_available && $quantity_available != 0 && $quantity_available > 0 ) {
                    wc_clear_notices();
                    echo wc_add_notice( sprintf( esc_html__( 'Available tour is %s', 'ova-brw'  ), $number_available ), 'error');
                } else {
                    wc_clear_notices();
                    echo wc_add_notice( esc_html__( 'Tour isn\'t available for this time, Please book other time.', 'ova-brw' ), 'error');
                }
                return false;
            }

            if ( $quantity_available < 0 ) {
                $quantity_available = 0;
            }
        }

        $data_quantity = [
            'passed'              => $passed,
            'quantity_available'  => $quantity_available,
        ];

        return $data_quantity;
    }
}

/**
 * Check Unavailable
 */
if ( !function_exists( 'ovabrw_check_unavailable' ) ) {
    function ovabrw_check_unavailable( $product_id, $pickup_date, $pickoff_date ) {
        // Error: Unvailable time for renting
        $untime_startdate = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $untime_enddate   = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

        if ( $untime_startdate ) {
            foreach( $untime_startdate as $key => $value ) {
                if ( isset( $untime_enddate[$key] ) && $untime_enddate[$key] && $untime_startdate[$key] ) {
                    $stt_untime_startdate   = strtotime( $untime_startdate[$key] );
                    $stt_untime_enddate     = strtotime( $untime_enddate[$key] );

                    if ( !( $pickup_date > $stt_untime_enddate || $pickoff_date < $stt_untime_startdate ) ) {
                        return true;
                    }
                }
            }
        }

        // Error: Unavailable Date for booking in settings
        $disable_week_day      = get_option( 'ova_brw_calendar_disable_week_day', '' );
        $data_disable_week_day = $disable_week_day != '' ? explode( ',', $disable_week_day ) : '';

        if ( $data_disable_week_day && $pickup_date && $pickoff_date ) {

            $datediff       = absint( $pickoff_date ) - absint( $pickup_date );
            $total_datediff = round( $datediff / (60 * 60 * 24), wc_get_price_decimals() ) + 1;

            // get number day
            $pickup_date_of_week   = date('w', $pickup_date );

            $pickup_date_timestamp = $pickup_date;
            
            $i = 0;

            while ( $i <= $total_datediff ) {
                if ( in_array( $pickup_date_of_week, $data_disable_week_day ) ) {
                    return true;
                }

                $pickup_date_of_week  = date('w', $pickup_date_timestamp );
                $pickup_date_timestamp = strtotime('+1 day', $pickup_date_timestamp);
                $i++;
            }
        }

        return false;
    }
}


/**
 * Standardized Pick-up, Drop-off that the Guest enter at frontend
 * User for: Search, Compare with real date
 */
if ( ! function_exists( 'ovabrw_new_input_date' ) ) {
    function ovabrw_new_input_date( $product_id = '', $pickup_date = '', $pickoff_date = '', $date_format = 'd-m-Y' ) {
        if ( !$product_id ) return array( 'pickup_date_new' => '', 'pickoff_date_new' => '' );

        $pickup_date    = $pickup_date ? strtotime( date( $date_format, $pickup_date ) ) : '';
        $pickoff_date   = $pickoff_date ? strtotime( date( $date_format, $pickoff_date ) ) : '';

        return array( 'pickup_date_new' => $pickup_date, 'pickoff_date_new' => $pickoff_date );
    }
}
