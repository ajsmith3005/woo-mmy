<?php

namespace Woommy\CreateTaxonomies;

/**
 *Adds custom taxonomies
 */
function woommy_create_taxonomies() {
	$labels = array(
		'name'              =>_X('Makes and Models', 'taxonomy general name', 'textdomain'),
		'singular_name'     => _X('Make and Model', 'taxonomy singular name', 'textdomain'),
		'search_items'      => __( 'Search Makes and Models', 'textdomain' ),
		'all_items'         => __( 'All Makes and Models', 'textdomain' ),
		'parent_item'       => __( 'Parent Make', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Make:', 'textdomain' ),
		'edit_item'         => __( 'Edit Make or Model', 'textdomain' ),
		'update_item'       => __( 'Update Make or Model', 'textdomain' ),
		'add_new_item'      => __( 'Add New Make or Model', 'textdomain' ),
		'new_item_name'     => __( 'New Make or Model Name', 'textdomain' ),
		'menu_name'         => __( 'Makes and Models', 'textdomain' ),
	);

	$args = array(
		'hierarchical' => true,
		'labels'       => $labels,
		'show_ui'      => true,
		'show_in_rest' => true,
	);

	register_taxonomy( 'woommy-car-details', 'product', $args);
}

add_action( 'init', __NAMESPACE__ . '\woommy_create_taxonomies');