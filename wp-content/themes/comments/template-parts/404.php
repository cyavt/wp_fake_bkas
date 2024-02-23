<div class="ova_404_page">
	<img src="<?php echo get_template_directory_uri() . '/assets/img/base/404.png'; ?>" alt="404 not found"> 

	<h2 class="title">
		<?php echo esc_html__( 'Opps! That Links Is Broken.', 'tripgo' ); ?>
	</h2>
	<div class="description">
		<p >
			<?php echo esc_html__( 'Page does not exist or some other error occured. Go to our', 'tripgo' ); ?>
		</p>

		<a href="<?php echo esc_url( home_url() ); ?>"> <?php echo esc_html__(' Home Page', 'tripgo'); ?></a>
	</div>

	<div class="ova-go-home">
		<a href="<?php echo esc_url( home_url() ); ?>">
			<?php echo esc_html__( 'Go Home', 'tripgo' ); ?>
		</a>
	</div>
</div>