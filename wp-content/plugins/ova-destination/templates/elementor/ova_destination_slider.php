<?php

$data_options['items']              = $args['item_number'];
$data_options['slideBy']            = $args['slides_to_scroll'];
$data_options['margin']             = $args['margin_items'];
$data_options['autoplayHoverPause'] = $args['pause_on_hover'] === 'yes' ? true : false;
$data_options['loop']               = $args['infinite'] === 'yes' ? true : false;
$data_options['autoplay']           = $args['autoplay'] === 'yes' ? true : false;
$data_options['autoplayTimeout']    = $args['autoplay_speed'];
$data_options['smartSpeed']         = $args['smartspeed'];
$data_options['nav']                = $args['nav_control'] === 'yes' ? true : false;

$destinations = ovadestination_get_data_destination_slider_el( $args );

?>

<div class="ova-destination-slider">

	<div class="content slide-destination owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)) ?>">

		<?php if( $destinations->have_posts() ) : while ( $destinations->have_posts() ) : $destinations->the_post(); ?>

            <?php ovadestination_get_template( 'part/item-destination.php', $args ); ?>

		<?php endwhile; endif; wp_reset_postdata(); ?>

	</div>

</div>
