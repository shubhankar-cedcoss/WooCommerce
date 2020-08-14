<?php
/**
 * Plugin Name: Custom WP_remote Mailchimp Plugin
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: A plugin to handle mailchimp api to create user, lists, and others.
 * Version: 1.0
 *
 *  @package WordPress
 * Author: Shubh
 * Author URI: http://www.mywebsite.com
 */
function sync_mailchimp() {
	$api_key = '5dcf599a9ddb862bc704c25e3cb8c0d0-us17';
	$list_id = '261a6ae802';

	// $member_id   = md5( strtolower( $_POST['email'] ) );.
	$data_center = substr( $api_key, strpos( $api_key, '-' ) + 1 );
	$url         = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members';

	$data = array(
		'email_address' => wp_unslash( $_POST['email'] ),
		'status'        => 'subscribed',
		'merge_fields'  => array(
			'FNAME' => wp_unslash( $_POST['username'] ),
			'LNAME' => '',
		),

	);

	$args = array(
		'method'    => 'POST',
		'timeout'   => 10,
		'sslverify' => false, // true - will check to see if the SSL certificate is valid.
		'headers'   => array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
		),
		'body'      => wp_json_encode( $data ),
	);

	$result = wp_remote_post( $url, $args );
	//print_r( $result );
}
add_action( 'user_register', 'sync_mailchimp' );
