<?php defined( 'ABSPATH' ) || exit();

/* Send mail in Request for booking */
function ovabrw_request_booking( $data ){
    // Get subject setting
    $subject = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_subject', esc_html__('Request For Booking') ) );
    if ( empty( $subject ) ) {
        $subject = esc_html__('Request For Booking', 'ova-brw');
    }

    // Get email setting
    $mail_to_setting = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) ) );

    if ( empty( $mail_to_setting ) ) {
        $mail_to_setting = get_option( 'admin_email' );
    }
    $mail_to = array( $mail_to_setting, $data['email'] );

    // Emails Cc
    $email_cc = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_cc_email' ) );
    if ( $email_cc ) {
        $email_cc = explode( '|', $email_cc );
        $email_cc = array_map('trim', $email_cc);

        if ( $email_cc && is_array( $email_cc ) ) {
            $mail_to = array_unique( array_merge ( $mail_to, $email_cc ) );
        }
    }

    $body = '';

    $product_name   = isset( $data['product_name'] ) ? $data['product_name'] : '';
    $product_id     = isset( $data['product_id'] ) ? $data['product_id'] : '';

    $name       = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
    $email      = isset( $data['email'] ) ? sanitize_text_field( $data['email'] ) : '';
    $number     = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
    $address    = isset( $data['address'] ) ? sanitize_text_field( $data['address'] ) : '';
    $pickup_date    = isset( $data['ovabrw_request_pickup_date'] ) ? sanitize_text_field( $data['ovabrw_request_pickup_date'] ) : '';
    $pickoff_date   = isset( $data['ovabrw_request_pickoff_date'] ) ? sanitize_text_field( $data['ovabrw_request_pickoff_date'] ) : '';
    $adults         = isset( $data['ovabrw_adults'] ) ? $data['ovabrw_adults'] : 1;
    $childrens      = isset( $data['ovabrw_childrens'] ) ? $data['ovabrw_childrens'] : 0;
    $resources      = isset( $data['ovabrw_rs_checkboxs'] ) ? $data['ovabrw_rs_checkboxs'] : [];
    $services       = isset( $data['ovabrw_service'] ) ? $data['ovabrw_service'] : [];
    $extra          = isset( $data['extra'] ) ? $data['extra'] : '';
    
    $service_ids    = get_post_meta( $product_id, 'ovabrw_service_id', true );
    $service_name   = get_post_meta( $product_id, 'ovabrw_service_name', true );
    $arr_services   = array();

    if ( !empty( $services ) && is_array( $services ) ) {
        foreach( $services as $s_id ){
            if( $s_id && $service_ids && is_array( $service_ids ) ){
                foreach( $service_ids as $key_id => $service_id_arr ) {
                    $key = array_search( $s_id, $service_id_arr );

                    if ( !is_bool( $key ) ) {
                        $name = '';
                        if ( ovabrw_check_array( $service_name, $key_id ) ) {
                            if ( ovabrw_check_array( $service_name[$key_id], $key ) ) {
                                $name = $service_name[$key_id][$key];
                                if ( $name ) {
                                    array_push( $arr_services , $name );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // get order
    $order = $product_id ? '<h2>'.esc_html__( 'Order details: ', 'ova-brw' ).'</h2><table><tr><td>'.esc_html__( 'Tour: ', 'ova-brw' ).'</td><td><a href="'.get_permalink( $product_id ).'">'.$product_name.'</a><td></tr>' : '';

    $order .= $name ? '<tr><td>'.esc_html__( 'Name: ', 'ova-brw' ).'</td><td>'.$name.'</td></tr>' : '';
    $order .= $email ? '<tr><td>'.esc_html__( 'Email: ', 'ova-brw' ).'</td><td>'.$email.'</td></tr>' : '';

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number', 'yes' ) ) == 'yes' ) {
        $order .= $number ? '<tr><td>'. esc_html__( 'Phone: ', 'ova-brw' ).'</td><td>'.$number.'</td></tr>' : '';
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_address', 'yes' ) ) == 'yes' ) {
        $order .= $address ? '<tr><td>'.esc_html__( 'Address: ', 'ova-brw' ).'</td><td>'.$address.'</td></tr>' : '';
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_dates', 'yes' ) ) == 'yes' ) {
        $order .= $pickup_date ? '<tr><td>'.esc_html__( 'Check-in: ', 'ova-brw' ).'</td><td>'.$pickup_date.'</td></tr>' : '';
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_dates', 'yes' ) ) == 'yes' ) {
        $order .= $pickoff_date ? '<tr><td>'.esc_html__( 'Check-out: ', 'ova-brw' ).'</td><td>'.$pickoff_date.'</td></tr>' : '';
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_guests', 'yes' ) ) == 'yes' ) {
        $order .= $adults ? '<tr><td>'. esc_html__( 'Adults: ', 'ova-brw' ).'</td><td>'.$adults.'</td></tr>' : '';
        $order .= $childrens ? '<tr><td>'.esc_html__( 'Childrens: ', 'ova-brw' ).'</td><td>'.$childrens.'</td></tr>' : '';
    }

    // Custom Checkout Fields
    $list_fields = get_option( 'ovabrw_booking_form', array() );

    if ( !empty( $list_fields ) && is_array( $list_fields ) ) {
        foreach( $list_fields as $key => $field ) {
            $value = array_key_exists( $key, $data ) ? $data[$key] : '';
            if ( !empty( $value ) && 'on' === $field['enabled'] ) {
                if ( 'select' === $field['type'] ) {
                    $options_key = $options_text = array();
                    if ( ovabrw_check_array( $field, 'ova_options_key' ) ) {
                        $options_key = $field['ova_options_key'];
                    }

                    if ( ovabrw_check_array( $field, 'ova_options_text' ) ) {
                        $options_text = $field['ova_options_text'];
                    }

                    $key_op = array_search( $value, $options_key );
                    if ( !is_bool( $key_op ) ) {
                        if ( ovabrw_check_array( $options_text, $key_op ) ) {
                            $value = $options_text[$key_op];
                        }
                    }
                }
                $order .= '<tr><td>'.sprintf( '%s: ', esc_html( $field['label'] ) ).'</td><td>'.esc_html( $value ).'</td></tr>';
            }
        }
    }
    
    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_service', 'yes' ) ) == 'yes' ) {
        if ( !empty( $resources ) && is_array( $resources ) ) {
            $order .= '<tr><td>'. esc_html__( 'Resource: ', 'ova-brw' );
            $resource = $resources ? implode(', ', $resources) : '';
            $order .= '</td><td>'. $resource.'</td></tr>';
        }
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) ) === 'yes' ) {
        if ( !empty( $arr_services ) && is_array( $arr_services ) ) {
            $order .= '<tr><td>'.esc_html__( 'Services: ', 'ova-brw' );
            $service = $arr_services ? implode(', ', $arr_services) : '';
            $order .= '</td><td>'.$service.'</td></tr>';
        }
    }
    
    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) ) == 'yes' ) {
        $order .= $extra ? '<tr><td>'.esc_html__( 'Extra: ', 'ova-brw' ).'</td><td>'.$extra.'</td></tr><table>' : '';
    }

    // Get Email Content
    $body = apply_filters( 'ovabrw_request_booking_content_mail', ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_content', esc_html__( 'You booked the tour: [product-name] from [check-in] to [check-out]. [order_details]', 'ova-brw' ) ) ) );
    if ( empty( $body ) ) {
        $body = esc_html__( 'You booked the tour: [product-name] from [check-in] to [check-out]. [order_details]', 'ova-brw' );
    }

    $body = str_replace('[br]', '<br/>', $body);
    $body = str_replace('[product-name]', '<a href="'.get_permalink($product_id).'" target="_blank">'.$product_name.'</a>', $body);

    // Replace body
    $body = str_replace('[check-in]', $pickup_date, $body);
    $body = str_replace('[check-out]', $pickoff_date, $body);
    $body = str_replace('[order_details]', $order, $body);

    return ovabrw_sendmail( $mail_to, $subject, $body );
}


function ova_wp_mail_from(){
    return $mail_to_setting = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) ) );
}

function ova_wp_mail_from_name(){
    $ova_wp_mail_from_name = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_name', esc_html__( 'Request For Booking', 'ova-brw' ) ) );
    if ( empty( $ova_wp_mail_from_name ) ) {
        $ova_wp_mail_from_name = esc_html__( 'Request For Booking', 'ova-brw' );
    }
    return $ova_wp_mail_from_name;
}

function ovabrw_sendmail( $mail_to, $subject, $body ){

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=".get_bloginfo( 'charset' )."\r\n";
    
    add_filter( 'wp_mail_from', 'ova_wp_mail_from' );
    add_filter( 'wp_mail_from_name', 'ova_wp_mail_from_name' );

    if( wp_mail($mail_to, $subject, $body, $headers ) ){
        $result = true;
    }else{
        $result = false;
    }

    remove_filter( 'wp_mail_from', 'ova_wp_mail_from');
    remove_filter( 'wp_mail_from_name', 'ova_wp_mail_from_name' );

    return $result;
}

