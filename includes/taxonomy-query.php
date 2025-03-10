<?php

/**
 * Apply filers submitted by the rps-mmy form on the shop page
 */
 	
 function add_woommy_tax_query( $query ) {
	if ( is_shop() && isset( $_GET['make'] ) && isset( $_GET['model'] ) && isset( $_GET['car-year']) ) {
		//modify the query parameters
		$query->set( 'tax_query', array(
			array(
				'taxonomy' => 'woommy-car-details',
				'field'    => 'slug',
				'terms'    => $_GET['car-year'],
			),
		) );
	}
}

add_action( 'woocommerce_product_query', 'add_woommy_tax_query' );