<?php
/**
 * Plugin Name: Custom Payment Gateway
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: A plugin to handle mailchimp api to create user, lists, and others.
 * Version: 1.0
 *
 *  @package WordPress
 * Author: Shubh
 * Author URI: http://www.mywebsite.com
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
	/**
	 * Function to add a payment gateway
	 *
	 * @return void
	 */
	function woocommerce_gateway_name_init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}
		/**
		* Localisation loads a plugin’s translated strings.
		*/
		load_plugin_textdomain( 'wc-gateway-name', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		/**
		 * Gateway class
		 */
		require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-gateway-name.php';
		new WC_Gateway_Name();

		/**
		 * Add the Gateway to WooCommerce
		 *
		 * @param [type] $methods is used to store the custom payment gateway.
		 */
		function woocommerce_add_gateway_name_gateway( $methods ) {
			$methods[] = 'WC_Gateway_Name';
			return $methods;
		}
		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_name_gateway' );

		/**
		 * Gateway class for cod
		 */
		require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-gateway-cod.php';
		new WC_Gateway_COD();

		/**
		 * Add the Gateway to WooCommerce
		 *
		 * @param [type] $methods is used to store the custom payment gateway.
		 */
		function woocommerce_add_gateway_name_gateway_cod( $methods ) {
			$methods[] = 'WC_Gateway_COD';
			return $methods;
		}
		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_name_gateway_cod' );
	}
		add_action( 'plugins_loaded', 'woocommerce_gateway_name_init', 0 );
}
