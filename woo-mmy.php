<?php
/*
 * Plugin Name:       WooMMY
 * Description:       Search products by Make, Model, and Year by using custom taxonomies to categorize them.
 * Version:           0.1.0
 * Author:            Andrew Smith
 * Author URI:        https://andycodes.net/
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Requires Plugins:  woocommerce
 */

 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'WOOMMY_PLUGIN' , __FILE__ );
define( 'WOOMMY_PLUGIN_DIR', untrailingslashit( dirname( WOOMMY_PLUGIN) ) );

require_once( WOOMMY_PLUGIN_DIR . '/includes/functions.php' );

/**
 *Adds custom taxonomies
 */
function woommy_create_taxonomies() {
	$labels = array(
		'name'              =>_X('Makes and Models', 'taxonomy general name', 'textdomain'),
		'singular_name'     => _X('Make and Model', 'taxonomy singular name', 'textdomain'),
		'search_items'      => __( 'Search Makes and Models', 'textdomain' ),
		'all_items'         => __( 'All Makes and Models', 'textdomain' ),
		'parent_item'       => __( 'Parent Make', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Make:', 'textdomain' ),
		'edit_item'         => __( 'Edit Make or Model', 'textdomain' ),
		'update_item'       => __( 'Update Make or Model', 'textdomain' ),
		'add_new_item'      => __( 'Add New Make or Model', 'textdomain' ),
		'new_item_name'     => __( 'New Make or Model Name', 'textdomain' ),
		'menu_name'         => __( 'Makes and Models', 'textdomain' ),
	);

	$args = array(
		'hierarchical' => true,
		'labels'       => $labels,
		'show_ui'      => true,
		'show_in_rest' => true,
	);

	register_taxonomy( 'woommy-car-details', 'product', $args);
}

add_action( 'init', 'woommy_create_taxonomies');

/**
 * Add a custom field to the product edit page to input the make, model, and year.
 */
function add_field() {
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

add_action( 'woocommerce_product_options_general_product_data', 'add_field' );

/**
 * Save the year, make, and model information for a product.
 */
function save_field( $post_id, $post ) {
	if ( ! isset( $_POST['_make_model_year_information'] ) ) {
		return;
	}

	$car_input = $_POST['_make_model_year_information'];

	$car_list = preg_split( '/\r\n|\r|\n/', $car_input );

	$term_id_array = array();

	foreach( $car_list as $car ) {

		$car_array = explode( ',', $car );
		
		$make  = array_key_exists( 0, $car_array ) ? trim( $car_array[0] ) : '';
		$model = array_key_exists( 1, $car_array ) ? trim( $car_array[1] ) : '';
		$years = array_key_exists( 2, $car_array ) ? trim( $car_array[2] ) : '';

		if ( '' === $make ) {
			continue;
		}

		/**
		 * Make Term
		 */
		$make_term = get_term_by( 'slug', sanitize_title( $make ), 'woommy-car-details' );
		
		if ( ! $make_term ) {
			wp_insert_term( sanitize_text_field( $make ), 'woommy-car-details' );
			$make_term = get_term_by( 'slug', sanitize_title( $make ), 'woommy-car-details' );
		}

		array_push( $term_id_array, $make_term->term_id );

		/**
		 * Model Term
		 */
		$model_term = get_term_by( 'slug', sanitize_title( $model ), 'woommy-car-details' );
		
		if ( '' !== $model && ! $model_term ) {
			wp_insert_term( sanitize_text_field( $model ), 'woommy-car-details', array( 'parent' => $make_term->term_id ) );
			$model_term = get_term_by( 'slug', sanitize_title( $model ), 'woommy-car-details' );
		}

		array_push( $term_id_array, $model_term->term_id );

		/**
		 * Year Term
		 */
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

add_action( 'woocommerce_process_product_meta', 'save_field', 10, 2 );

/**
 * Custom shortcode to add year, make, model search form
 */
function make_model_year_shortcode() {
	
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

add_shortcode('make-model-year-form', 'make_model_year_shortcode');

function get_make_options() {
	$terms = get_terms( array( 
		'taxonomy' => 'woommy-car-details',
	) );

	//iterate over the custom taxonomy terms array and build an array of make names that have child models with the specified year
	$makes = array();
	$make_options = '';
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

 function get_models( $selected_make ) {
	$terms = get_terms( array( 
		'taxonomy' => 'woommy-car-details',
	) );

	//iterate over the custom taxonomy terms array and build an array of model names that have parents of the specified make
	$models = array();
	$make_term = get_term_by( 'slug', sanitize_title($selected_make), 'woommy-car-details' );
	$make_id = $make_term->term_id;

	foreach ( $terms as $term ) {
		if ( $term->parent === $make_id ) {
			$model = array(
				"slug" => $term->slug,
				"name" => $term->name
			);

			if ( ! in_array( $model["slug"], array_column( $models, "slug" ) ) ) {
				array_push( $models, $model );
			}
		}
	}

	return $models;
}

function get_model_options() {
	
	$model_options = array(
		'disabled' => 'disabled',
		'options' => ''
	);
	
	if( is_shop() && isset( $_GET['make'] ) && isset( $_GET['model'] ) && isset( $_GET['car-year']) ) {

		$model_options['disabled'] = '';

		$models = get_models( $_GET['make'] );

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

function get_years( $selected_model ) {
	$terms = get_terms( array( 
		'taxonomy' => 'woommy-car-details',
	) );

	//iterate over the custom taxonomy terms array and build an array of year slugs and names that have the specified parent model
	$selected_model_id = get_term_by('slug', sanitize_title( $selected_model ), 'woommy-car-details')->term_id;
	$years = array();

	foreach ( $terms as $term ) {
		if ( $term->parent === $selected_model_id ) {
			$year = array(
				"slug" => $term->slug,
				"name" => $term->name
			);
			
			if ( $year && ! in_array( $year["slug"], array_column( $years, "slug" ) ) ) {
				array_push( $years, $year );
			}
		}	
	}

	return $years;
}

function get_year_options() {
	
	$year_options = array(
		'disabled' => 'disabled',
		'options' => ''
	);
	
	if( is_shop() && isset( $_GET['make'] ) && isset( $_GET['model'] ) && isset( $_GET['car-year']) ) {

		$year_options['disabled'] = '';

		$years = get_years( $_GET['model'] );

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

//Custom enpoint to grab models from the custom taxonomy based on selected make
function custom_model_endpoint( WP_REST_Request $request ) {
	$selected_make = $request->get_param( 'selected_make' );

	$models = get_models( $selected_make );

	return rest_ensure_response( $models );
}

//Custom enpoint to grab years from the custom taxonomy based on selected model
function custom_year_endpoint( WP_REST_Request $request ) {
	$selected_model = $request->get_param('selected_model');

	$years = get_years( $selected_model );
	return rest_ensure_response( $years );
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'woommy/v1', '/models/', array(
		'methods' => 'GET',
		'callback' => 'custom_model_endpoint',
		'permission_callback' => '__return_true',
	) );
	register_rest_route( 'woommy/v1', '/years/', array(
	  'methods' => 'GET',
	  'callback' => 'custom_year_endpoint',
	  'permission_callback' => '__return_true',
	) );
} );