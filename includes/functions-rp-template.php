<?php
/**
 * RestaurantPress Template
 *
 * Functions for the templating system.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add body classes for RP pages.
 *
 * @param  array $classes
 * @return array
 */
function rp_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_restaurantpress() ) {

		$classes[] = 'restaurantpress';
		$classes[] = 'restaurantpress-page';

	} elseif ( is_group_menu_page() ) {

		$classes[] = 'restaurantpress-group';
		$classes[] = 'restaurantpress-page';

	}

	return array_unique( $classes );
}
