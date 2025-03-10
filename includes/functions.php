<?php


function woommy_plugin_url( $path = '' ) {
	$url = plugins_url( $path, WOOMMY_PLUGIN );

	if ( is_ssl()
	and 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}

// Add javascript
function add_custom_scripts() {

	// register the script
	wp_register_script( 
		'woommy-main-js', 
		woommy_plugin_url('/includes/js/index.js'), 
		array(),
		false,
		array(
			'strategy' => 'defer'
		)
	);

	//enqueue the script
	wp_enqueue_script( 'woommy-main-js' );

}
add_action( 'wp_enqueue_scripts', 'add_custom_scripts' );

function add_custom_styles() {

	// register the style
	wp_register_style( 
		'rps-main-css',
		woommy_plugin_url('/assets/css/styles.css')
	);

	//enqueue the style
	wp_enqueue_style( 'rps-main-css' );

}
add_action( 'wp_enqueue_scripts', 'add_custom_styles' );

