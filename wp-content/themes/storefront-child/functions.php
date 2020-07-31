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

/**
 * Create post type
 */
function custom_post_type() {
	$labels = array(
		'name'               => 'Education',
		'singular_name'      => 'education',
		'add_new'            => 'Add Education',
		'all_items'          => 'All Education',
		'add_new_item'       => 'Add Education',
		'edit_item'          => 'Edit Education',
		'new_item'           => 'New Education',
		'view_item'          => 'View Education',
		'search_item'        => 'Search Education',
		'not_found'          => 'No education found',
		'not_found_in_trash' => 'No education found in trash',
		'parent_item_colon'  => 'Parent Education',
	);
	$args   = array(
		'labels'          => $labels,
		'public'          => true,
		'has_archive'     => true,
		'rewrite'         => true,
		'capability_type' => 'post',
		'menu_position'   => 4,
		'show_in_rest'    => true,
		'rewrite'         => array( 'slug' => 'education' ),
		'supports'        => array(
			'editor',
			'thumbnail',
			'excerpt',
			'title',
			'comments',
		),
	);
	register_post_type( 'education', $args );
}
add_action( 'init', 'custom_post_type' );


/**
 * Description: A short example showing how to add a taxonomy called Course.
 */
function register_taxonomy_course() {
	$labels = array(
		'name'              => _x( 'Course', 'taxonomy general name', 'course' ),
		'singular_name'     => _x( 'Course', 'taxonomy singular name', 'course' ),
		'search_items'      => __( 'Search Courses' ),
		'all_items'         => __( 'All Courses' ),
		'parent_item'       => __( 'Parent Course' ),
		'parent_item_colon' => __( 'Parent Course:' ),
		'edit_item'         => __( 'Edit Course' ),
		'update_item'       => __( 'Update Course' ),
		'view_item'         => __( 'View Course' ),
		'add_new_item'      => __( 'Add New Course' ),
		'seacrh_item'       => __( 'Search Course' ),
		'not_found'         => __( 'No course found' ),
		'new_item_name'     => __( 'New Course Name' ),
		'menu_name'         => __( 'Course' ),
	);
	$args   = array(
		'hierarchical'      => true, // make it hierarchical (like categories) .
		'labels'            => $labels,
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'course' ),
	);
	register_taxonomy( 'course', array( 'education' ), $args );

}

add_action( 'init', 'register_taxonomy_course', 0 );
