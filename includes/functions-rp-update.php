<?php
/**
 * RestaurantPress Updates
 *
 * Function for updating data, used by the background updater.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rp_update_130_termmeta() {
	global $wpdb;

	/**
	 * Migrate term meta to WordPress tables
	 */
	if ( get_option( 'db_version' ) >= 34370 && $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}restaurantpress_termmeta';" ) ) {
		if ( $wpdb->query( "INSERT INTO {$wpdb->termmeta} ( term_id, meta_key, meta_value ) SELECT restaurantpress_term_id, meta_key, meta_value FROM {$wpdb->prefix}restaurantpress_termmeta;" ) ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}restaurantpress_termmeta" );
			wp_cache_flush();
		}
	}
}

function rp_update_130_food_groups() {
	global $wpdb;

	// Upgrade food grouping to support term ID instead of slug.
	$existing_food_groups = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = 'food_grouping' AND meta_value != '';" );

	if ( $existing_food_groups ) {

		foreach ( $existing_food_groups as $existing_food_group ) {

			$needs_update = false;
			$new_value    = array();
			$value        = maybe_unserialize( trim( $existing_food_group->meta_value ) );

			if ( $value ) {
				foreach ( $value as $key => $food_data ) {
					if ( empty( $food_data ) || is_array( $food_data ) ) {
						continue;
					}

					if ( ! is_numeric( $food_data ) ) {
						$needs_update      = true;
						$food_menu_term    = get_term_by( 'slug', $food_data, 'food_menu_cat' );
						$new_value[ $key ] = (int) $food_menu_term->term_id;
					} else {
						$new_value[ $key ] = $food_data;
					}
				}
				if ( $needs_update ) {
					$new_value = serialize( $new_value );

					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = %s, meta_value = %s WHERE meta_id = %d", 'food_grouping', $new_value, $existing_food_group->meta_id ) );
				}
			}
		}
	}
}

function rp_update_130_db_version() {
	RP_Install::update_db_version( '1.3.0' );
}

function rp_update_131_db_version() {
	RP_Install::update_db_version( '1.3.1' );
}

function rp_update_132_db_version() {
	RP_Install::update_db_version( '1.3.2' );
}
