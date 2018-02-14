<?php
	
/*
Plugin Name: PtbT Subscribers
Plugin URi: http://pickthebestteam.com
Description: <strong>Pick the Best Team Subscribers</strong> plugin allows you to create a sports team management system in Wordpress. Add coaches, teams, players (junior & senior), track attendence, set up events, track stats. Build a better overview of which players are putting in the work and make you selection choices easier with quality information. <strong>This plugin adds a newsletter subscribers feature to Pick the Best Team</strong>.
Version: 0.1
Author: Pick the Best Team
Author URi: pickthebestteam.com
License: GPL2
License URi: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: pick-the-best-team
*/

/* ! 0. TABLE OF CONTENTS */

/*
	1. HOOKS
		1.1 - registers all our custom shortcodes
		1.2 - register custom admin column headers
		1.3 - register custom admin column data
		1.4 - register ajax actions
		1.5 - load external files to public website
		1.6 - Advanced Custom Fields settings
		1.7 - register our custom menus
		1.8 - load external files in Wordpress admin
		1.9 - register plugin options
		1.10 - trigger reward downloads
	
	2. SHORTCODES
		2.1 - ptbt_register_shortcodes()
		2.2 - ptbt_form_shortcode()
		2.3 - ptbt_manage_subscriptions_shortcode()
		2.4 - ptbt_confirm_subscription_shortcode()
		2.5 - ptbt_download_reward()
	
	3. FILTERS
		3.1 - ptbt_subscriber_column_headers()
		3.2 - ptbt_subscriber_column_data()
		3.2.2 - ptbt_register_custom_admin_titles()
		3.2.3 - ptbt_custom_admin_titles()
		3.3 - ptbt_list_column_headers()
		3.4 - ptbt_list_column_data()
		3.5 - ptbt_admin_menus() - register custom plugin admin menus
	
	4. EXTERNAL SCRIPTS
		4.1 - Include ACF
		4.2 - loads external files into PUBLIC website
		4.3 - loads external files into wordpress ADMIN
	
	5. ACTIONS
		5.1 - ptbt_save_subscription()
		5.2 - ptbt_save_subscriber()
		5.3 - ptbt_add_subscription()
		5.4 - ptbt_unsubscribe()
		5.5 - ptbt_remove_subscription()
		5.6 - ptbt_send_subscriber_email()
		5.7 - ptbt_confirm_subscription()
		5.8 - ptbt_create_plugin_tables()
		5.9 - ptbt_activate_plugin()
		5.10 - ptbt_add_reward_link()
		5.11 - ptbt_trigger_reward_download()
		5.12 - ptbt_update_reward_link_downloads()
		5.13 - ptbt_download_subscribers_csv()
		5.14 - ptbt_parse_import_csv()
		5.15 - ptbt_import_subscribers()
		5.16 - ptbt_check_wp_version()
		5.17 - ptbt_uninstall_plugin()
		5.18 - ptbt_remove_plugin_tables()
		5.19 - ptbt_remove_post_data()
	
	6. HELPERS
		6.1 - ptbt_subscriber_has_subscription()
		6.2 - ptbt_get_subscriber_id()
		6.3 - ptbt_get_subscriptions()
		6.4 - ptbt_return_json()
		6.5 - ptbt_get_acf_key()
		6.6 - ptbt_get_subscriber_data()
		6.7 - ptbt_get_page_select()
		6.8 - ptbt_get_default_options()
		6.9 - ptbt_get_option()
		6.10 - ptbt_get_current_options()
		6.11 - ptbt_get_manage_subscriptions_html()
		6.12 - ptbt_get_email_template()
		6.13 - ptbt_validate_list()
		6.14 - ptbt_validate_subscriber()
		6.15 - ptbt_get_querystring_start()
		6.16 - ptbt_get_querystring_start()
		6.17 - ptbt_get_optin_link()
		6.18 - ptbt_get_message_html()
		6.19 - ptbt_get_list_reward()
		6.20 - ptbt_get_reward_link()
		6.21 - ptbt_generate_reward_uid()
		6.22 - ptbt_get_reward()
		6.23 - ptbt_get_list_subscribers()
		6.24 - ptbt_get_list_subscriber_count()
		6.25 - ptbt_get_export_link()
		6.26 - ptbt_csv_to_array()
		6.27 - ptbt_get_admin_notice()
		
	
	7. CUSTOM POST TYPES
		7.1 - Subscribers CPT
		7.2 - List CPT
	
	8. ADMIN PAGES
		8.1 - dashboard
		8.2 - import subscribers
		8.3 - plugin options
	
	9. SETTINGS
		9.1 - ptbt_register_options()
	
*/


/* !1. HOOKS  */

	// 1.1 hint: registers all our custom shortcodes on init (wordpress event)
	add_action('init','ptbt_register_shortcodes');
	
	// 1.2 hint: registers custom admin column headers
	add_filter('manage_edit-ptbt_subscriber_columns','ptbt_subscriber_column_headers' );
	add_filter('manage_edit-ptbt_list_columns','ptbt_list_column_headers' );
	
	// 1.3 hint: register custom admin column data
	add_filter('manage_ptbt_subscriber_posts_custom_column', 'ptbt_subscriber_column_data',1,2 );
	add_action(
		'admin_head-edit.php', 
		'ptbt_register_custom_admin_titles'
	);
	add_filter('manage_ptbt_list_posts_custom_column', 'ptbt_list_column_data',1,2 );
	
	// 1.4 hint: register ajax actions
	add_action('wp_ajax_nopriv_ptbt_subscription', 'ptbt_save_subscription'); // regular website visitor
	add_action('wp_ajax_ptbt_save_subscription', 'ptbt_save_subscription'); // admin user
	add_action('wp_ajax_nopriv_ptbt_unsubscribe', 'ptbt_unsubscribe'); // regular website visitor
	add_action('wp_ajax_ptbt_unsubscribe', 'ptbt_unsubscribe'); // admin user
	add_action('wp_ajax_ptbt_download_subscribers_csv', 'ptbt_download_subscribers_csv'); // admin user
	add_action('wp_ajax_ptbt_parse_import_csv', 'ptbt_parse_import_csv'); // admin user
	add_action('wp_ajax_ptbt_import_subscribers', 'ptbt_import_subscribers'); // admin user
	
	// 1.5 load external files to public website
	add_action('wp_enqueue_scripts', 'ptbt_public_scripts');
	
	// 1.6 hint: ACF Settings
		// 1.6.1 customize ACF path
		add_filter('/acf/settings/path', 'ptbt_acf_settings_path');
		// 1.6.2. customize ACF dir
		add_filter('/acf/settings/dir', 'ptbt_acf_settings_dir');
		// 1.6.3. Hide ACF field group menu item
		add_filter('/acf/settings/show_admin', '__return_false');
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		//if( !defined('ACF_LITE') ) define('ACF_LITE',true); // turn off ACF plugin menu
		
	// 1.7 hint: register our custom menus
	add_action('admin_menu', 'ptbt_admin_menus');
	
	// 1.8 hint: load external files in Wordpress admin
	add_action('admin_enqueue_scripts', 'ptbt_admin_scripts');
	
	// 1.9 hint: register plugin options
	add_action('admin_init', 'ptbt_register_options');
	
	// 1.10 hint: register activate/deactivate/uninstall functions
	register_activation_hook( __FILE__, 'ptbt_activate_plugin' );
	add_action( 'admin_notices', 'ptbt_check_wp_version' );
	register_uninstall_hook(__FIL__ , 'ptbt_uninstall_plugin');
	
	// 1.11 hint: trigger reward downloads
	add_action('wp', 'ptbt_trigger_reward_download');


