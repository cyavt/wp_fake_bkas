<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// Return value of setting
if( ! function_exists( 'ovabrw_get_setting' ) ) {
	function ovabrw_get_setting( $setting ) {
		if( trim($setting) == '' ) return;
		return esc_html__( $setting, 'BRW Admin Settings' , 'ova-brw' );
	}
}

// Get Date Format in Events Setting
if( !function_exists( 'ovabrw_get_date_format' ) ){
	function ovabrw_get_date_format() {
		return apply_filters( 'ovabrw_get_date_format_hook', ovabrw_get_setting( get_option( 'ova_brw_booking_form_date_format', 'd-m-Y' ) ) );
	}
}

if ( !function_exists('ovabrw_get_placeholder_date') ) {
	function ovabrw_get_placeholder_date() {
		$placeholder = '';
		$dateformat = ovabrw_get_date_format();

		if ( 'Y-m-d' === $dateformat ) {
			$placeholder = esc_html__( 'YYYY-MM-DD', 'ova-brw' );
		} elseif ( 'm/d/Y' === $dateformat ) {
			$placeholder = esc_html__( 'MM/DD/YYYY', 'ova-brw' );
		} elseif ( 'Y/m/d' === $dateformat ) {
			$placeholder = esc_html__( 'YYYY/MM/DD', 'ova-brw' );
		} else {
			$placeholder = esc_html__( 'DD-MM-YYYY', 'ova-brw' );
		}

		return $placeholder;
	}
}

// Return real path template in Plugin or Theme
if( !function_exists( 'ovabrw_locate_template' ) ){
	function ovabrw_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		
		// Set variable to search in ovabrw-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'ovabrw-templates/';
		endif;

		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = OVABRW_PLUGIN_PATH . 'ovabrw-templates/'; // Path to the template folder
		endif;

		// Search template file in theme folder.
		$template = locate_template( array(
			$template_path . $template_name
			// ,$template_name
		) );

		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;

		return apply_filters( 'ovabrw_locate_template', $template, $template_name, $template_path, $default_path );
	}
}

// Include Template File
function ovabrw_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
	if ( is_array( $args ) && isset( $args ) ) :
		extract( $args );
	endif;
	$template_file = ovabrw_locate_template( $template_name, $tempate_path, $default_path );
	if ( ! file_exists( $template_file ) ) :
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
		return;
	endif;

	include $template_file;
}

// List custom checkout fields array
if( ! function_exists( 'ovabrw_get_list_field_checkout' ) ) {
	function ovabrw_get_list_field_checkout( $post_id ) {
		if( !$post_id ) return [];

		$list_ckf_output = [];

		$ovabrw_manage_custom_checkout_field = get_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', true );

		$list_field_checkout = get_option( 'ovabrw_booking_form', array() );

		// Get custom checkout field by Category
		$product_cats = wp_get_post_terms( $post_id, 'product_cat' );
		$cat_id = isset( $product_cats[0] ) ? $product_cats[0]->term_id : '';
		$ovabrw_custom_checkout_field = $cat_id ? get_term_meta($cat_id, 'ovabrw_custom_checkout_field', true) : '';

		$ovabrw_choose_custom_checkout_field = $cat_id ? get_term_meta($cat_id, 'ovabrw_choose_custom_checkout_field', true) : '';
		
		if ( $ovabrw_manage_custom_checkout_field === 'new' ) {
			$list_field_checkout_in_product = get_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', true );
			$list_field_checkout_in_product_arr = explode( ',', $list_field_checkout_in_product );
			$list_field_checkout_in_product_arr = array_map( 'trim', $list_field_checkout_in_product_arr );
			$list_ckf_output = [];

			if( ! empty( $list_field_checkout_in_product_arr ) && is_array( $list_field_checkout_in_product_arr ) ) {
				foreach( $list_field_checkout_in_product_arr as $field_name ) {
					if( array_key_exists( $field_name, $list_field_checkout ) ) {
						$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
					}
				}
			} 
		} else if ( $ovabrw_choose_custom_checkout_field == 'all' ) {
			$list_ckf_output = $list_field_checkout;
		} else if ( $ovabrw_choose_custom_checkout_field == 'special' ) {
			if ( $ovabrw_custom_checkout_field ) {
				foreach( $ovabrw_custom_checkout_field as $field_name ) {
					if( array_key_exists( $field_name, $list_field_checkout ) ) {
						$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
					}
				}
			} else {
				$list_ckf_output = [];
			}
		} else {
			$list_ckf_output = $list_field_checkout;
		}

		return $list_ckf_output;
	}
}

