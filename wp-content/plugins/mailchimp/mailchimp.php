<?php
/**
 * Plugin Name: Custom Mailchimp Plugin
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: A plugin to handle mailchimp api to create user, lists, and others.
 * Version: 1.0
 *
 *  @package WordPress
 * Author: Shubh
 * Author URI: http://www.mywebsite.com
 */

$data = array(
	'email'     => wp_unslash( $_POST['email'] ),
	'status'    => 'subscribed',
	'firstname' => wp_unslash( $_POST['username'] ),
	// 'lastname'  => wp_unslash( $_POST['reg_username'] ),
);
$dbms = sync_mailchimp( $data );
echo $dbms;

/**
 * Below function is used to sync mailchimp
 *
 * @param [string] $data is string.
 */
function sync_mailchimp( $data ) {
	$api_key = '5dcf599a9ddb862bc704c25e3cb8c0d0-us17';
	$list_id = '261a6ae802';

	$member_id   = md5( strtolower( $data['email'] ) );
	$data_center = substr( $api_key, strpos( $api_key, '-' ) + 1 );
	$url         = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . $member_id;

	$json = json_encode(
		array(
			'email_address' => $data['email'],
			'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
			'firstname'     => $data['firstname'],
			// 'merge_fields'  => array(
				// 'FNAME' => $data['firstname'],
				// 'LNAME' => $data['lastname'],
			// ),
		)
	);

	$ch = curl_init( $url );

	curl_setopt( $ch, CURLOPT_USERPWD, 'user:' . $api_key );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );

	$result = curl_exec( $ch );
	$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close( $ch );

	return $http_code . $result;
}
