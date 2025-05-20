<?php
/**
 * Taxonomy Query
 */

namespace Woommy\TaxonomyQuery;

/**
 * WooMMY Taxonomy Query
 * 
 * Alters the WooCommerce product query to filter the displayed products
 * by the selected make, model, and year.
 */
 	
 function add_woommy_tax_query( $query ): void {
	if ( is_shop() && isset( $_GET['make'] ) && isset( $_GET['model'] ) && isset( $_GET['car-year']) ) {
		$query->set( 'tax_query', array(
			array(
				'taxonomy' => 'woommy-car-details',
				'field'    => 'slug',
				'terms'    => $_GET['car-year'],
			),
		) );
	}
}

add_action( 'woocommerce_product_query', __NAMESPACE__ . '\add_woommy_tax_query' );
