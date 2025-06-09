<?php
$model_options = Woommy\Shortcodes\get_model_options();
$year_options = Woommy\Shortcodes\get_year_options();
$wrapper_attributes = get_block_wrapper_attributes();

$submit_text = ( $attributes['submitText'] !== '' ) ? $attributes['submitText'] : 'Search'; 

echo '
	<form id="make-model-year-form" ' . $wrapper_attributes . ' action="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) .  '" method="get">
		<div class="container">
			<select id="make" name="make" required>
				<option value="">Make</option>'. Woommy\Shortcodes\get_make_options() .
			'</select>
			<select id="model" name="model" ' . $model_options['disabled'] . ' required>
				<option value="">Model</option>' . $model_options['options'] . 
			'</select>
			<select id="car-year" name="car-year" ' . $year_options['disabled'] . ' required>
				<option value="">Year</option>' . $year_options['options'] .
			'</select>
		</div>
		<div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex">
			<div class="wp-block-button"><button class="wp-block-button__link wp-element-button" type="submit">' . $submit_text . '</button></div>
		</div>
	</form>
';
?>