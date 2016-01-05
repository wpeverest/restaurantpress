<?php
/**
 * RestaurantPress Admin Functions
 *
 * @author   ThemeGrill
 * @category Core
 * @package  RestaurantPress/Admin/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all RestaurantPress screen ids.
 * @return array
 */
function rp_get_screen_ids() {

	$rp_screen_id = sanitize_title( __( 'RestaurantPress', 'restaurantpress' ) );
	$screen_ids   = array(
		'toplevel_page_' . $rp_screen_id,
		$rp_screen_id . '_page_rp-settings',
		'edit-food_menu',
		'food_menu',
		'edit-food_group',
		'food_group',
		'edit-food_menu_cat'
	);

	return apply_filters( 'restaurantpress_screen_ids', $screen_ids );
}

/**
 * Display a RestaurantPress help tip.
 *
 * @param  string $tip Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
function rp_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = rp_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="restaurantpress-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Output admin fields.
 * @param array $options
 */
function restaurantpress_admin_fields( $options ) {

	if ( ! class_exists( 'RP_Admin_Settings' ) ) {
		include 'class-rp-admin-settings.php';
	}

	RP_Admin_Settings::output_fields( $options );
}

/**
 * Update all settings which are passed.
 * @param array $options
 */
function restaurantpress_update_options( $options ) {

	if ( ! class_exists( 'RP_Admin_Settings' ) ) {
		include 'class-rp-admin-settings.php';
	}

	RP_Admin_Settings::save_fields( $options );
}

/**
 * Get a setting from the settings API.
 * @param  mixed $option_name
 * @param  mixed $default
 * @return string
 */
function restaurantpress_settings_get_option( $option_name, $default = '' ) {

	if ( ! class_exists( 'RP_Admin_Settings' ) ) {
		include 'class-rp-admin-settings.php';
	}

	return RP_Admin_Settings::get_option( $option_name, $default );
}
