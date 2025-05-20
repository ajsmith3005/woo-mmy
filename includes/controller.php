<?php
/**
 * Controller
 */

namespace Woommy\Controller;

/**
 * Adds custom JavaScript.
 * 
 * The main woommy JavaScript file is registered with defer and enqueued.
 */
function add_custom_scripts(): void {
	wp_register_script( 
		'woommy-main-js', 
		woommy_plugin_url('/assets/js/index.js'), 
		array(),
		false,
		array(
			'strategy' => 'defer'
		)
	);

	wp_enqueue_script( 'woommy-main-js' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\add_custom_scripts' );


/**
 * Adds custom styles.
 * 
 * The main woommy stylesheed is registered and enqueued.
 */
function add_custom_styles(): void {
	wp_register_style( 
		'woommy-css',
		woommy_plugin_url('/assets/css/styles.css')
	);

	wp_enqueue_style( 'woommy-css' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\add_custom_styles' );
