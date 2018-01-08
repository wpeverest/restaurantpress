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
 * @since 1.4.0
 *
 * @param  mixed $the_food Post object or post ID of the food.
 * @return RP_Food|null|false
 */
function rp_get_food( $the_food = false ) {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		/* translators: 1: rp_get_food 2: restaurantpress_init */
		rp_doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'restaurantpress' ), 'rp_get_food', 'restaurantpress_init' ), '1.4' );
		return false;
	}

	return RP()->food_factory->get_food( $the_food );
}

/**
 * Filter to allow food_menu_cat in the permalinks for foods.
 *
 * @param  string  $permalink The existing permalink URL.
 * @param  WP_Post $post The current post object.
 * @return string
 */
function rp_food_post_type_link( $permalink, $post ) {
	// Abort if post is not a food_menu.
	if ( 'food_menu' !== $post->post_type ) {
		return $permalink;
	}

	// Abort early if the placeholder rewrite tag isn't in the generated URL.
	if ( false === strpos( $permalink, '%' ) ) {
		return $permalink;
	}

	// Get the custom taxonomy terms in use by this post.
	$terms = get_the_terms( $post->ID, 'food_menu_cat' );

	if ( ! empty( $terms ) ) {
		if ( function_exists( 'wp_list_sort' ) ) {
			$terms = wp_list_sort( $terms, 'term_id', 'ASC' );
		} else {
			usort( $terms, '_usort_terms_by_ID' );
		}
		$category_object = apply_filters( 'rp_food_menu_post_type_link_food_menu_cat', $terms[0], $terms, $post );
		$category_object = get_term( $category_object, 'food_menu_cat' );
		$food_menu_cat   = $category_object->slug;

		if ( $category_object->parent ) {
			$ancestors = get_ancestors( $category_object->term_id, 'food_menu_cat' );
			foreach ( $ancestors as $ancestor ) {
				$ancestor_object = get_term( $ancestor, 'food_menu_cat' );
				$food_menu_cat   = $ancestor_object->slug . '/' . $food_menu_cat;
			}
		}
	} else {
		// If no terms are assigned to this post, use a string instead (can't leave the placeholder there).
		$food_menu_cat = _x( 'uncategorized', 'slug', 'restaurantpress' );
	}

	$find = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		'%post_id%',
		'%category%',
		'%food_menu_cat%',
	);

	$replace = array(
		date_i18n( 'Y', strtotime( $post->post_date ) ),
		date_i18n( 'm', strtotime( $post->post_date ) ),
		date_i18n( 'd', strtotime( $post->post_date ) ),
		date_i18n( 'H', strtotime( $post->post_date ) ),
		date_i18n( 'i', strtotime( $post->post_date ) ),
		date_i18n( 's', strtotime( $post->post_date ) ),
		$post->ID,
		$food_menu_cat,
		$food_menu_cat,
	);

	$permalink = str_replace( $find, $replace, $permalink );

	return $permalink;
}
add_filter( 'post_type_link', 'rp_food_post_type_link', 10, 2 );

/**
 * Get the placeholder image URL.
 *
 * @return string
 */
function rp_placeholder_img_src() {
	return apply_filters( 'restaurantpress_placeholder_img_src', RP()->plugin_url() . '/assets/images/placeholder.png' );
}

/**
 * Get the placeholder image.
 *
 * @param  string $size Image size.
 * @return string
 */
function rp_placeholder_img( $size = 'restaurantpress_thumbnail' ) {
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
