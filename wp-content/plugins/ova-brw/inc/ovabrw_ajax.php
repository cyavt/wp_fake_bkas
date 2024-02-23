<?php
if ( !defined( 'ABSPATH' ) ) exit();

add_action( 'wp_ajax_ovabrw_load_data_product_create_order', 'ovabrw_load_data_product_create_order' );
add_action( 'wp_ajax_nopriv_ovabrw_load_data_product_create_order', 'ovabrw_load_data_product_create_order' );
function ovabrw_load_data_product_create_order() {
	$product_id 	= isset($_POST['product_id']) ? absint( sanitize_text_field( $_POST['product_id'] ) ) : '';

	// Price
	$adult_price 	= get_post_meta( $product_id, '_regular_price', true );
	$children_price = get_post_meta( $product_id, 'ovabrw_children_price', true );
	
	// Guests
	$adults_max		= get_post_meta( $product_id, 'ovabrw_adults_max', true );
	$adults_min 	= get_post_meta( $product_id, 'ovabrw_adults_min', true );
	$childrens_max 	= get_post_meta( $product_id, 'ovabrw_childrens_max', true );
	$childrens_min 	= get_post_meta( $product_id, 'ovabrw_childrens_min', true );

	// Days
	$days = get_post_meta( $product_id, 'ovabrw_number_days', true );

	// Amount of insurance
	$amount_insurance = get_post_meta( $product_id, 'ovabrw_amount_insurance', true );

	// Number of Tours
	$stock_quantity = get_post_meta( $product_id, 'ovabrw_stock_quantity', true );

	// Get html resources
	$html_resources = ovabrw_get_html_resources_order( $product_id );

	// Get html services
	$html_services = ovabrw_get_html_services_order( $product_id );

	$data = [
		'adult_price' 		=> $adult_price ? $adult_price : 0,
		'children_price' 	=> $children_price ? $children_price: 0,
		'adults_max' 		=> $adults_max ? $adults_max : 1,
		'adults_min' 		=> $adults_min ? $adults_min : 1,
		'childrens_max' 	=> $childrens_max ? $childrens_max : 0,
		'childrens_min' 	=> $childrens_min ? $childrens_min: 0,
		'days' 				=> $days ? $days : 1,
		'amount_insurance' 	=> $amount_insurance ? $amount_insurance : 0,
		'stock_quantity' 	=> $stock_quantity ? $stock_quantity : 1,
		'html_resources' 	=> $html_resources,
		'html_services' 	=> $html_services,
	];

	echo json_encode( $data );

	wp_die();
}

add_action( 'wp_ajax_ovabrw_create_order_get_total', 'ovabrw_create_order_get_total' );
add_action( 'wp_ajax_nopriv_ovabrw_create_order_get_total', 'ovabrw_create_order_get_total' );
function ovabrw_create_order_get_total() {
	$product_id 	= isset($_POST['product_id']) ? absint( sanitize_text_field( $_POST['product_id'] ) ) : '';
	$pickup_date 	= isset($_POST['start_date']) ? trim( sanitize_text_field( $_POST['start_date'] ) ) : '';
	$dropoff_date 	= isset($_POST['end_date']) ? trim( sanitize_text_field( $_POST['end_date'] ) ) : '';
	$deposit_amount = isset($_POST['deposit_amount']) ? floatval( sanitize_text_field( $_POST['deposit_amount'] ) ) : 0;
	$resources 		= isset($_POST['resources']) ? recursive_array_replace( '\\', '', $_POST['resources'] )  : '';
	$services 		= isset($_POST['services']) ? recursive_array_replace( '\\', '', $_POST['services'] )  : '';

	// Get new date
	$date_format    = ovabrw_get_date_format();
	$min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
    $min_childrens  = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
    $adults 		= isset($_POST['adults']) ? absint( sanitize_text_field( $_POST['adults'] ) ) : absint( $min_adults );
	$childrens 		= isset($_POST['childrens']) ? absint( sanitize_text_field( $_POST['childrens'] ) ) : absint( $min_childrens );

	$cart_item['product_id'] 		= $product_id;
	$cart_item['ovabrw_adults']		= $adults;
	$cart_item['ovabrw_childrens']	= $childrens;
	$cart_item['ovabrw_quantity'] 	= 1;
	$cart_item['ovabrw_resources'] 	= (array)json_decode( $resources );
	$cart_item['ovabrw_services'] 	= (array)json_decode( $services );

	// Total
	$data_total = array(
		'error' => '',
		'remaining_amount' => 0,
	);
	$data_total['adults_price'] 	= 0;
	$data_total['childrens_price'] 	= 0;

	// Price Per Guests
	$price_per_guests = ovabrw_price_per_guests( $product_id, strtotime( $pickup_date ), $adults, $childrens );
	if ( ovabrw_check_array( $price_per_guests, 'adults_price' ) ) {
		$data_total['adults_price'] = $price_per_guests['adults_price'];
	}

	if ( ovabrw_check_array( $price_per_guests, 'childrens_price' ) ) {
		$data_total['childrens_price'] = $price_per_guests['childrens_price'];
	}

	// Amount Insurance
	$amount_insurance = floatval(get_post_meta( $product_id, 'ovabrw_amount_insurance', true ));
	$data_total['amount_insurance'] = $amount_insurance * ( $adults + $childrens );

	// Line Total
	$line_total = get_price_by_guests( $product_id, strtotime( $pickup_date ), strtotime( $dropoff_date ), $cart_item );

	$data_total['line_total'] = $line_total;

    $data_total = apply_filters( 'ovabrw_ft_ajax_create_order_data_total', $data_total, $product_id );

    // Deposit
    if ( $deposit_amount ) {
    	if ( $deposit_amount <= $line_total ) {
    		$remaining_amount = $line_total - $deposit_amount;
	    	$data_total['remaining_amount'] = $remaining_amount;
    	} else {
    		$data_total['line_total'] = 0;
    		$data_total['remaining_amount'] = 0;
    		$data_total['error'] = esc_html__( 'Deposit amount is greater than total.', 'ova-brw' );
    	}
    }

	echo json_encode( $data_total );

	wp_die();
}

