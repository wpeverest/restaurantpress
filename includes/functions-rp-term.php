<?php
/**
 * RestaurantPress Terms
 *
 * Functions for handling terms/term meta.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * When a term is split, ensure meta data maintained.
 * @param int    $old_term_id
 * @param int    $new_term_id
 * @param string $term_taxonomy_id
 * @param string $taxonomy
 */
function rp_taxonomy_metadata_update_content_for_split_terms( $old_term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
	global $wpdb;

	if ( 'food_menu_cat' === $taxonomy && get_option( 'db_version' ) < 34370 ) {
		$old_meta_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}restaurantpress_termmeta WHERE restaurantpress_term_id = %d;", $old_term_id ) );

		// Copy across to split term
		if ( $old_meta_data ) {
			foreach ( $old_meta_data as $meta_data ) {
				$wpdb->insert(
					"{$wpdb->prefix}restaurantpress_termmeta",
					array(
						'restaurantpress_term_id' => $new_term_id,
						'meta_key'            => $meta_data->meta_key,
						'meta_value'          => $meta_data->meta_value
					)
				);
			}
		}
	}
}
add_action( 'split_shared_term', 'rp_taxonomy_metadata_update_content_for_split_terms', 10, 4 );

/**
 * Migrate data from RP term meta to WP term meta
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
	return function_exists( 'update_term_meta' ) ? update_term_meta( $term_id, $meta_key, $meta_value, $prev_value ) : update_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value, $prev_value );
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
	return function_exists( 'add_term_meta' ) ? add_term_meta( $term_id, $meta_key, $meta_value, $unique ) : add_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value, $unique );
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
	return function_exists( 'delete_term_meta' ) ? delete_term_meta( $term_id, $meta_key, $meta_value ) : delete_metadata( 'restaurantpress_term', $term_id, $meta_key, $meta_value );
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
	return function_exists( 'get_term_meta' ) ? get_term_meta( $term_id, $key, $single ) : get_metadata( 'restaurantpress_term', $term_id, $key, $single );
}
