<?php
/**
 * Twenty Nineteen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage storefront
 *
 * Theme_enqueue_styles
 */
function theme_enqueue_styles() {
	$parent_style = 'parent-style';
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array(), '1.1', 'all' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ), '1.1', 'all' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
