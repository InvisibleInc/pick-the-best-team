<?php
	// Custom Post Types
	function cptui_register_my_cpts_ptbt_list() {
	
		/**
		 * Post Type: Lists.
		 */
	
		$labels = array(
			"name" => __( "Lists", "pick_the_best_team" ),
			"singular_name" => __( "List", "pick_the_best_team" ),
		);
	
		$args = array(
			"label" => __( "Lists", "pick_the_best_team" ),
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
			"rewrite" => array( "slug" => "ptbt_list", "with_front" => false ),
			"query_var" => true,
			"supports" => array( "title" ),
		);
	
		register_post_type( "ptbt_list", $args );
	}
	
	add_action( 'init', 'cptui_register_my_cpts_ptbt_list' );
	
	
	
	if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_5a5678965b2eb',
		'title' => 'List Settings',
		'fields' => array(
			array(
				'key' => 'field_5a5678a589835',
				'label' => 'Enable Reward on opt-in',
				'name' => 'ptbt_enable_reward',
				'type' => 'radio',
				'instructions' => 'Whether or not you\'d like to reward subscribers when they sign-up to your list.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					0 => 'No',
					1 => 'Yes',
				),
				'allow_null' => 0,
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => 0,
				'layout' => 'vertical',
				'return_format' => 'value',
			),
			array(
				'key' => 'field_5a56791589836',
				'label' => 'Reward Title',
				'name' => 'ptbt_reward_title',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5a5678a589835',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
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
				'key' => 'field_5a56794b89837',
				'label' => 'Reward File',
				'name' => 'ptbt_reward_file',
				'type' => 'file',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5a5678a589835',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'library' => 'all',
				'min_size' => '',
				'max_size' => '',
				'mime_types' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'ptbt_list',
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