// List Order Status
if ( !function_exists( 'brw_list_order_status' ) ) {
	function brw_list_order_status(){
		return apply_filters( 'brw_list_order_status', array( 'wc-completed', 'wc-processing' ) );
	}
}

// Stock Quantity Product
if ( !function_exists( 'ovabrw_get_total_stock' ) ) {
	function ovabrw_get_total_stock( $product_id ) {
	    $stock_quantity = 1;
		$number_stock 	= get_post_meta( $product_id, 'ovabrw_stock_quantity', true );

		if ( $number_stock ) {
			$stock_quantity = absint( $number_stock );
		}

		return $stock_quantity;
	}
}

// Get dates between
if ( !function_exists( 'ovabrw_createDatefull' ) ) {
	function ovabrw_createDatefull( $start, $end, $format = "Y-m-d" ){
	    $dates = array();

	    while( $start <= $end ) {
	        array_push( $dates, date( $format, $start) );
	        $start += 86400;
	    }

	    return $dates;
	} 
}

// Get number dates between
if ( !function_exists( 'total_between_2_days' ) ) {
	function total_between_2_days( $start, $end ) {
    	return floor( abs( strtotime( $end ) - strtotime( $start ) ) / (60*60*24) );
	}
}

// Get Array Product ID with WPML
if ( !function_exists( 'ovabrw_get_wpml_product_ids' ) ) {
	function ovabrw_get_wpml_product_ids( $product_id_original ){
		$translated_ids = Array();

		// get plugin active
		$active_plugins = get_option('active_plugins');

		if ( in_array ( 'polylang/polylang.php', $active_plugins ) || in_array ( 'polylang-pro/polylang.php', $active_plugins ) ) {
				$languages = pll_languages_list();
				if ( !isset( $languages ) ) return;
				foreach ($languages as $lang) {
					$translated_ids[] = pll_get_post($product_id_original, $lang);
				}
		} elseif ( in_array ( 'sitepress-multilingual-cms/sitepress.php', $active_plugins ) ) {
			global $sitepress;
		
			if(!isset($sitepress)) return;
			
			$trid = $sitepress->get_element_trid($product_id_original, 'post_product');
			$translations = $sitepress->get_element_translations($trid, 'product');
			foreach( $translations as $lang=>$translation){
			    $translated_ids[] = $translation->element_id;
			}

		} else {
			$translated_ids[] = $product_id_original;
		}

		return apply_filters( 'ovabrw_multiple_languages', $translated_ids );
	}
}

// Get Pick up date from URL in Product detail
if ( !function_exists( 'ovabrw_get_current_date_from_search' ) ) {
	function ovabrw_get_current_date_from_search( $type = 'pickup_date', $product_id ){
		// Get date from URL
		if ( $type == 'pickup_date'  ){
			$time = ( isset( $_GET['pickup_date'] ) ) ? strtotime( $_GET['pickup_date'] ) : '';
		} else if ( $type == 'dropoff_date' ) {
			$time = ( isset( $_GET['dropoff_date'] ) ) ? strtotime( $_GET['dropoff_date'] ) : '';
		}

		$dateformat = ovabrw_get_date_format();

		if ( $time ) {
			return date( $dateformat, $time );
		}

		return '';
	}
}

// Get All custom taxonomy display in listing of product
if ( !function_exists( 'get_all_cus_tax_dis_listing' ) ) {
	function get_all_cus_tax_dis_listing( $pid ){
		$all_cus_choosed 		= array();
		$all_cus_choosed_tmp 	= array();

		// Get All Categories of this product
		$categories = get_the_terms( $pid, 'product_cat' );
		if ( $categories ) {
			foreach ($categories as $key => $value) {
				$cat_id = $value->term_id;

				// Get custom tax display in category
				$ovabrw_custom_tax = get_term_meta($cat_id, 'ovabrw_custom_tax', true);

				if ( $ovabrw_custom_tax ) {
					foreach ($ovabrw_custom_tax as $slug_tax) {
						// Get value of terms in product
						$terms = get_the_terms( $pid, $slug_tax );

						// Get option: custom taxonomy
						$ovabrw_custom_taxonomy =  get_option( 'ovabrw_custom_taxonomy', '' );
						$show_listing_status = 'no';
						if( $ovabrw_custom_taxonomy ){
							foreach ($ovabrw_custom_taxonomy as $slug => $value) {
								if( $slug_tax == $slug && isset( $value['show_listing'] ) && $value['show_listing'] == 'on' ){
									$show_listing_status = 'yes';
									break;
								}
							}
						}

						if ( $terms && $show_listing_status == 'yes' ) {
							foreach ( $terms as $term ) {
								if( !in_array( $slug_tax, $all_cus_choosed_tmp ) ){
									// Assign array temp to check exist
									array_push($all_cus_choosed_tmp, $slug_tax);
									array_push($all_cus_choosed, array( 'slug' => $slug_tax, 'name' => $term->name) );
								}
							}
						}
					}
				}
			}
		}

		return $all_cus_choosed;
	}
}

