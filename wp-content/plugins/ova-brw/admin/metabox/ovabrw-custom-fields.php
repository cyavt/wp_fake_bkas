<?php if ( !defined( 'ABSPATH' ) ) exit();

	// Check product if with wcfm plugin
	global $wp;
	
	if ( !empty($wp->query_vars) ) {
		$post_id = $wp->query_vars['wcfm-products-manage'];
	} else {
		$post_id = false;
	}
		
	if( !$post_id ){
		$post_id = get_the_ID();
	}
	
	global $woocommerce, $post;

	if ( ! function_exists( 'woocommerce_wp_text_input' ) && ! is_admin() ) {
		include_once WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php';
	}
?>

	<div class="options_group show_if_ovabrw_car_rental ovabrw_metabox_car_rental">

		<?php  woocommerce_wp_text_input(
			array(
				'id' 			=> 'ovabrw_children_price',
				'class' 		=> 'short',
				'label' 		=> sprintf( esc_html__( 'Children price (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ),
				'desc_tip' 		=> 'true',
				'type' 			=> 'text',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_children_price', true ) : '',
			));
		?>

		<!-- Days -->
		<?php  woocommerce_wp_text_input(
			array(
				'id' 			=> 'ovabrw_number_days',
				'class' 		=> 'short',
				'label' 		=> esc_html__( 'Days', 'ova-brw' ),
				'desc_tip' 		=> 'true',
				'placeholder' 	=> 1,
				'type' 			=> 'number',
				'value' 		=> $post_id && get_post_meta( $post_id, 'ovabrw_number_days', true ) ? get_post_meta( $post_id, 'ovabrw_number_days', true ) : 1,
				'custom_attributes' => array( 'autocomplete' => 'off' ),
			));
		?>

		<!-- Amount of insurance -->
		<?php  woocommerce_wp_text_input(
			array(
				'id' 			=> 'ovabrw_amount_insurance',
				'class' 		=> 'short',
				'label' 		=> sprintf( esc_html__( 'Amount of insurance (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ),
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( 'This amount will be added to the cart. Decimal use dot (.)', 'ova-brw' ),
				'placeholder' 	=> '10.5',
				'type' 			=> 'text',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_amount_insurance', true ) : '',
				'custom_attributes' => array( 'autocomplete' => 'off' ),
			));
		?>

		<?php if( apply_filters( 'ovabrw_show_backend_deposit', true ) ){ ?>

			<!-- Enable deposit -->
			<?php  woocommerce_wp_select(
				array(
					'id' 			=> 'ovabrw_enable_deposit',
					'label' 		=> esc_html__( 'Enable deposit', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> array(
						'no'	=> esc_html__( 'No', 'ova-brw' ),
						'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
					),
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_enable_deposit', true ) : 'no',
			   	));
			?>

			<!-- Force deposit -->
			<?php  woocommerce_wp_select(
				array(
					'id' 			=> 'ovabrw_force_deposit',
					'label' 		=> esc_html__( 'Require deposit', 'ova-brw' ),
					'desc_tip' 		=> 'true',
					'description' 	=> esc_html__( 'Yes: Allow pay Full Payment, No: Only Deposit', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> array(
						'no' 	=> esc_html__( 'No', 'ova-brw' ),
						'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
					),
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_force_deposit', true ) : 'no',
			   	));
			?>

			<!-- Type deposit -->
			<?php  woocommerce_wp_select(
			  	array(
					'id' 			=> 'ovabrw_type_deposit',
					'label' 		=> esc_html__( 'Deposit type', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> array(
						'percent'	=> esc_html__( 'Percentage of price', 'ova-brw' ),
						'value'		=> esc_html__( 'Fixed value', 'ova-brw' ),
					),
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_type_deposit', true ) : 'percent',
					'custom_attributes' => array( 
						'data-currency' => get_woocommerce_currency_symbol(),
						'data-label' 	=> esc_html__( 'Deposit amount', 'ova-brw' ),
					),
			   	));
			?>
			
			<!-- amount deposit -->
			<?php  woocommerce_wp_text_input(
				array(
					'id' 			=> 'ovabrw_amount_deposit',
					'class' 		=> 'short',
					'label' 		=> esc_html__( 'Deposit amount', 'ova-brw' ),
					'placeholder' 	=> '50',
					'desc_tip' 		=> 'true',
					'description' 	=> esc_html__( 'decimal use dot (.)', 'ova-brw' ),
					'type' 			=> 'text',
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_amount_deposit', true ) : '',
			   	));
			?>

		<?php } ?>	

		<!-- Total Vehicle -->
		<?php  
		$ovabrw_stock_quantity = get_post_meta( $post_id, 'ovabrw_stock_quantity', true ) ? get_post_meta( $post_id, 'ovabrw_stock_quantity', true ) : 1;
		woocommerce_wp_text_input(
		  	array(
				'id' 			=> 'ovabrw_stock_quantity',
				'class' 		=> 'short',
				'label' 		=> esc_html__( 'Maximum tour at a time', 'ova-brw' ),
				'placeholder' 	=> '10',
				'value' 		=> $ovabrw_stock_quantity,
				'type' 			=> 'number'
		   	));
		?>

		<?php
			// Adults
			woocommerce_wp_text_input(
			  	array(
					'id' 			=> 'ovabrw_adults_max',
					'class' 		=> 'short',
					'label' 		=> esc_html__( 'Maximum Adults', 'ova-brw' ),
					'placeholder' 	=> '10',
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_adults_max', true ) : 10,
					'type' 			=> 'number'
			   	));

			woocommerce_wp_text_input(
			  	array(
					'id' 			=> 'ovabrw_adults_min',
					'class' 		=> 'short',
					'label' 		=> esc_html__( 'Minimum Adults', 'ova-brw' ),
					'placeholder' 	=> '1',
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_adults_min', true ) : 1,
					'type' 			=> 'number'
			   	));

			// Childrens
			woocommerce_wp_text_input(
			  	array(
					'id' 			=> 'ovabrw_childrens_max',
					'class' 		=> 'short',
					'label' 		=> esc_html__( 'Maximum Childrens', 'ova-brw' ),
					'placeholder' 	=> '5',
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_childrens_max', true ) : 5,
					'type' 			=> 'number'
			   	));

			woocommerce_wp_text_input(
			  	array(
					'id' 			=> 'ovabrw_childrens_min',
					'class' 		=> 'short',
					'label' 		=> esc_html__( 'Minimum Childrens', 'ova-brw' ),
					'placeholder' 	=> '1',
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_childrens_min', true ) : 1,
					'type' 			=> 'number'
			   	));

			// Video
			woocommerce_wp_text_input(
			  	array(
					'id' 			=> 'ovabrw_embed_video',
					'class' 		=> 'short',
					'label' 		=> esc_html__( 'Embed Video Link', 'ova-brw' ),
					'placeholder' 	=> 'https://www.youtube.com/',
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_embed_video', true ) : '',
					'type' 			=> 'text',
					'custom_attributes' => array( 'autocomplete' => 'off' ),
			   	));
		?>

		<!-- Destination -->
		<?php  
			woocommerce_wp_select(
			  	array(
					'id' 			=> 'ovabrw_destination',
					'label' 		=> esc_html__( 'Destination', 'ova-brw' ),
					'options' 		=> ovabrw_get_destinations(),
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_destination', true ) : '',
			   	));
		?>

		<!-- Fixed Time -->
		<div class="ovabrw-form-field ovabrw_fixed_time-field">
	  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Fixed Time', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_fixed_time.php' ); ?>
		</div>

		<!-- Feature -->
		<div class="ovabrw-form-field ovabrw_features-field">
	  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Features', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_features.php' ); ?>
		</div>


		<!-- Global Discount -->
		<div class="ovabrw-form-field price_not_period_time ">
	  		<br/>
	  		<strong class="ovabrw_heading_section"><?php esc_html_e('Global Discount (GD) / Price Per Person', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_global_discount.php' ); ?>
		</div>


		<!-- Price by range time -->
		<div class="ovabrw-form-field price_not_period_time ">
			<br/>
			<strong class="ovabrw_heading_section"><?php esc_html_e('Special Time (ST) / Price Per Person', 'ova-brw'); ?></strong>
			<span class="ovabrw_right"><?php esc_html_e( "Note: ST doesn't use GD, it will use DST", 'ova-brw' ); ?></span>
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_st.php' ); ?>
		</div>
		

		<!-- Resources -->
		<div class="ovabrw-form-field ovabrw_resources_field">
	  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Resources', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_resources.php' ); ?>
		</div>

		<!-- Services -->
		<div class="ovabrw-form-field ovabrw_service_field">
	  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Services', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service.php' ); ?>
		</div>

		<!-- unavailable time -->
		<div class="ovabrw-form-field ovabrw_untime_field">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Unavailable Time (UT)', 'ova-brw' ); ?></strong>
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_untime.php' ); ?>
		</div>

		<?php if( apply_filters( 'ovabrw_show_checkout_field_setting_product', true ) ){ ?>
			<div class="ovabrw-form-field">
				<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Custom Checkout Field', 'ova-brw' ); ?></strong>
				<?php  woocommerce_wp_select(
				  	array(
						'id' 			=> 'ovabrw_manage_custom_checkout_field',
						'label' 		=> esc_html__( 'Custom Checkout Field', 'ova-brw' ),
						'placeholder' 	=> '',
						'options' 		=> array(
							'all'	=> esc_html__( 'All', 'ova-brw' ),
							'new'	=> esc_html__( 'New', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', true ) : 'all',
				   	));
				?>
				<br/>
				<?php
					woocommerce_wp_textarea_input(
					    array(
					        'id' 			=> 'ovabrw_product_custom_checkout_field',
					        'placeholder' 	=> esc_html__( '', 'ova-brw' ),
					        'label' 		=> esc_html__('Custom Checkout Field', 'ova-brw'),
					        'description' 	=> esc_html__('Insert name in general custom checkout field. Example: ova_email_field, ova_address_field', 'ova-brw'),
					        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', true ) : '',
					    )
					);
				?>
			</div>
		<?php } ?>

		<!-- Map -->
		<div class="ovabrw-form-field">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Map', 'ova-brw' ); ?></strong>
			<?php
				$ovabrw_map_name = $post_id ? get_post_meta( $post->ID ,'ovaev_map_name', true ) : esc_html__('New York', 'ova-brw');
				$ovabrw_address  = $post_id ? get_post_meta( $post_id, 'ovabrw_address', true ) : esc_html__( 'New York, NY, USA', 'ova-brw' );
				if ( ! $ovabrw_address ) {
					$ovabrw_address = esc_html__( 'New York, NY, USA', 'ova-brw' );
				}
				// Address
				woocommerce_wp_text_input(
					array(
						'id' 				=> 'pac-input',
						'class' 			=> 'controls',
						'label'				=> esc_html__( '', 'ova-brw' ),
						'placeholder'		=> esc_html__( 'Enter a venue', 'ova-brw' ),
						'type' 				=> 'text',
						'value' 			=> $ovabrw_address,
						'custom_attributes' => array(
							'autocomplete' 	=> 'off',
							'autocorrect'	=> 'off',
							'autocapitalize'=> 'none'
						),
					)
				);
			?>
			<div id="admin_show_map"></div>
			<div id="infowindow-content">
				<span id="place-name" class="title"><?php echo esc_attr( $ovabrw_map_name ); ?></span><br>
				<span id="place-address"><?php echo esc_attr( $ovabrw_address ); ?></span>
			</div>

			<div id="map_info">
				<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_product_map.php' ); ?>
			</div>
		</div>	

	</div>
