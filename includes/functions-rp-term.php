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
 * RestaurantPress Term Meta API - set table name.
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
 * Migrate data from RP term meta to WP term meta.
 *
 * When the database is updated to support term meta, migrate RP term meta data across.
 * We do this when the new version is >= 34370, and the old version is < 34370 (34370 is when term meta table was added).
 *
 * @param string $wp_db_version The new $wp_db_version.
 * @param string $wp_current_db_version The old (current) $wp_db_version.
 */
function rp_taxonomy_metadata_migrate_data( $wp_db_version, $wp_current_db_version ) {
	if ( $wp_db_version >= 34370 && $wp_current_db_version < 34370 ) {
		global $wpdb;
		if ( $wpdb->query( "INSERT INTO {$wpdb->termmeta} ( term_id, meta_key, meta_value ) SELECT restaurantpress_term_id, meta_key, meta_value FROM {$wpdb->prefix}restaurantpress_termmeta;" ) ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}restaurantpress_termmeta" );
		}
	}
}
add_action( 'wp_upgrade', 'rp_taxonomy_metadata_migrate_data', 10, 2 );

/**
 * RestaurantPress Term Meta API
 *
 * RP tables for storing term meta are @deprecated from WordPress 4.4 since 4.4 has its own table.
 * This function serves as a wrapper, using the new table if present, or falling back to the RP table.
 *
 * @todo These functions should be deprecated with notices in a future RP version, allowing users a chance to upgrade WordPress.
 *
 * @param  mixed  $term_id
 * @param  string $meta_key
 * @param  mixed  $meta_value
 * @param  string $prev_value (default: '')
 * @return bool
 */
function update_restaurantpress_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
	// If term meta table is not installed (pre-wp-4.4), use RP tables.
	if ( get_option( 'db_version' ) < 34370 || ! function_exists( 'update_term_meta' ) ) {
		return update_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value, $prev_value );
	} else {
		return update_term_meta( $term_id, $meta_key, $meta_value, $prev_value );
	}
}

/**
 * RestaurantPress Term Meta API
 *
 * RP tables for storing term meta are @deprecated from WordPress 4.4 since 4.4 has its own table.
 * This function serves as a wrapper, using the new table if present, or falling back to the RP table.
 *
 * @todo These functions should be deprecated with notices in a future RP version, allowing users a chance to upgrade WordPress.
 *
 * @param  mixed $term_id
 * @param  mixed $meta_key
 * @param  mixed $meta_value
 * @param  bool  $unique (default: false)
 * @return bool
 */
function add_restaurantpress_term_meta( $term_id, $meta_key, $meta_value, $unique = false ) {
	// If term meta table is not installed (pre-wp-4.4), use RP tables.
	if ( get_option( 'db_version' ) < 34370 || ! function_exists( 'add_term_meta' ) ) {
		return add_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value, $unique );
	} else {
		return add_term_meta( $term_id, $meta_key, $meta_value, $unique );
	}
}

/**
 * RestaurantPress Term Meta API
 *
 * RP tables for storing term meta are @deprecated from WordPress 4.4 since 4.4 has its own table.
 * This function serves as a wrapper, using the new table if present, or falling back to the RP table.
 *
 * @todo These functions should be deprecated with notices in a future RP version, allowing users a chance to upgrade WordPress.
 *
 * @param  mixed  $term_id
 * @param  mixed  $meta_key
 * @param  string $meta_value (default: '')
 * @param  bool   $deprecated (default: false)
 * @return bool
 */
function delete_restaurantpress_term_meta( $term_id, $meta_key, $meta_value = '', $deprecated = false ) {
	// If term meta table is not installed (pre-wp-4.4), use RP tables.
	if ( get_option( 'db_version' ) < 34370 || ! function_exists( 'delete_term_meta' ) ) {
		return delete_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value );
	} else {
		return delete_term_meta( $term_id, $meta_key, $meta_value );
	}
}

/**
 * RestaurantPress Term Meta API
 *
 * RP tables for storing term meta are @deprecated from WordPress 4.4 since 4.4 has its own table.
 * This function serves as a wrapper, using the new table if present, or falling back to the RP table.
 *
 * @todo These functions should be deprecated with notices in a future RP version, allowing users a chance to upgrade WordPress.
 *
 * @param  mixed  $term_id
 * @param  string $key
 * @param  bool   $single (default: true)
 * @return mixed
 */
function get_restaurantpress_term_meta( $term_id, $key, $single = true ) {
	// If term meta table is not installed (pre-wp-4.4), use RP tables.
	if ( get_option( 'db_version' ) < 34370 || ! function_exists( 'get_term_meta' ) ) {
		return get_metadata( 'restaurantpress_term', $term_id, $key, $single );
	} else {
		return get_term_meta( $term_id, $key, $single );
	}
}
