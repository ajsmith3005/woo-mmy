<?php
/**
 * Shortcodes
 */

namespace Woommy\Shortcodes;

/**
 * MMY Form Shortcode
 * 
 * Custom shortcode to add year, make, model search form.
 * 
 * @return string HTML for the MMY form
 */
function make_model_year_shortcode(): string {
	$model_options = get_model_options();
	$year_options = get_year_options();
	
	return '
		<form id="make-model-year-form" action="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) .  '" method="get">
			<div class="container">
				<select id="make" name="make" required>
					<option value="">Make</option>'. get_make_options() .
				'</select>
				<select id="model" name="model" ' . $model_options['disabled'] . ' required>
					<option value="">Model</option>' . $model_options['options'] . 
				'</select>
				<select id="car-year" name="car-year" ' . $year_options['disabled'] . ' required>
					<option value="">Year</option>' . $year_options['options'] .
				'</select>
			</div>
			<div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex">
				<div class="wp-block-button"><button class="wp-block-button__link wp-element-button" type="submit">Search</button></div>
			</div>
		</form>
	';
}

add_shortcode('make-model-year-form', __NAMESPACE__ . '\make_model_year_shortcode');

/**
 * Get Make Options
 * 
 * Prepares the "make" options elements for use within a select element.
 * 
 * @return string A concatenated string of options elements
 */
function get_make_options(): string {
	$terms = get_terms( array( 
		'taxonomy' => 'woommy-car-details',
	) );

	$makes = array();
	$make_options = '';

	// Iterate over the custom taxonomy terms array and build an array of
	// make names that have child models with the specified year
	foreach ( $terms as $term ) {
		if ( 0 === $term->parent ) {
			$make = array(
				'slug' => $term->slug,
				'name' => $term->name
			);
			if ( $make && ! in_array( $make["slug"], array_column( $makes, "slug" ) ) ) {
				array_push( $makes, $make );
			}
		}
		
	}

	foreach( $makes as $make ) {
		$make_is_selected = '';
		 
		if( is_shop() && isset( $_GET['make'] ) && $make["slug"] === $_GET['make'] ) {
			$make_is_selected = 'selected'; 
		}
		
		$make_options .= '<option value="' . $make["slug"] . '" ' . $make_is_selected . '>' . $make["name"] . '</option>';
	}

	return $make_options;
 }

/**
 * Get Model Options
 * 
 * Prepares the "model" options elements for use within a select element.
 * 
 * @return array Associative array of model options
 */
function get_model_options(): array {
	$model_options = array(
		'disabled' => 'disabled',
		'options' => ''
	);
	
	if( is_shop() && isset( $_GET['make'] ) && isset( $_GET['model'] ) && isset( $_GET['car-year']) ) {
		$model_options['disabled'] = '';

		$models = \Woommy\RestApi\get_models( $_GET['make'] );

		foreach( $models as $model ) {
			$model_is_selected = '';

			if( $model["slug"] === $_GET['model'] ) {
				$model_is_selected = 'selected';
			}
			
			$model_options['options'] .= '<option value="' . $model["slug"] . '" ' . $model_is_selected . '>' . $model["name"] . '</option>';
		}
	} 

	return $model_options;
}

/**
 * Get Year Options
 * 
 * Prepares the "year" options elements for use within a select element.
 * 
 * @return array Associative array of year options
 */
function get_year_options(): array {
	$year_options = array(
		'disabled' => 'disabled',
		'options' => ''
	);
	
	if( is_shop() && isset( $_GET['make'] ) && isset( $_GET['model'] ) && isset( $_GET['car-year']) ) {
		$year_options['disabled'] = '';

		$years = \Woommy\RestApi\get_years( $_GET['model'] );

		foreach( $years as $year ) {
			$year_is_selected = '';

			if( $year["slug"] === $_GET['car-year'] ) {
				$year_is_selected = 'selected';
			}
			
			$year_options['options'] .= '<option value="' . $year["slug"] . '" ' . $year_is_selected . '>' . $year["name"] . '</option>';
		}
	} 

	return $year_options;
}
