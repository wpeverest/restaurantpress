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
 * Main function for returning foods, uses the RP_Food_Factory class.
 *
 * @param  mixed $the_food Post object or post ID of the food.
 * @return RP_Food|null|false
 */
function rp_get_food( $the_food = false, $deprecated = array() ) {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		/* translators: 1: rp_get_food 2: restaurantpress_init */
		_doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'restaurantpress' ), 'rp_get_food', 'restaurantpress_init' ), '1.4' );
		return false;
	}

	return RP()->food_factory->get_food( $the_food );
}

/**
 * Get the placeholder image URL.
 * @return string
 */
function rp_placeholder_img_src() {
	return apply_filters( 'restaurantpress_placeholder_img_src', RP()->plugin_url() . '/assets/images/placeholder.png' );
}

/**
 * Get the placeholder image.
 * @return string
 */
function rp_placeholder_img( $size = 'food_thumbnail' ) {
	$dimensions = rp_get_image_size( $size );

	return apply_filters( 'restaurantpress_placeholder_img', '<img src="' . rp_placeholder_img_src() . '" alt="' . esc_attr__( 'Placeholder', 'restaurantpress' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="restaurantpress-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
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

/**
 * Returns the food tags in a list.
 *
 * @param int    $food_id
 * @param string $sep (default: ', ').
 * @param string $before (default: '').
 * @param string $after (default: '').
 * @return string
 */
function rp_get_food_tag_list( $food_id, $sep = ', ', $before = '', $after = '' ) {
	return get_the_term_list( $food_id, 'food_menu_tag', $before, $sep, $after );
}
