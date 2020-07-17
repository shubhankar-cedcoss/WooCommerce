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

	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-tpwcp-admin.php';

	/**
	 * To show custom field on front end
	 */
	if ( ! defined( 'TPWCP_PLUGIN_VERSION' ) ) {
		define( 'TPWCP_PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'TPWCP_PLUGIN_DIR_PATH' ) ) {
		define( 'TPWCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-tpwcp-front.php';

	/**
	 * Start the plugin.
	 */
	function tpwcp_init() {
		if ( is_admin() ) {
			$t_p_w_c_p = new TPWCP_Admin();
			$t_p_w_c_p->init();
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
			// 'terms'    => array( 'electronics' ), // Don't display products in the clothing category on the shop page.
			'operator' => 'NOT IN',
		);

		$q->set( 'tax_query', $tax_query );

	}
	add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );

	/**
	 * Override loop template and show quantities next to add to cart buttons
	 */
	function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
			$html  = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
			$html .= woocommerce_quantity_input( array(), $product, false );
			$html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
			$html .= '</form>';
		}
		return $html;
	}
	add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );

	/**
	 * Change the default state and country on the checkout page
	 */
	function change_default_checkout_country() {
		return 'IN'; // country code.
	}

	function change_default_checkout_state() {
		return 'UP'; // state code.
	}
	add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );
	add_filter( 'default_checkout_billing_state', 'change_default_checkout_state' );

	/**
	 * Add custom sorting options (asc/desc)
	 */
	function custom_woocommerce_get_catalog_ordering_args( $args ) {
		$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		if ( 'random_list' === $orderby_value ) {
			$args['orderby']  = 'rand';
			$args['order']    = '';
			$args['meta_key'] = '';
		}
		return $args;
	}
	add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );

	/**
	 * Undocumented function
	 *
	 * @param [type] $sortby
	 * @return void
	 */
	function custom_woocommerce_catalog_orderby( $sortby ) {
		$sortby['random_list'] = 'Random';
		return $sortby;
	}
	add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
	add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );

	/**
	 * Remove product data tabs
	 */
	// add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );.

	function woo_remove_product_tabs( $tabs ) {

		unset( $tabs['description'] );      // Remove the description tab.
		unset( $tabs['reviews'] ); // Remove the reviews tab.
		unset( $tabs['additional_information'] ); // Remove the additional information tab.

		return $tabs;
	}

	/**
	 * Remove product content based on category
	 * Removes the image of product on the single product page
	 */
	function remove_product_content() {
		// If a product in the 'shoes' category is being viewed...
		// if ( is_product() && has_term( 'shoes', 'product_cat' ) ) {
			// ... Remove the images.
			// remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			// For a full list of what can be removed please see woocommerce-hooks.php.
		// }.
	}
	add_action( 'wp', 'remove_product_content' );

	/**
	 * Add a 1% surcharge to your cart / checkout
	 * change the $percentage to set the surcharge to a value to suit
	 */
	function woocommerce_custom_surcharge() {
		global $woocommerce;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		$percentage = 0.01;
		$surcharge  = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;
		$woocommerce->cart->add_fee( 'Surcharge', $surcharge, true, '' );

	}
	add_action( 'woocommerce_cart_calculate_fees', 'woocommerce_custom_surcharge' );

	/**
	 * Add a message above the login / register form on my-account page
	 */
	function jk_login_message() {
		if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
			?>
			<div class="woocommerce-info">
				<p><?php esc_html_e( 'Returning customers login. New users register for next time so you can:' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'View your order history' ); ?></li>
					<li><?php esc_html_e( 'Check on your orders' ); ?></li>
					<li><?php esc_html_e( 'Edit your addresses' ); ?></li>
					<li><?php esc_html_e( 'Change your password' ); ?></li>
				</ul>
			</div>
			<?php
		}
	}
	add_action( 'woocommerce_before_customer_login_form', 'jk_login_message' );

	/**
	 * Apply a coupon for minimum cart total
	 */
	function add_coupon_notice() {

			$cart_total     = WC()->cart->get_subtotal();
			$minimum_amount = 50;
			$currency_code  = get_woocommerce_currency();
			wc_clear_notices();

		if ( $cart_total < $minimum_amount ) {
				WC()->cart->remove_coupon( 'FIRST50' );
				wc_print_notice( "Get 30% off if you spend more than $minimum_amount $currency_code!", 'notice' );
		} else {
				WC()->cart->apply_coupon( 'FIRST50' );
				wc_print_notice( 'You just got 50% off your order!', 'notice' );
		}
			wc_clear_notices();
	}
	add_action( 'woocommerce_before_cart', 'add_coupon_notice' );
	add_action( 'woocommerce_before_checkout_form', 'add_coupon_notice' );
}
