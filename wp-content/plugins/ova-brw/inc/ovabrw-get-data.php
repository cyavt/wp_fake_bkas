<?php if ( !defined( 'ABSPATH' ) ) exit();

/*
* Get disabled dates
*/
if ( !function_exists( 'ovabrw_get_disabled_dates' ) ) {
    function ovabrw_get_disabled_dates( $product_id, $order_status = array( 'wc-completed' ) ) {
        global $wpdb;

        $order_disabled_dates = $disabled_dates = array();
        $quantity = (int)get_post_meta( $product_id, 'ovabrw_stock_quantity', true );
        
        // Get array product ids when use WPML
        $array_product_ids  = ovabrw_get_wpml_product_ids( $product_id );
        $orders_ids         = ovabrw_get_orders_by_product_id( $product_id, $order_status );
        $date_format        = ovabrw_get_date_format();

        foreach( $orders_ids as $key => $value ) {
            // Get Order Detail by Order ID
            $order = wc_get_order($value);

            // Get Meta Data type line_item of Order
            $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
           
            // For Meta Data
            foreach ( $order_line_items as $item_id => $item ) {
                $push_date_unavailable  = array();
                $pickup_date_store      = $pickoff_date_store = '';
                $order_quantity         = 1;
                
                if ( in_array( $item->get_product_id(), $array_product_ids ) ) {

                    // Get value of pickup date, pickoff date
                    foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {

                        if ( $meta->key == 'ovabrw_pickup_date' ) {
                            $pickup_date_store = strtotime( $meta->value );
                        }

                        if ( $meta->key == 'ovabrw_pickoff_date' ) {
                            $pickoff_date_store = strtotime( $meta->value );
                        }

                        if ( $meta->key == 'ovabrw_quantity' ) {
                            $order_quantity = intval( $meta->value );
                        }
                    }
                }

                if ( $pickoff_date_store >= current_time( 'timestamp' ) ) {
                    for( $i = 0; $i < $order_quantity ; $i++ ) { 
                        $order_push_disabled    = ovabrw_push_disabled_dates( $pickup_date_store, $pickoff_date_store, $product_id );
                        $order_disabled_dates   = array_merge_recursive( $order_disabled_dates, $order_push_disabled );
                    }
                }
            }
        }

        // Check Unavaiable Time in Product
        $untime_startdate   = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $untime_enddate     = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

        if ( is_array( $untime_startdate ) && !empty( $untime_enddate ) ) {
            foreach( $untime_startdate as $key => $value ) {
                if ( ! empty( $untime_startdate[$key] ) ) {
                    $untime_dates   = array();
                    $untime_dates   = ovabrw_push_disabled_dates( strtotime( $untime_startdate[$key] ), strtotime( $untime_enddate[$key] ) );
                    $disabled_dates = array_merge_recursive( $disabled_dates, $untime_dates );
                }
            }
        }

        // Add disabled dates in order
        if ( !empty( $order_disabled_dates ) && is_array( $order_disabled_dates ) ) {
            $order_disabled_dates = array_count_values( $order_disabled_dates );
            foreach( $order_disabled_dates as $date => $count ) {
                if ( $count >= $quantity && !in_array( $date, $disabled_dates ) ) {
                    array_push( $disabled_dates, $date );
                }
            }
        }

        // Remove duplicate value
        $disabled_dates = array_unique( $disabled_dates );

        return json_encode( $disabled_dates );
    }
}

if ( !function_exists( 'ovabrw_push_disabled_dates' ) ) {
    function ovabrw_push_disabled_dates( $pickup_date_store, $pickoff_date_store, $product_id = '' ) {
        $date_format    = ovabrw_get_date_format();
        $start_date     = date( $date_format, $pickup_date_store );
        $end_date       = date( $date_format, $pickoff_date_store );
        $disabled_dates = array();
        $dates_between  = total_between_2_days( $start_date, $end_date );

        if ( $dates_between == 0 ) {
            if ( !in_array( $start_date, $disabled_dates ) ) {
                array_push( $disabled_dates, $start_date );
            }

        } else {
            $dates_between = ovabrw_createDatefull( strtotime( $start_date ), strtotime( $end_date ), $format= $date_format );

            foreach ( $dates_between as $key => $value ) {
                if ( !in_array( $value, $disabled_dates ) ) {
                    array_push( $disabled_dates, $value );
                }
            }
        }
        
        return $disabled_dates;
    }
}

/**
 * get_order_rent_time return all date available
 * @param  number $product_id   Product ID
 * @param  array  $order_status wc-completed, wc-processing
 * @return json               dates available
 */
if ( !function_exists( 'get_order_rent_time' ) ) {
    function get_order_rent_time( $product_id, $order_status = array( 'wc-completed' ) ){
        global $wpdb;

        $order_date = $dates_un_avaiable = array();

        $stock_quantity = ovabrw_get_total_stock( $product_id );
        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $product_id );
        $orders_ids     = ovabrw_get_orders_by_product_id( $product_id, $order_status );
        $date_format    = ovabrw_get_date_format();

        foreach( $orders_ids as $key => $value ) {

            // Get Order Detail by Order ID
            $order = wc_get_order($value);

            // Get Meta Data type line_item of Order
            $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
           
            // For Meta Data
            foreach( $order_line_items as $item_id => $item ) {
                $push_date_unavailable  = array();
                $pickup_date_store      = $pickoff_date_store = '';
                $ovabrw_quantity = 1;
                
                if ( in_array( $item->get_product_id(), $array_product_ids) ) {
                    // Get value of pickup date, pickoff date
                    foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                        if ( $meta->key === 'ovabrw_pickup_date' ) {
                            $pickup_date_store = strtotime( $meta->value );
                        }

                        if ( $meta->key === 'ovabrw_pickoff_date' ) {
                            $pickoff_date_store = strtotime( $meta->value );
                        }

                        if ( $meta->key === 'ovabrw_quantity' ) {
                            $ovabrw_quantity = absint( $meta->value );
                        }
                    }
                }

                // $ovabrw_pickoff_date_store - $ovabrw_pickup_date_store
                if ( $pickup_date_store && $pickoff_date_store ) {
                    $push_date_unavailable = push_date_unavailable( $pickup_date_store, $pickoff_date_store );

                    if ( !empty( $push_date_unavailable ) ) {
                        for ( $i = 0; $i < $ovabrw_quantity ; $i++ ) { 
                            $dates_un_avaiable = array_merge_recursive( $dates_un_avaiable, $push_date_unavailable );
                        }
                    }
                }
            }
        }

        // Check Unavaiable Time in Product
        $ovabrw_untime_startdate    = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $ovabrw_untime_enddate      = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );

        if ( !empty( $ovabrw_untime_startdate ) && is_array( $ovabrw_untime_startdate ) ) {
            foreach ($ovabrw_untime_startdate as $key => $value) {
                if ( !empty( $ovabrw_untime_startdate[$key] ) ) {
                    if ( ovabrw_check_array( $ovabrw_untime_enddate, $key ) ) {
                        $un_start   = strtotime( $ovabrw_untime_startdate[$key] );
                        $un_end     = strtotime( $ovabrw_untime_enddate[$key] );
                        $push_date_unavailable_untime = push_date_unavailable( $un_start, $un_end );

                        if ( !empty( $push_date_unavailable_untime ) ) {
                            for( $i = 0; $i < $stock_quantity; $i++ ) {
                                $dates_un_avaiable = array_merge_recursive( $dates_un_avaiable, $push_date_unavailable_untime );
                            }
                        }
                    }
                }
            }
        }

        // Unavailable Date for booking
        $data_unavailable = array_count_values( $dates_un_avaiable );
        if ( !empty( $data_unavailable ) && is_array( $data_unavailable ) ) {
            foreach( $data_unavailable as $date => $quantity ) {
                array_push( $order_date, array(
                    'title'     => $quantity . esc_html__( '/', 'ova-brw' ) . $stock_quantity,
                    'start'     => $date,
                    'color'     => apply_filters( 'ovabrw_ft_color_event', '#FF1A1A' ),
                    'textColor' => apply_filters( 'ovabrw_ft_text_color_event', '#FFFFFF' ),
                ));

                if ( $quantity >= $stock_quantity ) {
                    array_push( $order_date, array(
                        'start'             => $date,
                        'display'           => 'background',
                        'backgroundColor'   => apply_filters( 'ovabrw_ft_background_color_event', '#FF1A1A' ),
                    ));
                }
            }
        }

        return $order_date;
    }
}

