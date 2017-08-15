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
 * Output generator tag to aid debugging.
 *
 * @param string $gen
 * @param string $type
 *
 * @return string
 */
function rp_generator_tag( $gen, $type ) {
	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="RestaurantPress ' . esc_attr( RP_VERSION ) . '">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="RestaurantPress ' . esc_attr( RP_VERSION ) . '" />';
			break;
	}
	return $gen;
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

/** Global ****************************************************************/

if ( ! function_exists( 'restaurantpress_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 */
	function restaurantpress_output_content_wrapper() {
		rp_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'restaurantpress_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 */
	function restaurantpress_output_content_wrapper_end() {
		rp_get_template( 'global/wrapper-end.php' );
	}
}