/* !2. SHORTCODES */

	// 2.1
	// hint: function to register our custom shortcodes
	function ptbt_register_shortcodes() {
		
		add_shortcode('ptbt_form', 'ptbt_form_shortcode');
		add_shortcode('ptbt_manage_subscriptions', 'ptbt_manage_subscriptions_shortcode');
		add_shortcode('ptbt_confirm_subscription', 'ptbt_confirm_subscription_shortcode');
		add_shortcode('ptbt_download_reward', 'ptbt_download_reward_shortcode');
				
	}
	
	
	// 2.2 hint: returns a html string for our ptbt newsletter form
	function ptbt_form_shortcode( $args, $content="") {
		
		// hint: get the list id
		$list_id = 0;
		if( isset($args['id']) ) $list_id = (int)$args['id'];
		
		// title
		$title = '';
		if( isset($args['title']) ) $title = (string)$args['title'];
		
		// setup our output variable - the form html
		$output = '
		
			<div class="ptbt">
									
				<form id="ptbt_register_form" name="ptbt_form" role="form" class="option1form ptbt-form" action="/wp-admin/admin-ajax.php?action=ptbt_save_subscription" method="post"> 
				    <div class="row">
				    
					    <input type="hidden" name="ptbt_list" value="'. $list_id .'">;
					    
					    '. wp_nounce_field( 'ptbt-register-subscription',$list_id, '_wpnonce', true, false );
					    
					    
					    if( strlen($title) ):
					    
						    $output .= '<h3 class="ptbt_list">'. $title .'</h3>';
						    
					    endif;
					    
					    
					    $output .= '<div class="col-md-6">
						    <div class="form-group"> 
						        <label class="control-label" for="ptbt_fname">First Name</label>
						        <input type="text" class="form-control" id="ptbt_fname" name="ptbt_fname" placeholder="Enter your first name">
						    </div> 
					    </div>
					    <div class="col-md-6">
						    <div class="form-group"> 
						        <label class="control-label" for="ptbt_lname">Last Name</label>
						        <input type="text" class="form-control" id="ptbt_lname" name="ptbt_lname" placeholder="Enter your last name">
						    </div> 
					    </div>    
					    <div class="col-md-6">
						    <div class="form-group"> 
						        <label class="control-label" for="email">Email address</label>
						        <input type="email" class="form-control" id="ptbt_email" name="ptbt_email" placeholder="Enter your email"> 
						    </div>
					    </div>
				    </div>'; 
				    
				    // including content in our form html if content is passed to our function
				    if( strlen($content) ): // colon not semi-colon
					    
					    $output .= '<div class="row">
						    <div class="col-md-12">
							    <div class="ptbt-content">'. wpautop($content) .'</div>
						    </div>
					    </div>'; // automatically add p to content that might have line breaks
					    			    
				    endif;
				    
				    // get reward
				    $reward = ptbt_get_list_reward( $list_id );
				    
				    // IF reward exists
					if( $reward !== false ):
					
						// include message about reward
						$output .='
							<div class="ptbt-content ptbt-reward-message alert alert-info">
								<p>Get a FREE DOWNLOAD of <strong>'. $reward['title'] .'</strong> when you join this list!</p>
							</div>
						';
					
					endif;
				    
				    // completing our form html   
				    $output .= '
				    <div class="row">
					    <div class="col-md-12">
						    <button type="submit" name="ptbt_submit" class="btn btn-default btn-lg">Submit</button> 
					    </div>
				    </div>
				</form>
								
			</div>
		';
		
		// return our results/html
		return $output;
	}

	// 2.3 hint: displays a form for managing the users list subscriptions
	// example: [ptbt_manage_subscriptions]
	function ptbt_manage_subscriptions_shortcode( $args, $content="" ) {
		
		// setup our return string
		$output = '<div class="ptbt ptbt-manage-subscriptions">';
		
		try {
			
			// get the email address from the URL
			$email = ( isset( $_GET['email'] ) ) ? esc_attr( $_GET['email'] ) : '';
			
			// get the subscriber id from the email address
			$subscriber_id = ptbt_get_subscriber_id( $email );
			
			// get subscriber data 
			$subscriber_data = ptbt_get_subscriber_data( $subscriber_id );
			
			// IF subscriber exists
			if( $subscriber_id ):
			
				// get subscriptions html
				$output .= ptbt_get_manage_subscriptions_html( $subscriber_id );
				
			else:
			
				// invalid link
				$output .= '<p>This link is invalid.</p>';
			
			endif;
		
		
		} catch(Exception $e) {
			
			// php error
			
		}
		
		// close our html div tag
		$output .= '</div>';
		
		// return our html
		return $output;
		
	}
	
	// 2.4 hint: displays subscription opt-in confirmation text and link to manage sunscriptions
	// example: [ptbt_confirm_subscription]
	function ptbt_confirm_subscription_shortcode( $args, $content="" ) {
		
		// setup output variable 
		$output = '<div class="ptbt">';
		
		// setup email and list_id variables and handle if they are not defined in the GET scope
		$email = ( isset( $_GET['email'] ) ) ? esc_attr( $_GET['email'] ) : '';
		$list_id = ( isset( $_GET['list'] ) ) ? esc_attr( $_GET['list'] ) : 0;
		
		// get subscriber id from email
		$subscriber_id = ptbt_get_subscriber_id( $email );
		$subscriber = get_post( $subscriber_id );
		
		// IF we found a subscriber matching that email address
		if( $subscriber_id && ptbt_validate_subscriber( $subscriber ) ):
		
			// get list object
			$list = get_post( $list_id );
			
			// IF list and subscriber are valid
			if( ptbt_validate_list( $list ) ):
			
			
				// IF subscriptions has not yet been added
				if( !ptbt_subscriber_has_subscription( $subscriber_id, $list_id) ):
					
					// complete opt-in
					$optin_complete = ptbt_confirm_subscription( $subscriber_id, $list_id );
					
					if( !$optin_complete ):
					
						$output .= ptbt_get_message_html('Due to an unknown error, we were unable to confirm your subscription.', 'error');
						$output .= '</div>';
						
						return $output;
					
					endif;
			
				endif;
			
				// get confirmation message html and append it to output
				$output .= ptbt_get_message_html( 'Your subscription to '. $list->post_title .' has now been confirmed.', 'confirmation' );
				
				// get manage subscriptions link
				$manage_subscriptions_link = ptbt_get_manage_subscriptions_link( $email );
				
				// append link to output
				$output .= '<p><a href="'. $manage_subscriptions_link .'">Click here to manage your subscriptions.</a></p>';
			
			else:
			
				$output .= ptbt_get_message_html( 'This link is invalid.', 'error');
			
			endif;
		
		else: 
		
			$output .= ptbt_get_message_html( 'This link is invalid. Invalid Subscriber '. $email .'.', 'error');
		
		endif;
		
		// close .ptbt div
		$output .= '</div>';
		
		// return output html
		return $output;
		
	}
	
	// 2.5 hint: returns a message if the download link has expired or is invalid
	function ptbt_download_reward_shortcode( $args, $content="" ) {
		
		$output = '';
		
		$uid = ($_GET['reward']) ? (string)$_GET['reward'] : 0;
			
		// get reward form link uid
		$reward = ptbt_get_reward( $uid );
		
		// IF reward was found
		if( $reward !== false ):
		
			if( $reward['downloads'] >= ptbt_get_option( 'ptbt_download_limit') ):
		
				$output .= ptbt_get_message_html( 'This link has reached it\'s download limit.', 'warning');
			
			endif;
		
		else:
		
			$output .= ptbt_get_message_html( 'This link is invalid.', 'error');
		
		endif;
		
		return $output;
		
	}


/* !3. FILTERS */
	// 3.1 hint: maniuplate the column headers for admin pages
	function ptbt_subscriber_column_headers( $columns ) {
		
		// creating custom column header data
		$columns = array(
			'cb'=>'<input type="checkbox" />',
			'title'=>__('Subscriber Name'),
			'email'=>__('Email Address'),
		);
		
		// returning new columns
		return $columns;
	}
	
	// 3.2
	function ptbt_subscriber_column_data( $column, $post_id ) {
		
		// setup our return text
		$output = '';
		
		switch( $column ) {
			
			case 'name':
				// get the custom name data
				$fname = get_field('ptbt_fname', $post_id );
				$lname = get_field('ptbt_lname', $post_id );
				$output .= $fname .' '. $lname;
				break;
			case 'email':
				// get the custom email data
				$email = get_field('ptbt_email', $post_id );
				$output .= $email;
				break;
			
		}
		
		// echo the output
		echo $output;
	}
	
	// 3.2.2 hint: registers special custom admin title columns
	function ptbt_register_custom_admin_titles() {
		add_filter('the_title', 'ptbt_custom_admin_titles', 99, 2);
	}
	
	// 3.2.3 hint: handles custom admin title "title" column data for post types without titles
	function ptbt_custom_admin_titles( $title, $post_id ) {
		
		global $post;
		
		$output = $title;
		
		if( isset($post->post_type) ):
			switch ( $post->post_type ) {
				case 'ptbt_subscriber':
					$fname = get_field('ptbt_fname', $post_id );
					$lname = get_field('ptbt_lname', $post_id );
					$output = $fname .' '. $lname;
					break;
			}
		endif;
			
		return $output;
		
	}
	
	// 3.3 hint: maniuplate the column headers for admin pages
	function ptbt_list_column_headers( $columns ) {
		
		// creating custom column header data
		$columns = array(
			'cb'=>'<input type="checkbox" />',
			'title'=>__('List Name'),
			'reward'=>__('Opt In Reward'),
			'subscribers'=>__('Subscribers'),
			'shortcode'=>__('Shortcode'),
		);
		
		// returning new columns
		return $columns;
	}
	
	// 3.4
	function ptbt_list_column_data( $column, $post_id ) {
		
		// setup our return text
		$output = '';
		
		switch( $column ) {
			
			case 'reward':
				$reward = ptbt_get_list_reward( $post_id );
				if( $reward !== false ):
								
					$output .= '<a href="'. $reward['file']['url'] .'" download="'. $reward['title'] .'">'. $reward['title'] .'</a>';
					endif;
				break;
				
			case 'subscribers':
			// get the count of current subscribers
				$subscriber_count = ptbt_get_list_subscriber_count( $post_id );
				// get our unique export link
				$export_href = ptbt_get_export_link( $post_id );
				// append the subscriber count to our output
				$output .= $subscriber_count;
				// IF we have more than one subscriber, add our new export link to $output
				if( $subscriber_count ) $output.= ' <a href="'. $export_href .'">Export</a>';
				break;
			case 'shortcode':
				$output .= '[ptbt_form id="'. $post_id .'"]';
				break;
			
		}
		
		// echo the output
		echo $output;
	}
	
	// 3.5 hint: register custom plugin admin menus
	function ptbt_admin_menus() {
		
		$top_menu_item = 'ptbt_dashboard_admin_page';
		
		add_menu_page('', 'List Builder', 'manage_options', 'ptbt_dashboard_admin_page', 'ptbt_dashboard_admin_page', 'dashicons-email-alt' );
		
		/* submenu items */
		
		// dashboard
		add_submenu_page( $top_menu_item, 'Dashboard', 'Dashboard', 'manage_options', $top_menu_item, $top_menu_item );
		
		// email lists
		add_submenu_page( $top_menu_item, '', 'Email List', 'manage_options', 'edit.php?post_type=ptbt_list' );
		
		// subscribers
		add_submenu_page( $top_menu_item, '', 'Subscribers', 'manage_options', 'edit.php?post_type=ptbt_subscriber' );
		
		// import subscribers
		add_submenu_page( $top_menu_item, '', 'Import Subscribers', 'manage_options', 'ptbt_import_admin_page', 'ptbt_import_admin_page' );
		
		// plugin options
		add_submenu_page( $top_menu_item, 'Plugin Options', 'Plugin Options', 'manage_options', 'ptbt_options_admin_page', 'ptbt_options_admin_page' );
		
	}


/* !4. EXTERNAL SCRIPTS */
	
	// 4.1 Include ACF
	include_once( plugin_dir_path( __FILE__ ) .'lib/advanced-custom-fields-pro/acf.php' );
	
	// 4.2 hint: loads external files into PUBLIC website
	function ptbt_public_scripts() {
		
		// register scripts with Wordpress's internal library
		wp_register_script('ptbt-list-builder-js-public', plugins_url('/js/public/ptbt-list-builder.js',__FILE__), array('jquery'), '',true);
		wp_register_style('ptbt-list-builder-css-public', plugins_url('/css/public/ptbt-list-builder.css',__FILE__));
		
		// add to queue of scripts that get loaded into every page
		wp_enqueue_script('ptbt-list-builder-js-public');
		wp_enqueue_style('ptbt-list-builder-css-public');
		
	}

	// 4.3 hint: loads external files into wordpress ADMIN
	function ptbt_admin_scripts() {
		
		// register scripts with Wordpress's internal library
		wp_register_script('ptbt-list-builder-js-private', plugins_url('/js/private/ptbt-list-builder.js', __FILE__), array('jquery'), '', true);
		
		// add to queue of scripts that get loaded into every admin page
		wp_enqueue_script('ptbt-list-builder-js-private');
		
	}