if ( !function_exists( 'push_date_unavailable' ) ) {
    function push_date_unavailable( $ovabrw_pickup_date, $ovabrw_pickoff_date ) {
        $date_format    = 'Y-m-d';
        $date_pickup    = date( $date_format, $ovabrw_pickup_date );
        $date_pickoff   = date( $date_format, $ovabrw_pickoff_date );
        $dates_avaiable = array();
        $between_2_days = total_between_2_days( $date_pickup, $date_pickoff );

        if ( $between_2_days == 0 ) { // In a day
            array_push( $dates_avaiable, $date_pickup );
        } else if ( $between_2_days == 1 ) { // 2 day beside
            array_push( $dates_avaiable, $date_pickup );
            array_push( $dates_avaiable, $date_pickoff );
        } else { // from 3 days 
            array_push( $dates_avaiable, $date_pickup ); 

            $date_between = ovabrw_createDatefull( strtotime( $date_pickup ), strtotime( $date_pickoff ), $format= $date_format );
            // Remove first and last array
            array_shift( $date_between ); 
            array_pop( $date_between );

            foreach( $date_between as $key => $value ) {
                array_push( $dates_avaiable, $value ); 
            }
            array_push( $dates_avaiable, $date_pickoff );
        }
        
        return $dates_avaiable;
    }
}

/**
 * get all Order id of a product
 * @param  [number] $product_id   product id
 * @param  array  $order_status wc-completed, wc-processing
 * @return [array object]               all order id
 */
function ovabrw_get_orders_by_product_id( $product_id, $order_status = array( 'wc-completed' ) ){
    
    global $wpdb;

    $orders_ids = array();


    // Get array product ids when use WPML
    $array_product_ids = ovabrw_get_wpml_product_ids( $product_id );

    foreach ($array_product_ids as $key => $value) {

        $order_id = $wpdb->get_col("
            SELECT DISTINCT order_items.order_id
            FROM {$wpdb->prefix}woocommerce_order_items as order_items
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
            LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
            WHERE posts.post_type = 'shop_order'
            AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            AND order_items.order_item_type = 'line_item'
            AND order_item_meta.meta_key = '_product_id'
            AND order_item_meta.meta_value = '$value'
        ");

        $orders_ids = array_merge( $orders_ids, $order_id );

    }
    

    return ( $orders_ids );
    
}


/**
 * ovabrw_search_vehicle Search Product
 * @param  array $data_search more value
 * @return [array object]              false | array object wq_query
 */
function ovabrw_search_vehicle( $data_search ){
    
    $destination     = isset( $data_search['ovabrw_destination'] )  ? sanitize_text_field( $data_search['ovabrw_destination'] )     : 'all';
    $name_product    = isset( $data_search['ovabrw_name_product'] ) ? sanitize_text_field( $data_search['ovabrw_name_product'] )    : '';
    $pickup_date     = isset( $data_search['ovabrw_pickup_date'] )  ? strtotime( $data_search['ovabrw_pickup_date'] )               : '';
    $adults          = isset( $data_search['ovabrw_adults'] )       ? sanitize_text_field( $data_search['ovabrw_adults'] )          : '';
    $childrens       = isset( $data_search['ovabrw_childrens'] )    ? sanitize_text_field( $data_search['ovabrw_childrens'] )       : '';

    $order           = isset( $data_search['order'] )   ? sanitize_text_field( $data_search['order'] ) : 'DESC';
    $orderby         = isset( $data_search['orderby'] ) ? sanitize_text_field( $data_search['orderby'] ) : 'ID' ;

    $name_attribute  = isset( $data_search['ovabrw_attribute'] ) ? sanitize_text_field( $data_search['ovabrw_attribute'] ) : '';
    $value_attribute = isset( $data_search[$name_attribute] )    ? sanitize_text_field( $data_search[$name_attribute] ) : '';

    $category        = isset( $data_search['cat'] )                 ? sanitize_text_field( $data_search['cat'] ) : '';
    $tag_product     = isset( $data_search['ovabrw_tag_product'] )  ? sanitize_text_field( $data_search['ovabrw_tag_product'] ) : '';
    $list_taxonomy   = ovabrw_create_type_taxonomies();

    $slug_custom_taxonomy  = isset( $data_search['ovabrw_slug_custom_taxonomy'] )  ? sanitize_text_field( $data_search['ovabrw_slug_custom_taxonomy'] )     : '';

    $arg_taxonomy_arr      = [];
    if ( ! empty( $list_taxonomy ) ) {
        foreach( $list_taxonomy as $taxonomy ) {
            $taxonomy_get  = isset( $data_search[$taxonomy['slug'].'_name'] ) ? sanitize_text_field( $data_search[$taxonomy['slug'].'_name'] ) : '';
            if ( $taxonomy_get != 'all' && $taxonomy['slug'] == $slug_custom_taxonomy ) {
                $arg_taxonomy_arr[] = array(
                    'taxonomy' => $taxonomy['slug'],
                    'field'    => 'slug',
                    'terms'    => $taxonomy_get
                );
            } else {
                $arg_taxonomy_arr[] = '';
            }
        }
    }

    $statuses = brw_list_order_status();
    $error    = array();
    $items_id = $args_cus_tax_custom = array();

    $args_meta_query_arr   =  $args_cus_meta_custom = array();

    if ( $destination != 'all' ) {
        $args_meta_query_arr[] = [
            'key'     => 'ovabrw_destination',
            'value'   => $destination,
            'type'    => 'numeric',
            'compare' => 'IN',
        ];
    }

    if ( $name_product == '' ) {
        $args_base = array(
            'post_type'      => 'product',
            'posts_per_page' => '-1',
            'post_status'    => 'publish'
        );
    } else {
        $args_base = array(
            'post_type'      => 'product',
            'posts_per_page' => '-1',
            's'              => $name_product,
            'post_status'    => 'publish'
        );
    }
    
    if ( $category != '' ) {
        $arg_taxonomy_arr[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $category
        ];
    }

    if ( $name_attribute != '' ) {
        $arg_taxonomy_arr[] = [
            'taxonomy' => 'pa_' . $name_attribute,
            'field' => 'slug',
            'terms' => [$value_attribute],
            'operator'       => 'IN',
        ];
    }

    if ( $tag_product != '' ) {
        $arg_taxonomy_arr[] = [
            'taxonomy' => 'product_tag',
            'field' => 'name',
            'terms' => $tag_product
        ];
    }

    if( !empty($arg_taxonomy_arr) ){
        $args_cus_tax_custom = array(
            'tax_query' => array(
                'relation'  => 'AND',
                $arg_taxonomy_arr
            )
        );
    }

    if( $adults != '') {
        $args_meta_query_arr[] = [
            'key'     => 'ovabrw_adults_max',
            'value'   => $adults,
            'type'    => 'numeric',
            'compare' => '>=',
        ];
    }

    if( $childrens != '') {
        $args_meta_query_arr[] = [
            'key'     => 'ovabrw_childrens_max',
            'value'   => $childrens,
            'type'    => 'numeric',
            'compare' => '>=',
        ];
    }

    if( !empty($args_meta_query_arr) ){
        $args_cus_meta_custom = array(
            'meta_query' => array(
                'relation'  => 'AND',
                $args_meta_query_arr
            )
        );
    }
   

    $args = array_merge_recursive( $args_base, $args_cus_tax_custom, $args_cus_meta_custom );



    // Get All products
    $items = new WP_Query( $args );

    if ( $items->have_posts() ) : while ( $items->have_posts() ) : $items->the_post();

        // Product ID
        $id = get_the_id();

        $day   = get_post_meta( $id, 'ovabrw_number_days', true );

        $pickoff_date = '';
        if ( $pickup_date ) {
            $pickoff_date = $pickup_date + $day*86400;
        }

        // Set Pick-up, Drop-off Date again
        $new_input_date     = ovabrw_new_input_date( $id, $pickup_date, $pickoff_date, '' );
        $pickup_date_new  = $new_input_date['pickup_date_new'];
        $pickoff_date_new = $new_input_date['pickoff_date_new'];


        $ova_validate_manage_store = ova_validate_manage_store( $id, $pickup_date, $pickup_date, $passed = false, $validate = 'search' ) ;
        
        if( $ova_validate_manage_store && $ova_validate_manage_store['status'] ){
            array_push($items_id, $id);
        }

    endwhile; else :

        return $items_id;

    endif; wp_reset_postdata();
    

    if( $items_id ){

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $search_items_page = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();

        $args_product = array(
            'post_type' => 'product',
            'posts_per_page' => $search_items_page,
            'paged' => $paged,
            'post_status' => 'publish',
            'post__in' => $items_id,
            'order' => $order,
            'orderby' => $orderby
        );

        $rental_products = new WP_Query( $args_product );

        return $rental_products;
    }

    return false;
}

// Get Price Type
function ovabrw_get_price_type( $post_id ){
    return get_post_meta( $post_id, 'ovabrw_price_type', true ) ;
}

// Get Global Price of Rental Type - Day
function ovabrw_get_price_day( $post_id ){
    return wc_price( get_post_meta( $post_id, '_regular_price', true ) );
}

// Get Global Price of Rental Type - Hour
function ovabrw_get_price_hour( $post_id ){
    return wc_price( get_post_meta( $post_id, 'ovabrw_regul_price_hour', true ) );
}

// Get All Rooms
function get_all_rooms(){
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => '-1',
        'post_status'   => 'publish'
    );
    $rooms = new WP_Query( $args );
    
    return $rooms;
}

