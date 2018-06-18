<?php

/**
 * Plugin Name:       WP Twilio Core - C3 Addon
 * Plugin URI:        https://creativecomputerms.com
 * Description:       This is an addon for WP Twilio Core.
 * Version:           1.0.0
 * Author:            Creative Computer
 * Author URI:        https://creativecomputerms.com
 */

add_action('admin_menu', 'c3_sms_blasts_setup_menu');
add_action('init', 'c3_sms_blasts_staff_cpt');

function c3_sms_blasts_setup_menu(){
     add_menu_page( 'SMS Blasts', 'SMS Blasts', 'manage_options', 'c3-sms-blasts', 'c3_sms_blasts' );
}

function c3_sms_blasts(){
	echo '<div class="wrap">';

	// Verify WP Twilio Core is installed
	if( !defined( 'TWL_TD' ) ) {
		twilio_core_not_installed();
	}
	else {
		c3_sms_blasts_home();
	}

	echo '</div>';
}

function c3_sms_blasts_home() {
	echo "<h1>C3 SMS Blasts</h1>";

	c3_sms_blasts_form_panel();

	// c3_sms_blasts_diag_panel();

	c3_sms_blasts_contacts_panel();
}

function c3_sms_blasts_diag_panel() {
	echo '<div class="welcome-panel">';
		echo '<div class="welcome-panel-content">';
			echo '<h2>Diagnostic Panel</h2>';
			echo '<p>POST values:</p><pre>';
				if( isset( $_POST ) && !empty( $_POST ) ) {
					print_r( $_POST );
				}
				else {
					echo 'POST NOT SET';
				}
			echo '</pre>';
		echo '</div>';
	echo '</div>';
}

function c3_sms_blasts_form_panel() {
	echo '<div class="welcome-panel">';
		echo '<div class="welcome-panel-content">';
			echo '<form method="POST" id="c3_sms_blasts">';
				echo '<input style="padding: 3px 8px;font-size: 1.7em;line-height: 100%;height: 1.7em;width: 100%;outline: 0;margin: 0 0 3px;background-color: #fff;" type="text" id="title" placeholder="Enter SMS text here" name="sms_message" required>';
				echo '<p><button class="button button-primary button-large">Send SMS Blast</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
}

function c3_sms_blasts_contacts_panel() {
	echo '<div class="welcome-panel">';
		echo '<div class="welcome-panel-content">';

			echo '<h2>Contacts</h2>';

			$args = array (
				'post_type'              => array( 'sms_contacts' ),
				'post_status'            => array( 'publish' ),
				'nopaging'               => true,
				'order'                  => 'ASC'
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
				echo '<ul>';
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					if( isset( $_POST ) && !empty( $_POST ) ) {
                        $phone_number = get_the_title();
                        preg_replace('/\W/g', '', $phone_number);

						$sms_args = array(
							'number_to' => '+1'.$phone_number,
							'message' => $_POST['sms_message'],
						);
						twl_send_sms( $sms_args );
					}

					echo '<li>+1'.get_the_title().'</li>';
				}
				echo '</ul>';
				wp_reset_postdata();
			} else {
				// no posts found
			}
		echo '</div>';
	echo '</div>';
}

function c3_sms_blasts_staff_cpt() {
	register_post_type('sms_contacts',
		array(
			'public' => true,
			'has_archive' => false,
			'menu_icon' => 'dashicons-groups',
			'labels' => array(
				'name' => __('SMS Contacts'),
				'singular_name' => __('SMS Contact'),
				'add_new' => __('Add New'),
				'add_new_item' => __('Add New SMS Contact'),
				'edit' => __('Edit'),
				'edit_item' => __('Edit SMS Contact'),
				'new_item' => __('New SMS Contact'),
				'view' => __('View SMS Contact'),
				'view_item' => __('View SMS Contact'),
				'search_items' => __('Search SMS Contacts'),
				'not_found' => __('No SMS Contacts found'),
				'not_found_in_trash' => __('No SMS Contacts found in Trash'),
				'parent' => __('Parent SMS Contact'),
			),
			'supports' => array('title')
		)
	);
}

function twilio_core_not_installed () {
	echo '<h1>Install WP Twilio Core first!</h1>';
	echo '<p><a href="https://themebound.com/shop/wp-twilio-core/" target="_blank">Click here to view plugin</a></p>';
}
