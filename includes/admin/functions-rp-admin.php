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

	$screen_ids = array(
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