// Get all location
function ovabrw_get_locations(){
    $locations = new WP_Query(
        array(
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => '-1'
        )
    );

    return $locations;
}

// Get all Products has Product Data: Rental
if ( !function_exists('ovabrw_get_all_products') ) {
    function ovabrw_get_all_products(){

        $args = array(
            'post_type'      => 'product',
            'post_status'   => 'publish',
            'posts_per_page' => '-1',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'ovabrw_car_rental', 
                ),
            ),
        );

        $results = new WP_Query( $args );

        return $results;
    }
}

function ovabrw_get_locations_array(){
    $locations = get_posts(
        array(
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => '-1',
            'fields'    => 'id',
        )
    );

    $html = array();

    if( $locations ){ 
        foreach ($locations as $location) {

        $html[ trim( get_the_title( $location->ID  ) )] = trim( get_the_title( $location->ID ) );

        }
    }
    
    return $html;
}

function ovabrw_get_list_pickup_dropoff_loc_transport( $id_product ) {
    
    if( ! $id_product ) return [];

    $ovabrw_pickup_location = get_post_meta( $id_product, 'ovabrw_pickup_location', 'false' );
    $ovabrw_dropoff_location = get_post_meta( $id_product, 'ovabrw_dropoff_location', 'false' );


    $list_loc_pickup_dropoff = [];
    if( ! empty( $ovabrw_pickup_location ) && ! empty( $ovabrw_dropoff_location ) ) {
        foreach( $ovabrw_pickup_location as $key => $location ) {
            $list_loc_pickup_dropoff[$location][] = $ovabrw_dropoff_location[$key];
        }
    }

    return $list_loc_pickup_dropoff;
}

function ovabrw_get_locations_transport_html( $name = '', $required = 'required', $selected = '', $id_product, $type='pickup' ){
    
    $list_loc_pickup_dropoff = ovabrw_get_list_pickup_dropoff_loc_transport( $id_product );

    $html = '<select name="'.$name.'" class="ovabrw-transport '.$required.'">';
    $html .= '<option value="">'. esc_html__( 'Select Location', 'ova-brw' ).'</option>';

    if( $type == 'pickup' ) {
        
        if( $list_loc_pickup_dropoff ) {
            foreach( $list_loc_pickup_dropoff as $loc => $item_loc ) {
                $active = ( trim( $loc ) === trim( $selected ) ) ? 'selected="selected"' : '';
                $html .= '<option data-item_loc="'.esc_attr(json_encode($item_loc)).'" value="'.trim( $loc ).'" '.$active.'>'.trim( $loc ).'</option>';
            }
        }
    }
    
    $html .= '</select>';

    return $html;

}


function ovabrw_get_locations_html( $name = '', $required = 'required', $selected = '', $pid = '', $type = 'pickup' ){
    $locations = new WP_Query(
        array(
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => '-1'
        )
    );

    $show_other_loc = true;
    if( $pid ) {
        if( $type == 'pickup' ) {
            $show_other_loc = get_post_meta( $pid, 'ovabrw_show_other_location_pickup_product', true );
            $show_other_loc = ( $show_other_loc == 'no' ) ? false : true;
        } else {
            $show_other_loc = get_post_meta( $pid, 'ovabrw_show_other_location_dropoff_product', true );
            $show_other_loc = ( $show_other_loc == 'no' ) ? false : true;
        }
    }

    $html = '<select name="'.$name.'" class="'.$required.'">';
    $html .= '<option value="">'. esc_html__( 'Select Location', 'ova-brw' ).'</option>';
    
    if($locations->have_posts() ) : while ( $locations->have_posts() ) : $locations->the_post();
        global $post;
        $active = ( trim( get_the_title() ) === trim( $selected ) ) ? 'selected="selected"' : '';
        $html .= '<option value="'.get_the_title().'" '.$active.'>'.get_the_title().'</option>';
    endwhile; endif;wp_reset_postdata();
    
    if( $show_other_loc ) {

        $active = ( 'other_location' === trim( $selected ) ) ? 'selected="selected"' : '';

        $html .= '<option value="other_location" '.$active.'>'. esc_html__( 'Other Location', 'ova-brw' ).'</option>';
    }
    
    $html .= '</select>';

    return $html;

}

