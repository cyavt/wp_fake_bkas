<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();

$product = wc_get_product( $product_id );

if ( !$product || !$product->is_type('ovabrw_car_rental') ) return;

$list_extra_fields = ovabrw_get_list_field_checkout( $product_id );

if ( !empty( $list_extra_fields ) && is_array( $list_extra_fields ) ):
	foreach( $list_extra_fields as $key => $field ):
		if ( array_key_exists( 'enabled', $field ) && $field['enabled'] == 'on' ):
			$class_field = $class_required = '';

			if ( ovabrw_check_array( $field, 'required' ) ) {
				$class_required = 'required';
			}

			if ( $field['class'] && $class_required ) {
				$class_field = $field['class'] . ' ' . $class_required;
			} else if ( !$field['class'] && $class_required ) {
				$class_field = $class_required;
			} else {
				$class_field = $field['class'];
			}

	?>
		<div class="rental_item">
			<label>
				<?php echo esc_html( $field['label'] ); ?>
			</label>
			<?php if ( 'textarea' !== $field['type'] && 'select' !== $field['type'] ): ?>
				<input 
					type="<?php echo esc_attr( $field['type'] ); ?>" 
					name="<?php echo esc_attr( $key ); ?>"  
					class="<?php echo esc_attr( $class_field ); ?>" 
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
					value="<?php echo esc_attr( $field['default'] ); ?>" 
					data-error="<?php echo sprintf( esc_html__( '%s is required.', 'tripgo' ), esc_attr( $field['label'] ) ); ?>" />
			<?php endif; ?>

			<?php if( 'textarea' === $field['type'] ): ?>
				<textarea 
					name="<?php echo esc_attr( $key ); ?>" 
					class="<?php echo esc_attr( $class_field ); ?>" 
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
					value="<?php echo esc_attr( $field['default'] ); ?>" 
					rows="5" 
					data-error="<?php echo sprintf( esc_html__( '%s is required.', 'tripgo' ), esc_attr( $field['label'] ) ); ?>"></textarea>
			<?php endif; ?>

			<?php if ( 'select' === $field['type'] ): 
				$ova_options_key = $ova_options_text = [];

				if ( array_key_exists( 'ova_options_key', $field ) ) {
					$ova_options_key = $field['ova_options_key'];
				}

				if ( array_key_exists( 'ova_options_text', $field ) ) {
					$ova_options_text = $field['ova_options_text'];
				}
			?>
				<select name="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $class_field ); ?>" data-error="<?php echo sprintf( esc_html__( '%s is required.', 'tripgo' ), esc_attr( $field['label'] ) ); ?>">
				<?php 
					if ( !empty( $ova_options_text ) && is_array( $ova_options_text ) ): ?>
						<option value="">
							<?php echo sprintf( esc_html__( 'Select %s', 'tripgo' ), esc_attr( $field['label'] ) ); ?>
						</option>
						<?php foreach( $ova_options_text as $key => $text ): 
							$selected = '';
							if ( $ova_options_key[$key] == $field['default'] ) {
								$selected = 'selected';
							}

							$value = '';

							if ( ovabrw_check_array( $ova_options_key, $key ) ) {
								$value = $ova_options_key[$key];
							}
				?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $value ); ?>">
								<?php echo esc_html( $text ); ?>
							</option>
				<?php endforeach; endif; ?>
				</select>
			<?php endif; ?>
		</div>
<?php endif; endforeach; endif; ?>