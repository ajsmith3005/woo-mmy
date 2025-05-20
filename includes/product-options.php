<?php
/**
 * Product Options
 */

namespace Woommy\ProductOptions;

/**
 * Add Field
 * 
 * Adds a custom text area field to the product edit page for inputting 
 * the make, model, and year.
 */
function add_field(): void {
	global $product_object;
	?>
	<div class="options_group show_if_simple show_if_variable">
		<?php woocommerce_wp_textarea_input(
			array(
				'id'      	=> '_make_model_year_information',
				'label'   	=> __( 'Make, Model, Year', 'woo_product_field' ),
				'description' => __( 'Input make model and year with the following format:<br> "Make,Model,StartYear-EndYear" <br><br> If product is for a single year: <br> "Make,Model,Year" <br><br> If part fits multiple vehicles, add multiple lines following the previously described format.', 'woo_product_field' ),
				'desc_tip'	=> true,
				'value' => $product_object->get_meta( '_make_model_year_information' ),
				'placeholder' => __('Make, Model, StartYear-EndYear'),
				'rows' => '5',
				'style' => 'height: unset;'
			)
		); ?>
	</div>
	<?php
}

add_action( 'woocommerce_product_options_general_product_data', __NAMESPACE__ . '\add_field' );

/**
 * Save Field
 * 
 * Saves the year, make, and model information for a product.
 * 
 * @param int $post_id
 * @param WP_Post $post
 */
function save_field( $post_id, $post ): void {
	if ( ! isset( $_POST['_make_model_year_information'] ) ) {
		return;
	}

	$car_input = $_POST['_make_model_year_information'];
	$car_list = preg_split( '/\r\n|\r|\n/', $car_input );
	$term_id_array = array();

	foreach( $car_list as $car ) {

		$car_array = explode( ',', $car );
		
		$make  = array_key_exists( 0, $car_array ) ? ucwords(trim( $car_array[0] )) : '';
		$model = array_key_exists( 1, $car_array ) ? ucwords(trim( $car_array[1] )) : '';
		$years = array_key_exists( 2, $car_array ) ? trim( $car_array[2] ) : '';

		if ( '' === $make ) {
			continue;
		}

		// Add make term
		$make_term = get_term_by( 'slug', sanitize_title( $make ), 'woommy-car-details' );
		
		if ( ! $make_term ) {
			wp_insert_term( sanitize_text_field( $make ), 'woommy-car-details' );
			$make_term = get_term_by( 'slug', sanitize_title( $make ), 'woommy-car-details' );
		}

		array_push( $term_id_array, $make_term->term_id );

		// Add model term
		$model_term = get_term_by( 'slug', sanitize_title( $model ), 'woommy-car-details' );
		
		if ( '' !== $model && ! $model_term ) {
			wp_insert_term( sanitize_text_field( $model ), 'woommy-car-details', array( 'parent' => $make_term->term_id ) );
			$model_term = get_term_by( 'slug', sanitize_title( $model ), 'woommy-car-details' );
		}

		array_push( $term_id_array, $model_term->term_id );

		// Add year term
		$years_array = explode( '-', $years );

		if ( count( $years_array ) > 1 ) {
			$years_array = range( $years_array[0], end($years_array) );
		}

		$count = 0;
		foreach( $years_array as $year ) {
			if ( $count > 49 ) {
				exit;
			}

			$year_term_slug = sanitize_title( $year ) . '_' . sanitize_title( $make ) . '_' . sanitize_title( $model );
			$year_term = get_term_by( 'slug', $year_term_slug, 'woommy-car-details' );
			
			if ( '' !== $year && ! $year_term ) {
				wp_insert_term( sanitize_text_field( $year ), 'woommy-car-details', array( 'parent' => $model_term->term_id, 'slug' => $year_term_slug ) );
				$year_term = get_term_by( 'slug', $year_term_slug, 'woommy-car-details' );
			}

			array_push( $term_id_array, $year_term->term_id );

			$count++;
		}
	}

	wp_set_post_terms( $post_id, $term_id_array, 'woommy-car-details' );

	$product = wc_get_product( intval( $post_id ) );
	$product->update_meta_data( '_make_model_year_information', sanitize_textarea_field( $car_input ) );
	$product->save_meta_data();
}

add_action( 'woocommerce_process_product_meta', __NAMESPACE__ . '\save_field', 10, 2 );