// Get all ids product
function ovabrw_get_all_id_product(){
   $all_ids = get_posts( array(
        'post_type' => 'product',
        'numberposts' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
    ) );

    return $all_ids;
}

function ovabrw_get_vehicle_loc_title( $id_metabox ){
    $vehicle_arr = array();
    $vehicle = new WP_Query(
        array(
            'post_type' => 'vehicle',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key'     => 'ovabrw_id_vehicle',
                    'value'   => $id_metabox,
                    'compare' => '=',
                ),
            ),
        )
    );
    if($vehicle->have_posts() ) : while ( $vehicle->have_posts() ) : $vehicle->the_post();
        $vehicle_arr['loc'] = get_post_meta( get_the_id(), 'ovabrw_id_vehicle_location', true );
        $vehicle_arr['require_loc'] = get_post_meta( get_the_id(), 'ovabrw_vehicle_require_location', true );
        $vehicle_arr['untime'] = get_post_meta( get_the_id(), 'ovabrw_id_vehicle_untime_from_day', true );
        $vehicle_arr['id_vehicle'] = get_post_meta( get_the_id(), 'ovabrw_id_vehicle', true );
        $vehicle_arr['title'] = get_the_title();
    endwhile;endif;wp_reset_postdata();

    return $vehicle_arr;
}


function ovabrw_taxonomy_dropdown( $selected, $required, $exclude_id, $slug_taxonomy, $name_taxonomy ) {
    $args = array(
        'show_option_all'    => '',
        'show_option_none'   => esc_html__( 'All ', 'ovabrw' ) . esc_html( $name_taxonomy ) ,
        'option_none_value'  => '',
        'orderby'            => 'ID',
        'order'              => 'ASC',
        'show_count'         => 0,
        'hide_empty'         => 0,
        'child_of'           => 0,
        'exclude'            => $exclude_id,
        'include'            => '',
        'echo'               => 0,
        'selected'           => $selected,
        'hierarchical'       => 1,
        'name'               => $slug_taxonomy.'_name',
        'id'                 => '',
        'class'              => 'postform '.$required,
        'depth'              => 0,
        'tab_index'          => 0,
        'taxonomy'           => $slug_taxonomy,
        'hide_if_empty'      => false,
        'value_field'        => 'slug',
    );

    return wp_dropdown_categories($args);
}



/* Select html Category Rental */
function ovabrw_cat_rental( $selected, $required, $exclude_id, $label = '' ){
    if ( ! $label ) {
        $label = esc_html__( 'Select Category', 'ova-brw' );
    }
    
    $args = array(
        'show_option_all'    => '',
        'show_option_none'   => $label,
        'option_none_value'  => '',
        'orderby'            => 'ID',
        'order'              => 'ASC',
        'show_count'         => 0,
        'hide_empty'         => 0,
        'child_of'           => 0,
        'exclude'            => $exclude_id,
        'include'            => '',
        'echo'               => 0,
        'selected'           => $selected,
        'hierarchical'       => 1,
        'name'               => 'cat',
        'id'                 => '',
        'class'              => 'postform '.$required,
        'depth'              => 0,
        'tab_index'          => 0,
        'taxonomy'           => 'product_cat',
        'hide_if_empty'      => false,
        'value_field'        => 'slug',
    );

    return wp_dropdown_categories($args);
}




// Return start time, end time when rental is period hour or time
function get_rental_info_period( $product_id, $start_date, $ovabrw_rental_type, $ovabrw_period_package_id ){
    
    $start_date = $start_date == '' ? null : $start_date;
    
    $ovabrw_unfixed = get_post_meta( $product_id, 'ovabrw_unfixed_time', true );
    if( $ovabrw_unfixed != 'yes' ) {
        $start_date = date('Y-m-d', $start_date);
    } else {
        $start_date = date('Y-m-d H:i', $start_date);
    }


    $rental_start_time = $rental_end_time = 0;
    $period_label = '';
    $period_price = 0;
    $start_date_totime = strtotime($start_date);

    $package_type = '';

    if( trim( $ovabrw_rental_type ) == trim( 'period_time' ) ){

        $ovabrw_petime_id       = get_post_meta( $product_id, 'ovabrw_petime_id', true );
        $ovabrw_petime_price    = get_post_meta( $product_id, 'ovabrw_petime_price', true );
        $ovabrw_petime_days     = get_post_meta( $product_id, 'ovabrw_petime_days', true );
        $ovabrw_petime_label    = get_post_meta( $product_id, 'ovabrw_petime_label', true );
        $ovabrw_petime_discount = get_post_meta( $product_id, 'ovabrw_petime_discount', true );
        $ovabrw_package_type    = get_post_meta( $product_id, 'ovabrw_package_type', true );

        
        $ovabrw_pehour_unfixed     = get_post_meta( $product_id, 'ovabrw_pehour_unfixed', true );

        $ovabrw_pehour_start_time   = get_post_meta( $product_id, 'ovabrw_pehour_start_time', true );
        $ovabrw_pehour_end_time     = get_post_meta( $product_id, 'ovabrw_pehour_end_time', true );

        if( $ovabrw_petime_id ){ 
            foreach ( $ovabrw_petime_id as $key => $value ) {

                if( $ovabrw_petime_id[$key] ==  $ovabrw_period_package_id ){

                    // Check pakage type
                    if( $ovabrw_package_type[$key] == 'inday' ){

                        $rental_start_time = isset( $ovabrw_pehour_start_time[$key] ) ? strtotime( $start_date.' '.$ovabrw_pehour_start_time[$key] ) : 0;
                        $rental_end_time = isset( $ovabrw_pehour_end_time[$key] ) ? strtotime( $start_date.' '.$ovabrw_pehour_end_time[$key] ) : 0;
                        

                        if( $ovabrw_unfixed == 'yes' ) {
                            $retal_pehour_unfixed = isset( $ovabrw_pehour_unfixed[$key] ) ? (float)$ovabrw_pehour_unfixed[$key] : 0;

                            $rental_start_time = $start_date_totime;
                            $rental_end_time = $start_date_totime + $retal_pehour_unfixed * 3600;
                        }


                        
                        $period_label = isset( $ovabrw_petime_label[$key] ) ? $ovabrw_petime_label[$key] : '';
                        $period_price = isset( $ovabrw_petime_price[$key] ) ? floatval( $ovabrw_petime_price[$key] ) : 0;
                        $package_type = 'inday';

                        if( isset($ovabrw_petime_discount[$key]) && $ovabrw_petime_discount[$key]['price'] ){
                            foreach ( $ovabrw_petime_discount[$key]['price'] as $k => $v) {
                                // Start Time Discount < Rental Time < End Time Discount

                                $start_time_dis = strtotime( $ovabrw_petime_discount[$key]['start_time'][$k] );
                                $end_time_dis = strtotime( $ovabrw_petime_discount[$key]['end_time'][$k] );

                                if( $start_time_dis <= $start_date_totime && $start_date_totime <= $end_time_dis ){
                                    $period_price = floatval( $ovabrw_petime_discount[$key]['price'][$k] );
                                    break;
                                }
                            }    
                        }

                    }else if( $ovabrw_package_type[$key] == 'other' ){

                        if ( $ovabrw_unfixed == 'yes' ) {
                            $start_date_date = date( 'Y-m-d H:i', $start_date_totime ) ;
                        } else {
                            $start_date_date = date( 'Y-m-d', $start_date_totime ) ;
                        }
                        
                        $start_date_totime = strtotime( $start_date_date );

                        $rental_start_time = $start_date_totime;



                        $rental_end_time = $start_date_totime + intval( $ovabrw_petime_days[$key] )*24*60*60;
                        $period_label = isset( $ovabrw_petime_label[$key] ) ? $ovabrw_petime_label[$key] : '';
                        $period_price = isset( $ovabrw_petime_price[$key] ) ? floatval( $ovabrw_petime_price[$key] ) : 0;
                        $package_type = 'other';




                        if( isset($ovabrw_petime_discount[$key]) && $ovabrw_petime_discount[$key]['price'] ){
                            foreach ( $ovabrw_petime_discount[$key]['price'] as $k => $v) {
                                // Start Time Discount < Rental Time < End Time Discount

                                $start_time_dis = strtotime( $ovabrw_petime_discount[$key]['start_time'][$k] );
                                $end_time_dis = strtotime( $ovabrw_petime_discount[$key]['end_time'][$k] );

                                if( $start_time_dis <= $start_date_totime && $start_date_totime <= $end_time_dis ){
                                    $period_price = floatval( $ovabrw_petime_discount[$key]['price'][$k] );
                                    break;
                                }
                            }    
                        }

                    }

                    break;
                }
            }
        }

    }

    return array( 'start_time' => $rental_start_time, 'end_time' => $rental_end_time, 'period_label' => $period_label, 'period_price' => $period_price, 'package_type' => $package_type );
}

