<?php
/**
 * Plugin Name: WooCommerce Settings Tab Demo
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: This is my First WooCommmerce extension
 * Version: 1.0.0
 * Author: Shubhankar
 * Author URI: http://yourdomain.com/
 * Developer: Your Name
 * Developer URI: http://yourdomain.com/
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * @package : woocommerce
 *
 * Woo: 12345:342928dfsfhsf8429842374wdf4234sfd
 * WC requires at least: 2.2
 * WC tested up to: 2.3
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	class WC_Settings_Tab_Demo {

		/**
		 * Bootstraps the class and hooks required actions & filters.
		 */
		public static function init() {
			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
			add_action( 'woocommerce_settings_tabs_settings_tab_demo', __CLASS__ . '::settings_tab' );
			add_action( 'woocommerce_update_options_settings_tab_demo', __CLASS__ . '::update_settings' );
		}


		/**
		 * Add a new settings tab to the WooCommerce settings tabs array.
		 *
		 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
		 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
		 */
		public static function add_settings_tab( $settings_tabs ) {
			$settings_tabs['settings_tab_demo'] = __( 'Settings Demo Tab', 'woocommerce-settings-tab-demo' );
			return $settings_tabs;
		}


		/**
		 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
		 *
		 * @uses woocommerce_admin_fields()
		 * @uses self::get_settings()
		 */
		public static function settings_tab() {
			woocommerce_admin_fields( self::get_settings() );
		}


		/**
		 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
		 *
		 * @uses woocommerce_update_options()
		 * @uses self::get_settings()
		 */
		public static function update_settings() {
			woocommerce_update_options( self::get_settings() );
		}


		/**
		 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
		 *
		 * @return array Array of settings for @see woocommerce_admin_fields() function.
		 */
		public static function get_settings() {

			$settings = array(
				'section_title' => array(
					'name' => __( 'Section Title', 'woocommerce-settings-tab-demo' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'wc_settings_tab_demo_section_title',
				),
				'title'         => array(
					'name' => __( 'Title', 'woocommerce-settings-tab-demo' ),
					'type' => 'text',
					'desc' => __( 'This is some helper text', 'woocommerce-settings-tab-demo' ),
					'id'   => 'wc_settings_tab_demo_title',
				),
				'description'   => array(
					'name' => __( 'Description', 'woocommerce-settings-tab-demo' ),
					'type' => 'textarea',
					'desc' => __( 'This is a paragraph describing the setting. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda.', 'woocommerce-settings-tab-demo' ),
					'id'   => 'wc_settings_tab_demo_description',
				),
				'section_end'   => array(
					'type' => 'sectionend',
					'id'   => 'wc_settings_tab_demo_section_end',
				),
			);

			return apply_filters( 'wc_settings_tab_demo_settings', $settings );
		}

	}

	WC_Settings_Tab_Demo::init();

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Define constants
	 */
	if ( ! defined( 'TPWCP_PLUGIN_VERSION' ) ) {
		define( 'TPWCP_PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'TPWCP_PLUGIN_DIR_PATH' ) ) {
		define( 'TPWCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	require( TPWCP_PLUGIN_DIR_PATH . '/classes/class-tpwcp-admin.php' );

	/**
	 * Start the plugin.
	 */
	function tpwcp_init() {
		if ( is_admin() ) {
			$TPWCP = new TPWCP_Admin();
			$TPWCP->init();
		}
	}
	add_action( 'plugins_loaded', 'tpwcp_init' );

	/**
	 * Removes that product from shop page whose category is passed in terms field
	 */
	function custom_pre_get_posts_query( $q ) {

		$tax_query = (array) $q->get( 'tax_query' );

		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array( 'electronics' ), // Don't display products in the clothing category on the shop page.
			'operator' => 'NOT IN',
		);

		$q->set( 'tax_query', $tax_query );

	}
	add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );

}


