<?php
/**
 * Exit if accessed directly.
 *
 * @package TPWCP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TPWCP_Admin' ) ) {
	/**
	 * Class to create additional product panel in admin
	 */
	class TPWCP_Admin {
		/**
		 * __construct() function fir admin.
		 */
		public function __construct() {
		}

		/**
		 * __init() function fir admin.
		 */
		public function init() {
			// Create the custom tab.
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'create_giftwrap_tab' ) );
			// Add the custom fields.
			add_action( 'woocommerce_product_data_panels', array( $this, 'display_giftwrap_fields' ) );
			// Save the custom fields.
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
		}

		/**
		 * Add the new tab to the $tabs array
		 *
		 * @see https://github.com/woocommerce/woocommerce/blob/e1a82a412773c932e76b855a97bd5ce9dedf9c44/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
		 * @param [string] $tabs is a string.
		 * @since   1.0.0
		 */
		public function create_giftwrap_tab( $tabs ) {
			$tabs['giftwrap'] = array(
				'label'    => __( 'Giftwrap', 'tpwcp' ), // The name of your panel.
				'target'   => 'gifwrap_panel', // Will be used to create an anchor link so needs to be unique.
				'class'    => array( 'giftwrap_tab', 'show_if_simple', 'show_if_variable' ), // Class for your panel tab - helps hide/show depending on product type.
				'priority' => 80, // Where your panel will appear. By default, 70 is last item.
			);
			return $tabs;
		}

		/**
		 * Display fields for the new panel
		 *
		 * @see https://docs.woocommerce.com/wc-apidocs/source-function-woocommerce_wp_checkbox.html
		 * @since   1.0.0
		 */
		public function display_giftwrap_fields() { ?>

			<div id='gifwrap_panel' class='panel woocommerce_options_panel'>
				<div class="options_group">
					<?php
					woocommerce_wp_checkbox(
						array(
							'id'          => 'include_giftwrap_option',
							'label'       => __( 'Include giftwrap option', 'tpwcp' ),
							'desc_tip'    => true,
							'description' => __( 'Select this option to show giftwrapping options for this product', 'tpwcp' ),
						)
					);
					woocommerce_wp_checkbox(
						array(
							'id'          => 'include_custom_message',
							'label'       => __( 'Include custom message', 'tpwcp' ),
							'desc_tip'    => true,
							'description' => __( 'Select this option to allow customers to include a custom message', 'tpwcp' ),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => 'giftwrap_cost',
							'label'       => __( 'Giftwrap cost', 'tpwcp' ),
							'type'        => 'number',
							'desc_tip'    => true,
							'description' => __( 'Enter the cost of gift wrapping this product', 'tpwcp' ),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => 'person_name',
							'label'       => __( 'Name of Receiver', 'tpwcp' ),
							'type'        => 'text',
							'desc_tip'    => true,
							'description' => __( 'Enter the name of the person who will recieve this gift', 'tpwcp' ),
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'          => 'date',
							'label'       => __( 'Expected date for gift to be delivered', 'tpwcp' ),
							'type'        => 'date',
							'desc_tip'    => true,
							'description' => __( 'Enter the date on which you expect to be deliverd', 'tpwcp' ),
						)
					);

					?>
			</div>
		</div>

			<?php
		}

		/**
		 * Save the custom fields using CRUD method
		 *
		 * @param [string] $post_id is a string.
		 * @since 1.0.0
		 */
		public function save_fields( $post_id ) {

			$product = wc_get_product( $post_id );
			if ( ! empty( $_REQUEST['nonce'] ) ) {

				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) );
				if ( ! wp_verify_nonce( $nonce, 'nonce' ) ) {
					echo 'Nonce value cannot be verified';
				}
			}

			// Save the include_giftwrap_option setting.
			$include_giftwrap_option = isset( $_POST['include_giftwrap_option'] ) ? 'yes' : 'no';
			// update_post_meta( $post_id, 'include_giftwrap_option', sanitize_text_field( $include_giftwrap_option ) );.
			$product->update_meta_data( 'include_giftwrap_option', sanitize_text_field( $include_giftwrap_option ) );

			// Save the include_giftwrap_option setting.
			$include_custom_message = isset( $_POST['include_custom_message'] ) ? 'yes' : 'no';
			$product->update_meta_data( 'include_custom_message', sanitize_text_field( $include_custom_message ) );

			// Save the giftwrap_cost setting.
			$giftwrap_cost = isset( $_POST['giftwrap_cost'] ) ? sanitize_text_field( wp_unslash( $_POST['giftwrap_cost'] ) ) : '';
			$product->update_meta_data( 'giftwrap_cost', sanitize_text_field( $giftwrap_cost ) );

			// Save the giftwrap_cost setting.
			$person_name = isset( $_POST['person_name'] ) ? sanitize_text_field( wp_unslash( $_POST['person_name'] ) ) : '';
			$product->update_meta_data( 'person_name', sanitize_text_field( $person_name ) );

			// Save the giftwrap_cost setting.
			$date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
			$product->update_meta_data( 'date', sanitize_text_field( $date ) );

			$product->save();

		}

	}

	/**
	 * Display custom field on the front end
	 *
	 * @since 1.0.0
	 */
	function cfwc_display_custom_checkbox() {
		global $post;
		// Check for the custom field value.
		$product = wc_get_product( $post->ID );
		$check   = $product->get_meta( 'include_giftwrap_option' );
		$msg     = $product->get_meta( 'include_custom_message' );
		$price   = $product->get_meta( 'giftwrap_cost' );
		if ( 'yes' === $check ) {
			// Only display our field if we've got a value for the field title.
			printf(
				'<div class="giftwrap">
				<p><input type="checkbox" id="cfwc-custom-checkbox" name="cfwc-custom-checkbox" value="">Giftwrap this product? ($%s)<br>',
				esc_html( $price )
			);
		}
		if ( 'yes' === $msg ) {
			printf(
				'<br><label for="cfwc-custom-message">Add a custom message?</label><br><input type="text" id="cfwc-custom-message" name="cfwc-custom-message" value=""></p></div>',
			);
		}
	}
	add_action( 'woocommerce_before_add_to_cart_button', 'cfwc_display_custom_checkbox' );
}
