<?php
/*
Plugin Name: Hello World!
Description: This is just a test.
Author: Jérémy Heleine
Version: 1.0
Author URI: http://jeremyheleine.me
*/
function custom_mu_plugin() {
	echo '<p style="position: absolute; top: 150px; right: 0; padding: 10px; background-color: #0096FF; color: #FFFFFF;">Hello World!</p>';
}
//add_action( 'woocommerce_after_shop_loop_item', 'custom_mu_plugin' );
?>