add_action( 'wp_ajax_ovabrw_calculate_total', 'ovabrw_calculate_total', 10, 0 );
add_action( 'wp_ajax_nopriv_ovabrw_calculate_total', 'ovabrw_calculate_total', 10, 0 );
function ovabrw_calculate_total() {
	$product_id 	= isset($_POST['product_id']) ? sanitize_text_field( $_POST['product_id'] ) : '';
	$pickup_date 	= isset($_POST['pickup_date']) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
	$dropoff_date 	= isset($_POST['dropoff_date']) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
	$quantity 		= isset($_POST['quantity']) ? absint( sanitize_text_field( $_POST['quantity'] ) ) : 1;
	$deposit 		= isset($_POST['deposit']) ? sanitize_text_field( $_POST['deposit'] ) : '';
	$resources 		= isset($_POST['resources']) ? recursive_array_replace( '\\', '', $_POST['resources'] )  : '';
	$services 		= isset($_POST['services']) ? recursive_array_replace( '\\', '', $_POST['services'] )  : '';

	// Get new date
	$date_format    = ovabrw_get_date_format();
	$min_adults     = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
    $min_childrens  = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
    $adults 		= isset($_POST['adults']) ? absint( sanitize_text_field( $_POST['adults'] ) ) : absint( $min_adults );
	$childrens 		= isset($_POST['childrens']) ? absint( sanitize_text_field( $_POST['childrens'] ) ) : absint( $min_childrens );

	$cart_item['product_id'] 		= $product_id;
	$cart_item['ovabrw_adults']		= $adults;
	$cart_item['ovabrw_childrens']	= $childrens;
	$cart_item['ovabrw_quantity'] 	= $quantity;
	$cart_item['ovabrw_resources'] 	= (array)json_decode( $resources );
	$cart_item['ovabrw_services'] 	= (array)json_decode( $services );
	$cart_item['ova_type_deposit'] 	= $deposit;

	// Total
	$data_total = array();
	$data_total['adults_price'] 	= '';
	$data_total['childrens_price'] 	= '';

	// Price Per Guests
	$price_per_guests = ovabrw_price_per_guests( $product_id, strtotime( $pickup_date ), $adults, $childrens );
	if ( ovabrw_check_array( $price_per_guests, 'adults_price' ) ) {
		$data_total['adults_price'] = wc_price( $price_per_guests['adults_price'] );
	}

	if ( ovabrw_check_array( $price_per_guests, 'childrens_price' ) ) {
		$data_total['childrens_price'] = wc_price( $price_per_guests['childrens_price'] );
	}

	// Amount Insurance
	$amount_insurance = floatval(get_post_meta( $product_id, 'ovabrw_amount_insurance', true ));
	$data_total['amount_insurance'] = $amount_insurance * ( $adults + $childrens ) * $quantity;

	// Line Total
	$line_total = get_price_by_guests( $product_id, strtotime( $pickup_date ), strtotime( $dropoff_date ), $cart_item );

	$data_total['line_total'] = $line_total;

	// Check product in order
	$store_quantity = ovabrw_quantity_available_in_order( $product_id, strtotime( $pickup_date ), strtotime( $dropoff_date ) );

	// Check product in cart
	$cart_quantity  = ovabrw_quantity_available_in_cart( $product_id, 'cart', strtotime( $pickup_date ), strtotime( $dropoff_date ) );

	// Get array quantity available
    $data_quantity 	= ovabrw_get_quantity_available( $product_id, $store_quantity, $cart_quantity, $quantity, false, 'cart' );

    // Number quantity available
    $data_total['quantity_available'] = absint( $data_quantity['quantity_available'] );

    // Check Unavailable
    $unavailable = ovabrw_check_unavailable( $product_id, strtotime( $pickup_date ), strtotime( $dropoff_date ) );
    if ( $unavailable ) {
    	$data_total['quantity_available'] = 0;
    }

    $data_total = apply_filters( 'ovabrw_ft_ajax_data_total', $data_total, $product_id );

    // Deposit
    $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
    if ( 'yes' === $deposit_enable && apply_filters( 'ovabrw_ajax_deposit_enable', true ) ) {
    	$value_deposit = floatval( get_post_meta ( $product_id, 'ovabrw_amount_deposit', true ) );
    	$deposit_type  = get_post_meta ( $product_id, 'ovabrw_type_deposit', true );

    	if ( 'deposit' === $deposit ) {
    		if ( $deposit_type === 'percent' ) {
    			$line_total = ( $line_total * $value_deposit ) / 100;
    		} else {
    			$line_total = $value_deposit;
    		}
    	}
    }
	
	if ( $line_total < 0 ) {
		$data_total['line_total'] 		= wc_price( 0 );
		$data_total['amount_insurance'] = wc_price( $data_total['amount_insurance'] );

		echo json_encode($data_total);
	} else {
		$data_total['line_total'] 		= wc_price( $line_total );
		$data_total['amount_insurance'] = wc_price( $data_total['amount_insurance'] );

		echo json_encode($data_total);
	}

	wp_die();
}

