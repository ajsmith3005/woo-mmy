<?php
// This file is generated. Do not modify it manually.
return array(
	'mmy-form' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'woommy/mmy-form',
		'version' => '0.1.0',
		'title' => 'Mmy Form',
		'category' => 'woommy',
		'icon' => 'forms',
		'description' => 'Make Model Year form.',
		'keywords' => array(
			'form',
			'make',
			'model',
			'year',
			'search',
			'woommy'
		),
		'example' => array(
			
		),
		'attributes' => array(
			'submitText' => array(
				'type' => 'string',
				'default' => 'Search'
			)
		),
		'supports' => array(
			'html' => false,
			'color' => array(
				'background' => true
			)
		),
		'textdomain' => 'mmy-form',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js',
		'render' => 'file:./render.php'
	)
);