// Get all order has pickup date larger current time
function ovabrw_get_orders_feature(){

    global $wpdb;

    $order_status = brw_list_order_status();
    $order_id = $wpdb->get_col("
        SELECT DISTINCT order_items.order_id
        FROM {$wpdb->prefix}woocommerce_order_items as order_items
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        WHERE posts.post_type = 'shop_order'
        AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
    ");

    return $order_id;

}

/**
 * Get price include tax
 */
function ovabrw_get_price_include_tax( $product_id, $product_price ) {

    $display_price_cart = get_option( 'woocommerce_tax_display_cart', 'incl' );

    if ( wc_tax_enabled() && wc_prices_include_tax() && $product_price && $display_price_cart === 'excl' ) {

        $product_data   = wc_get_product($product_id);
        $tax_rates_data = WC_Tax::get_rates( $product_data->get_tax_class() );
        $rate_data      = reset($tax_rates_data);

        if ( $rate_data && isset( $rate_data['rate'] ) ) {

            $rate           = $rate_data['rate'];
            $tax_price      = $product_price - ( $product_price / ( ( $rate / 100 ) + 1 ) );
            $product_price  = $product_price - round( $tax_price, wc_get_price_decimals() );
        }
    }
    
    return apply_filters( 'ovabrw_get_price_include_tax', $product_price );;
}

/**
 * Get html Resources
 */
function ovabrw_get_html_resources( $product_id, $resources = [], $adults_quantity, $childrens_quantity ) {

    $html = '';

    if ( get_option( 'ova_brw_booking_form_show_extra', 'no' ) == 'no' ) {
        return $html;
    }

    if ( !empty( $resources ) && is_array( $resources ) ) {
        $rs_ids             = get_post_meta( $product_id, 'ovabrw_rs_id', true );
        $rs_names           = get_post_meta( $product_id, 'ovabrw_rs_name', true );
        $rs_adult_price     = get_post_meta( $product_id, 'ovabrw_rs_adult_price', true );
        $rs_children_price  = get_post_meta( $product_id, 'ovabrw_rs_children_price', true );
        $rs_duration_type   = get_post_meta( $product_id, 'ovabrw_rs_duration_type', true );

        foreach ( $resources as $rs_id => $rs_name ) {
            $rs_price = 0;

            $key = array_search( $rs_id, $rs_ids );

            if ( !is_bool( $key ) ) {
                $adult_price = 0;
                if ( ovabrw_check_array( $rs_adult_price, $key ) ) {
                    $adult_price = $rs_adult_price[$key];
                }

                $children_price = 0;
                if ( ovabrw_check_array( $rs_children_price, $key ) ) {
                    $children_price = $rs_children_price[$key];
                }

                $duration_type = 'person';
                if ( ovabrw_check_array( $rs_duration_type, $key ) ) {
                    $duration_type = $rs_duration_type[$key];
                }

                if ( 'person' === $duration_type ) {
                    $rs_price += $adult_price * $adults_quantity + $children_price * $childrens_quantity;
                } else {
                    $rs_price += $adult_price + $children_price;
                }

                $html .=  '<li>' . $rs_name . esc_html__( ': ', 'ova-brw' ) . wc_price( $rs_price ) . '</li>';
            }
        }
    }

    return $html;
}

/**
 * Get html Services
 */
function ovabrw_get_html_services( $product_id, $services = [], $adults_quantity, $childrens_quantity ) {

    $html = '';

    if ( get_option( 'ova_brw_booking_form_show_extra', 'no' ) == 'no' ) {
        return $html;
    }

    if ( $services && is_array( $services ) ) {
        $service_ids            = get_post_meta( $product_id, 'ovabrw_service_id', true );
        $service_name           = get_post_meta( $product_id, 'ovabrw_service_name', true );
        $service_adult_price    = get_post_meta( $product_id, 'ovabrw_service_adult_price', true );
        $service_children_price = get_post_meta( $product_id, 'ovabrw_service_children_price', true );
        $service_duration_type  = get_post_meta( $product_id, 'ovabrw_service_duration_type', true );

        foreach( $services as $ovabrw_s_id ) {
            $service_price = 0;

            if ( $ovabrw_s_id && $service_ids && is_array( $service_ids ) ) {
                foreach( $service_ids as $key_id => $service_id_arr ) {
                    $key = array_search( $ovabrw_s_id, $service_id_arr );

                    if ( !is_bool( $key ) ) {
                        $adult_price = 0;
                        if ( ovabrw_check_array( $service_adult_price, $key_id ) ) {
                            if ( ovabrw_check_array( $service_adult_price[$key_id], $key ) ) {
                                $adult_price = $service_adult_price[$key_id][$key];
                            }
                        }

                        $children_price = 0;
                        if ( ovabrw_check_array( $service_children_price, $key_id ) ) {
                            if ( ovabrw_check_array( $service_children_price[$key_id], $key ) ) {
                                $children_price = $service_children_price[$key_id][$key];
                            }
                        }

                        $duration_type = 'person';
                        if ( ovabrw_check_array( $service_duration_type, $key_id ) ) {
                            if ( ovabrw_check_array( $service_duration_type[$key_id], $key ) ) {
                                $duration_type = $service_duration_type[$key_id][$key];
                            }
                        }

                        if ( 'person' === $duration_type ) {
                            $service_price += $adult_price * $adults_quantity + $children_price * $childrens_quantity;
                        } else {
                            $service_price += $adult_price + $children_price;
                        }

                        $html .= '<li>' . $service_name[$key_id][$key] . esc_html__( ': ', 'ova-brw' ) . wc_price( $service_price ) . '</li>';
                    }
                }
            }
        }
    }

    return $html;
}

/**
 * Get html Resources + Services
 */
function ovabrw_get_html_extra( $resource_html= '', $service_html = '' ) {

    $html = '';
    if ( !empty( $resource_html ) || !empty( $service_html ) ) {
        $html .= '<ul class="ovabrw_extra_item">';
        $html .= $service_html;
        $html .= $resource_html;
        $html .= '</ul>';
    }

    return apply_filters( 'ovabrw_ft_get_html_extra', $html );
}

/**
 * Get html total pay when wc_tax_enabled()
 */
function ovabrw_get_html_total_pay( $total, $cart_item ) {

    $html = '';

    if ( ! $total || ! $cart_item ) {
        return $html;
    }

    $product_id = $cart_item['product_id'];
    $product    = wc_get_product( $product_id );
    $tax_rates  = WC_Tax::get_rates( $product->get_tax_class() );

    if ( wc_tax_enabled() ) {

        if ( wc_prices_include_tax() ) {

            if ( ! WC()->cart->display_prices_including_tax() ) {
                $incl_tax = WC_Tax::calc_inclusive_tax( $total, $tax_rates );
                $total   -= array_sum( $incl_tax );
            }
        } else {

            if ( WC()->cart->display_prices_including_tax() ) {
                $excl_tax = WC_Tax::calc_exclusive_tax( $total, $tax_rates );
                $total   += array_sum( $excl_tax ); 
            }
        }
    }

    $html .= '<br/><small>' . sprintf( __( '%s payable in total', 'ova-brw' ), wc_price( $total ) ) . '</small>';

    return apply_filters( 'ovabrw_ft_get_html_total_pay', $html );
}

/**
 * Get price when wc_tax_enabled()
 */
function ovabrw_get_price_tax( $price, $cart_item ) {

    if ( ! $price || ! $cart_item ) {
        return 0;
    }

    $product_id = $cart_item['product_id'];
    $product    = wc_get_product( $product_id );
    $tax_rates  = WC_Tax::get_rates( $product->get_tax_class() );

    if ( wc_tax_enabled() ) {

        if ( wc_prices_include_tax() ) {

            if ( ! WC()->cart->display_prices_including_tax() ) {
                $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                $price   -= round( array_sum( $incl_tax ), wc_get_price_decimals() ); 
            }

        } else {

            if ( WC()->cart->display_prices_including_tax() ) {
                $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                $price   += round( array_sum( $excl_tax ), wc_get_price_decimals() ); 
            }
        }
    }

    return apply_filters( 'ovabrw_ft_get_price_tax', $price );
}

/**
 * Get taxes when wc_tax_enabled()
 */
function ovabrw_get_taxes_by_price( $price, $product_id, $prices_include_tax ) {

    $taxes = 0;

    if ( ! $price || ! $product_id || ! $prices_include_tax ) {
        return $taxes;
    }

    $product    = wc_get_product( $product_id );
    $tax_rates  = WC_Tax::get_rates( $product->get_tax_class() );

    if ( wc_tax_enabled() ) {

        if ( $prices_include_tax == 'yes' ) {

            $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
            $taxes    = round( array_sum( $incl_tax ), wc_get_price_decimals() );

        } else {

            $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
            $taxes    = round( array_sum( $excl_tax ), wc_get_price_decimals() );
        }
    }

    return apply_filters( 'ovabrw_ft_get_taxes_by_price', $taxes );
}

/**
 * Get tax_amount by price and tax rates
 */
function ovabrw_get_tax_amount_by_tax_rates( $price, $tax_rates, $prices_include_tax ) {

    if ( ! $price || ! $tax_rates || ! $prices_include_tax ) {
        return 0;
    }

    if ( wc_tax_enabled() ) {

        if ( $prices_include_tax == 'yes' ) {

            $tax_amount = round( $price - ( $price / ( ( $tax_rates / 100 ) + 1 ) ), wc_get_price_decimals() );

        } else {

            $tax_amount = round( $price * ( $tax_rates / 100 ), wc_get_price_decimals() );
        }
    }

    return apply_filters( 'ovabrw_ft_get_tax_amount_by_tax_rates', $tax_amount );
}

/**
 * Get html resources when created order in admin
 */
function ovabrw_get_html_resources_order( $product_id ) {
    $html       = '';
    $ovabrw_rs_id   = get_post_meta( $product_id, 'ovabrw_rs_id', true );

    if ( $ovabrw_rs_id ) {
        $ovabrw_rs_name             = get_post_meta( $product_id, 'ovabrw_rs_name', true );
        $ovabrw_rs_adult_price      = get_post_meta( $product_id, 'ovabrw_rs_adult_price', true );
        $ovabrw_rs_children_price   = get_post_meta( $product_id, 'ovabrw_rs_children_price', true );
        $ovabrw_rs_duration_type    = get_post_meta( $product_id, 'ovabrw_rs_duration_type', true );

        $html .= '<div class="resources_order">';

        foreach( $ovabrw_rs_id as $k => $rs_id ) {
            if ( $rs_id ) {
                $rs_name            = isset( $ovabrw_rs_name[$k] ) ? $ovabrw_rs_name[$k] : '';
                $rs_adult_price     = isset( $ovabrw_rs_adult_price[$k] ) ? $ovabrw_rs_adult_price[$k] : 0;
                $rs_children_price  = isset( $ovabrw_rs_children_price[$k] ) ? $ovabrw_rs_children_price[$k] : 0;
                $rs_duration_type   = isset( $ovabrw_rs_duration_type[$k] ) ? $ovabrw_rs_duration_type[$k] : 'person';

                $html .=    '<div class="item"><div class="left">';
                $html .=    '<input 
                                type="checkbox" 
                                id="ovabrw_resource_checkboxs_bk_'. esc_html( $k ) .'" 
                                data-resource_key="'. $rs_id .'" 
                                name="ovabrw_resource_checkboxs['. $product_id .'][]" 
                                value="'. esc_attr( $rs_name ) .'" 
                                class="ovabrw_resource_checkboxs" />';
                $html .=    '<label for="ovabrw_resource_checkboxs_bk_'. esc_html( $k ) .'">'. $rs_name .'</label>';
                $html .=    '</div>';

                $html .=    '<div class="right">';

                // Adult price
                $html .=    '<div class="adult-price">';
                $html .=    '<label class="adult-label">'. esc_html__( 'Adult: ', 'ova-brw' ) .'</label>';
                $html .=    '<span class="price">'. wc_price( $rs_adult_price ) .'</span>';
                $html .=    '<span class="duration">';

                if ( 'person' === $rs_duration_type ) {
                    $html .= esc_html__( '/per person', 'ova-brw' );
                } else {
                    $html .= esc_html__( '/total', 'ova-brw' );
                }

                $html .=    '</span>';
                $html .=    '</div>';

                // Children price
                $html .=    '<div class="children-price">';
                $html .=    '<label class="children-label">'. esc_html__( 'Children: ', 'ova-brw' ) .'</label>';
                $html .=    '<span class="price">'. wc_price( $rs_children_price ) .'</span>';
                $html .=    '<span class="duration">';

                if ( 'person' === $rs_duration_type ) {
                    $html .= esc_html__( '/per person', 'ova-brw' );
                } else {
                    $html .= esc_html__( '/total', 'ova-brw' );
                }

                $html .=    '</span>';
                $html .=    '</div>';

                $html .=    '</div></div>';
            }
        }
        $html .= '</div>';
    }

    return $html;
}

/**
 * Get html services when created order in admin
 */
function ovabrw_get_html_services_order( $product_id ) {
    $html      = '';
    $services  = get_post_meta( $product_id, 'ovabrw_label_service', true );

    if ( $services ) {
        $service_id                 = get_post_meta( $product_id, 'ovabrw_service_id', true );
        $service_required           = get_post_meta( $product_id, 'ovabrw_service_required', true );
        $service_name               = get_post_meta( $product_id, 'ovabrw_service_name', true );
        $service_adult_price        = get_post_meta( $product_id, 'ovabrw_service_adult_price', true );
        $service_children_price     = get_post_meta( $product_id, 'ovabrw_service_children_price', true );
        $service_duration_type      = get_post_meta( $product_id, 'ovabrw_service_duration_type', true );

        $html .= '<div class="services_order">';

        for ( $i = 0; $i < count( $services ); $i++ ) {
            $sv_ids = isset( $service_id[$i] ) && $service_id[$i] ? $service_id[$i] : '';
            $service_required = isset( $service_required[$i] ) ? $service_required[$i] : '';

            if ( 'yes' === $service_required ) {
                $requires = ' class="required" data-error="'.sprintf( esc_html__( '%s is required.', 'ova-brw' ), $services[$i] ).'"';
            } else {
                $requires = '';
            }

            if ( $sv_ids && is_array( $sv_ids ) ) {
                $html .= '<div class="item">';
                $html .= '<select name="ovabrw_service['.$product_id.'][]"'. $requires .'>';
                $html .= '<option value="">'. sprintf( esc_html__( 'Select %s', 'ova-brw' ), $services[$i] ) .'</option>';

                foreach( $sv_ids as $key => $value ) {
                    $sv_name            = isset( $service_name[$i][$key] ) ? $service_name[$i][$key] : '';
                    $sv_adult_price     = isset( $service_adult_price[$i][$key] ) ? $service_adult_price[$i][$key] : 0;
                    $sv_children_price  = isset( $service_children_price[$i][$key] ) ? $service_children_price[$i][$key] : 0;
                    $sv_duration_type   = isset( $service_duration_type[$i][$key] ) ? $service_duration_type[$i][$key] : 'person';
                    $html_duration      = esc_html__( '/total', 'ova-brw' );
                    if ( 'person' === $sv_duration_type ) {
                        $html_duration = esc_html__( '/per person', 'ova-brw' );
                    }

                    $html_price = sprintf( esc_html__( ' (Adult: %s%s - Children:%s%s)', 'ova-brw' ), wc_price($sv_adult_price), $html_duration, wc_price($sv_children_price), $html_duration );
                    
                    $html .= '<option value="'. esc_attr( $value ) .'">'. esc_html( $sv_name ) . $html_price .'</option>';
                }

                $html .= '</select>';
                $html .= '</div>';
            }
        }
        $html .=    '</div>';
    }

    return $html;
}

/**
 *  HTML Dropdown Attributes
 */
function ovabrw_dropdown_attributes( $label = '' ) {
    $args       = array(); 
    $html       = $html_attr_value = '';
    $attributes = wc_get_attribute_taxonomies();

    if ( ! $label ) {
        $label = esc_html__( 'Select Attribute', 'ova-brw' );
    }

    if ( ! empty( $attributes ) ) {
        $html .= '<select name="ovabrw_attribute" class="ovabrw_attribute"><option value="">'. $label .'</option>';

        foreach ( $attributes as $obj_attr ) {
            if ( taxonomy_exists( wc_attribute_taxonomy_name( $obj_attr->attribute_name ) ) ) {
                $html .= "<option value='". $obj_attr->attribute_name ."'>". $obj_attr->attribute_label ."</option>";

                $term_attributes = get_terms( wc_attribute_taxonomy_name( $obj_attr->attribute_name ), 'orderby=name&hide_empty=0' );
                if ( ! empty( $term_attributes ) ) {

                    $html_attr_value .= '<div class="label_search s_field ovabrw-value-attribute" id="'. $obj_attr->attribute_name .'">
                                            <select name="ovabrw_attribute_value" >';

                    foreach ( $term_attributes as $obj_attr_value ) {
                        $html_attr_value .= '<option value="'.$obj_attr_value->slug.'">'.$obj_attr_value->name.'</option>';
                    }

                    $html_attr_value .= '</select></div>';
                }
            }
        }
        $html .= '</select>';
    }
    $args['html_attr']         = $html;
    $args['html_attr_value']   = $html_attr_value;

    return $args;
}

/**
 *  HTML Destinantion Dropdown
 */
function ovabrw_destination_dropdown( $placeholder, $id_selected ) {

    $html     = '';
    $args_cat = array(
       'taxonomy' => 'cat_destination',
       'orderby' => 'name',
       'order'   => 'ASC'
    );

    $cats = get_categories($args_cat);

    if ( ! $placeholder ) {
        $placeholder = esc_html__( 'What are you going', 'ova-brw' );
    }

    if ( ! empty( $cats ) ) {
        $html .= '<select id="brw-destinations-select-box" name="ovabrw_destination"><option value="all">'. $placeholder .'</option>';

        foreach ( $cats as $cat ) {

            $cat_id = $cat->term_id;
            $html .= '<optgroup label="'. $cat->name. '">';

            $args_destination  = array( 
                'post_type' => 'destination',
                'posts_per_page' => -1,
                'order' => 'ASC',
                'orderby' => 'title',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'cat_destination',
                        'field'    => 'term_id',
                        'terms'    => $cat_id,
                    ),
                ),
            ); 

            $destinations = new WP_Query( $args_destination );

            if ( $destinations->have_posts()) : while ( $destinations->have_posts()) : $destinations->the_post(); ?>
                <?php
                    global $post;
                    $id    = get_the_id();
                    $title = get_the_title();
                    if( $id == $id_selected ) {
                        $html .= '<option value="'.$id.'" selected="selected">'.$title.'</option>';
                    } else {
                        $html .= '<option value="'.$id.'">'.$title.'</option>';
                    } 
                ?>
            <?php endwhile; endif; wp_reset_postdata();  $html .= '</optgroup>';
        }

        $html .= '</select>';
    }

    if ( empty( $cats ) ) {
        $html .= '<select id="brw-destinations-select-box" name="ovabrw_destination"><option value="all">'. $placeholder .'</option>';

        $args_destination  = array( 
            'post_type' => 'destination',
            'posts_per_page' => -1,
            'order' => 'ASC',
            'orderby' => 'title'
        ); 

        $destinations = new WP_Query( $args_destination );

        if ( $destinations->have_posts()) : while ( $destinations->have_posts()) : $destinations->the_post(); ?>
            <?php
                global $post;
                $id    = get_the_id();
                $title = get_the_title();

                if ( $id == $id_selected ) {
                    $html .= '<option value="'.$id.'" selected="selected">'.$title.'</option>';
                } else {
                    $html .= '<option value="'.$id.'">'.$title.'</option>';
                }
            ?>
        <?php endwhile; endif; wp_reset_postdata();

        $html .= '</select>';
    }

    return $html;
}


