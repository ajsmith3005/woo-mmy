<?php
/*
 * Plugin Name:       WooMMY
 * Description:       Search products by Make, Model, and Year by using custom taxonomies to categorize them.
 * Version:           0.2.0
 * Author:            Andrew Smith
 * Author URI:        https://andycodes.net/
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Requires Plugins:  woocommerce
 */

 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'WOOMMY_VERSION', '0.2.0' );

define( 'WOOMMY_PLUGIN' , __FILE__ );

define( 'WOOMMY_PLUGIN_DIR', untrailingslashit( dirname( WOOMMY_PLUGIN) ) );

require_once( WOOMMY_PLUGIN_DIR . '/includes/controller.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/functions.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/create-taxonomy.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/shortcodes.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/rest-api.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/product-options.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/taxonomy-query.php' );

function woommy_delete_plugin() {

	$terms = get_terms(
		array(
			'number' => '',
			'taxonomy' => 'woommy-car-details',
			'post_status' => 'any',
			'hide_empty' => false
		)
	);

	foreach ( $terms as $term ) {
		wp_delete_term( $term->term_id, 'woommy-car-details' );
	}

	$meta_key = "_make_model_year_information";

	$posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type' => 'product',
			'post_status' => 'any',
		)
		);

	foreach ( $posts as $post ) {
		delete_post_meta( $post->ID, $meta_key );
	}
}

register_uninstall_hook( __FILE__, 'woommy_delete_plugin' );