/* !5. ACTIONS  */
	// 5.1
	function ptbt_save_subscription() {
	
		// setup default result data
		$result = array(
			'status' => 0,
			'message' => 'Subscription was not saved. ',
			'error'=>'',
			'errors'=>array()
		);
		
		try {
			
			// get list_id
			$list_id = (int)$_POST['ptbt_list'];
		
			// prepare subscriber data
			$subscriber_data = array(
				'fname'=> esc_attr( $_POST['ptbt_fname'] ),
				'lname'=> esc_attr( $_POST['ptbt_lname'] ),
				'email'=> esc_attr( $_POST['ptbt_email'] ),
			);
 			
			// setup our errors array
			$errors = array();
			
			// form validation
			if( !strlen( $subscriber_data['fname'] ) ) $errors['fname'] = 'First name is required. ';
			if( !strlen( $subscriber_data['email'] ) ) $errors['email'] = 'Email address is required. ';
			if( strlen( $subscriber_data['email'] ) && !is_email( $subscriber_data['email'] ) ) $errors['email'] = 'Email address must be valid.';
			
			// IF there are errors
			if( count($errors) ):
			
				// append errors to result structure for later use
				$result['error'] = 'Some fields are still required. ';
				$result['errors'] = $errors;
			
			else: 
			// IF there are no errors, proceed...
			
				// attempt to create/save subscriber
				$subscriber_id = ptbt_save_subscriber( $subscriber_data );
				
				// IF subscriber was saved successfully $subscriber_id will be greater than 0
				if( $subscriber_id ):
				
					// IF subscriber already has this subscription
					if( ptbt_subscriber_has_subscription( $subscriber_id, $list_id ) ):
					
						// get list object
						$list = get_post( $list_id );
						
						// return detailed error
						$result['error'] = esc_attr( $subscriber_data['email'] .' is already subscribed to '. $list->post_title .'.');
						
					else: 
					
						// send new subscriber a confirmation email, returns true if we were successful
						$email_sent = ptbt_send_subscriber_email( $subscriber_id, 'new_subscription', $list_id );
						
						// IF email was sent
						if( !email_sent ):
							
							// email could not be sent
							$result['error'] = 'Unable to send email. ';
							
						else:
						
							// email sent and subscription saved!
							$result['status']=1;
							$result['message']='Success! A confirmation email has been sent to '. $subscriber_data['email'];
							
							// clean up: remove our empty error
							unset( $result['error'] );
														
						endif;
					
					endif;
				
				endif;
			
			endif;
			
		} catch ( Exception $e ) {
			
		}
		
		// return result as json
		ptbt_return_json($result);
		
	}
	
	// 5.2 hint: creates a new subscriber or updates an existing one
	function ptbt_save_subscriber( $subscriber_data ) {
		
		// setup default subscriber id
		// 0 means the subscriber was not saved
		$subscriber_id = 0;
		
		try {
			
			$subscriber_id = ptbt_get_subscriber_id( $subscriber_data['email'] );
			
			// IF the subscriber does not already exist…
			if( !$subscriber_id ):
				
				// add new subscriber to database
				$subscriber_id = wp_insert_post(
					array(
						'post_type'=>'ptbt_subscriber',
						'post_title'=>$subscriber_data['fname'] .' '. $subscriber_data['lname'],
						'post_status'=>'publish',
					),
					true
				);
				
			endif;
			
			// add/update custom meta data
			update_field(ptbt_get_acf_key('ptbt_fname'), $subscriber_data['fname'], $subscriber_id);
			update_field(ptbt_get_acf_key('ptbt_lname'), $subscriber_data['lname'], $subscriber_id);
			update_field(ptbt_get_acf_key('ptbt_email'), $subscriber_data['email'], $subscriber_id);
			
		} catch( Exception $e ) {
			
			// a php error occurred
			
		}
		
		// reset the wordpress post object
		// wp_reset_query();
		
		// return subscriber id
		return $subscriber_id;
	}
	
	// 5.3 hint: adds list to subscribers subscriptions
	function ptbt_add_subscription( $subscriber_id, $list_id ) {
		
		// setup default return value
		$subscription_saved = false;
		
		// IF the subscriber does NOT have the current list subscription
		if( !ptbt_subscriber_has_subscription( $subscriber_id, $list_id) ):
			
			// get subscriptions and append new $list_id
			$subscriptions = ptbt_get_subscriptions( $subscriber_id );
			$subscriptions[]=$list_id;
			
			// update ptbt_subscriptions
			update_field(ptbt_get_acf_key('ptbt_subscriptions'), $subscriptions, $subscriber_id);
			
			// subscriptions updated!
			$subscription_saved = true;
			
		endif;
		
		// return result
		return $subscription_saved;
		
	}
	
	// 5.4 hint: removes one or more subscriptions from a subscriber and notifies them via email
	// this function is an ajax form handler…
	// expects form post data: $_POST['subscriber_id'] and $_POST['list_id']
	function ptbt_unsubscribe() {
		
		// setup default result data
		$result = array (
			'status' => 0,
			'message' => 'Subscriptions were NOT updated. ',
			'error' => '',
			'errors' => array(),
		);
		
		$subscriber_id = ( isset($_POST['subscriber_id']) ) ? esc_attr( (int)$_POST['subscriber_id'] ) : 0;
		$list_ids = ( isset($_POST['list_ids']) ) ? $_POST['list_ids'] : 0;
		
		try {
			
			// if there are lists to remove
			if( is_array($list_ids) ):
			
				// loop over lists to remove
				foreach( $list_ids as &$list_id ):
				
					// remove this subscription
					ptbt_remove_subscription( $subscriber_id, $list_id );
					
				endforeach;
				
			endif;
			
			// setup success status and message
			$result['status']=1;
			$result['message']='Subscriptions updated. ';
			
			// get the updated list of subscriptions as html
			$result['html']= ptbt_get_manage_subscriptions_html( $subscriber_id );
			
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return result as json
		ptbt_return_json( $result );
		
	}
	
	// 5.5 hint: removes a single subscription from a subscriber
	function ptbt_remove_subscription( $subscriber_id, $list_id ) {
	
		// setup default return value
		$subscription_saved = false;
		
		// IF the subscriber has the current list subscription
		if( ptbt_subscriber_has_subscription( $subscriber_id, $list_id ) ):
		
			// get current subscriptions
			$subscriptions = ptbt_get_subscriptions( $subscriber_id );
			
			// get the position of the $list_id to remove
			$needle = array_search( $list_id, $subscriptions );
			
			// remove $list_id from $subscriptions array
			unset( $subscriptions[$needle] );
			
			// update ptbt_subscriptions
			update_field(ptbt_get_acf_key( 'ptbt_subscriptions' ), $subscriptions, $subscriber_id);
			
			// subscriptions updated!
			$subscription_saved = true;
			
		endif;
		
		// return result
		return $subscription_saved;
		
	}
	
	// 5.6 hint: sends a unique customized email to a subscriber
	function ptbt_send_subscriber_email ( $subscriber_id, $email_template_name, $list_id ) {
		
		// set up return variable
		$email_sent = false;
		
		// get email template data
		$email_template_object = ptbt_get_email_template( $subscriber_id, $email_template_name, $list_id );
		
		// IF email template data found
		if( !empty( $email_template_object ) ):
		
			// get subscriber data
			$subscriber_data = ptbt_get_subscriber_data( $subscriber_id );
			
			// set wp_mail headers
			$wp_mail_headers = wp_mail( array('Content-Type: text/html; charset=UTF-8') );
			
			// use wp_mail to send email
			$email_sent = wp_mail( array( $subscriber_data['email'] ) , $email_template_object['subject'], $email_template_object['body'], $wp_mail_headers );
			
		endif;
		
		return $email_sent;
		
	}
	
	// 5.7 hint: adds subcription to database and emails subscriber confirmation email
	function ptbt_confirm_subscription( $subscriber_id, $list_id ) {
		
		// setup return variable
		$optin_complete = false;
		
		// add new subscription
		$subscription_saved = ptbt_add_subscription( $subscriber_id, $list_id );
		
		// IF subscription was saved
		if( $subscription_saved ):
		
			// send email
			$email_sent = ptbt_send_subscriber_email( $subscriber_id, 'subscription_confirmed', $list_id );
			
			// IF email sent
			if( $email_sent ):
			
				// return true
				$optin_complete = true;
		
			endif;
			
		endif;
		
		// return result
		return $optin_complete;
		
	}	
	
	// 5.8 hint: creates custom tables for our plugin
	function ptbt_create_plugin_tables() {
	
	global $wpdb; // Class to interact with databases in Wordpress
	
	// setup return value
	$return_value = false;
	
	try {
		
		$table_name = $wpdb->prefix . "ptbt_reward_links";
		$table_name1 = $wpdb->prefix . "ptbtcountry";
		$table_name2 = $wpdb->prefix . "ptbtstuff";
		$charset_collate = $wpdb->get_charset_collate();
	
		// sql for our table creation
		$sql = "CREATE TABLE $table_name (
			id mediumint(11) NOT NULL AUTO_INCREMENT,
			uid varchar(128) NOT NULL,
			subscriber_id mediumint(11) NOT NULL,
			list_id mediumint(11) NOT NULL,
			attachment_id mediumint(11) NOT NULL,
			downloads mediumint(11) DEFAULT 0 NOT NULL ,
			UNIQUE KEY id (id)
			) $charset_collate;";			
		
		$ptbt_stuff = "CREATE TABLE $table_name2 (
			PTBTAnythingID int(11) NOT NULL AUTO_INCREMENT,
			PTBTAnything char(40) NOT NULL,
			UNIQUE KEY id (PTBTAnythingID)
			) $charset_collate;".
			
			"INSERT INTO `wp_ptbtstuff`(`PTBTAnythingID`, `PTBTAnything`) VALUES ('','Stuff');";
		
		$ptbt_countries = "CREATE TABLE $table_name1 (
			PTBTCountryId int(11) NOT NULL,
			PTBTRegionId int(11) NOT NULL,
			PTBTCountryCode char(5) NOT NULL,
			PTBTCountryName char(40) NOT NULL
			UNIQUE KEY id (PTBTCountryId)
			) $charset_collate;".
			
		"INSERT INTO `wp_ptbtcountry` (`PTBTCountryId`, `PTBTRegionId`, `PTBTCountryCode`, `PTBTCountryName`) VALUES
				(33, 5, 'AU', 'Australia'),
				(34, 5, 'BN', 'Brunei'),
				(35, 5, 'KH', 'Cambodia'),
				(36, 5, 'CN', 'China'),
				(37, 5, 'TL', 'East Timor (Timor-Leste)'),
				(38, 5, 'FM', 'Federated States of Micronesia'),
				(39, 5, 'FJ', 'Fiji'),
				(40, 5, 'IN', 'India'),
				(41, 5, 'ID', 'Indonesia'),
				(42, 5, 'JP', 'Japan'),
				(43, 5, 'KI', 'Kiribati'),
				(44, 5, 'LA', 'Laos'),
				(45, 5, 'MY', 'Malaysia'),
				(46, 5, 'MH', 'Marshall Islands'),
				(47, 5, 'NR', 'Nauru'),
				(48, 5, 'NZ', 'New Zealand'),
				(49, 5, 'KP', 'North Korea'),
				(50, 5, 'PW', 'Palau'),
				(51, 5, 'PG', 'Papua New Guinea'),
				(52, 5, 'PH', 'Philippines'),
				(53, 5, 'RU', 'Russia'),
				(54, 5, 'WS', 'Samoa'),
				(55, 5, 'SG', 'Singapore'),
				(56, 5, 'SB', 'Solomon Islands'),
				(57, 5, 'KR', 'South Korea'),
				(58, 5, 'LK', 'Sri Lanka'),
				(59, 5, 'TH', 'Thailand'),
				(60, 5, 'TO', 'Tonga'),
				(61, 5, 'TV', 'Tuvalu'),
				(62, 5, 'TZ', 'United Republic of Tanzania'),
				(63, 5, 'VU', 'Vanuatu'),
				(64, 5, 'VN', 'Vietnam'),
				(65, 4, 'AM', 'Armenia'),
				(66, 4, 'AT', 'Austria'),
				(67, 4, 'AZ', 'Azerbaijan'),
				(68, 4, 'BH', 'Bahrain'),
				(69, 4, 'BY', 'Belarus'),
				(70, 4, 'BE', 'Belguim'),
				(71, 4, 'BW', 'Botswana'),
				(72, 4, 'BG', 'Bulgaria'),
				(73, 4, 'BI', 'Burundi'),
				(74, 4, 'KM', 'Comoros'),
				(75, 4, 'HR', 'Croatia'),
				(76, 4, 'CY', 'Cyprus'),
				(77, 4, 'CZ', 'Czech Republic'),
				(78, 4, 'DK', 'Denmark'),
				(79, 4, 'DJ', 'Djibouti'),
				(80, 4, 'EG', 'Egypt'),
				(81, 4, 'ER', 'Eritrea'),
				(82, 4, 'EE', 'Estonia'),
				(83, 4, 'FI', 'Finland'),
				(84, 4, 'FR', 'France'),
				(85, 4, 'GE', 'Georgia'),
				(86, 4, 'DE', 'Germany'),
				(87, 4, 'GH', 'Ghana'),
				(88, 4, 'GR', 'Greece'),
				(89, 4, 'HU', 'Hungary'),
				(90, 4, 'IR', 'Iran'),
				(91, 4, 'IQ', 'Iraq'),
				(92, 4, 'IE', 'Ireland'),
				(93, 4, 'IL', 'Israel'),
				(94, 4, 'IT', 'Italy'),
				(95, 4, 'JO', 'Jordan'),
				(96, 4, 'KZ', 'Kazakhstan'),
				(97, 4, 'KE', 'Kenya'),
				(98, 4, 'KW', 'Kuwait'),
				(99, 4, 'LV', 'Latvia'),
				(100, 4, 'LB', 'Lebanon'),
				(101, 4, 'LT', 'Lithuania'),
				(102, 4, 'LU', 'Luxembourg'),
				(103, 4, 'MG', 'Madagascar'),
				(104, 4, 'MW', 'Malawi'),
				(105, 4, 'MA', 'Morocco'),
				(106, 4, 'NA', 'Namibia'),
				(107, 4, 'NL', 'Netherlands'),
				(108, 4, 'NG', 'Nigeria'),
				(109, 4, 'NO', 'Norway'),
				(110, 4, 'OM', 'Oman'),
				(111, 4, 'PK', 'Pakistan'),
				(112, 4, 'PL', 'Poland'),
				(113, 4, 'PT', 'Portugal'),
				(114, 4, 'QA', 'Qatar'),
				(115, 4, 'SA', 'Saudi Arabia'),
				(116, 4, 'SL', 'Sierra Leone'),
				(117, 4, 'ZA', 'South Africa'),
				(118, 4, 'ES', 'Spain'),
				(119, 4, 'SZ', 'Swaziland'),
				(120, 4, 'SY', 'Syria'),
				(121, 4, 'TJ', 'Tajikistan'),
				(122, 4, 'TR', 'Turkey'),
				(123, 4, 'UA', 'Ukraine'),
				(124, 4, 'AE', 'United Arab Emirates'),
				(125, 4, 'GB', 'United Kingdom'),
				(126, 4, 'YE', 'Yemen'),
				(127, 4, 'ZM', 'Zambia'),
				(128, 3, 'AR', 'Argentina'),
				(129, 3, 'AW', 'Aurba'),
				(130, 3, 'BZ', 'Belize'),
				(131, 3, 'BO', 'Boliva'),
				(132, 3, 'BR', 'Brazil'),
				(133, 3, 'CL', 'Chile'),
				(134, 3, 'CO', 'Columbia'),
				(135, 3, 'CR', 'Costa Rica'),
				(136, 3, 'CU', 'Cuba'),
				(137, 3, 'DO', 'Dominican Republic'),
				(138, 3, 'EC', 'Ecuador'),
				(139, 3, 'SV', 'El Salvador'),
				(140, 3, 'GF', 'French Guiana'),
				(141, 3, 'GT', 'Guatemala'),
				(142, 3, 'HT', 'Haiti'),
				(143, 3, 'HN', 'Honduras'),
				(144, 3, 'JM', 'Jamaica'),
				(145, 3, 'MX', 'Mexico'),
				(146, 3, 'NI', 'Nicaragua'),
				(147, 3, 'PA', 'Panama'),
				(148, 3, 'PY', 'Paraguay'),
				(149, 3, 'PE', 'Peru'),
				(150, 3, 'PR', 'Puerto Rico'),
				(151, 3, 'MF', 'Saint Martin'),
				(152, 3, 'UY', 'Uruguay'),
				(153, 3, 'VE', 'Venezuela'),
				(154, 1, 'CA', 'Canada'),
				(155, 1, 'US', 'United States');";
		
		// make sure we include wordpress functions for dbDelta	
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
		// dbDelta will create a new table if none exists or update an existing one
		dbDelta($sql);
		dbDelta($ptbt_countries);
		dbDelta($ptbt_stuff);
		
		// return true
		$return_value = true;
	
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return result
		return $return_value;
		
	}
	
	// 5.9
	// hint: runs on plugin activation
	function ptbt_activate_plugin() {
		
		// setup custom database tables
		ptbt_create_plugin_tables();
		
	}
		
	// 5.10 hint: adds new reward links to the database
	function ptbt_add_reward_link( $uid, $subscriber_id, $list_id, $attachment_id ) {
		
		global $wpdb;
	
		// setup our return value
		$return_value = false;
		
		try {
			
			$table_name = $wpdb->prefix . "ptbt_reward_links";
			
			$wpdb->insert(
				$table_name, 
				array( 
					'uid' => $uid, 
					'subscriber_id' => $subscriber_id,
					'list_id' => $list_id, 
					'attachment_id' => $attachment_id, 
				), 
				array( 
					'%s', 
					'%d', 
					'%d',
					'%d', 
				) 
			);
			
			// return true
			$return_value = true;
		
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return result
		return $return_value;
		
	}
	
	// 5.11 hint: triggers a download of the reward file
	function ptbt_trigger_reward_download() {
	
		global $post;
		
		if( $post->ID == ptbt_get_option( 'ptbt_reward_page_id') && isset($_GET['reward']) ):
			
			$uid = ($_GET['reward']) ? (string)$_GET['reward'] : 0;
			
			// get reward form link uid
			$reward = ptbt_get_reward( $uid );
			
			// IF reward was found
			if( $reward !== false && $reward['downloads'] < ptbt_get_option( 'ptbt_download_limit') ):
			
				ptbt_update_reward_link_downloads( $uid );
				
				// get reward mimetype
				$mimetype = $reward['file']['mime_type'];
				// extract the filetype from the mimetype
				$mimetype_array = explode('/',$mimetype);
				$filetype = $mimetype_array[1];
			
				// setup file headers
				header("Content-type: ".$mimetype,true,200);
			    header("Content-Disposition: attachment; filename=".$reward['title'] .'.'. $filetype);
			    header("Pragma: no-cache");
			    header("Expires: 0");
			    readfile($reward['file']['url']);
			    exit();
		    
		    endif;
		
		endif;
		
	}
	
	// 5.12 hint: increases reward link download count by one
	function ptbt_update_reward_link_downloads( $uid ) {
		
		global $wpdb;
	
		// setup our return value
		$return_value = false;
		
		try {
			
			$table_name = $wpdb->prefix . "ptbt_reward_links";
			
			// get current download count
			$current_count = $wpdb->get_var( 
				$wpdb->prepare( 
					"
						SELECT downloads 
						FROM $table_name 
						WHERE uid = %s
					", 
					$uid
				) 
			);
			
			// set new count
			$new_count = (int)$current_count+1;
			
			// update downloads for this reward link entry
			$wpdb->query(
				$wpdb->prepare( 
					"
						UPDATE $table_name
						SET downloads = $new_count  
						WHERE uid = %s
					", 
					$uid
				) 
			);
			
			$return_value = true;
			
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		return $return_value;
		
	}
	
	// 5.13  hint: generates a .csv file of subscribers data
	// expects $_GET['list_id'] to be set in the URL
	function ptbt_download_subscribers_csv() {
		
		// get the list id from the URL scope
		$list_id = ( isset($_GET['list_id']) ) ? (int)$_GET['list_id'] : 0;
		
		// setup our return data
		$csv = '';
		
		// get the list object
		$list = get_post( $list_id );
		
		// get the list's subscribers or get all subscribers if no list id is given
		$subscribers = ptbt_get_list_subscribers( $list_id );
		
		// IF we have confirmed subscribers
		if( $subscribers !== false ):
		
			// get the current date
			$now = new DateTime();
			
			// setup a unique filename for the generated export file
			$fn1 = 'ptbt-list-builder-export-list_id-'. $list_id .'-date-'. $now->format('Ymd'). '.csv';
			$fn2 = plugin_dir_path( __FILE__ ) .'exports/'.$fn1;
			
			// open new file in write mode
			$fp = fopen($fn2, 'w');
			
			// get the first subscriber's data
			$subscriber_data = ptbt_get_subscriber_data( $subscribers[0] );
			
			// remove the subscriptions and name column from the data
			unset($subscriber_data['subscriptions']);
			unset($subscriber_data['name']);
			
			// build our csv headers array from $subscriber_data's data keys
			$csv_headers = array();
			foreach( $subscriber_data as $key => $value ):
				array_push($csv_headers, $key);
			endforeach;
			
			// append $csv_headers to our csv file
			fputcsv($fp, $csv_headers);
		
			// loop over all our subscribers
			foreach( $subscribers as &$subscriber_id ):
		
				// get the subscriber data of the current subscriber
				$subscriber_data = ptbt_get_subscriber_data( $subscriber_id );
			
				// remove the subscriptions and name columns from the data
				unset($subscriber_data['subscriptions']);
				unset($subscriber_data['name']);
				
				// append this subscriber's data to our csv file
				fputcsv($fp, $subscriber_data);
			
			endforeach;
			
			// read open our new file is read mode
			$fp = fopen($fn2, 'r');
			// read our new csv file and store it's contents in $fc
			$fc = fread($fp, filesize($fn2) );
			// close our open file pointer
			fclose($fp);
		
			// setup file headers
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=".$fn1);
			// echo the contents of our file and return it to the browser
			echo($fc);
			// exit php processes 
			exit;
		
		endif;
		
		// return false if we were unable to download our csv
		return false;
		
	}
	
	// 5.14 hint: this function retrieves a csv file from the server and parses the data into a php array
	// it then returns that array in a json formatted object
	// this function is a ajax post form handler
	// expects: $_POST['ptbt_import_file_id']
	function ptbt_parse_import_csv() {
		
		// setup our return array
		$result = array(
			'status'=>0,
			'message'=>'Could not parse import CSV. ',
			'error'=>'',
			'data'=>array(),
		);
		
		try {
		
			// get the attachment id from $_POST['ptbt_import_file_id']
			$attachment_id = (isset($_POST['ptbt_import_file_id'])) ? esc_attr( $_POST['ptbt_import_file_id'] ) : 0;
			
			// get the filename using wp's get_attached_file
			$filename = get_attached_file( $attachment_id );
			
			// IF we got the filename
			if( $filename !== false):
			
				// parse the csv file into a php array using ptbt_csv_to_array
				$csv_data = ptbt_csv_to_array($filename,',');
			
				// IF we were able to parse the file and there's data in it
				if( $csv_data !== false && count($csv_data) ):
			
					// append the data to our result array and return as success
					$result = array(
						'status'=>1,
						'message'=>'CSV Import data parsed successfully',
						'error'=>'',
						'data'=>$csv_data,
					);
				
				endif;
				
			else:
			
				// return an error message if we could not retrieve the file
				$result['error']='The import file does not exist. ';
			
			endif;
		
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return the result as json
		ptbt_return_json( $result );
		
	}
	
	// 5.15 hint: imports new subscribers from our import admin page
	// this function is a form handler and expect subscriber data in the $_POST scope
	function ptbt_import_subscribers() {
		
		// setup our return array
		$result = array(
			'status'=>0,
			'message'=>'Could not import subscribers. ',
			'error'=>'',
			'errors'=>array(),
		);
		
		try {
			
			// get the assignment values
			$fname_column = (isset($_POST['ptbt_fname_column'])) ? (int)$_POST['ptbt_fname_column'] : 0;
			$lname_column = (isset($_POST['ptbt_lname_column'])) ? (int)$_POST['ptbt_lname_column'] : 0;
			$email_column = (isset($_POST['ptbt_email_column'])) ? (int)$_POST['ptbt_email_column'] : 0;
			
			// get the list id to import to
			$list_id = (isset($_POST['ptbt_import_list_id'])) ? (int)$_POST['ptbt_import_list_id'] : 0;
			
			// get the selected subscriber rows to import
			$selected_rows = (isset($_POST['ptbt_import_rows'])) ? (array)$_POST['ptbt_import_rows'] : array();
			
			// setup the data for selected rows
			$subscribers = array();
			
			// setup a variable for counting the subscribers we add
			$added_count = 0;
			
			// loop over selected rows and get the data
			foreach( $selected_rows as &$row_id ):
			
				// build our subscriber data 
				$subscriber_data = array(
					'fname'=>(string)$_POST['s_'. $row_id .'_'. $fname_column],
					'lname'=>(string)$_POST['s_'. $row_id .'_'. $lname_column],
					'email'=>(string)$_POST['s_'. $row_id .'_'. $email_column],
				);
				
				// IF the subscriber email is invalid
				if( !is_email($subscriber_data['email']) ):
				
					// don't attempt to add the subscriber if the email is not valid
					$result['errors'][] = 'Invalid email detected: '. $subscriber_data['email'] .'. This subscriber was not added';
				
				else:
				
					// IF the subscriber email is valid...
					// add subscriber to the database
					$subscriber_id = ptbt_save_subscriber( $subscriber_data );
					
					// IF subscriber was created or updated successfully
					if( $subscriber_id ):
					
						// add subscription for this subscriber without opt-in
						$subscription_added = ptbt_add_subscription( $subscriber_id, $list_id );
					
						// updated our added count
						$added_count++;
					
					endif;
				
				endif;
			
			endforeach;
			
			// IF no subscribers were actually added...
			if( $added_count == 0 ):
			
				// return error message
				$result['error'] = 'No subscribers were imported. ';
			
			else:
			
				// IF subscribers were added...
				// return success!
				$result = array(
					'status'=>1,
					'message'=> $added_count .' Subscribers imported successfully. ',
					'error'=>'',
					'errors'=>array(),
				);
			
			endif;
		
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return result as json
		ptbt_return_json( $result );
		
		
	}
	
	// 5.16 hint: checks the current version of wordpress and displays a message in the plugin page if the version is untested
	function ptbt_check_wp_version() {
		
		global $pagenow;
		
		
		if ( $pagenow == 'plugins.php' && is_plugin_active('pick-the-best-team/pick-the-best-team.php') ):
		
			// get the wp version
			$wp_version = get_bloginfo('version');
			
			// tested vesions
			// these are the versions we've tested our plugin in
			$tested_versions = array(
				'4.2.0',
				'4.9.0',
				'4.9.1',
			);
			
			// IF the current wp version is not in our tested versions...
			if( !in_array( $wp_version, $tested_versions ) ):
				
				// get notice html
				$notice = ptbt_get_admin_notice('Pick the best Team List Builder has not been tested in your version of WordPress. It still may work though…','error');
				
				// echo the notice html
				echo( $notice );
				
			endif;
		
		endif;
		
	}
	
	// 5.17 hint: runs functions for plugin uninstall
	function ptbt_uninstall_plugin() {
		
		// remove our custom plugin tables
		ptbt_remove_plugin_tables();
		// remove custom post types posts and data
		ptbt_remove_post_data();
		// remove plugin options
		ptbt_remove_options();
		
	}
	
	// 5.18 hint: removes our custom database tabels
	function ptbt_remove_plugin_tables() {
		
		// get WP's wpdb class
		global $wpdb;
		
		// setup return variable
		$tables_removed = false;
		
		try {
			
			// get our custom table name
			$table_name = $wpdb->prefix . "ptbt_reward_links";
		
			// delete table from database
			$tables_removed = $wpdb->query("DROP TABLE IF EXISTS $table_name;");
		
		} catch( Exception $e ) {
			
			
		}
		
		// return result
		return $tables_removed;
		
	}
	
	// 5.19 hint: removes plugin related custom post type post data
	function ptbt_remove_post_data() {
		
		// get WP's wpdb class
		global $wpdb;
		
		// setup return variable
		$data_removed = false;
		
		try {
			
			// get our custom table name, DOES THIS NEED TO BE SET uP FOR EACH TABLE
			$table_name = $wpdb->prefix . "posts";
			
			// set up custom post types array
			$custom_post_types = array(
				'ptbt_subscriber',
				'ptbt_list'
			);
			
			// remove data from the posts db table where post types are equal to our custom post types
			$data_removed = $wpdb->query(
				$wpdb->prepare( 
					"
						DELETE FROM $table_name 
						WHERE post_type = %s OR post_type = %s
					", 
					$custom_post_types[0],
					$custom_post_types[1]
				) 
			);
			
			// get the table names for postmet and posts with the correct prefix
			$table_name_1 = $wpdb->prefix . "postmeta";
			$table_name_2 = $wpdb->prefix . "posts";
			
			// delete orphaned meta data
			$wpdb->query(
				$wpdb->prepare( 
					"
					DELETE pm
					FROM $table_name_1 pm
					LEFT JOIN $table_name_1 wp ON wp.ID = pm.post_id
					WHERE wp.ID IS NULL
					"
				) 
			);
			
			
			
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return result
		return $data_removed;
		
	}


/* !6. HELPERS  */
	// 6.1 hint: returns true or false
	function ptbt_subscriber_has_subscription( $subscriber_id, $list_id ) {
		
		// setup up default return value
		$has_subscription = false;
		
		// get subscriber
		$subscriber = get_post($subscriber_id);
		
		// get subscriptions
		$subscriptions = ptbt_get_subscriptions( $subscriber_id );
		
		// check subscriptions for $list_id
		if( in_array($list_id, $subscriptions) ):
			
			// found the $list_id in $subscriptions
			// this subscriber is already subscribed to this list
			$has_subscription = true;
			
		else:
		
			// did not find $list_id in $subscriptions
			// this subscriber is not yet subscribed to this list
			
		endif;
		
		return $has_subscription;
		
	}
	
	// 6.2 hint: retrives a subscriber_id from an email address
	function ptbt_get_subscriber_id( $email ) {
		
		$subscriber_id = 0;
		
		try {
			
			// check if subscriber already exists
			$subscriber_query = new WP_Query(
				array(
					'post_type' => 'ptbt_subscriber',
					'posts_per_page' => 1,
					'meta_key' => 'ptbt_email',
					'meta_query' => array(
						array(
							'key' => 'ptbt_email',
							'value' => $email, // or whatever it is we want to call it
							'compare' => '=',
						),
					),
				) // no comma on this one
			);
			
			// IF the subscriber exists
			if( $subscriber_query->have_posts() ):
				
				// get the subscriber_id
				$subscriber_query->the_post();
				$subscriber_id = get_the_ID();
				
			endif;
			
		} catch( Exception $e ) {
			
			// a php error occurred
			
		}
		
		// reset this Wordpress post object
		wp_reset_query();
		
		return (int)$subscriber_id;
		
	}
	
	// 6.3 hint: returns an array of list_id's
	function ptbt_get_subscriptions( $subscriber_id ) {
		
		$subscriptions = array();
		
		// get subscriptions (returns array of list objects)
		$lists = get_field( ptbt_get_acf_key('ptbt_subscriptions'), $subscriber_id );
		
		// IF $list returns something
		if( $lists ):
		
			// IF $list is an array and there is one or more items
			if( is_array($lists) && count($lists) ):
				// build subscriptions: array of list id's
				foreach( $lists as &$list):
					$subscriptions[]= (int)$list->ID;
				endforeach;
			elseif( is_numeric($lists) ):
				// single result returned
				$subscriptions[]= $lists;
			endif;
			
		endif;
		
		return (array)$subscriptions;
		
	}
	
	// 6.4 hint: return result as a json string
	function ptbt_return_json( $php_array ) {
		
		// encode result as json string
		$json_result = json_encode( $php_array );
		
		// return result
		die( $json_result );
		
		// stop all other processing
		// exit; not sure if this is needed because of die( $json_result )
	}
	
	// 6.5 hint: gets the unique acf field key from the field name
	function ptbt_get_acf_key( $field_name ) {
	
		$field_key = $field_name;
		
		switch( $field_name ) {
			
			case 'ptbt_fname':
				$key = 'field_5a3fd7b3740c3'; // copy field name from acf dev inspect tool
				break;
			case 'ptbt_lname':
				$key = 'field_5a3fd7ca740c4'; // copy field name from acf dev inspect tool
				break;
			case 'ptbt_email':
				$key = 'field_5a3fd7fb740c5'; // copy field name from acf dev inspect tool
				break;
			case 'ptbt_subscriptions':
				$key = 'field_5a3fd9d3f8d5e'; // copy field name from acf dev inspect tool
				break;
			case 'ptbt_enable_reward':
				$key = 'field_5a5678a589835'; // copy field name from acf dev inspect tool
				break;
			case 'ptbt_reward_title':
				$key = 'field_5a56791589836'; // copy field name from acf dev inspect tool
				break;
			case 'ptbt_reward_file':
				$key = 'field_5a56794b89837'; // copy field name from acf dev inspect tool
				break;
				
		}
		
		return $field_key;
		
	}
	
	// 6.6 hint: returns an array of subscriber data including subscriptions
	function ptbt_get_subscriber_data( $subscriber_id ) {
		
		// setup subscriber_data
		$subscriber_data = array();
		
		// get subscriber object
		$subscriber = get_post( $subscriber_id );
		
		// IF subscriber object is valid
		if( isset($subscriber->post_type) && $subscriber->post_type == 'ptbt_subscriber' ):
			
			$fname = get_field( ptbt_get_acf_key('ptbt_fname'), $subscriber_id);
			$lname = get_field( ptbt_get_acf_key('ptbt_lname'), $subscriber_id);			
			// build subscriber_data for return
			$subscriber_data = array(
				'name'=>$fname .' '. $lname,
				'fname'=>$fname,
				'lname'=>$lname,
				'email'=>get_field( ptbt_get_acf_key('ptbt_email'), $subscriber_id),
				'subscriptions'=>ptbt_get_subscriptions( $subscriber_id )
			);
			
		endif;
		
		// return subscriber data
		return $subscriber_data;
	}
	
	// 6.7 hint: returns html for a page selector
	function ptbt_get_page_select( $input_name="ptbt_page", $input_id="", $parent=-1, $value_field="id", $selected_value="" ) {
	
		// get WP pages
		$pages = get_pages( 
			array(
				'sort_order' => 'asc',
				'sort_column' => 'post_title',
				'post_type' => 'page',
				'parent' => $parent,
				'status'=>array('draft','publish'),	
			)
		);
		
		// setup our select html
		$select = '<select name="'. $input_name .'" ';
		
		// IF $input_id was passed in
		if( strlen($input_id) ):
		
			// add an input id to our select html
			$select .= 'id="'. $input_id .'" ';
		
		endif;
		
		// setup our first select option
		$select .= '><option value="">- Select One -</option>';
		
		// loop over all the pages
		foreach ( $pages as &$page ): 
		
			// get the page id as our default option value
			$value = $page->ID;
			
			// determine which page attribute is the desired value field
			switch( $value_field ) {
				case 'slug':
					$value = $page->post_name;
					break;
				case 'url':
					$value = get_page_link( $page->ID );
					break;
				default:
					$value = $page->ID;
			}
			
			// check if this option is the currently selected option
			$selected = '';
			if( $selected_value == $value ):
				$selected = ' selected="selected" ';
			endif;
		
			// build our option html
			$option = '<option value="' . $value . '" '. $selected .'>';
			$option .= $page->post_title;
			$option .= '</option>';
			
			// append our option to the select html
			$select .= $option;
			
		endforeach;
		
		// close our select html tag
		$select .= '</select>';
		
		// return our new select 
		return $select;
		
	}
	
	// 6.8 hint: returns default option values as an associative array
	function ptbt_get_default_options() {
		
		$defaults = array();
		
		try {
			
			// get front page id
			$front_page_id = get_option('page_on_front');
		
			// setup default email footer
			$default_email_footer = '
				<p>
					Sincerely, <br /><br />
					The '. get_bloginfo('name') .' Team<br />
					<a href="'. get_bloginfo('url') .'">'. get_bloginfo('url') .'</a>
				</p>
			';
			
			// setup defaults array
			$defaults = array(
				'ptbt_manage_subscription_page_id'=>$front_page_id,
				'ptbt_confirmation_page_id'=>$front_page_id,
				'ptbt_reward_page_id'=>$front_page_id,
				'ptbt_default_email_footer'=>$default_email_footer,
				'ptbt_download_limit'=>3,
			);
		
		} catch( Exception $e) {
			
			// php error
			
		}
		
		// return defaults
		return $defaults;
		
		
	}
	
	// 6.9 hint: returns the requested page option value or it's default
	function ptbt_get_option( $option_name ) {
		
		// setup return variable
		$option_value = '';	
		
		
		try {
			
			// get default option values
			$defaults = ptbt_get_default_options();
			
			// get the requested option
			switch( $option_name ) {
				
				case 'ptbt_manage_subscription_page_id':
					// subscription page id
					$option_value = (get_option('ptbt_manage_subscription_page_id')) ? get_option('ptbt_manage_subscription_page_id') : $defaults['ptbt_manage_subscription_page_id'];
					break;
				case 'ptbt_confirmation_page_id':
					// confirmation page id
					$option_value = (get_option('ptbt_confirmation_page_id')) ? get_option('ptbt_confirmation_page_id') : $defaults['ptbt_confirmation_page_id'];
					break;
				case 'ptbt_reward_page_id':
					// reward page id
					$option_value = (get_option('ptbt_reward_page_id')) ? get_option('ptbt_reward_page_id') : $defaults['ptbt_reward_page_id'];
					break;
				case 'ptbt_default_email_footer':
					// email footer
					$option_value = (get_option('ptbt_default_email_footer')) ? get_option('ptbt_default_email_footer') : $defaults['ptbt_default_email_footer'];
					break;
				case 'ptbt_download_limit':
					// reward download limit
					$option_value = (get_option('ptbt_download_limit')) ? (int)get_option('ptbt_download_limit') : $defaults['ptbt_download_limit'];
					break;
				
			}
			
		} catch( Exception $e) {
			
			// php error
			
		}
		
		// return option value or it's default
		return $option_value;
		
	}
	
	// 6.10 hint: get's the current options and returns values in associative array
	function ptbt_get_current_options() {
	
		// setup our return variable
		$current_options = array();
		
		try {
		
			// build our current options associative array
			$current_options = array(
				'ptbt_manage_subscription_page_id' => ptbt_get_option('ptbt_manage_subscription_page_id'),
				'ptbt_confirmation_page_id' => ptbt_get_option('ptbt_confirmation_page_id'),
				'ptbt_reward_page_id' => ptbt_get_option('ptbt_reward_page_id'),
				'ptbt_default_email_footer' => ptbt_get_option('ptbt_default_email_footer'),
				'ptbt_download_limit' => ptbt_get_option('ptbt_download_limit'),
			);
		
		} catch( Exception $e ) {
			
			// php error
		
		}
		
		// return current options
		return $current_options;
		
	}
	
	// 6.11 hint: generates an html form for managing subscriptions
	function ptbt_get_manage_subscriptions_html( $subscriber_id ) {
		
		$output = '';
		
		try {
			
			// get array of list_ids for this subscriber
			$lists = ptbt_get_subscriptions( $subscriber_id );
			
			// get the subscriber data
			$subscriber_data = ptbt_get_subscriber_data( $subscriber_id );
			
			// set the title
			$title = $subscriber_data['fname'] .'\'s Subscriptions';
			
			$nounce = wp_nonce_field( 'ptbt-update-subscription',$subscriber_id, '_wpnonce', true, false );
		
			// build out output html
			$output = '
				<form id="ptbt_manage_subscriptions_form" class="ptbt-form" method="post"  
				action="/wp-admin/admin-ajax.php?action=ptbt_unsubscribe">					
					
					
					<input type="hidden" name="subscriber_id" value="'. $subscriber_id .'">
					
					<h3 class="ptbt-title">'. $title .'</h3>';
					
					if( !count($lists) ):
						
						$output .='<div class="row"><div class="col-md-12"><p>There are no active subscriptions.</p></div></div>';
					
					else:
					
						$output .= '<div class="row"><div class="col-md-12">
							';
							
						// loop over lists
						foreach( $lists as &$list_id ):
						
							$list_object = get_post( $list_id );
						
							$output .= '
							<div class="row">
								<div class="col-md-3">
								    <h4>'. $list_object->post_title .'</h4>
							    </div>
							    <div class="col-md-9">
							        <div class="form-check">
										<label class="checkbox" for="">
											<input class="form-check-input" type="checkbox" name="list_ids[]" value="'. $list_object->ID .'" id="subscriptionCheckbox">
										Unsubscribe</label>
									</div>
								</div>							
							
							</div>';
							
						endforeach;
						
						// close up our output html
						$output .='
											
							<div class="row">
							    <div class="col-md-12">
								    <button type="submit" class="btn btn-default btn-lg" value="Save Changes">Save Changes</button> <br><br>
							    </div>
						    </div>';
					
					endif;
					
				$output .='
					</form></div></div>
				';
		
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return output 
		return $output;
		
	}
	
	// 6.12 // hint: returns an array of email template data IF the template exists
	function ptbt_get_email_template( $subscriber_id, $email_template_name, $list_id ) {
		
		// setup return variable
		$template_data = array();
		
		// create new array to store email templates
		$email_templates = array();
		
		// get list object
		$list = get_post( $list_id );
		
		// get subscriber object
		$subscriber = get_post( $subscriber_id );
		
		if( !ptbt_validate_list( $list ) || !ptbt_validate_subscriber( $subscriber ) ):
		
			// the list or the subscriber is not valid
		
		else:
		
			// get subscriber data 
			$subscriber_data = ptbt_get_subscriber_data( $subscriber_id );
		
			// get unique manage subscription link
			$manage_subscriptions_link = ptbt_get_manage_subscriptions_link( $subscriber_data['email'], $list_id );
			
			// get default email header 
			$default_email_header = '
				<p>
					Hello, '. $subscriber_data['fname'] .'
				</p>
			';
			
			// get default email footer 
			$default_email_footer = ptbt_get_option('ptbt_default_email_footer');
			
			// setup unsubscribe text
			$unsubscribe_text = '
				<br /><br />
				<hr />
				<p><a href="'. $manage_subscriptions_link .'">Click here to unsubscribe</a> from this or any other email list.</p>';
				
			// get reward
			$reward = ptbt_get_list_reward( $list_id );
			
			// setup reward text
			$reward_text = '';
			
			// IF reward exists
			if( $reward !== false ):
			
				// setup the appropriate reward text
				switch( $email_template_name ) {
					
					case 'new_subscription':
						// set reward text
						$reward_text = '<p>After confirming your subscription, we will send you a link for a FREE DOWNLOAD of '. $reward['title'] .'</p>';
						break;
					case 'subscription_confirmed':
						// get download limit
						$download_limit = ptbt_get_option('ptbt_download_limit');
						// generate new download link
						$download_link = ptbt_get_reward_link( $subscriber_id, $list_id );
						// set reward text
						$reward_text = '<p>Here is your <a href="'. $download_link .'">UNIQUE DOWNLOAD LINK</a> for '. $reward['title'] .'. This will expire after '. $download_limit .' downloads.</p>';
						break;
					
				}
			
			endif;
			
			// setup email templates
			
				// get unique opt-in link
				$optin_link = ptbt_get_optin_link( $subscriber_data['email'], $list_id );
							
				// template: new_subscription
				$email_templates['new_subscription'] = array(
					'subject' => 'Thank you for subscribing to '. $list->post_title .'! Please confirm your subscription.',
					'body' => '
						'. $default_email_header .'
						<p>Thank you for subscribing to '. $list->post_title .'!</p>
						<p>Please <a href="'. $optin_link .'">click here to confirm your subscription</a></p>
						'. $reward_text . $default_email_footer . $unsubscribe_text,
				);
				
				// template: subscription confirmed
				$email_templates['subscription_confirmed'] = array(
					'subject' => 'You are now subscribed to '. $list->post_title .'!',
					'body' => '
						'. $default_email_header .'
						<p>Thank you for confirming your subscription. You are now subscribed to '. $list->post_title .'!</p>
						'. $reward_text . $default_email_footer . $unsubscribe_text,
				);				
		
		endif;
		
		// IF the requested email template exists
		if( isset( $email_templates[ $email_template_name ] ) ):
		
			// add template data to return variable
			$template_data = $email_templates[ $email_template_name ];
		
		endif;
		
		// return template data
		return $template_data;
		
	}
	
	// 6.13 hint: validates whether the post object exists and that it's a validate post_type
	function ptbt_validate_list( $list_object ) {
		
		$list_valid = false;
		
		if( isset($list_object->post_type) && $list_object->post_type == 'ptbt_list' ):
		
			$list_valid = true;
		
		endif;
		
		return $list_valid;
		
	}
	
	// 6.14 hint: validates whether the post object exists and that it's a validate post_type
	function ptbt_validate_subscriber( $subscriber_object ) {
		
		$subscriber_valid = false;
		
		if( isset($subscriber_object->post_type) && $subscriber_object->post_type == 'ptbt_subscriber' ):
		
			$subscriber_valid = true;
		
		endif;
		
		return $subscriber_valid;
		
	}
	
	// 6.15 hint: returns a unique link for managing a particular users subscriptions
	function ptbt_get_manage_subscriptions_link( $email, $list_id=0 ) {
		
		$link_href = '';
		
		try {
			
			$page = get_post( ptbt_get_option('ptbt_manage_subscription_page_id') );
			$slug = $page->post_name;
			
			$permalink = get_permalink($page);
			
			// get character to start querystring
			$startquery = ptbt_get_querystring_start( $permalink );
			
			$link_href = $permalink . $startquery .'email='. urlencode($email) .'&list='. $list_id;
			
		} catch( Exception $e ) {
			
			//$link_href = $e->getMessage();
			
		}
		
		return esc_url($link_href);
		
	}
	
	// 6.16 hint: returns the appropriate character for the begining of a querystring
	function ptbt_get_querystring_start( $permalink ) {
		
		// setup our default return variable
		$querystring_start = '&';
		
		// IF ? is not found in the permalink
		if( strpos($permalink, '?') === false ):
			$querystring_start = '?';
		endif;
		
		return $querystring_start;
		
	}
	
	// 6.17 hint: returns a unique link for opting into an email list
	function ptbt_get_optin_link( $email, $list_id=0 ) {
		
		$link_href = '';
		
		try {
			
			$page = get_post( ptbt_get_option('ptbt_confirmation_page_id') );
			$slug = $page->post_name;
			$permalink = get_permalink($page);
			
			// get character to start querystring
			$startquery = ptbt_get_querystring_start( $permalink );
			
			$link_href = $permalink . $startquery .'email='. urldecode($email) .'&list='. $list_id;
			
		} catch ( Exception $e ) {
			
			//$link_href = $e->getMessage();
			
		}
		
		wp_reset_postdata();
		
		return esc_url($link_href);
		
	}
	
	// 6.18 hint: returns html for messages
	function ptbt_get_message_html( $message, $message_type ) {
		
		$output = '';
		
		try {
			
			$message_class = 'confirmation';
			
			switch( $message_type ) {
				case 'warning': 
					$message_class = 'ptbt-warning';
					break;
				case 'error': 
					$message_class = 'ptbt-error';
					break;
				default:
					$message_class = 'ptbt-confirmation';
					break;
			}
			
			$output .= '
				<div class="ptbt-message-container">
					<div class="ptbt-message '. $message_class .'">
						<p>'. $message .'</p>
					</div>
				</div>
			';
			
		} catch( Exception $e ) {
			
		}
		
		return $output;
		
	} 
	
	// 6.19 hint: returns false if list has no reward or returns the object containing file and tile if it does
	function ptbt_get_list_reward( $list_id ) {
	
		// setup return data
		$reward_data = false;
		
		// get enable_reward value
		$enable_reward = ( get_field( ptbt_get_acf_key('ptbt_enable_reward'), $list_id) ) ? true : false;
		
		// IF reward is enabled for this list
		if( $enable_reward ):
		
			// get reward file
			$reward_file = ( get_field( ptbt_get_acf_key('ptbt_reward_file'), $list_id) ) ? get_field( ptbt_get_acf_key('ptbt_reward_file'), $list_id) : false;
			// get reward title
			$reward_title = ( get_field(ptbt_get_acf_key('ptbt_reward_title'), $list_id) ) ? get_field(ptbt_get_acf_key('ptbt_reward_title'), $list_id) : 'Reward';
			
			
			
			// IF reward_file is a valid array
			if( is_array($reward_file) ):
		
		
				// setup return data
				$reward_data = array(
					'file' => $reward_file,
					'title' => $reward_title,
				);
			
			endif;
		
		endif;
		
		// return $reward_data
		return $reward_data;
		
	}
	
	// 6.20 hint: returns a unique link for downloading a reward file…
	function ptbt_get_reward_link( $subscriber_id, $list_id ) {
	
		$link_href = '';
		
		try {
			
			$page = get_post( ptbt_get_option('ptbt_reward_page_id') );
			$slug = $page->post_name;
			$permalink = get_permalink($page);
			
			// generate unique uid for reward link
			$uid = ptbt_generate_reward_uid( $subscriber_id, $list_id );
			
			// get list reward
			$reward = ptbt_get_list_reward( $list_id );
			
			// IF an attachment id was returned
			if( $uid && $reward !== false ):
			
				// add reward link to database
				$link_added = ptbt_add_reward_link( $uid, $subscriber_id, $list_id, $reward['file']['id'] );
				
				// IF link was added successfully
				if( $link_added === true ):
					
					// get character to start querystring
					$startquery = ptbt_get_querystring_start( $permalink );
				
					// build reward link
					$link_href = $permalink . $startquery .'reward='. urlencode($uid);
				
				endif;
			
			endif;
			
		} catch( Exception $e ) {
			
			//$link_href = $e->getMessage();
			
		}
		
		// return reward link
		return esc_url($link_href);
		
	}
	
	// 6.21 hint: generates a unique 
	function ptbt_generate_reward_uid( $subscriber_id, $list_id ) {
		
		// setup our return variable
		$uid = '';
		
		// get subscriber post object
		$subscriber = get_post( $subscriber_id );
		
		// get list post object
		$list = get_post( $list_id );
		
		// IF subscriber and list are valid
		if( ptbt_validate_subscriber( $subscriber ) && ptbt_validate_list( $list ) ):
				
				// get list reward
				$reward = ptbt_get_list_reward( $list_id );
				
				// IF reward is not equal to false
				if( $reward !== false ):
					
					// generate a unique id
					$uid = uniqid( 'ptbt', true );
				
				endif;
				
		
		endif;
		
		return $uid;
		
	}
	
	// 6.22 hint: returns false if list has no reward or returns the object containing file and title if it does
	function ptbt_get_reward( $uid ) {
		
		global $wpdb;
		
		// setup return data
		$reward_data = false;
		
		// reward links download table name
		$table_name = $wpdb->prefix . "ptbt_reward_links";
		
		// get list id from reward link
		$list_id = $wpdb->get_var( 
			$wpdb->prepare( 
				"
					SELECT list_id 
					FROM $table_name 
					WHERE uid = %s
				", 
				$uid
			) 
		);
		
		// get downloads from reward link
		$downloads = $wpdb->get_var( 
			$wpdb->prepare( 
				"
					SELECT downloads 
					FROM $table_name 
					WHERE uid = %s
				", 
				$uid
			) 
		);
		
		// get reward data
		$reward = ptbt_get_list_reward( $list_id );
		
		// IF reward was found
		if( $reward !== false ):
		
			// set reward data
			$reward_data = $reward;
			
			// add downloads to reward data
			$reward_data['downloads']=$downloads;
			
		endif;
		
		// return $reward_data
		return $reward_data;
		
	}
	
	// 6.23 hint: returns an array of subscriber_id's
	function ptbt_get_list_subscribers( $list_id=0 ) {
	
		// setup return variable
		$subscribers = false;
		
		// get list object
		$list = get_post( $list_id );
		
		if( ptbt_validate_list( $list ) ):
				
			// query all subscribers from post this list only
			$subscribers_query = new WP_Query( 
				array(
					'post_type' => 'ptbt_subscriber',
					'published' => true,
					'posts_per_page' => -1,
					'orderby'=>'post_date',
					'order'=>'DESC',
					'status'=>'publish',
					'meta_query' => array(
						array(
							'key' => 'ptbt_subscriptions', 
							'value' => ':"'.$list->ID.'"', 
							'compare' => 'LIKE'
						)
					)
				)
			);
			
		elseif( $list_id === 0 ):
		
			// query all subscribers from all lists
			$subscribers_query = new WP_Query( 
				array(
					'post_type' => 'ptbt_subscriber',
					'published' => true,
					'posts_per_page' => -1,
					'orderby'=>'post_date',
					'order'=>'DESC',
				)
			);
		
		endif;
			
		// IF $subscribers_query isset and query returns results
		if( isset($subscribers_query) && $subscribers_query->have_posts() ):
		
			// set subscribers array
			$subscribers = array();
			
			// loop over results
			while ($subscribers_query->have_posts() ) : 
			
				// get the post object
				$subscribers_query->the_post();
				
				$post_id = get_the_ID();
			
				// append result to subscribers array
				array_push( $subscribers, $post_id);
			
			endwhile;
		
		endif;
		
		// reset wp query/postdata
		wp_reset_query();
		wp_reset_postdata();
		
		// return result
		return $subscribers;
	}
	
	// 6.24 hint: returns the amount of subscribers in this list
	function ptbt_get_list_subscriber_count( $list_id = 0 ) {
		
		// setup return variable
		$count = 0;
		
		// get array of subscribers ids
		$subscribers = ptbt_get_list_subscribers( $list_id );
		
		// IF array was returned
		if( $subscribers !== false ):
		
			// update count
			$count = count($subscribers);
		
		endif;
		
		// return result
		return $count;
		
	}
	
	// 6.25 hint: returns a unique link for downloading a subscribers csv
	function ptbt_get_export_link( $list_id=0 ) {
		
		$link_href = 'admin-ajax.php?action=ptbt_download_subscribers_csv&list_id='. $list_id;
		
		// return reward link
		return esc_url($link_href);
		
	}
	
	// 6.26 hint: this function reads a csv file and converts the contents into a php array
	function ptbt_csv_to_array($filename='', $delimiter=',')
	{
	
		// this is an important setting!
		ini_set('auto_detect_line_endings', true);
		
		// IF the file doesn't exist or the file is not readable return false
	    if(!file_exists($filename) || !is_readable($filename))
	        return FALSE;
	      
	    // setup our return data  
	    $return_data = array();
	    
	    // IF we can open and read the file
	    if (($handle = fopen($filename, "r")) !== FALSE) {
		  
		  	$row = 0;
		  
		    // while data exists loop over data
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        // count the number of items in this data
		        $num = count($data);
		        // increment our row variable
		        $row++;
		        // setup our row data array
		        $row_data = array();
		        // loop over all items and append them to our row data
		        for ($c=0; $c < $num; $c++) {
		            // if this is the first row set it up as our header
					if( $row == 1):
						$header[] = $data[$c];
					else:
						// all rows greate than 1
						// add row data item
						$return_data[$row-2][$header[$c]] = $data[$c];
					endif;
		        }
		    } 
		    
		    // close our file
		    fclose($handle);
		}
		
		// return the new data as a php array
	    return $return_data;
	    
	}
	
	// 6.27 hint: returns html formatted for WP admin notices
	function ptbt_get_admin_notice( $message, $class ) {
		
		// setup our return variable
		$output = '';
		
		try {
			
			// create output html
			$output = '
			 <div class="'. $class .'">
			    <p>'. $message .'</p>
			</div>
			';
		    
		} catch( Exception $e ) {
			
			// php error
			
		}
		
		// return output
		return $output;
		
	}
	
	// 6.28 hint: get's an array of plugin option data (group and settings) so as to save it all in one place
	function ptbt_get_options_settings() {
		
		// setup our return data
		$settings = array( 
			'group'=>'ptbt_plugin_options',
			'settings'=>array(
				'ptbt_manage_subscription_page_id',
				'ptbt_confirmation_page_id',
				'ptbt_reward_page_id',
				'ptbt_default_email_footer',
				'ptbt_download_limit',
			),
		);
		
		// return option data
		return $settings;
		
	}


/* !7. CUSTOM POST TYPES  */

	// 7.1 Subscribers CPT	
	include_once( plugin_dir_path( __FILE__ ). 'cpt/ptbt_subscriber.php');
	
	// 7.2 List CPT
	include_once( plugin_dir_path( __FILE__ ). 'cpt/ptbt_list.php');


/* !8. ADMIN  */
	// 8.1 hint: dashboard admin page
	function ptbt_dashboard_admin_page() {
		
		// get our export link
		$export_href = ptbt_get_export_link();
		
		$output = '
			<div class="wrap">
				
				<h2>Pick the best Team List Builder</h2>
				
				<p>Pick the best Team email list building plugin for Wordpress. Capture new subscribers. Build unlimited lists. Import and export subscribers easily with .csv</p>
				<p><a href="'. $export_href .'" class="button button-primary">Export All Subscriber Data</a></p>
			</div>
			';
			
			echo $output;
			
	}
	
	// 8.2 hint: import subscribers admin page
	function ptbt_import_admin_page() {
		
		// enque special scripts required for our file import field
		wp_enqueue_media();
		
		echo('
		
		<div class="wrap" id="import_subscribers">
				
				<h2>Import Subscribers</h2>
							
				<form id="import_form_1">
				
					<table class="form-table">
					
						<tbody>
					
							<tr>
								<th scope="row"><label for="ptbt_import_file">Import CSV</label></th>
								<td>
									
									<div class="wp-uploader">
									    <input type="text" name="ptbt_import_file_url" class="file-url regular-text" accept="csv">
									    <input type="hidden" name="ptbt_import_file_id" class="file-id" value="0" />
									    <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload">
									</div>
									
									
									<p class="description" id="ptbt_import_file-description">Expects a CSV file containing a "Name" (First, Last orFul) and "Email Address".</p>
								</td>
							</tr>
							
						</tbody>
						
					</table>
					
				</form>
				
				<form id="import_form_2" method="post" action="wp-admin/admin-ajax.php?action=ptbt_import_subscribers">
					
					<table class="form-table">
					
						<tbody class="ptbt-dynamic-content">
							
						</tbody>
						
						<tbody class="form-table show-only-on-valid" style="display: none">
							
							<tr>
								<th scope="row"><label>Import To List</label></th>
								<td>
									<select name="ptbt_import_list_id">');
										
										
											// get all our email lists
											$lists = get_posts(
												array(
													'post_type'			=>'ptbt_list',
													'status'			=>'publish',
													'posts_per_page'   	=> -1,
													'orderby'         	=> 'post_title',
													'order'            	=> 'ASC',
												)
											);
											
											// loop over each email list
											foreach( $lists as &$list ):
											
												// create the select option for that list
												$option = '
													<option value="'. $list->ID .'">
														'. $list->post_title .'
													</option>';
												
												// echo the new option	
												echo $option;
											
											endforeach;
											
									echo('</select>
									<p class="description"></p>
								</td>
							</tr>
							
						</tbody>
						
					</table>
					
					<p class="submit show-only-on-valid" style="display:none"><input type="submit" name="submit" id="submit" class="button button-primary" value="Import"></p>
					
				</form>
				
		</div>
		
		');
		
	}
	
	// 8.3 hint: plugin options admin page
	function ptbt_options_admin_page() {
	
	// get the default values for our options
	$options = ptbt_get_current_options();
	
	echo('<div class="wrap">
		
		<h2>Pick the best Team List Builder Options</h2>
		
		<form action="options.php" method="post">');
		
			// outputs a unique nounce for our plugin options
			settings_fields('ptbt_plugin_options');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('ptbt_plugin_options');
			
			echo('<table class="form-table">
			
				<tbody>
			
					<tr>
						<th scope="row"><label for="ptbt_manage_subscription_page_id">Manage Subscriptions Page</label></th>
						<td>
							'. ptbt_get_page_select( 'ptbt_manage_subscription_page_id', 'ptbt_manage_subscription_page_id', 0, 'id', $options['ptbt_manage_subscription_page_id'] ) .'
							<p class="description" id="ptbt_manage_subscription_page_id-description">This is the page where Pick the best Team List Builder will send subscribers to manage their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[ptbt_manage_subscriptions]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="ptbt_confirmation_page_id">Opt-In Page</label></th>
						<td>
							'. ptbt_get_page_select( 'ptbt_confirmation_page_id', 'ptbt_confirmation_page_id', 0, 'id', $options['ptbt_confirmation_page_id'] ) .'
							<p class="description" id="ptbt_confirmation_page_id-description">This is the page where Pick the best Team List Builder will send subscribers to confirm their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[ptbt_confirm_subscription]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="ptbt_reward_page_id">Download Reward Page</label></th>
						<td>
							'. ptbt_get_page_select( 'ptbt_reward_page_id', 'ptbt_reward_page_id', 0, 'id', $options['ptbt_reward_page_id'] ) .'
							<p class="description" id="ptbt_reward_page_id-description">This is the page where Pick the best Team List Builder will send subscribers to retrieve their reward downloads. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[ptbt_download_reward]</strong>.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="ptbt_default_email_footer">Email Footer</label></th>
						<td>');
						
							
							// wp_editor will act funny if it's stored in a string so we run it like this...
							wp_editor( $options['ptbt_default_email_footer'], 'ptbt_default_email_footer', array( 'textarea_rows'=>8 ) );
							
							
							echo('<p class="description" id="ptbt_default_email_footer-description">The default text that appears at the end of emails generated by this plugin.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="ptbt_download_limit">Reward Download Limit</label></th>
						<td>
							<input type="number" name="ptbt_download_limit" value="'. $options['ptbt_download_limit'] .'" class="" />
							<p class="description" id="ptbt_download_limit-description">The amount of downloads a reward link will allow before expiring.</p>
						</td>
					</tr>
			
				</tbody>
				
			</table>');
		
			// outputs the WP submit button html
			@submit_button();
		
		
		echo('</form>
	
	</div>');
	
}



/* !9. SETTINGS  */

	// 9.1 hint: registers all our plugin options
	function ptbt_register_options() {
		
		// get plugin options settings
		
		$options = ptbt_get_options_settings();
		
		// loop over settings
		foreach( $options['settings'] as $setting ):
		
			// register this setting
			register_setting($options['group'], $setting);
		
		endforeach;
		
	}


/* !10. MISC. */
 
function ptbt_acf_settings_dir( $dir ) {
 
    // update path
    $dir = get_stylesheet_directory_uri() . '/lib/advanced-custom-fields-pro/';
   
    // return
    return $dir;
   
}
 
function ptbt_acf_settings_path( $path ) {
 
    // update path
    $path = get_stylesheet_directory() . '/lib/advanced-custom-fields-pro/';
   
    // return
    return $path;
   
}



/* !11. Trying some custom code */

function ptbt_query_countriesDB() {
	
	global $wpdb;
	$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ptbtcountry", OBJECT );
	
	
	
}