/**
 *  Get html taxonomy search ajax
 */
function ovabrw_search_taxonomy_dropdown( $slug_taxonomy, $name_taxonomy, $slug_value_selected ) {
    $args = array(
        'show_option_all'    => '',
        'show_option_none'   => esc_html( $name_taxonomy ) ,
        'option_none_value'  => 'all',
        'orderby'            => 'ID',
        'order'              => 'ASC',
        'show_count'         => 0,
        'hide_empty'         => 0,
        'child_of'           => 0,
        'exclude'            => '',
        'include'            => '',
        'echo'               => 0,
        'selected'           => $slug_value_selected,
        'hierarchical'       => 1,
        'name'               => $slug_taxonomy.'_name',
        'id'                 => 'brw_custom_taxonomy_dropdown',
        'class'              => 'brw_custom_taxonomy_dropdown',
        'depth'              => 0,
        'tab_index'          => 0,
        'taxonomy'           => $slug_taxonomy,
        'hide_if_empty'      => false,
        'value_field'        => 'slug',
    );

    return wp_dropdown_categories($args);
}

/**
 *  Get product search ajax
 */
function ovabrw_search_products( $data ) {
    $number     = $data['posts_per_page']   ? $data['posts_per_page']   : 12;
    $orderby    = $data['orderby']          ? $data['orderby']          : 'date';
    $order      = $data['order']            ? $data['order']            : 'DESC';

    $args = array(
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'posts_per_page'    => $number,
        'orderby'           => $orderby,
        'order'             => $order,
    );

    $products = new WP_Query( $args );

    return $products;
}


