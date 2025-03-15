<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

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

	return true;
}

if ( ! defined( 'WOOMMY_VERSION' ) ) {
	woommy_delete_plugin();
}
