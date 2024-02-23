<?php if( ! defined( 'ABSPATH' ) ) exit();

extract( $args );

$posts_per_page   = $args['posts_per_page'];
$args['method']   = 'POST';

$search_results_layout   = $args['search_results_layout'];
$grid_column   			 = $args['search_results_grid_column'];

// Avanced Search Settings
$show_advanced_search    = $args['show_advanced_search'];
$advanced_search_label   = $args['advanced_search_label'];
$advanced_search_icon    = $args['advanced_search_icon'];
$filter_price_label      = $args['filter_price_label'];
$review_label            = $args['review_label'];
$filter_category_label   = $args['filter_category_label'];

// Filter Settings
$show_filter             = $args['show_filter'];
$tour_found_text         = $args['tour_found_text'];
$clear_filter_text       = $args['clear_filter_text'];
    // list category
	$args_product_categories = array(
	    'taxonomy'   => "product_cat",
	    'orderby' 	 => 'ID',
		'order' 	 => 'DESC',
	);
	$product_categories  = get_terms( $args_product_categories );
	// get min max price
	$prices              = ovabrw_get_filtered_price();
	$min_price           = floor($prices->min_price);
	$max_price           = round($prices->max_price);
	$currency_symbol     = get_woocommerce_currency_symbol();

?>


