<?php

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
