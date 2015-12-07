<?php
/**
 * RestaurantPress Terms
 *
 * Functions for handling terms/term meta.
 *
 * @author   ThemeGrill
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RestaurantPress Term Meta API - set table name
 */
function rp_taxonomy_metadata_wpdbfix() {
	global $wpdb;
	$termmeta_name = 'restaurantpress_termmeta';

	$wpdb->restaurantpress_termmeta = $wpdb->prefix . $termmeta_name;

	$wpdb->tables[] = 'restaurantpress_termmeta';
}
add_action( 'init', 'rp_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'rp_taxonomy_metadata_wpdbfix', 0 );

/**
 * RestaurantPress Term Meta API - Update term meta
 *
 * @param  mixed  $term_id
 * @param  string $meta_key
 * @param  mixed  $meta_value
 * @param  string $prev_value (default: '')
 * @return bool
 */
function update_restaurantpress_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value, $prev_value );
}

/**
 * RestaurantPress Term Meta API - Add term meta
 *
 * @param  mixed $term_id
 * @param  mixed $meta_key
 * @param  mixed $meta_value
 * @param  bool  $unique (default: false)
 * @return bool
 */
function add_restaurantpress_term_meta( $term_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value, $unique );
}

/**
 * RestaurantPress Term Meta API - Delete term meta
 *
 * @param  mixed  $term_id
 * @param  mixed  $meta_key
 * @param  string $meta_value (default: '')
 * @param  bool   $delete_all (default: false)
 * @return bool
 */
function delete_restaurantpress_term_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false ) {
	return delete_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value, $delete_all );
}

/**
 * RestaurantPress Term Meta API - Get term meta
 *
 * @param  mixed  $term_id
 * @param  string $key
 * @param  bool   $single (default: true)
 * @return mixed
 */
function get_restaurantpress_term_meta( $term_id, $key, $single = true ) {
	return get_metadata( 'restaurantpress_term', $term_id, $key, $single );
}
