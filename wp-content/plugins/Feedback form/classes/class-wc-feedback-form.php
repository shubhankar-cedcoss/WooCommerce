<?php
/**
 * Class to create Point and reward setting tab
 *
 * @package Woocommerce
 * @version 1.0.0
 */
class WC_Feedback_Form {
	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_feedback_form', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_feedback_form', __CLASS__ . '::update_settings' );
	}


	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['feedback_form'] = __( 'Custom Form Settings', 'woocommerce-feedback-form' );
		return $settings_tabs;
	}


	public static function settings_tab() {
		// To access a static method use the class name, double colon (::), and the method name:.
		woocommerce_admin_fields( self::get_settings() );
	}


	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}


	public static function get_settings() {

		$settings = array(
			'section_title' => array(
				'name' => __( 'Custom Feedback Form', 'woocommerce-feedback-form' ),
				'type' => 'title',
				'desc' => 'Here you can customize your feedback form fields according to you.',
				'id'   => 'woocommerce-feedback-form_section_title',
			),
			'name'          => array(
				'name' => __( 'Name', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'desc' => __( '', 'woocommerce-feedback-form' ),
				'id'   => 'name_field',
			),
			'email'         => array(
				'name' => __( 'Email', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'desc' => __( '', 'woocommerce-feedback-form' ),
				'id'   => 'email_field',
			),
			'query'         => array(
				'name' => __( 'Message', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'desc' => __( '', 'woocommerce-feedback-form' ),
				'id'   => 'query_field',
			),
			'section_end'   => array(
				'type' => 'sectionend',
				'id'   => 'woocommerce-feedback-form_section_end',
			),
		);

		return apply_filters( 'wc_points_and_rewards_settings', $settings );
	}
}
WC_Feedback_Form::init();