<div class="ovabrw-search-ajax">
	<div class="wrap-search-ajax" 
	    data-adults="<?php echo esc_attr( $args['default_adult_number'] ) ;?>"
	    data-childrens="<?php echo esc_attr( $args['default_children_number'] ) ;?>"
	    data-sort_by_default="<?php echo esc_attr( $args['sort_by_default'] ) ;?>"
	    data-start-price="<?php echo esc_attr( $min_price ) ;?>"
	    data-end-price="<?php echo esc_attr( $max_price ) ;?>"
	    data-grid_column="<?php echo esc_attr( $grid_column ) ;?>"
	 >
		
		<!-- Search -->

		<?php ovabrw_get_template('single/ovabrw_search.php', $args) ; ?>

		<!-- Advanced Search -->
		<?php if( $show_advanced_search === 'yes') : ?>
	        <div class="ovabrw-search-advanced">

	        	<div class="search-advanced-input">
	        		<?php if( $advanced_search_icon ) { ?>
		        		<div class="advanced-search-icon">
		        			<?php \Elementor\Icons_Manager::render_icon( $advanced_search_icon, [ 'aria-hidden' => 'true' ] ); ?>
		        		</div>
	        		<?php } ?>
		        	<span class="search-advanced-text">
		        		<?php echo esc_html( $advanced_search_label ); ?>
		        	</span>
		        	<i aria-hidden="true" class="icomoon icomoon-chevron-down"></i>
	        	</div>

	        	<div class="search-advanced-field-wrapper">
	        		<!-- Price Filter -->
	        	    <div class="search-advanced-field price-field">
	        	    	<span class="ovabrw-label">
	        	    		<?php echo esc_html($filter_price_label) ; ?>
	        	    	</span>
	        	    	<div class="brw-tour-price-input" data-currency_symbol="<?php echo esc_attr($currency_symbol); ?>">
		        	    	<input type="text" class="brw-tour-price-from" value="<?php echo esc_attr($min_price) ;?>" data-value="<?php echo esc_attr($min_price) ;?>"/>
							<input type="text" class="brw-tour-price-to" value="<?php echo esc_attr($max_price) ;?>" data-value="<?php echo esc_attr($max_price) ;?>"/>
						</div>
	        	     	<div class="slider-wrapper">
						    <div id="brw-tour-price-slider"></div>
						</div> 
	        	    </div>
	        	    <!-- Rating Filter -->
	        	    <div class="search-advanced-field rating-field">
	        	    	<span class="ovabrw-label">
	        	    		<?php echo esc_html($review_label) ; ?>
	        	    	</span>
	        	     	<?php for( $i = 5; $i>=1 ; $i--) { ?>
	        	     		<div class="total-rating-stars">

	        	     			<div class="input-rating">
	        	     				<input id="rating-filter-<?php echo esc_attr($i) ;?>" type="checkbox" class="rating-filter" name="rating_value[<?php echo esc_attr($i) ;?>]" value="<?php echo esc_attr($i) ;?>">

	        	     				<label for="rating-filter-<?php echo esc_attr($i) ;?>">
	        	     					<?php switch ($i) {
	        	     						case 1: ?>
												<span class="rating-stars">
													<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
												</span>
												<?php break; ?>
											<?php case 2: ?>
												<span class="rating-stars">
													<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
													<span class="star star-2" data-rating-val="2"><i class="fas fa-star"></i></span>
												</span>
												<?php break; ?>
											<?php case 3: ?>
												<span class="rating-stars">
													<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
													<span class="star star-2" data-rating-val="2"><i class="fas fa-star"></i></span>
													<span class="star star-3" data-rating-val="3"><i class="fas fa-star"></i></span>
												</span>
												<?php break; ?>
											<?php case 4: ?>
												<span class="rating-stars">
													<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
													<span class="star star-2" data-rating-val="2"><i class="fas fa-star"></i></span>
													<span class="star star-3" data-rating-val="3"><i class="fas fa-star"></i></span>
													<span class="star star-4" data-rating-val="4"><i class="fas fa-star"></i></span>
												</span>
											    <?php break; ?>
		        	     					<?php case 5: ?>
												<span class="rating-stars">
													<span class="star star-1" data-rating-val="1"><i class="fas fa-star"></i></span>
													<span class="star star-2" data-rating-val="2"><i class="fas fa-star"></i></span>
													<span class="star star-3" data-rating-val="3"><i class="fas fa-star"></i></span>
													<span class="star star-4" data-rating-val="4"><i class="fas fa-star"></i></span>
													<span class="star star-5" data-rating-val="5"><i class="fas fa-star"></i></span> 
												</span>
											    <?php break; ?>
									   <?php } ?>
									</label>

	        	     			</div>

	        	     		</div>
	        	     	<?php } ?>
	        	    </div>
	        	    <!-- Tour Categories Filter -->
	        	    <div class="search-advanced-field tour-categories-field">
	        	    	<span class="ovabrw-label">
	        	    		<?php echo esc_html($filter_category_label) ; ?>
	        	    	</span>
	        	     	<?php foreach( $product_categories as $pro_cat ) : ?>
	        	     		<div class="tour-category-field">

	        	     			<input id="tour-category-filter-<?php echo esc_attr($pro_cat->slug) ;?>" type="checkbox" class="tour-category-filter" name="category_value" value="<?php echo esc_attr($pro_cat->slug) ;?>">

		        	     		<label for="tour-category-filter-<?php echo esc_attr($pro_cat->slug) ;?>">
									<span class="tour-category-name">
										<?php echo esc_html( $pro_cat->name ) ; ?>
									</span>
								</label>

		        	     	</div>
	        	     	<?php endforeach;?>
	        	    </div>
	        	</div>
	        </div>
	    <?php endif; ?>

	    <!-- Filter -->
		<?php if( $show_filter === 'yes') : ?>
	        <div class="ovabrw-tour-filter">
	        	
	        	<div class="left-filter">
	        		<span class="tour-found-text number-result-tour-found">
		        		<?php echo esc_html__( '0', 'ova-brw' ); ?>
		        	</span>
	        		<span class="tour-found-text">
		        		<?php echo esc_html( $tour_found_text ); ?>
		        	</span>
		        	<span class="clear-filter">
		        		<?php echo esc_html( $clear_filter_text ); ?>
		        	</span>
	        	</div>

	        	<div class="right-filter">
	        		<div class="filter-sort">

	        			<input type="text" class="input_select_input" name="sr_sort_by_label" value="<?php echo esc_html__('Sort by','ova-brw'); ?>" autocomplete="off" readonly="readonly">

						<input type="hidden" class="input_select_input_value" name="sr_sort_by" value="id_desc">

						<ul class="input_select_list" style="display: none;">
						    <li class="term_item <?php if( $sort_by_default == 'id_desc' ) { echo 'term_item_selected' ; } ?>" 
						    	data-id="id_desc" 
						    	data-value="<?php esc_attr_e('Sort by latest','ova-brw'); ?>"
						    >
							    <?php echo esc_html__('Latest','ova-brw'); ?>
							</li>
							<li class="term_item <?php if( $sort_by_default == 'rating_desc' ) { echo 'term_item_selected' ; } ?>" 
								data-id="rating_desc" 
								data-value="<?php esc_attr_e('Sort by rating','ova-brw'); ?>"
							>
								<?php echo esc_html__('Rating','ova-brw'); ?>
							</li>
							<li class="term_item <?php if( $sort_by_default == 'price_asc' ) { echo 'term_item_selected' ; } ?>" 
								data-id="price_asc" 
								data-value="<?php esc_attr_e('Sort by price: low to high','ova-brw'); ?>"
							>
								<?php echo esc_html__('Price: low to high','ova-brw'); ?>
							</li>
							<li class="term_item <?php if( $sort_by_default == 'price_desc' ) { echo 'term_item_selected' ; } ?>" 
								data-id="price_desc" 
								data-value="<?php esc_attr_e('Sort by price: high to low','ova-brw'); ?>"
							>
								<?php echo esc_html__('Price: high to low','ova-brw'); ?>
							</li>
						</ul>
					</div>

					<div class="asc_desc_sort">
	        			<i aria-hidden="true" class="asc_sort icomoon icomoon-chevron-up"></i>
	        		    <i aria-hidden="true" class="desc_sort icomoon icomoon-chevron-down"></i>
	        		</div>

	        		<div class="filter-result-layout">
		        		<i aria-hidden="true" class="filter-layout <?php if( $search_results_layout == 'list' )  { echo 'filter-layout-active' ; } ?> icomoon icomoon-list" data-layout="list"></i>
						<i aria-hidden="true" class="filter-layout <?php if( $search_results_layout == 'grid' )  { echo 'filter-layout-active' ; } ?> icomoon icomoon-gird" data-layout="grid"></i>
					</div>
	         	</div>	
	        </div>
	    <?php endif; ?>

		<!-- Load more -->
		<div class="wrap-load-more" style="display: none;">
			<svg class="loader" width="50" height="50">
				<circle cx="25" cy="25" r="10" />
				<circle cx="25" cy="25" r="20" />
			</svg>
		</div>
		<!-- End load more -->

		<!-- Search result -->
		<?php if( $sort_by_default == 'id_desc' ) : ?>
			<div 
				id="brw-search-ajax-result" 
				class="brw-search-ajax-result" 
				data-order="DESC" 
				data-orderby="ID"
				data-orderby_meta_key="" 
				data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
			</div>
		<?php elseif( $sort_by_default == 'rating_desc' ) :?>
            <div 
				id="brw-search-ajax-result" 
				class="brw-search-ajax-result" 
				data-order="DESC" 
				data-orderby="meta_value_num"
				data-orderby_meta_key="_wc_average_rating" 
				data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
			</div>
		<?php elseif( $sort_by_default == 'price_asc' ) :?>
            <div 
				id="brw-search-ajax-result" 
				class="brw-search-ajax-result" 
				data-order="ASC" 
				data-orderby="meta_value_num"
				data-orderby_meta_key="_price" 
				data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
			</div>
		<?php elseif( $sort_by_default == 'price_desc' ) :?>
            <div 
				id="brw-search-ajax-result" 
				class="brw-search-ajax-result" 
				data-order="DESC" 
				data-orderby="meta_value_num"
				data-orderby_meta_key="_price" 
				data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
			</div>
		<?php endif; ?>
    </div>
</div>