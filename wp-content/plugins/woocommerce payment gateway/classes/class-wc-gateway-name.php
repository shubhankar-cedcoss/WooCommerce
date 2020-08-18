<?php
/**
 * Class to create payment gateway
 *
 * @package Woocommerce
 * @version 1.0.0
 */
class WC_Gateway_Name extends WC_Payment_Gateway {
	/**
	 * Construct function
	 */
	public function __construct() {
		$this->id                 = 'other_payment';
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
	 * You will need this function if you want your custom credit card form
	 */
	public function payment_fields() {

		if ( $this->description ) {
			// you can instructions for test mode, I mean test card numbers etc.
			if ( $this->testmode ) {
				$this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
				$this->description  = trim( $this->description );
			}
			// display the description with <p> tags etc.
			echo wpautop( wp_kses_post( $this->description ) );
		}

		echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

		// Add this action hook if you want your custom payment gateway to support it.
		do_action( 'woocommerce_credit_card_form_start', $this->id );


		echo '<div class="form-row form-row-wide"><label>Card Number <span class="required">*</span></label>
			<input id="misha_ccNo" name="card_number" type="text" autocomplete="off">
			</div>
			<div class="form-row form-row-first">
				<label>Expiry Date <span class="required">*</span></label>
				<input id="misha_expdate" name="exp_date" type="text" autocomplete="off" placeholder="MM / YY">
			</div>
			<div class="form-row form-row-last">
				<label>Card Code (CVC) <span class="required">*</span></label>
				<input id="misha_cvv" name="cvv_no" type="password" autocomplete="off" placeholder="CVC">
			</div>
			<div class="clear"></div>';

		do_action( 'woocommerce_credit_card_form_end', $this->id );

		echo '<div class="clear"></div></fieldset>';

	}

	/**
	 * This fucntion validates the form fields
	 */
	public function validate_fields() {
		if ( empty( $_POST['card_number'] ) ) {
			wc_add_notice( 'Card number is required', 'error' );
			return false;
		} elseif ( empty( $_POST['exp_date'] ) ) {
			wc_add_notice( 'Expiry date is required', 'error' );
			return false;
		} elseif ( empty( $_POST['cvv_no'] ) ) {
			wc_add_notice( 'CVV number is required', 'error' );
			return false;
		}
		return true;
	}

	/**
	 *  We're processing the payments here
	 *
	 * @param [string] $order_id is string.
	 */
	public function process_payment( $order_id ) {

		global $woocommerce;

		// we need it to get any order detailes.
		$order = wc_get_order( $order_id );

		/*
		* Array with parameters for API interaction
		 */
		$args = array();

		/*
		 * Your API interaction could be built with wp_remote_post()
		  */
		$response = wp_remote_post( '{payment processor endpoint}', $args );

		if ( ! is_wp_error( $response ) ) {

			$body = json_decode( $response['body'], true );

			// it could be different depending on your payment processor.
			if ( 'APPROVED' === $body['response']['responseCode'] ) {

				// we received the payment.
				$order->payment_complete();

				// some notes to customer (replace true with false to make it private).
				$order->add_order_note( 'Hey, your order is paid! Thank you!', true );

				// Empty cart.
				$woocommerce->cart->empty_cart();

				// Redirect to the thank you page.
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);

			} else {
				wc_add_notice( 'Please try again.', 'error' );
				return;
			}
		} else {
			wc_add_notice( 'Connection error.', 'error' );
			return;
		}

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
			'testmode'    => array(
				'title'       => 'Test mode',
				'label'       => 'Enable Test Mode',
				'type'        => 'checkbox',
				'description' => 'Place the payment gateway in test mode using test API keys.',
				'default'     => 'yes',
				'desc_tip'    => true,
			),
		);
	}
}
