<?php
/**
 * Plugin Name: Points and Rewards
 * Plugin URI: https://gist.github.com/BFTrick/b5e3afa6f4f83ba2e54a
 * Description: A plugin demonstrating how to add a WooCommerce settings tab.
 * Author: Shubh
 *
 * @package WooCommerce
 * Author URI: http://speakinginbytes.com/
 * Version: 1.0
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	/**
	 * Define constants
	 */
	if ( ! defined( 'TPWCP_PLUGIN_VERSION' ) ) {
		define( 'TPWCP_PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'TPWCP_PLUGIN_DIR_PATH' ) ) {
		define( 'TPWCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-points-and-rewards.php';
	new WC_Points_And_Rewards();

	/**
	 * Function is used for print points
	 */
	function gained_points() {
		$per_visit       = get_option( 'points_per_visit' );
		$per_add_to_cart = get_option( 'points_add_to_cart' );
		$per_checkout    = get_option( 'points_checkout' );
		$total           = $per_visit + $per_add_to_cart + $per_checkout;

		echo 'Your total points are ' . $total;
	}
	add_action( 'woocommerce_cart_coupon', 'gained_points' );

	/**
	 * Below function is used to show points column
	 *
	 * @param [string] $user_columns is string.
	 */
	function gained_points_column( $user_columns ) {

		return array_slice( $user_columns, 0, 3, true ) // 4 columns before
		+ array( 'earned_points' => 'Earned Points' ) // our column is 5th.
		+ array_slice( $user_columns, 3, null, true );
	}
	add_filter( 'manage_users_columns', 'gained_points_column', 20 );

	/**
	 * Below function is used to get current user role
	 */
	function get_user_role() {
		global $current_user;

		$user_roles = $current_user->roles;
		$user_role  = array_shift( $user_roles );

		return $user_role;
	}

	/**
	 * Below function is used to show points column fields
	 *
	 * @param [string] $row_output is string.
	 * @param [string] $user_column_name is string.
	 * @param [string] $user_id is string.
	 */
	function points_fields( $row_output, $user_column_name, $user_id ) {
		if ( 'earned_points' === $user_column_name ) {

			//$user_id = get_current_user_id();
			$points = get_user_meta( $user_id, 'points_gained', true );
			return $points;

		}
	}
	add_filter( 'manage_users_custom_column', 'points_fields', 10, 3 );

	/**
	 * Add_user_credita is used to add credit in user wallet
	 */
	function add_user_credit() {
		$wp_user_query = new WP_User_Query( array( 'role' => 'Customer' ) );

		// Get the result.
		$users = $wp_user_query->get_results();

		// Check for results.
		if ( ! empty( $users ) ) {
			// loop through each user.
			foreach ( $users as $user ) {
				// add meta key as points for all the user's data.
				add_user_meta( $user->id, 'points_gained', '0', true );
			}
		}
	}
	add_action( 'init', 'add_user_credit' );
	add_action( 'user_register', 'add_user_credit' );

	/**
	 * Calculate_points
	 */
	function calculate_points() {
		$user_role = get_user_role();
		$total     = '';
		$user_id   = get_current_user_id();
		//echo $user_role;

		if ( 'customer' === $user_role ) {
			$points = get_user_meta( $user_id, 'points_gained', true );
			//echo $points;
			if ( is_product() ) {
				$credit = get_option( 'points_per_visit' );
				$total  = $credit + $points;
				update_user_meta( $user_id, 'points_gained', $total );
			}

			if ( WC()->cart->get_cart_contents_count() !== 0 ) {
				$point  = get_user_meta( $user_id, 'points_gained', true );
				$credit = get_option( 'points_add_to_cart' );
				$total  = $point + $credit;
				update_user_meta( $user_id, 'points_gained', $total );
			}

			if ( is_wc_endpoint_url( 'order-received' ) ) {
				$point  = get_user_meta( $user_id, 'points_gained', true );
				$credit = get_option( 'points_checkout' );
				$total  = $point + $credit;
				update_user_meta( $user_id, 'points_gained', $total );
			}
			echo $total;
		}
	}
	add_action( 'wp_head', 'calculate_points' );
}

