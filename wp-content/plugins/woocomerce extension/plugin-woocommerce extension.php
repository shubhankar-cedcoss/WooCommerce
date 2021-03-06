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
	 * WC_Settings_Tab_Demo
	 */
	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-settings-tab-demo.php';
	new WC_Settings_Tab_Demo();

	/**
	 * Removes that product from shop page whose category is passed in terms field
	 *
	 * @param [string] $q is a string .
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
	 * Add custom tracking code to the thank-you page
	 *
	 * @param [string] $order_id is a string.
	 */
	function my_custom_tracking( $order_id ) {

		// Lets grab the order.
		$order = wc_get_order( $order_id );

		/**
		 * Put your tracking code here
		 * You can get the order total etc e.g. $order->get_total();
		 */

		// This is the order total.
		$order->get_total();

		// This is how to grab line items from the order .
		$line_items = $order->get_items();

		// This loops over line items.
		foreach ( $line_items as $item ) {
			// This will be a product.
			$product = $order->get_product_from_item( $item );

			// This is the products SKU.
			$sku = $product->get_sku();

			// This is the qty purchased.
			$qty = $item['qty'];

			// Line item total cost including taxes and rounded.
			$total = $order->get_line_total( $item, true, true );

			// Line item subtotal (before discounts).
			$subtotal = $order->get_line_subtotal( $item, true, true );
		}
	}
	add_action( 'woocommerce_thankyou', 'my_custom_tracking' );

	/**
	 * Override loop template and show quantities next to add to cart buttons
	 *
	 * @param [string] $html is a string .
	 * @param [string] $product is a string .
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
	 * Change the default country on the checkout page
	 */
	function change_default_checkout_country() {
		return 'IN'; // country code.
	}
	/**
	 * Change the default state on the checkout page
	 */
	function change_default_checkout_state() {
		return 'UP'; // state code.
	}
	add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );
	add_filter( 'default_checkout_billing_state', 'change_default_checkout_state' );

	/**
	 * Add custom sorting options (asc/desc)
	 *
	 * @param [string] $args is a string .
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
	 * Allow HTML in term (category, tag) descriptions
	 */
	function something() {
		foreach ( array( 'pre_term_description' ) as $filter ) {
			remove_filter( $filter, 'wp_filter_kses' );
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				add_filter( $filter, 'wp_filter_post_kses' );
			}
		}

		foreach ( array( 'term_description' ) as $filter ) {
			remove_filter( $filter, 'wp_kses_data' );
		}
	}
	add_action( 'init', 'something' );


	/**
	 * Undocumented function
	 *
	 * @param [string] $sortby is a string .
	 * @return array
	 */
	function custom_woocommerce_catalog_orderby( $sortby ) {
		$sortby['random_list'] = 'Random';
		return $sortby;
	}
	add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
	add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );

	/**
	 * Customize product data tabs
	 *
	 * @param [string] $tabs is a string .
	 */
	function woo_custom_description_tab( $tabs ) {

		$tabs['description']['callback'] = 'woo_custom_description_tab_content';// Custom description callback.

		return $tabs;
	}
	add_filter( 'woocommerce_product_tabs', 'woo_custom_description_tab', 98 );

	/**
	 * Form for custom decription
	 */
	function woo_custom_description_tab_content() {
		echo '<h2>Custom Description</h2>';
		echo '<p>Here\'s a custom description</p>';
	}

	/**
	 * Remove product data tabs
	 *
	 * @param [string] $tabs is a string .
	 */
	function woo_remove_product_tabs( $tabs ) {

		unset( $tabs['description'] );      // Remove the description tab.
		unset( $tabs['reviews'] ); // Remove the reviews tab.
		unset( $tabs['additional_information'] ); // Remove the additional information tab.

		return $tabs;
	}
	// add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );.

	/**
	 * Rename product data tabs
	 *
	 * @param [string] $tabs is a string .
	 */
	function woo_rename_tabs( $tabs ) {

		$tabs['description']['title']            = __( 'More Information' );  // Rename the description tab.
		$tabs['reviews']['title']                = __( 'Ratings' );// Rename the reviews tab.
		$tabs['additional_information']['title'] = __( 'Product Data' ); // Rename the additional information tab.

		return $tabs;
	}
	add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );

	/**
	 * Add a custom product data tab
	 *
	 * @param [string] $tabs is a string .
	 */
	function woo_new_product_tab( $tabs ) {

		// Adds the new tab.

		$tabs['test_tab'] = array(
			'title'    => __( 'New Product Tab', 'woocommerce' ),
			'priority' => 50,
			'callback' => 'woo_new_product_tab_content',
		);

		return $tabs;

	}
	add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );

	/**
	 * Adding a custom tab
	 */
	function woo_new_product_tab_content() {

		// The new tab content.

		echo '<h2>New Product Tab</h2>';
		echo '<p>Here\'s your new product tab.</p>';

	}

	/**
	 * Hide sub-category product count in product archives
	 */
	add_filter( 'woocommerce_subcategory_count_html', '__return_false' );

	/**
	 * Remove product content based on category
	 * Removes the image of product on the single product page
	 */
	function remove_product_content() {
		// If a product in the 'shoes' category is being viewed...
		if ( is_product() && has_term( 'shoes', 'product_cat' ) ) {
			// ... Remove the images.
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			// For a full list of what can be removed please see woocommerce-hooks.php.
		}
	}
	// add_action( 'wp', 'remove_product_content' );.

	/**
	 * Auto Complete all WooCommerce orders.
	 *
	 * @param [string] $order_id is a string .
	 */
	function custom_woocommerce_auto_complete_order( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		$order->update_status( 'processing' );
	}
	add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );

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




					// / / // GENERAL SNIPPETS //////

	if ( ! function_exists( 'woocommerce_template_single_excerpt' ) ) {
		/**
		 * Allow shortcodes in product excerpts
		 *
		 * Woocommerce_template_single_excerpt()
		 *
		 * @param [string] $post is string.
		 */
		function woocommerce_template_single_excerpt( $post ) {
			global $post;
			if ( $post->post_excerpt ) {
				echo '<div itemprop="description">' . do_shortcode( wpautop( wptexturize( $post->post_excerpt ) ) ) . '</div>';
			}
		}
	}

	/**
	 * Send an email each time an order with coupon(s) is completed
	 * The email contains coupon(s) used during checkout process
	 *
	 * @param [string] $order_id is a string .
	 */
	function woo_email_order_coupons( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( $order->get_coupon_codes() ) {

			$to      = 'youremail@yourcompany.com';
			$subject = 'New Order Completed';
			$headers = 'From: My Name <youremail@yourcompany.com>' . "\r\n";

			$message  = 'A new order has been completed.\n';
			$message .= 'Order ID: ' . $order_id . '\n';
			$message .= 'Coupons used:\n';

			foreach ( $order->get_coupon_codes() as $coupon ) {
				$message .= $coupon . '\n';
			}
			@wp_mail( $to, $subject, $message, $headers );
		}
	}
	add_action( 'woocommerce_thankyou', 'woo_email_order_coupons' );

	/**
	 * Goes in theme functions.php or a custom plugin
	 *
	 * @param [string] $subject is a string .
	 * @param [string] $order is a string
	 *
	 * Subject filters:
	 *   woocommerce_email_subject_new_order
	 *   woocommerce_email_subject_customer_processing_order
	 *   woocommerce_email_subject_customer_completed_order
	 *   woocommerce_email_subject_customer_invoice
	 *   woocommerce_email_subject_customer_note
	 *   woocommerce_email_subject_low_stock
	 *   woocommerce_email_subject_no_stock
	 *   woocommerce_email_subject_backorder
	 *   woocommerce_email_subject_customer_new_account
	 *   woocommerce_email_subject_customer_invoice_paid.
	 */
	function change_admin_email_subject( $subject, $order ) {
		global $woocommerce;

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$subject = sprintf( '[%s] New Customer Order (# %s) from Name %s %s', $blogname, $order->id, $order->billing_first_name, $order->billing_last_name );

		return $subject;
	}
	add_filter( 'woocommerce_email_subject_new_order', 'change_admin_email_subject', 1, 2 );


	/**
	 * Adjust the quantity input values
	 *
	 *  @param [string] $args is a string .
	 *  @param [string] $product is a string .
	 */
	function jk_woocommerce_quantity_input_args( $args, $product ) {
		if ( is_singular( 'product' ) ) {
			$args['input_value'] = 1; // Starting value (we only want to affect product pages, not cart).
		}
		$args['max_value'] = 80; // Maximum value.
		$args['min_value'] = 1;  // Minimum value.
		$args['step']      = 1;  // Quantity steps.
		return $args;
	}
	add_filter( 'woocommerce_quantity_input_args', 'jk_woocommerce_quantity_input_args', 10, 2 ); // Simple products.
	/**
	 * Adjust the quantity input values
	 *
	 *  @param [string] $args is a string .
	 */
	function jk_woocommerce_available_variation( $args ) {
		$args['max_qty'] = 80; // Maximum value (variations).
		$args['min_qty'] = 4;  // Minimum value (variations).
		return $args;
	}
	add_filter( 'woocommerce_available_variation', 'jk_woocommerce_available_variation' ); // Variations.

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
		* Localisation
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
	}
		add_action( 'plugins_loaded', 'woocommerce_gateway_name_init', 0 );

	/**
	 * Allow customers to access wp-admin
	 */
	add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
	add_filter( 'woocommerce_disable_admin_bar', '__return_false' );


	/**
	 * Automatically add product whose ID is given to cart on visit
	 */
	function add_product_to_cart() {
		if ( ! is_admin() ) {
			$product_id = 50; // replace with your own product id.
			$found      = false;
			// check if product already in cart.
			if ( count( WC()->cart->get_cart() ) > 0 ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->get_id() === $product_id ) {
						$found = true;
					}
				}
				// if product not found, add it.
				if ( ! $found ) {
					WC()->cart->add_to_cart( $product_id );
				}
			} else {
				// if no products in cart, add it.
				WC()->cart->add_to_cart( $product_id );
			}
		}
	}
	// add_action( 'template_redirect', 'add_product_to_cart' );.

	/**
	 * Show product weight on archive pages(Shop page)
	 */
	function rs_show_weights() {

		global $product;
		$weight = $product->get_weight();

		if ( $product->has_weight() ) {
			echo '<div class="product-meta"><span class="product-meta-label">Weight: </span>' . $weight . get_option( 'woocommerce_weight_unit' ) . '</div></br>';
		}
	}
	add_action( 'woocommerce_after_shop_loop_item', 'rs_show_weights', 9 );

	/**
	 * Prevent PO box shipping
	 *
	 * @param [string] $posted is a string.
	 */
	function deny_pobox_postcode( $posted ) {
		global $woocommerce;

		$address  = ( isset( $posted['shipping_address_1'] ) ) ? $posted['shipping_address_1'] : $posted['billing_address_1'];
		$postcode = ( isset( $posted['shipping_postcode'] ) ) ? $posted['shipping_postcode'] : $posted['billing_postcode'];

		$replace  = array( ' ', '.', ',' );
		$address  = strtolower( str_replace( $replace, '', $address ) );
		$postcode = strtolower( str_replace( $replace, '', $postcode ) );

		if ( strstr( $address, 'pobox' ) || strstr( $postcode, 'pobox' ) ) {
			wc_add_notice( sprintf( __( 'Sorry, we cannot ship to PO BOX addresses.' ) ), 'error' );
		}
	}
	add_action( 'woocommerce_after_checkout_validation', 'deny_pobox_postcode' );

	/**
	 * Notify admin when a new customer account is created
	 *
	 * @param [string] $customer_id is a string .
	 */
	function woocommerce_created_customer_admin_notification( $customer_id ) {
		wp_send_new_user_notifications( $customer_id, 'admin' );
	}
	add_action( 'woocommerce_created_customer', 'woocommerce_created_customer_admin_notification' );

	/**
	 * Trim zeros in price decimals
	 */
	add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

	/**
	 * Display product attribute archive links
	 */
	function wc_show_attribute_links() {
		// if you'd like to show it on archive page, replace "woocommerce_product_meta_end" with "woocommerce_shop_loop_item_title".
		global $post;
		$attribute_names = array( 'pa_color', 'size' ); // Add attribute names here and remember to add the pa_ prefix to the attribute name.

		foreach ( $attribute_names as $attribute_name ) {
			$taxonomy = get_taxonomy( $attribute_name );

			if ( $taxonomy && ! is_wp_error( $taxonomy ) ) {
				$terms       = wp_get_post_terms( $post->ID, $attribute_name );
				$terms_array = array();

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$archive_link = get_term_link( $term->slug, $attribute_name );
						$full_line    = '<a href="' . $archive_link . '">' . $term->name . '</a>';
						array_push( $terms_array, $full_line );
					}
					echo $taxonomy->labels->name . ' ' . implode( $terms_array, ', ' );
				}
			}
		}
	}
	add_action( 'woocommerce_product_meta_end', 'wc_show_attribute_links' );
	add_action( 'woocommerce_shop_loop_item_title', 'wc_show_attribute_links' );

	/**
	 * Show product dimensions on archive pages for WC 3+
	 */
	function rs_show_dimensions() {
		global $product;
		$dimensions = wc_format_dimensions( $product->get_dimensions( false ) );

		if ( $product->has_dimensions() ) {
			echo '<div class="product-meta"><span class="product-meta-label">Dimensions: </span>' . $dimensions . '</div>';
		}
	}
	add_action( 'woocommerce_after_shop_loop_item', 'rs_show_dimensions', 9 );


	/**
	 * After applying this only follwing states are shown on states list in checkout page
	 *
	 * @param [string] $states is a string .
	 */
	function custom_woocommerce_states( $states ) {
		$states['IN'] = array(
			'IN1' => 'Maharashtra',
			'IN2' => 'Uttar Pradesh',
			'IN3' => 'Punjab',
		);

		return $states;
	}
	// add_filter( 'woocommerce_states', 'custom_woocommerce_states' );.


	/**
	 * Unhook and remove WooCommerce default emails.
	 *
	 * @param [string] $email_class is a string .
	 */
	function unhook_those_pesky_emails( $email_class ) {
		// Hooks for sending emails during store events.

		remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
		remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
		remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );

		// New order emails.
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

		// Processing order emails.
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

		// Completed order emails.
		remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );

		// Note emails.
		remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
	}
	add_action( 'woocommerce_email', 'unhook_those_pesky_emails' );

	/**
	 * Hide shipping rates when free shipping is available.
	 * Updated to support WooCommerce 2.6 Shipping Zones.
	 *
	 * @param array $rates Array of rates found for the package.
	 * @return array
	 */
	function my_hide_shipping_when_free_is_available( $rates ) {
		$free = array();
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'free_shipping' === $rate->method_id ) {
				$free[ $rate_id ] = $rate;
				break;
			}
		}
		return ! empty( $free ) ? $free : $rates;
	}
	add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );

	/**
	 * Set a minimum order amount for checkout
	 */
	function wc_minimum_order_amount() {
		// Set this variable to specify a minimum order value.
		$minimum = 20;

		if ( WC()->cart->total < $minimum ) {

			if ( is_cart() ) {

				wc_print_notice(
					sprintf(
						'Your current order total is %s — you must have an order with a minimum of %s to place your order ',
						wc_price( WC()->cart->total ),
						wc_price( $minimum )
					),
					'error'
				);

			} else {

				wc_add_notice(
					sprintf(
						'Your current order total is %s — you must have an order with a minimum of %s to place your order',
						wc_price( WC()->cart->total ),
						wc_price( $minimum )
					),
					'error'
				);

			}
		}
	}
	add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
	add_action( 'woocommerce_before_cart', 'wc_minimum_order_amount' );




					// // THEMING SNIPPET////.



	/**
	 * Change number of products that are displayed per page (shop page)
	 *
	 * @param [string] $cols is a string .
	 */
	function new_loop_shop_per_page( $cols ) {
		// $cols contains the current number of products per page based on the value stored on Options -> Reading
		// Return the number of products you wanna show per page.
		$cols = 4;
		return $cols;
	}
	add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

	/**
	 * Change number or products per row to 3
	 */
	if ( ! function_exists( 'loop_columns' ) ) {
		/**
		 * Loop column
		 */
		function loop_columns() {
			return 2; // 3 products per row
		}
	}
	add_filter( 'loop_shop_columns', 'loop_columns', 999 );

	/**
	 * Disable all stylesheets by WooCommerce
	 */
	add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

	/**
	 * Show cart contents / total Ajax
	 *
	 * @param [string] $fragments is a string.
	 */
	function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;

		ob_start();

		?>
		<a class="cart-customlocation" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'woothemes' ); ?>"><?php echo sprintf( _n( '%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes' ), $woocommerce->cart->cart_contents_count ); ?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
		<?php
		$fragments['a.cart-customlocation'] = ob_get_clean();
		return $fragments;
	}
	add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );

	/**
	 * Hide Add to cart button for out of stock items .
	 */
	if ( ! function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
		/**
		 * Woocommerce_template_loop_add_to_cart
		 *
		 * @return void
		 */
		function woocommerce_template_loop_add_to_cart() {
			global $product;
			if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
				return;
			}
			wc_get_template( 'loop/add-to-cart.php' );
		}
	}

	/**
	 * Change number of related products output
	 */
	function woo_related_products_limit() {
		global $product;

		$args['posts_per_page'] = 3;
		return $args;
	}
	add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
	/**
	 * Jk_related_products_args
	 *
	 * @param [string] $args is a string .
	 */
	function jk_related_products_args( $args ) {
		$args['posts_per_page'] = 4; // 4 related products
		$args['columns']        = 2; // arranged in 2 columns.
		return $args;
	}

	/**
	 * Change number of upsells output
	 *
	 * @param [string] $args is a string .
	 */
	function wc_change_number_related_products( $args ) {
		$args['posts_per_page'] = 2;
		$args['columns']        = 4; // change number of upsells here.
		return $args;
	}
	add_filter( 'woocommerce_upsell_display_args', 'wc_change_number_related_products', 20 );

	/**
	 * Change the placeholder image
	 *
	 * @param [string] $src is a string .
	 */
	function custom_woocommerce_placeholder_img_src( $src ) {
		$upload_dir = wp_upload_dir();
		$uploads    = untrailingslashit( $upload_dir['baseurl'] );
		// replace with path to your image.
		$src = $uploads . '/woocommerce-placeholder.png';

		return $src;
	}
	add_filter( 'woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src' );

	/**
	 * Show product categories in Woorramework breadcrumbs
	 * Get breadcrumbs on product pages that read: Home > Shop > Product category > Product Name.
	 *
	 * @param [string] $trail is a string.
	 */
	function woo_custom_breadcrumbs_trail_add_product_categories( $trail ) {
		if ( ( get_post_type() === 'product' ) && is_singular() ) {
			global $post;

			$taxonomy = 'product_cat';

			$terms = get_the_terms( $post->ID, $taxonomy );
			$links = array();

			if ( $terms && ! is_wp_error( $terms ) ) {
				$count = 0;
				foreach ( $terms as $c ) {
					$count++;
					if ( $count > 1 ) {
						continue;
					}
					$parents = woo_get_term_parents( $c->term_id, $taxonomy, true, ', ', $c->name, array() );

					if ( '' !== $parents && ! is_wp_error( $parents ) ) {
						$parents_arr = explode( ', ', $parents );

						foreach ( $parents_arr as $p ) {
							if ( '' !== $p ) {
								$links[] = $p;
							}
						}
					}
				}

				// Add the trail back on to the end.
				// $links[] = $trail['trail_end'];.
				$trail_end = get_the_title( $post->ID );

				// Add the new links, and the original trail's end, back into the trail.
				array_splice( $trail, 2, count( $trail ) - 1, $links );

				$trail['trail_end'] = $trail_end;
			}
		}

		return $trail;
	}
	add_filter( 'woo_breadcrumbs_trail', 'woo_custom_breadcrumbs_trail_add_product_categories', 20 );

	if ( ! function_exists( 'woo_get_term_parents' ) ) {
		/**
		 * Retrieve term parents with separator.
		 *
		 * @param int    $id Term ID.
		 * @param string $taxonomy is used to store taxonomy.
		 * @param bool   $link Optional, default is false. Whether to format with link.
		 * @param string $separator Optional, default is '/'. How to separate terms.
		 * @param bool   $nicename Optional, default is false. Whether to use nice name for display.
		 * @param array  $visited Optional. Already linked to terms to prevent duplicates.
		 * @return string
		 */
		function woo_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
			$chain  = '';
			$parent = &get_term( $id, $taxonomy );
			if ( is_wp_error( $parent ) ) {
				return $parent;
			}

			if ( $nicename ) {
				$name = $parent->slug;
			} else {
				$name = $parent->name;
			}

			if ( $parent->parent && ( $parent->parent !== $parent->term_id ) && ! in_array( $parent->parent, $visited, true ) ) {
				$visited[] = $parent->parent;
				$chain    .= woo_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
			}

			if ( $link ) {
				$chain .= '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s' ), $parent->name ) ) . '">' . $parent->name . '</a>' . $separator;
			} else {
				$chain .= $name . $separator;
			}
			return $chain;
		}
	}

	/**
	 * Remove related products output
	 */
	// remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );.
	add_filter( 'woocommerce_product_related_posts_query', '__return_empty_array', 100 );

	/**
	 * Check if WooCommerce is activated
	 */
	if ( ! function_exists( 'is_woocommerce_activated' ) ) {
		/**
		 * Is_woocommerce_activated
		 */
		function is_woocommerce_activated() {
			if ( class_exists( 'woocommerce' ) ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Changes the redirect URL for the Return To Shop button in the cart.
	 */
	function wc_empty_cart_redirect_url() {
		return 'www.google.com';
	}
	add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url', 11 );

	/**
	 * Hook in
	 * Our hooked in function - $address_fields is passed via the filter!
	 *
	 * @param [string] $address_fields is string.
	 */
	function custom_override_default_address_fields( $address_fields ) {
		$address_fields['billing']['billing_address_2']['required'] = true;

		return $address_fields;
	}
	add_filter( 'woocommerce_checkout_fields', 'custom_override_default_address_fields' );


	/**
	 * Our hooked in function - $fields is passed via the filter!
	 *
	 * @param [string] $fields is string.
	 */
	function custom_override_checkout_fields( $fields ) {
		$fields['shipping']['shipping_phone'] = array(
			'label'       => __( 'Phone', 'woocommerce' ),
			'placeholder' => _x( 'Phone', 'placeholder', 'woocommerce' ),
			'required'    => true,
			'class'       => array( 'form-row-wide' ),
			'clear'       => true,
		);

		return $fields;
	}
	// Hook in.
	add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

	/**
	 * Add the field to the checkout
	 *
	 * @param [string] $checkout is string.
	 */
	function my_custom_checkout_field( $checkout ) {

		echo '<div id="my_custom_checkout_field"><h2>' . __( 'My Customized Field' ) . '</h2>';

		woocommerce_form_field(
			'my_field_name',
			array(
				'type'        => 'text',
				'class'       => array( 'my-field-class form-row-wide' ),
				// 'required'    => 'true',.
				'label'       => __( 'Fill in this field' ),
				'placeholder' => __( 'Enter something' ),
			),
			$checkout->get_value( 'my_field_name' )
		);

		echo '</div>';
	}
	add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );

	/**
	 * Process the checkout
	 */
	function my_custom_checkout_field_process() {
		// Check if set, if its not set add an error.
		if ( sanitize_text_field( wp_unslash( ! $_POST['my_field_name'] ) ) ) {
			wc_add_notice( __( 'Please enter something into your customized field.' ), 'error' );
		}
	}
	// add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');.

	/**
	 * Update the order meta with field value
	 * my_custom_checkout_field_update_order_meta is used to update order
	 *
	 * @param [string] $order_id is a string.
	 */
	function my_custom_checkout_field_update_order_meta( $order_id ) {
		if ( ! empty( $_POST['my_field_name'] ) ) {
			update_post_meta( $order_id, 'My Field', sanitize_text_field( wp_unslash( $_POST['my_field_name'] ) ) );
		}
	}
	add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

	/**
	 * Display field value on the order edit page
	 *
	 * @param [string] $order is string.
	 */
	function my_custom_checkout_field_display_admin_order_meta( $order ) {
		echo '<p><strong>' . __( 'My Field' ) . ':</strong> ' . get_post_meta( $order->id, 'My Field', true ) . '</p>';
	}
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

	/**
	 * WC_Your_Shipping_Method is used for customized shipping method
	 */
	function your_shipping_method_init() {
		if ( ! class_exists( 'WC_Your_Shipping_Method' ) ) {
			require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-your-shipping-method.php';
			new WC_Your_Shipping_Method();
		}
	}
	add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );
	/**
	 * Add_your_shipping_method function is used to add custom shippig method
	 *
	 * @param [string] $methods is string.
	 */
	function add_your_shipping_method( $methods ) {
		$methods['your_shipping_method'] = 'WC_Your_Shipping_Method';
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );
}
