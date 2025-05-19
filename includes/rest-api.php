<?php

namespace Woommy\RestApi;

use WP_REST_Request;

/**
 * Get Models
 * 
 * Iterates over the custom taxonomy terms and returns an array of models
 * based on the input parent make.
 * 
 * @param str $selected_make 
 * @return array Array of terms
 */
function get_models( $selected_make ) {
	$terms = get_terms( array( 
		'taxonomy' => 'woommy-car-details',
	) );

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

//Custom enpoint to grab models from the custom taxonomy based on selected make
function custom_model_endpoint( WP_REST_Request $request ) {
	$selected_make = $request->get_param( 'selected_make' );

	if( ! is_string( $selected_make ) ) {
		error_log( sprintf( 'Error: Variable $selected_make in %s must be a string, but received %s.', __FUNCTION__, gettype( $selected_make)));
		return;
	}

	$models = get_models( $selected_make );

	return rest_ensure_response( $models );
}

//Custom enpoint to grab years from the custom taxonomy based on selected model
function custom_year_endpoint( WP_REST_Request $request ) {
	$selected_model = $request->get_param('selected_model');

	if( ! is_string( $selected_model ) ) {
		error_log( sprintf( 'Error: Variable $selected_model in %s must be a string, but received %s.', __FUNCTION__, gettype( $selected_model)));
		return;
	}

	$years = get_years( $selected_model );
	return rest_ensure_response( $years );
}

function register_rest_routes() {
	register_rest_route( 'woommy/v1', '/models/', array(
		'methods' => 'GET',
		'callback' => 'Woommy\RestApi\custom_model_endpoint',
		'permission_callback' => '__return_true',
	) );
	register_rest_route( 'woommy/v1', '/years/', array(
	  'methods' => 'GET',
	  'callback' => 'Woommy\RestApi\custom_year_endpoint',
	  'permission_callback' => '__return_true',
	) );
}

add_action( 'rest_api_init', __NAMESPACE__ . '\register_rest_routes' );
