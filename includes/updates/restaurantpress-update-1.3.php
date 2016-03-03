<?php
/**
 * Update RP to 1.3.0
 *
 * @author   ThemeGrill
 * @category Admin
 * @package  RestaurantPress/Admin/Updates
 * @version  1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

/**
 * Migrate term meta to WordPress tables.
 */
if ( get_option( 'db_version' ) >= 34370 && $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}restaurantpress_termmeta';" ) ) {
	if ( $wpdb->query( "INSERT INTO {$wpdb->termmeta} ( term_id, meta_key, meta_value ) SELECT restaurantpress_term_id, meta_key, meta_value FROM {$wpdb->prefix}restaurantpress_termmeta;" ) ) {
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}restaurantpress_termmeta" );
	}
}
