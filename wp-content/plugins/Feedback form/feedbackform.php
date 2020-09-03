<?php
/**
 * Plugin Name: Custom Feedback Form
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: A plugin to handle mailchimp api to create user, lists, and others.
 * Version: 1.0
 *
 *  @package WordPress
 * Author: Shubh
 * Author URI: http://www.mywebsite.com
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ), true ) ) ) {
	/**
	 * Define constants
	 */
	if ( ! defined( 'TPWCP_PLUGIN_VERSION' ) ) {
		define( 'TPWCP_PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'TPWCP_PLUGIN_DIR_PATH' ) ) {
		define( 'TPWCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );// if anything in future we require something.
	}

	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-feedback-form.php';
	new WC_Feedback_Form();

	/**
	 * Function to define shortcode
	 */
	function custom_shortcode() {
		$user = get_current_user_id();

		$output   = '';
		$field1   = get_option( 'custom_field1' );
		$field1_1 = get_option( 'custom_field1_1' );
		$field1_2 = get_option( 'custom_field1_2' );
		$field2   = get_option( 'custom_field2' );
		$field2_1 = get_option( 'custom_field2_1' );
		$field2_2 = get_option( 'custom_field2_2' );
		$field3   = get_option( 'custom_field3' );
		$field3_1 = get_option( 'custom_field3_1' );
		$field3_2 = get_option( 'custom_field3_2' );
		$field4   = get_option( 'custom_field4' );
		$field4_1 = get_option( 'custom_field4_1' );
		$field4_2 = get_option( 'custom_field4_2' );
		$field5   = get_option( 'custom_field5' );
		$field5_1 = get_option( 'custom_field5_1' );
		$field5_2 = get_option( 'custom_field5_2' );

		$array = array();
		if ( 'yes' === $field1_2 ) {
			$array[$field1] = isset( $_POST[ $field1 ] ) ? $_POST[ $field1 ] : '';
		}

		if ( 'yes' === $field2_2 ) {
			$array[$field2] = isset( $_POST[ $field2 ] ) ? $_POST[ $field2 ] : '';
		}

		if ( 'yes' === $field3_2 ) {
			$array[$field3] = isset( $_POST[ $field3 ] ) ? $_POST[ $field3 ] : '';
		}

		if ( 'yes' === $field4_2 ) {
			$array[$field4] = isset( $_POST[ $field4 ] ) ? $_POST[ $field4 ] : '';
		}

		if ( 'yes' === $field5_2 ) {
			$array[$field5] = isset( $_POST[ $field5 ] ) ? $_POST[ $field5 ] : 'u';
		}

		$array['comments'] = sanitize_text_field( wp_unslash( $_POST['comments'] ) );

		$user = get_current_user_id();

		update_user_meta( $user, 'feedback', $array );
		$name     = sanitize_text_field( wp_unslash( $_POST[ $field1 ] ) );
		$email    = sanitize_text_field( wp_unslash( $_POST[ $field2 ] ) );
		$phone    = sanitize_text_field( wp_unslash( $_POST[ $field3 ] ) );
		$addresss = sanitize_text_field( wp_unslash( $_POST[ $field4 ] ) );
		foreach ( $array as $k => $v ) {
			if ( 1 === preg_match( '/name/', $k ) ) {
				$name = isset( $name ) ? $name . ' ' . $v : $v;
			}
			if ( 1 === preg_match( '/@gmail.com/', $v ) ) {
				$email = $v;
			}
			if ( 1 === preg_match( '/[0-9]/', $v ) ) {
				$phone = $v;
			}
			if ( 1 === preg_match( '/address/', $k ) ) {
				$address = $v;
			}
		}

		// Retrieves information about the current site.
		$admin_email = get_bloginfo( 'admin_email' );
		echo $admin_email;
		$msg      = $array['comments'];
		$feedback = 'Name:' . $name . '  Feedback:' . $msg;
		$header   = array(
			'From:' . $name . '<' . $email . '>',
		);

		// mail to admin.
		wp_mail( $admin_email, 'Feedback', $feedback, $header );

		// mail to customer.
		wp_mail( $email, 'Confirmation', 'Hello ' . $name . ', Your Feedback has been submitted successfully.' );

		//if ( isset( $_POST['submit'] ) ) {
		// wc_add_notice( 'Thank you, Your Feedback has been submitted', 'success' );
		//}
		$output .= '<form action="" method="post">';
		if ( 'yes' === $field1_2 ) {
				$output .= '<label>' . $field1 . ':</label><br>
				<input type="' . $field1_1 . '" name="' . $field1 . '" ></input>
				<br>';
		}

		if ( 'yes' === $field2_2 ) {
			$output .= '<label>' . $field2 . ':</label><br>
			<input type="' . $field2_1 . '" name="' . $field2 . '" ></input>
			<br>';
		}

		if ( 'yes' === $field3_2 ) {
			$output .= '<label>' . $field3 . ':</label><br>
			<input type="' . $field3_1 . '" name="' . $field3 . '" ></input>
			<br>';
		}

		if ( 'yes' === $field4_2 ) {
			$output .= '<label>' . $field4 . ':</label><br>
			<input type="' . $field4_1 . '" name="' . $field4 . '"></input>
			<br>';
		}

		if ( 'yes' === $field5_2 ) {
			$output .= '<label>' . $field5 . ':</label><br>
			<input type="' . $field5_1 . '" name="' . $field5 . '"></label>
			<br>';
		}

		$output .= '<label>Feedback:</label>
					<textarea textarea name="comments" rows="7" cols="5"></textarea>
					<br><br>
					<input type="submit" name="submit" value="Submit">			
					</form>';

		echo $output;

	}
	add_shortcode( 'feedback_form', 'custom_shortcode' );

	/**
	 * Function for phpmailer_init
	 *
	 * @param [string] $phpmailer is string.
	 */
	function phpmailer_mail( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host     = 'smtp.gmail.com';
		$phpmailer->SMTPAuth = true; // Ask it to use authenticate using the Username and Password properties
		$phpmailer->Port     = 25;
		$phpmailer->Username = 'shubhankarsaxena10@gmail.com';
		$phpmailer->Password = '';

		$phpmailer->SMTPSecure = 'tls'; // Choose 'ssl' for SMTPS on port 465, or 'tls' for SMTP+STARTTLS on port 25 or 587
	}
	add_action( 'phpmailer_init', 'phpmailer_mail' );
}
