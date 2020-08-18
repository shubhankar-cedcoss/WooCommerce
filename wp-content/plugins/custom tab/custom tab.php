<?php
/**
 * Plugin Name: Custom Tab on My Acoount Page
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: A plugin to handle mailchimp api to create user, lists, and others.
 * Version: 1.0
 *
 *  @package WordPress
 * Author: Shubh
 * Author URI: http://www.mywebsite.com
 */

/**
 * Register new endpoint to use on My Account page
 */
function custom_add_last_order_endpoint() {
	// EP_PAGES->Endpoint mask describing the places where the endpoint should be added..
	add_rewrite_endpoint( 'latest-order', EP_PERMALINK | EP_PAGES );
}
add_action( 'init', 'custom_add_last_order_endpoint' );

/**
 * Add new query var
 *
 * @param [string] $vars is string.
 */
function latest_order_query_vars( $vars ) {
	$vars[] = 'latest-order';
	return $vars;
}
add_action( 'query_vars', 'latest_order_query_vars', 0 );
// query_vars-> Filters the query variables allowed before processing.

/**
 * Insert the new endpoint into the my mccount menu
 *
 * @param [string] $items is string.
 */
function add_tab_last_order( $items ) {
	$items['latest-order'] = 'Last Purchase';
	return $items;
}
add_action( 'woocommerce_account_menu_items', 'add_tab_last_order' );

/**
 * Add content in the new tab
 */
function custom_tab_content() {

	$user = get_current_user_id();

	// Get instance of the WC_Order object.
	$order = new WC_Customer( $user );

	$order_id = $order->get_last_order();

	$order_item = $order_id->get_items();
	?>
	<div class="woocommerce-MyAccount-content">
	<div class="woocommerce-notices-wrapper"></div><p>
Order #<mark class="order-number"><?php echo $order_id->get_id(); ?></mark> was placed on <mark class="order-date"><?php echo $order_id->get_date_created()->date( 'F d,Y' ); ?></mark> and is currently <mark class="order-status"><?php echo $order_id->get_status(); ?></mark>.</p>


<section class="woocommerce-order-details">

	<h2 class="woocommerce-order-details__title">Order details</h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name">Product</th>
				<th class="woocommerce-table__product-table product-total">Total</th>
			</tr>
		</thead>

		<tbody>
		<?php
		foreach ( $order_item as $key ) {
			?> 
			<tr class="woocommerce-table__line-item order_item">

				<td class="woocommerce-table__product-name product-name">
					<a href="<?php echo get_permalink( $key->get_product_id() ); ?>"><?php echo $key->get_name(); ?></a> <strong class="product-quantity">&times;&nbsp;<?php echo $key->get_quantity(); ?></strong>	</td>

				<td class="woocommerce-table__product-total product-total">
					<span class="woocommerce-Price-amount amount"><?php echo wc_price( $key->get_total() );  ?></span>	</td>

			</tr>
			<?php
		}
		?>

		</tbody>

		<tfoot>
			<tr>
				<th scope="row">Subtotal:</th>
				<td><span class="woocommerce-Price-amount amount"><?php echo wc_price( $order_id->get_subtotal() ); ?></span></td>
			</tr>
								<tr>
				<th scope="row">Shipping:</th>
				<td><span class="woocommerce-Price-amount amount"><?php echo wc_price( $order_id->get_total_shipping() )?></span>&nbsp;<small class="shipped_via">via <?php echo $order_id->get_shipping_method() ?></small></td>
			</tr>
								<tr>
				<th scope="row">Payment method:</th>
				<td><?php echo $order_id->get_payment_method_title(); ?></td>
			</tr>
								<tr>
				<th scope="row">Total:</th>
				<td><span class="woocommerce-Price-amount amount"><?php echo wc_price( $order_id->get_total() ); ?></span></td>
			</tr>
		</tfoot>
	</table>

	</section>

<section class="woocommerce-customer-details">


	<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">


	<h2 class="woocommerce-column__title">Billing address</h2>

	<address>
	<?php
	echo $order_id->get_billing_first_name() . ' ' . $order_id->get_billing_last_name(); ?><br />
	<?php echo $order_id->get_billing_address_2(); ?><br />
	<?php echo $order_id->get_billing_address_1(); ?><br />
	<?php echo $order_id->get_billing_city() . '  ' . $order_id->get_billing_postcode(); ?><br />
	<?php echo $order_id->get_billing_state(); ?>
		<p class="woocommerce-customer-details--phone"><?php echo $order_id->get_billing_phone(); ?></p>

		<p class="woocommerce-customer-details--email"><?php echo $order_id->get_billing_email(); ?></p>
		</address>

		</div><!-- /.col-1 -->

		<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
			<h2 class="woocommerce-column__title">Shipping address</h2>
			<address>
				<?php echo $order_id->get_shipping_first_name() . ' ' . $order_id->get_shipping_last_name(); ?><br />
				<?php echo $order_id->get_shipping_address_2(); ?><br />
				<?php echo $order_id->get_shipping_address_1(); ?><br />
				<?php echo $order_id->get_shipping_city() . '  ' . $order_id->get_shipping_postcode(); ?><br />
				<?php echo $order_id->get_shipping_state();?>		
			</address>
		</div><!-- /.col-2 -->

	</section><!-- /.col2-set -->

</section>
</div>
	<?php
}
add_action( 'woocommerce_account_latest-order_endpoint', 'custom_tab_content' );

/**
 * Function to change title of page
 */
function change_title( $title ) {
	global $wp_query;
	$endpoint = isset( $wp_query->query_vars['latest-order'] );
	if ( $endpoint && ! is_admin() && in_the_loop() && is_account_page() ) {
		$title = __( 'Order#', 'woocommerce-extension' );

		remove_filter( 'the_title', 'lates_order_title' );
	}
	return $title;
}
add_filter( 'the_title', 'change_title' );

/**
 * Re-arranging the order of tabs on my account page
 */
function my_account_tab_order() {
	$menu_order = array(
		'dashboard'       => __( 'Dashboard', 'woocommerce' ),
		'latest-order'    => __( 'Latest Order', 'woocommerce' ),
		'orders'          => __( 'Orders', 'woocommerce' ),
		'downloads'       => __( 'Download', 'woocommerce' ),
		'edit-address'    => __( 'Addresses', 'woocommerce' ),
		'edit-account'    => __( 'Account Details', 'woocommerce' ),
		'customer-logout' => __( 'Logout', 'woocommerce' ),
	);
	return $menu_order;
}
add_filter( 'woocommerce_account_menu_items', 'my_account_tab_order' );


