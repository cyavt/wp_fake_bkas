<?php if( ! defined( 'ABSPATH' ) ) exit();

	extract( $args );

	$text_view_details 	= $args['label_view_details'];
	$icon 				= $args['icon'] ? $args['icon'] : '';

	//option owl-carousel
	$data_options['items']  			= $args['item_number'];
	$data_options['slideBy']            = $args['slides_to_scroll'];
	$data_options['margin']             = $args['margin_items'];
	$data_options['autoplayHoverPause'] = $args['pause_on_hover'] === 'yes' ? true : false;
	$data_options['loop']               = $args['infinite'] === 'yes' ? true : false;
	$data_options['autoplay']           = $args['autoplay'] === 'yes' ? true : false;
	$data_options['autoplayTimeout']    = $args['autoplay_speed'];
	$data_options['smartSpeed']         = $args['smartspeed'];
	$data_options['dots']               = false;
	$data_options['nav']               	= $args['nav_control'] === 'yes' ? true : false;
	$data_options['rtl']				= is_rtl() ? true: false;

	// get data
	$taxonomy 			= "product_cat";
	$terms 				= get_terms( $taxonomy, array('orderby' => 'slug', 'hide_empty' => true, ));
	
	?>

	<div class="ova_product_categories owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)); ?>">
		<?php foreach ($terms as $term) : ?>
			<?php
				$thumbnail_id 	= get_term_meta($term->term_id, 'thumbnail_id', true);
				$thumbnail_url 	= wp_get_attachment_url($thumbnail_id) ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src('thumbnail');
				$name  			= $term->name;
				$link 			= get_term_link( $term->term_id, $taxonomy );
			?>
			<div class="item">
				<div class="image-thumbnail">
					<img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_html($name); ?>">
					

					<?php if( $text_view_details != '' && isset($link) ): ?>
						<a href="<?php echo esc_url( $link ); ?>" class="btn read-more">
							<span>
								<?php echo esc_html($text_view_details); ?>
							</span>
							<?php if( $icon ){ ?>
								<?php 
							        \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
							    ?>
							<?php } ?>
						</a>
					<?php endif; ?>

				</div>
				<h4 class="title">
					<a href="<?php echo esc_url( $link ); ?>" >
						<?php echo esc_html($name); ?>
					</a>
				</h4>
			</div>
		<?php endforeach; ?>

	</div>