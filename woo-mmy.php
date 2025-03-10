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

require_once( WOOMMY_PLUGIN_DIR . '/includes/controller.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/functions.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/create-taxonomy.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/shortcodes.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/rest-api.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/product-options.php' );
require_once( WOOMMY_PLUGIN_DIR . '/includes/taxonomy-query.php' );