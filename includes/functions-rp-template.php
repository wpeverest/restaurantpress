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

if ( ! function_exists( 'restaurantpress_get_sidebar' ) ) {

	/**
	 * Get the food sidebar template.
	 *
	 */
	function restaurantpress_get_sidebar() {
		rp_get_template( 'global/sidebar.php' );
	}
}

/** Single Food ***********************************************************/

if ( ! function_exists( 'restaurantpress_show_food_chef_flash' ) ) {

	/**
	 * Output the food chef flash.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_show_food_chef_flash() {
		rp_get_template( 'single-food/chef-flash.php' );
	}
}
if ( ! function_exists( 'restaurantpress_show_food_images' ) ) {

	/**
	 * Output the food image before the single food summary.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_show_food_images() {
		rp_get_template( 'single-food/food-image.php' );
	}
}

if ( ! function_exists( 'restaurantpress_photoswipe' ) ) {

	/**
	 * Get the photoswipe template.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_photoswipe() {
		if ( current_theme_supports( 'rp-food-gallery-lightbox' ) ) {
			rp_get_template( 'single-food/photoswipe.php' );
		}
	}
}
if ( ! function_exists( 'restaurantpress_template_single_title' ) ) {

	/**
	 * Output the food title.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_title() {
		rp_get_template( 'single-food/title.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_price' ) ) {

	/**
	 * Output the food price.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_price() {
		rp_get_template( 'single-food/price.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_excerpt' ) ) {

	/**
	 * Output the food short description (excerpt).
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_excerpt() {
		rp_get_template( 'single-food/short-description.php' );
	}
}