/**
 * Pagination ajax
 */
function ovabrw_pagination_ajax( $total, $limit, $current  ) {

    $html   = '';
    $pages  = ceil( $total / $limit );

    if ( $pages > 1 ) {
        $html .= '<ul>';

        if ( $current > 1 ) {
            $html .=    '<li><span data-paged="'. ( $current - 1 ) .'" class="prev page-numbers" >'
                            . '<i class="icomoon icomoon-angle-left"></i>' . esc_html__( 'Prev', 'ova-brw' ) .
                        '</span></li>';
        }

        for ( $i = 1; $i <= $pages; $i++ ) {
            if ( $current == $i ) {
                $html .=    '<li><span data-paged="'. $i .'" class="prev page-numbers current" >'. esc_html( $i ) .'</span></li>';
            } else {
                $html .=    '<li><span data-paged="'. $i .'" class="prev page-numbers" >'. esc_html( $i ) .'</span></li>';
            }
        }

        if ( $current < $pages ) {
            $html .=    '<li><span data-paged="'. ( $current + 1 ) .'" class="next page-numbers" >'
                            . esc_html__( 'Next', 'ova-brw' ) . '<i class="icomoon icomoon-angle-right"></i>' .
                        '</span></li>';
        }
    }

    return $html;
}

/**
 * Recursive array replace \\
 */
