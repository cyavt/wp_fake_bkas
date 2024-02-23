<?php if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'pre_get_posts', 'ovadestination_post_per_page_archive' );
function ovadestination_post_per_page_archive( $query ) {
	if ( ( is_post_type_archive( 'destination' ) || is_tax('cat_destination') ) && ! is_admin() ) {
		if( $query->is_post_type_archive( 'destination' ) || $query->is_tax('cat_destination') ) {
			$query->set('post_type', array( 'destination' ) );
			$query->set('posts_per_page', get_theme_mod( 'ova_destination_total_record', 7 ) );
			$query->set('orderby', 'meta_value_num' );
            $query->set('order', 'DESC' );
            $query->set('meta_type', 'NUMERIC' );
            $query->set('meta_key', 'ova_destination_met_order_destination' );
		}
	}
}

// Get data destination
if ( !function_exists( 'ovadestination_get_data_destination_el' ) ) {
	function ovadestination_get_data_destination_el( $args ) {
		$category = $args['category'];

		if ( $category == 'all' ) {
			$args_new = array(
				'post_type' 		=> 'destination',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> $args['total_count'],
				'offset' 			=> $args['offset'],
			);
		} else {
			$args_new = array(
				'post_type' 		=> 'destination',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> $args['total_count'],
				'offset' 			=> $args['offset'],
				'tax_query' 		=> array(
					array(
						'taxonomy' => 'cat_destination',
						'field'    => 'slug',
						'terms'    => $category,
					)
				),
			);
		}

		$args_destination_order = [];
		if( $args['orderby_post'] === 'ova_destination_met_order_destination' ) {
			$args_destination_order = [
				'meta_key'   	=> $args['orderby_post'],
				'orderby'    	=> 'meta_value_num',
				'meta_type'  	=> 'NUMERIC',
				'order'   		=> $args['order'],
			];
		} else { 
			$args_destination_order = [
				'orderby' => $args['orderby_post'],
				'order'   => $args['order'],
			];
		}

		$args_destination 	= array_merge( $args_new, $args_destination_order );
		$destinations  		= new \WP_Query($args_destination);

		return $destinations;
	}
}

// Get data destination slider
if ( !function_exists( 'ovadestination_get_data_destination_slider_el' ) ) {
	function ovadestination_get_data_destination_slider_el( $args ) {
		$category = $args['category'];

		if( $category == 'all' ){
			$args_new = array(
				'post_type' 		=> 'destination',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> $args['total_count'],
			);
		} else {
			$args_new = array(
				'post_type' 		=> 'destination',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> $args['total_count'],
				'tax_query' 		=> array(
					array(
						'taxonomy' => 'cat_destination',
						'field'    => 'slug',
						'terms'    => $category,
					)
				),
			);
		}

		$args_destination_order = [];
		if( $args['orderby_post'] === 'ova_destination_met_order_destination' ) {
			$args_destination_order = [
				'meta_key'   	=> $args['orderby_post'],
				'orderby'    	=> 'meta_value_num',
				'meta_type' 	=> 'NUMERIC',
				'order'   		=> $args['order'],
			];
		} else { 
			$args_destination_order = [
				'orderby' => $args['orderby_post'],
				'order'   => $args['order'],
			];
		}

		$args_destination 	= array_merge( $args_new, $args_destination_order );
		$destinations  		= new \WP_Query($args_destination);

		return $destinations;
	}
}