// Get custom taxonomy of an product
if ( !function_exists( 'ovabrw_get_taxonomy_choosed_product' ) ) {
	function ovabrw_get_taxonomy_choosed_product( $pid ){
		// Custom taxonomies choosed in post
		$all_cus_tax 	= array();
		$exist_cus_tax 	= array();
		
		// Get Category of product
		$cats = get_the_terms( $pid, 'product_cat' );
		$show_taxonomy_depend_category = ovabrw_get_setting( get_option( 'ova_brw_search_show_tax_depend_cat', 'yes' ) );

		if ( 'yes' == $show_taxonomy_depend_category ) {
			if ( $cats ) {
				foreach ( $cats as $key => $cat ) {
					// Get custom taxonomy display in category
					$ovabrw_custom_tax = get_term_meta($cat->term_id, 'ovabrw_custom_tax', true);	
					
					if ( $ovabrw_custom_tax ){
						foreach ( $ovabrw_custom_tax as $key => $value ) {
							array_push( $exist_cus_tax, $value );
						}	
					}
				}
			}

			if ( $exist_cus_tax ) {
				foreach ($exist_cus_tax as $key => $value) {
					$cus_tax_terms = get_the_terms( $pid, $value );
					if ( $cus_tax_terms ) {
						foreach ( $cus_tax_terms as $key => $value ) {
							$list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

							if ( ! empty( $list_fields ) ) :
			                    foreach ( $list_fields as $key => $field ) : 
			                    	if ( is_object($value) && $value->taxonomy == $key ) {
			                    		if ( array_key_exists($key, $all_cus_tax) ) {
			                    			if ( !in_array( $value->name, $all_cus_tax[$key]['value'] ) ) {
			                    				array_push($all_cus_tax[$key]['value'], $value->name);	
			                    			}
			                    		} else {
		                    				if ( isset( $field['label_frontend'] ) && $field['label_frontend'] ) {
		                    					$all_cus_tax[$key]['name'] = $field['label_frontend'];	
		                    				} else {
		                    					$all_cus_tax[$key]['name'] = $field['name'];	
		                    				}
		                    				$all_cus_tax[$key]['value'] = array( $value->name );
			                    		}
			                    		break;
			                    	}
			                    endforeach;
			                endif;
						}
					}
				}
			}
		} else {
			$list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

			if ( ! empty( $list_fields ) ) {
				foreach ( $list_fields as $key => $field ) {
					$terms = get_the_terms( $pid, $key );
					if ( $terms && ! isset( $terms->errors ) ) {
						foreach ( $terms as $value ) {
							if ( is_object( $value ) ) {
								if ( array_key_exists( $key, $all_cus_tax ) ) {
									if ( ! in_array( $value->name, $all_cus_tax[$key]['value'] ) ) {
			            				array_push($all_cus_tax[$key]['value'], $value->name);	
			            			}
								} else {
									if ( isset( $field['label_frontend'] ) && $field['label_frontend'] ) {
			        					$all_cus_tax[$key]['name'] = $field['label_frontend'];	
			        				} else {
			        					$all_cus_tax[$key]['name'] = $field['name'];
			        				}

									$all_cus_tax[$key]['value'] = array( $value->name );
								}
							}
						}
					}
				}
			}
		}

		return $all_cus_tax;
	}
}

// Get product template
if ( ! function_exists( 'ovabrw_get_product_template' ) ) {
	function ovabrw_get_product_template( $id ) {
		$template = get_option( 'ova_brw_template_elementor_template', 'default' );

		if ( empty( $id ) ) {
			return $template;
		}

		$products 	= wc_get_product( $id );
		$categories = $products->get_category_ids();

		if ( ! empty( $categories ) ) {
	        $term_id 	= reset( $categories );
	        $template_by_category = get_term_meta( $term_id, 'ovabrw_product_templates', true );

	        if ( $template_by_category && $template_by_category !== 'global' ) {
	        	$template = $template_by_category;
	        }
	    }

		return $template;
	}
}

// Check key in array
if ( !function_exists( 'ovabrw_check_array' ) ) {
	function ovabrw_check_array( $args, $key ) {
		if ( $args && is_array( $args ) ) {
			if ( isset( $args[$key] ) && $args[$key] ) {
				return true;
			}
		}

		return false;
	}
}