if( !function_exists('recursive_array_replace') ){
    function recursive_array_replace( $find, $replace, $array ) {
        if ( !is_array( $array ) ) {
            return str_replace( $find, $replace, $array );
        }

        foreach ( $array as $key => $value ) {
            $array[$key] = recursive_array_replace( $find, $replace, $value );
        }

        return $array;
    }
}

/**
 * Get destinations
 */
if( !function_exists('ovabrw_get_destinations') ){
    function ovabrw_get_destinations() {
        
        $results = array(
            '' => esc_html__( 'All Destination', 'ova-brw' ),
        );

        $args = array(
            'post_type'         => 'destination',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'orderby'           => 'ID',
            'order'             => 'DESC',
            'fields'            => 'ids'
        );

        $destinations = get_posts( $args );

        if ( $destinations && is_array( $destinations ) ) {
            foreach( $destinations as $destination_id ) {
                $destination_title = get_the_title( $destination_id );
                $results[$destination_id] = $destination_title;
            }
        }

        return $results;
    }
}

if( !function_exists('ovabrw_get_filtered_price') ){
    function ovabrw_get_filtered_price() {
        global $wpdb;

        $sql = "
            SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
            FROM {$wpdb->wc_product_meta_lookup}
            WHERE product_id IN (
                SELECT ID FROM {$wpdb->posts} 
                WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', array( 'product' ) ) ) . "')
                AND {$wpdb->posts}.post_status = 'publish'
                " . ')';
                
        return $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
    }
}

/**
 * Get exclude ids not available pickup date
 */
if( !function_exists('ovabrw_get_exclude_ids') ){
    function ovabrw_get_exclude_ids( $pickup_date ) {

        $products    = ovabrw_get_all_products();
 
        $exclude_ids = array();

        if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();

            $product_id = get_the_id();
            $day        = get_post_meta( $product_id, 'ovabrw_number_days', true );

            $pickoff_date = '';
            if ( $pickup_date ) {
                $pickoff_date = $pickup_date + $day*86400;
            }

            // Check product in order
            $store_quantity = ovabrw_quantity_available_in_order( $product_id, $pickup_date, $pickoff_date );

            // Check product in cart
            $cart_quantity  = ovabrw_quantity_available_in_cart( $product_id, 'cart', $pickup_date, $pickoff_date );

            // Get array quantity available
            $data_quantity  = ovabrw_get_quantity_available( $product_id, $store_quantity, $cart_quantity, 1, false, 'cart' );

            // Check Unavailable
            $unavailable = ovabrw_check_unavailable( $product_id, $pickup_date, $pickoff_date );

            if ( $data_quantity ) {
                $qty_available = $data_quantity['quantity_available'];

                if ( $unavailable ) {
                    $qty_available = 0;
                }

                if ( $qty_available <= 0 || is_null( $qty_available ) ) {
                    array_push( $exclude_ids, $product_id );
                }
            }

            // Check time in Fixed Time
            $in_fixed_time = ovabrw_check_fixed_time( $product_id, $pickup_date );

            if ( ! $in_fixed_time && ! in_array( $product_id, $exclude_ids ) ) {
                array_push( $exclude_ids, $product_id );
            }
            
        endwhile; endif; wp_reset_postdata();

        return $exclude_ids;
    }
}

if ( ! function_exists( 'ovabrw_check_fixed_time' ) ) {
    function ovabrw_check_fixed_time( $product_id, $pickup_date ) {
        $flag = false;
        $fixed_time_check_in    = get_post_meta( $product_id, 'ovabrw_fixed_time_check_in', true );
        $fixed_time_check_out   = get_post_meta( $product_id, 'ovabrw_fixed_time_check_out', true );

        if ( ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) ) {
            foreach( $fixed_time_check_in as $k => $check_in ) {
                if ( isset( $fixed_time_check_out[$k] ) && $fixed_time_check_out[$k] ) {
                    if ( strtotime( $check_in ) <= $pickup_date && $pickup_date <= strtotime( $fixed_time_check_out[$k] ) ) {
                        $flag = true;
                        break;
                    }
                }
            }
        } else {
            $flag = true;
        }

        return $flag;
    }
}