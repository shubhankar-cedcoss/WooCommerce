<?php
/**
 * Plugin Name: Rating Plugin
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
		define( 'TPWCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Function to add ratings
	 */
	function rating_fields() { ?>
		<div class="product_custom_field">
		<?php
		woocommerce_wp_text_input(
			array(
				'id'          => 'rating',
				'label'       => __( 'Ratings', 'woocommerce' ),
				'desc_tip'    => 'true',
				'description' => __( 'Here you can give ratings to the product' ),
			)
		);
		?>
		</div>
		<?php
	}
	add_action( 'woocommerce_product_options_general_product_data', 'rating_fields' );

	/**
	 * Save the custom fields using CRUD method
	 *
	 * @param [string] $post_id is a string.
	 */
	function save_field( $post_id ) {
		$product = wc_get_product( $post_id );

		// Save the giftwrap_cost setting.
		$rating = isset( $_POST['rating'] ) ? sanitize_text_field( wp_unslash( $_POST['rating'] ) ) : '';
		$product->update_meta_data( 'rating', sanitize_text_field( $rating ) );

		$product->save();
	}
	add_action( 'woocommerce_process_product_meta', 'save_field' );

	/**
	 * Display custom field on frontend
	 */
	function rating_frontend() {
		global $post;
		$product = wc_get_product( $post->ID );
		$rating  = $product->get_meta( 'rating' );

		if ( $rating ) {
			echo '<div class="ratings">' . str_repeat( '&#9734;', $rating ) . '</div>';
		}
	}
	add_action( 'woocommerce_after_shop_loop_item', 'rating_frontend' );



	/************************  End Rating Section  *******************************/



	/**
	 * Add the new tab to the $tabs array
	 *
	 * @param [string] $tabs is string.
	 */
	function create_tip_tab( $tabs ) {
		$tabs['tip'] = array(
			'label'    => __( 'Delivery tip' ),
			'target'   => 'tip_panel', // Will be used to create an anchor link so needs to be unique.
			'class'    => array( 'show_if_simple' ),
			'priority' => 80,
		);
		return $tabs;
	}
	add_filter( 'woocommerce_product_data_tabs', 'create_tip_tab' );

	/**
	 * Function to add tip field
	 */
	function woocommerce_tip_field() {
		?>
		<div id="tip_panel" class="panel woocommerce_options_panel">
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'       => 'delivery_tip',
					'label'    => __( 'Include delivery tip' ),
					'desc_tip' => __( 'By enabling this you can add tip for delivery boy' ),
				)
			);
			?>
		</div>
		<?php
	}
	add_action( 'woocommerce_product_data_panels', 'woocommerce_tip_field' );

	/**
	 * Save the custom fields using CRUD method
	 *
	 * @param [string] $post_id is a string.
	 */
	function save_fields( $post_id ) {
		$product = wc_get_product( $post_id );


		$delivery_tip = isset( $_POST['delivery_tip'] ) ? sanitize_text_field( wp_unslash( $_POST['delivery_tip'] ) ) : '';
		$product->update_meta_data( 'delivery_tip', sanitize_text_field( $delivery_tip ) );

		$product->save();
	}
	add_action( 'woocommerce_process_product_meta', 'save_fields' );

	/**
	 * Function shows the value on frontend
	 *
	 * @return void
	 */
	function show_tip_option_on_frontend() {
		$option = get_post_meta( get_the_ID(), 'delivery_tip', true );
		if ( 'yes' === $option ) {
			?>
			<div class="option">
				<h4>Please select a value for tip:</h4>
				<ul>
					<li style="list-style: none;"><input type="radio" id="option1" name="opt" value="5"> $5</li>
					<li style="list-style: none;"><input type="radio" id="option2" name="opt" value="10"> $10</li>
					<li style="list-style: none;"><input type="radio" id="option3" name="opt" value="15"> $15</li>
				</ul>
			</div>
			<?php
		}
	}
	add_action( 'woocommerce_before_add_to_cart_button', 'show_tip_option_on_frontend' );

	/**
	 * Below function is used to add cart item
	 *
	 * @param [string] $cart_item_data is string.
	 */
	function add_tip_option_to_cart_item( $cart_item_data ) {

		$option = filter_input( INPUT_POST, 'opt' );

		if ( empty( $option ) ) {

			return $cart_item_data;

		}

		$cart_item_data['opt'] = $option;

		return $cart_item_data;

	}
	add_filter( 'woocommerce_add_cart_item_data', 'add_tip_option_to_cart_item', 10, 1 );

	/**
	 * Function is used to display tip on cart page
	 *
	 * @param [string] $item_data is string.
	 * @param [string] $cart_item is string.
	 */
	function display_tip_on_cart_page( $item_data, $cart_item ) {

		if ( empty( $cart_item['opt'] ) ) {

			return $item_data;

		}

		$item_data[] = array(

			'key'     => __( 'tip', 'iconic' ),

			'value'   => wc_clean( $cart_item['opt'] ),

			'display' => '',

		);

		return $item_data;
	}
	add_filter( 'woocommerce_get_item_data', 'display_tip_on_cart_page', 10, 2 );
}
