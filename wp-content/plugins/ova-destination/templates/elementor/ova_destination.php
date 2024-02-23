<?php

    $destinations  = ovadestination_get_data_destination_el( $args );
    $args['flag'] = 1;

?>
		
<div class="ova-destination">

	<div class="content content-destination">

		<div class="grid-sizer"></div>

		<?php if($destinations->have_posts() ) : while ( $destinations->have_posts() ) : $destinations->the_post(); ?>

			<?php ovadestination_get_template( 'part/item-destination.php', $args ); ?>

			<?php $args['flag'] += 1; ?>

		<?php endwhile; endif; wp_reset_postdata(); ?>

	</div>

</div>