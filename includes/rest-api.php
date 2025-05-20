<?php
/**
 * Rest API
 */

namespace Woommy\RestApi;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Get Models
 * 
 * Iterates over the custom taxonomy terms and returns an array of models
 * based on the input parent make.
 * 
 * @param string $selected_make 
 * @return array Array of arrays containing term slugs and names
 */
function get_models( string $selected_make ): array {
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

/**
 * Get Years
 * 
 * Iterates over the custom taxonomy terms and returns an array of years
 * that have the specified parent model.
 * 
 * @param string $selected_model
 * @return array Array of arrays containing term slugs and names
 */
function get_years( string $selected_model ): array {
	$terms = get_terms( array( 
		'taxonomy' => 'woommy-car-details',
	) );

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

/**
 * Model Endpoint
 * 
 * Custom enpoint to grab models from the custom WooMMY taxonomy based on selected make.
 * 
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */
function custom_model_endpoint( WP_REST_Request $request ): WP_REST_Response|WP_Error {
	$selected_make = $request->get_param( 'selected_make' );

	if( ! is_string( $selected_make ) ) {
		$error = sprintf( 'Error: Variable $selected_make in %s must be a string, but received %s.', __FUNCTION__, gettype( $selected_make));
		error_log( $error );
		return new WP_Error(
			'woo-mmy',
			$error
		);
	}

	$models = get_models( $selected_make );

	return rest_ensure_response( $models );
}

/**
 * Model Endpoint
 * 
 * Custom enpoint to grab years from the custom WooMMY taxonomy based on selected model.
 * 
 * @param WP_REST_Request $request
 * @return WP_REST_Response 
 */
function custom_year_endpoint( WP_REST_Request $request ) {
	$selected_model = $request->get_param('selected_model');

	if( ! is_string( $selected_model ) ) {
		$error = sprintf( 'Error: Variable $selected_model in %s must be a string, but received %s.', __FUNCTION__, gettype( $selected_model));
		error_log( $error );
		return new WP_Error(
			'woo-mmy',
			$error
		);
	}

	$years = get_years( $selected_model );

	return rest_ensure_response( $years );
}

/**
 * Register REST Routes
 * 
 * Registers the routes for the WooMMY REST API.
 */
function register_rest_routes(): void {
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
