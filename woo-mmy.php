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

	global $wpdb;

	$woommy_taxonomy = 'woommy-car-details';
	
	$term_taxonomy_ids = $wpdb->get_col( $wpdb->prepare( "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s", $woommy_taxonomy ) );
	if ( ! empty( $term_taxonomy_ids ) ) {
		$term_ids = $wpdb->get_col( $wpdb->prepare( "SELECT term_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s", $woommy_taxonomy ) );
		$term_taxonomy_ids_list = implode( ',', array_map( 'intval', $term_taxonomy_ids ) );
		$term_ids_list = implode( ',', array_map( 'intval', $term_ids ) );

		$wpdb->query( "DELETE FROM {$wpdb->term_taxonomy} WHERE term_taxonomy_id IN ({$term_taxonomy_ids_list})" );
		$wpdb->query( "DELETE FROM {$wpdb->terms} WHERE term_id IN ({$term_ids_list})" );
		$wpdb->query( "DELETE FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ({$term_taxonomy_ids_list})" );
	}

	
	$posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type' => 'product',
			'post_status' => 'any',
			)
		);
	
	$meta_key = "_make_model_year_information";
	
	foreach ( $posts as $post ) {
		delete_post_meta( $post->ID, $meta_key );
	}
}

register_uninstall_hook( __FILE__, 'woommy_delete_plugin' );