/**
 * Ajax search tour
 */
add_action( 'wp_ajax_ovabrw_search_ajax', 'ovabrw_search_ajax' );
add_action( 'wp_ajax_nopriv_ovabrw_search_ajax', 'ovabrw_search_ajax' );
function ovabrw_search_ajax() {
	$data = $_POST;
    
    $layout        	 	= isset( $data['layout'] )         		? sanitize_text_field( $data['layout'] )         	: 'grid';
    $grid_column        = isset( $data['grid_column'] )         ? sanitize_text_field( $data['grid_column'] )       : 'column4';
	$order 				= isset( $data['order'] ) 		   		? sanitize_text_field( $data['order'] ) 		    : 'DESC';
	$orderby 			= isset( $data['orderby'] ) 	   		? sanitize_text_field( $data['orderby'] ) 			: 'ID';
	$orderby_meta_key 	= isset( $data['orderby_meta_key'] ) 	? sanitize_text_field( $data['orderby_meta_key'] ) 	: '';
	$posts_per_page 	= isset( $data['posts_per_page'] ) 		? sanitize_text_field( $data['posts_per_page'] )	: '4';
	$paged 				= isset( $data['paged'] ) 		   		? (int)$data['paged']  								:  1;

    $destination    	= isset( $data['destination'] )    		? sanitize_text_field( $data['destination'] )		: 'all';
    $custom_taxonomy    = isset( $data['custom_taxonomy'] )     ? sanitize_text_field( $data['custom_taxonomy'] )	: 'all';
    $slug_taxonomy_name = isset( $data['slug_taxonomy'] )       ? sanitize_text_field( $data['slug_taxonomy'] )		: '';
    $pickup_date 		= isset( $data['start_date'] )     		? strtotime( $data['start_date'] ) 					: '';
    $adults 			= isset( $data['adults'] ) 		   		? sanitize_text_field( $data['adults'] )		    : '';
    $childrens 			= isset( $data['childrens'] ) 	   		? sanitize_text_field( $data['childrens'] )			: '';
    $start_price    	= isset( $data['start_price'] )    		? (int)$data['start_price']  						:  0;
    $end_price      	= isset( $data['end_price'] )      		? (int)$data['end_price']  					    	:  '';
    $review_score 		= isset( $data['review_score'] )   		? $data['review_score']   							: array();
    $categories 		= isset( $data['categories'] )     		? $data['categories']   							: array();
    
    $list_taxonomy      = ovabrw_create_type_taxonomies();
    $slug_taxonomy      = str_replace('_name', '', $slug_taxonomy_name) ;
    
    // Base Query
    $args_base = array(
		'post_type'      	=> 'product',
		'post_status'    	=> 'publish',
		'posts_per_page' 	=> -1,
		'order' 			=> $order,
		'orderby' 			=> $orderby,
		'meta_key'          => $orderby_meta_key,
		'tax_query' => array(
            array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'ovabrw_car_rental', 
            ),
        ),
	);
    
    // Tax Query custom taxonomy
    $arg_taxonomy_arr = [];
    if ( ! empty( $list_taxonomy ) ) {
        foreach( $list_taxonomy as $taxonomy ) {
            $taxonomy_get = isset( $custom_taxonomy ) ? sanitize_text_field( $custom_taxonomy ) : '';

            if ( $taxonomy_get != 'all' && $taxonomy['slug'] == $slug_taxonomy ) {
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
    
    $args_meta_query_arr = $args_cus_meta_custom = $args_cus_tax_custom = array();

    if (  $args_base ) {

    	if ( $destination != 'all' ) {
	        $args_meta_query_arr[] = [
	            'key'     => 'ovabrw_destination',
	            'value'   => $destination,
	            'type'    => 'numeric',
	            'compare' => 'IN',
	        ];
	    }

	    if ( $review_score != [] ) {
	        $args_meta_query_arr[] = [
	            'key'     => '_wc_average_rating',
	            'value'   => $review_score,
	            'type'    => 'numeric',
	            'compare' => 'IN',
	        ];
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

	    if ( $end_price != '' ) {
	        $args_meta_query_arr[] = [
	            'key'     => '_price',
	            'value'   => array($start_price,$end_price),
	            'type'    => 'numeric',
	            'compare' => 'BETWEEN',
	        ];
	    }

    	if ( $categories != [] ) {
	        $arg_taxonomy_arr[] = [
	            'taxonomy' => 'product_cat',
	            'field'    => 'slug',
	            'terms'    => $categories
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

	    if( !empty($args_meta_query_arr) ){
	        $args_cus_meta_custom = array(
	            'meta_query' => array(
	                'relation'  => 'AND',
	                $args_meta_query_arr
	            )
	        );
	    }

	    if ( ! empty( $pickup_date) ) {
	    	$exclude_ids  = ovabrw_get_exclude_ids( $pickup_date );
	        $args_base['post__not_in'] = $exclude_ids;
	    }

        $args = array_merge_recursive( $args_base, $args_cus_tax_custom, $args_cus_meta_custom );

        $products = new WP_Query( apply_filters( 'ovabrw_ft_query_search_ajax', $args, $data ));

        $number_results_found  = $products->found_posts;

        ob_start();
        ?>

	        <div class="ovabrw-products-result ovabrw-products-result-<?php echo esc_attr( $layout );?> <?php echo esc_attr( $grid_column );?>">

				<?php
					do_action( 'woocommerce_before_single_product' );
					if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();

						if( $layout == 'grid' ) {
							wc_get_template_part( 'content', 'product' );
						} elseif( $layout == 'list' ) {
                            wc_get_template_part( 'rental/content-item', 'product-list' );
						}
						
					endwhile; else :
					?>

						<div class="not_found_product">
							<h3 class="empty-list">
								<?php esc_html_e( 'Not available tours', 'ova-brw' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'It seems we can’t find what you’re looking for.', 'ova-brw' ); ?>
							</p>
						</div>

					<?php
					endif; wp_reset_postdata();
					do_action( 'woocommerce_after_single_product' );
				?>

                <input type="hidden" class="tour_number_results_found" name="tour_number_results_found" value="<?php echo esc_attr($number_results_found); ?>">

			</div>

        <?php

        $total = $products->max_num_pages;

		if (  $total > 1 ): ?>
			<div class="ovabrw-pagination-ajax" data-paged="<?php echo esc_attr( $paged ); ?>">
			<?php
				echo ovabrw_pagination_ajax( $number_results_found , $products->query_vars['posts_per_page'], $paged );
			?>
			</div>
			<?php
		endif;

		$result = ob_get_contents(); 
		ob_end_clean();

		echo json_encode( array( "result" => $result ));
		wp_die();
    } else {
    	echo json_encode( array( "result" => $result ));
    	wp_die();
    }
}

?>