<?php
	// Custom Post Types
	function cptui_register_my_cpts_ptbt_subscriber() {

		/**
		 * Post Type: Subscribers.
		 */
	
		$labels = array(
			"name" => __( "Subscribers", "pick_the_best_team" ),
			"singular_name" => __( "Subscriber", "pick_the_best_team" ),
		);
	
		$args = array(
			"label" => __( "Subscribers", "pick_the_best_team" ),
			"labels" => $labels,
			"description" => "",
			"public" => false,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => false,
			"show_in_menu" => false,
			"exclude_from_search" => true,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "ptbt_subscriber", "with_front" => false ),
			"query_var" => true,
			"supports" => false,
		);
	
		register_post_type( "ptbt_subscriber", $args );
	}
	
	add_action( 'init', 'cptui_register_my_cpts_ptbt_subscriber' );
	

	// ACF Fields
	
	if( function_exists('acf_add_local_field_group') ):
	
	acf_add_local_field_group(array(
		'key' => 'group_5a3fd79a93e23',
		'title' => 'Subscriber Details',
		'fields' => array(
			array(
				'key' => 'field_5a3fd7b3740c3',
				'label' => 'First name',
				'name' => 'ptbt_fname',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5a3fd7ca740c4',
				'label' => 'Last Name',
				'name' => 'ptbt_lname',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5a3fd7fb740c5',
				'label' => 'Email Address',
				'name' => 'ptbt_email',
				'type' => 'email',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_5a3fd9d3f8d5e',
				'label' => 'Subscriptions',
				'name' => 'ptbt_subscriptions',
				'type' => 'post_object',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array(
					0 => 'ptbt_list',
				),
				'taxonomy' => array(
				),
				'allow_null' => 1,
				'multiple' => 1,
				'return_format' => 'object',
				'ui' => 1,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'ptbt_subscriber',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'acf_after_title',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => array(
			0 => 'permalink',
			1 => 'the_content',
			2 => 'excerpt',
			3 => 'custom_fields',
			4 => 'discussion',
			5 => 'comments',
			6 => 'revisions',
			7 => 'slug',
			8 => 'author',
			9 => 'format',
			10 => 'page_attributes',
			11 => 'featured_image',
			12 => 'categories',
			13 => 'tags',
			14 => 'send-trackbacks',
		),
		'active' => 1,
		'description' => '',
	));
	
	endif;