<?php if ( !defined( 'ABSPATH' ) ) exit();

get_header();

$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

$args['flag']  = 1;


?>
<div class="row_site">
	<div class="container_site">

		<div class="archive_destination">
			
			<div class="content content-archive-destination">

				<div class="grid-sizer"></div>

				<?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<?php ovadestination_get_template( 'part/item-destination.php', $args ); ?>

					<?php $args['flag'] += 1; ?>

				<?php endwhile; endif; wp_reset_postdata(); ?>
				
			</div>
			
			<?php 
	    		 $args = array(
	                'type'      => 'list',
	                'next_text' => '<i class="ovaicon-next"></i>',
	                'prev_text' => '<i class="ovaicon-back"></i>',
	            );

	            the_posts_pagination($args);
	    	 ?>
		

		</div>
	</div>
</div>

<?php 
 get_footer();