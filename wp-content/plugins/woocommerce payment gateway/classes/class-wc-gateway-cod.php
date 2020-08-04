<?php
/**
 * Class to create payment gateway
 *
 * @package Woocommerce
 * @version 1.0.0
 */
class WC_Gateway_COD extends WC_Payment_Gateway {
	/**
	 * Construct function
	 */
	public function __construct() {
		$this->id                 = 'cod_payment';
		$this->icon               = apply_filters( 'woocommerce_custom_gateway_icon', '' );
		$this->method_title       = __( 'Custom Payment', 'woocommerce-other-payment-gateway' );
		$this->title              = __( 'Custom Payment', 'woocommerce-other-payment-gateway' );
		$this->method_description = __( 'Allows payments with custom gateway.', 'woocommerce-other-payment-gateway' );
		$this->has_fields         = true;

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->enabled     = $this->get_option( 'enabled' );
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * This function process the payment
	 *
	 * @param [string] $order_id is strong.
	 */
	function process_payment( $order_id ) {
		global $woocommerce;
		$order = new WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the cheque).
		$order->update_status( 'on-hold', __( 'Awaiting cheque payment', 'woocommerce' ) );

		// Remove cart.
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Function to create form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'     => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-other-payment-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Custom Payment', 'woocommerce-other-payment-gateway' ),
				'default' => 'yes',
			),
			'title'       => array(
				'title'       => __( 'Method Title', 'woocommerce-other-payment-gateway' ),
				'type'        => 'text',
				'description' => __( 'This controls the title', 'woocommerce-other-payment-gateway' ),
				'default'     => __( 'Custom Payment', 'woocommerce-other-payment-gateway' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Customer Message', 'woocommerce-other-payment-gateway' ),
				'type'        => 'textarea',
				'css'         => 'width:500px;',
				'default'     => 'None of the other payment options are suitable for you? please drop us a note about your favourable payment option and we will contact you as soon as possible.',
				'description' => __( 'The message which you want it to appear to the customer in the checkout page.', 'woocommerce-other-payment-gateway' ),
			),
		);
	}
}
