<?php

function woommy_plugin_url( $path = '' ) {
	$url = plugins_url( $path, WOOMMY_PLUGIN );

	if ( is_ssl()
	and 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}
