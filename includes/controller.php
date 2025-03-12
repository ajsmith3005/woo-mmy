<?php

namespace Woommy\Controller;

// Add javascript
function add_custom_scripts() {

	// register the script
	wp_register_script( 
		'woommy-main-js', 
		woommy_plugin_url('/assets/js/index.js'), 
		array(),
		false,
		array(
			'strategy' => 'defer'
		)
	);

	//enqueue the script
	wp_enqueue_script( 'woommy-main-js' );

}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\add_custom_scripts' );

function add_custom_styles() {

	// register the style
	wp_register_style( 
		'woommy-css',
		woommy_plugin_url('/assets/css/styles.css')
	);

	//enqueue the style
	wp_enqueue_style( 'woommy-css' );

}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\add_custom_styles' );
