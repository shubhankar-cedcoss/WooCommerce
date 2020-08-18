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
	 * Function to show form on frontend
	 */
	function show_custom_form() {
		?>
		<div>
			<form action="" method="POST">
			<?php
				$name    = get_option( 'name_field' );
				$email   = get_option( 'email_field' );
				$message = get_option( 'query_field' );

				$name_field = 'Your name:<br><input type="text" name="realname">
				<br><br>';
				$email_field = 'Your email:<br><input type="text" name="email">
				<br><br>';
				$msg_field = 'Your comments:<br>
				<textarea name="comments" rows="7" cols="5">
				</textarea> <br><br>
				<input type="submit" name="submit" value="Submit">';

			if ( is_user_logged_in() ) {
				if ( 'yes' === $message ) {
					echo $msg_field;
				}
			} else {
				if ( 'yes' === $name ) {
					echo $name_field;
				}
				if ( 'yes' === $email ) {
					echo $email_field;
				}
				if ( 'yes' === $message ) {
					echo $msg_field;
				}
			}
			?>
			</form>
			<?php
				$fname      = sanitize_text_field( wp_unslash( $_POST['realname'] ) );
				$user_email = sanitize_text_field( wp_unslash( $_POST['email'] ) );
				$feedback   = sanitize_text_field( wp_unslash( $_POST['comments'] ) );

				$user = get_current_user_id();
				// echo $feedback;.
				update_user_meta( $user, 'feedback', $feedback );

				$deatils = wp_get_current_user();

				$customer_email = $deatils->billing_email;
				// echo $customer_email;.

			if ( is_user_logged_in() ) {
					wp_mail( $customer_email, 'Feedback', $feedback );
			} else {
				wp_mail( $user_email, 'Feedback', $feedback );
			}

			?>
		</div>
		<?php
	}
	add_action( 'woocommerce_thankyou', 'show_custom_form' );
}
