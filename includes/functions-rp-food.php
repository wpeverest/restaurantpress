<?php
/**
 * RestaurantPress Food Functions
 *
 * Functions for food specific things.
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
 * Get the placeholder image URL.
 * @return string
 */
function rp_placeholder_img_src( $thumb_size = 'small' ) {
	return apply_filters( 'restaurantpress_placeholder_img_src', RP()->plugin_url() . '/assets/images/placeholder-' . $thumb_size . '.jpg' );
}

/**
 * Get the placeholder image.
 * @return string
 */
function rp_placeholder_img( $size = 'food_thumbnail' ) {
	$dimensions = rp_get_image_size( $size );
	$thumb_size = $dimensions['width'] == 100 ? 'small' : 'large';

	return apply_filters( 'restaurantpress_placeholder_img', '<img src="' . rp_placeholder_img_src( $thumb_size ) . '" alt="' . esc_attr__( 'Placeholder', 'restaurantpress' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="restaurantpress-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
}

/**
 * Returns the food terms.
 *
 * @access private
 * @param  int $food_id
 * @return array|WP_Error
 */
function _rp_get_food_terms( $food_id ) {
	return get_the_terms( $food_id, 'food_menu_cat' );
}

/**
 * Returns the food categories in a list.
 *
 * @param int    $food_id
 * @param string $sep (default: ', ').
 * @param string $before (default: '').
 * @param string $after (default: '').
 * @return string
 */
function rp_get_food_category_list( $food_id, $sep = ', ', $before = '', $after = '' ) {
	return get_the_term_list( $food_id, 'food_menu_cat', $before, $sep, $after );
}
