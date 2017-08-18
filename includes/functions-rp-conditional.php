<?php
/**
 * RestaurantPress Conditional Functions
 *
 * Functions for determining the current query/page.
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
 * is_restaurantpress - Returns true if on a page which uses RestaurantPress templates (layouts are standard pages with shortcodes and thus are not included).
 * @return bool
 */
function is_restaurantpress() {
	return apply_filters( 'is_restaurantpress', ( is_post_type_archive( 'food_menu' ) || is_food_menu_taxonomy() || is_food_menu() ) ? true : false );
}

if ( ! function_exists( 'is_food_menu_taxonomy' ) ) {

	/**
	 * is_food_menu_taxonomy - Returns true when viewing a food_menu taxonomy archive.
	 * @return bool
	 */
	function is_food_menu_taxonomy() {
		return is_tax( get_object_taxonomies( 'food_menu' ) );
	}
}

if ( ! function_exists( 'is_food_menu_category' ) ) {

	/**
	 * is_food_menu_category - Returns true when viewing a food_menu category.
	 * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_food_menu_category( $term = '' ) {
		return is_tax( 'food_menu_cat', $term );
	}
}

if ( ! function_exists( 'is_food_menu' ) ) {

	/**
	 * is_food_menu - Returns true when viewing a single product.
	 * @return bool
	 */
	function is_food_menu() {
		return is_singular( array( 'food_menu' ) );
	}
}

if ( ! function_exists( 'is_group_menu_page' ) ) {

	/**
	 * is_group_menu_page - Returns true when viewing an group menu page.
	 * @return bool
	 */
	function is_group_menu_page() {
		return rp_post_content_has_shortcode( 'restaurantpress_menu' ) || apply_filters( 'restaurantpress_is_group_menu_page', false ) || apply_filters( 'restaurantpress_is_widget_menu_active', is_active_widget( false, false, 'restaurantpress_widget_menu', true ) );
	}
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * is_ajax - Returns true when the page is loaded via ajax.
	 * @return bool
	 */
	function is_ajax() {
		return defined( 'DOING_AJAX' );
	}
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function rp_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}
