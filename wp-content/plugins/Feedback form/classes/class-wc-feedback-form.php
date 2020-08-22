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
			'name1'         => array(
				'name' => __( 'Custom Field', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field1',
			),
			'name1_1'         => array(
				'name' => __( 'Custom Field Type', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field1_1',
			),
			'name1_2'         => array(
				'name' => __( 'Custom Field Enable/Disable', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'id'   => 'custom_field1_2',
			),
			'name2'         => array(
				'name' => __( 'Custom Field', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field2',
			),
			'name2_1'         => array(
				'name' => __( 'Custom Field Type', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field2_1',
			),
			'name2_2'         => array(
				'name' => __( 'Custom Field Enable/Disable', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'id'   => 'custom_field2_2',
			),
			'name3'         => array(
				'name' => __( 'Custom Field', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field3',
			),
			'name3_1'         => array(
				'name' => __( 'Custom Field Type', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field3_1',
			),
			'name3_2'         => array(
				'name' => __( 'Custom Field Enable/Disable', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'id'   => 'custom_field3_2',
			),
			'name4'         => array(
				'name' => __( 'Custom Field', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field4',
			),
			'name4_1'         => array(
				'name' => __( 'Custom Field Type', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field4_1',
			),
			'name4_2'         => array(
				'name' => __( 'Custom Field Enable/Disable', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'id'   => 'custom_field4_2',
			),
			'name5'         => array(
				'name' => __( 'Custom Field', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field5',
			),
			'name5_1'         => array(
				'name' => __( 'Custom Field Type', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field5_1',
			),
			'name5_2'         => array(
				'name' => __( 'Custom Field Enable/Disable', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'id'   => 'custom_field5_2',
			),
			'name6'         => array(
				'name' => __( 'Custom Field', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field6',
			),
			'name6_1'         => array(
				'name' => __( 'Custom Field Type', 'woocommerce-feedback-form' ),
				'type' => 'text',
				'id'   => 'custom_field6_1',
			),
			'name6_2'         => array(
				'name' => __( 'Custom Field Enable/Disable', 'woocommerce-feedback-form' ),
				'type' => 'checkbox',
				'id'   => 'custom_field6_2',
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
