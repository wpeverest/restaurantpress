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

/**
 * Update term meta.
 */
function rp_update_130_termmeta() {
	global $wpdb;

	/**
	 * Migrate term meta to WordPress tables.
	 */
	if ( get_option( 'db_version' ) >= 34370 && $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}restaurantpress_termmeta';" ) ) {
		if ( $wpdb->query( "INSERT INTO {$wpdb->termmeta} ( term_id, meta_key, meta_value ) SELECT restaurantpress_term_id, meta_key, meta_value FROM {$wpdb->prefix}restaurantpress_termmeta;" ) ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}restaurantpress_termmeta" );
			wp_cache_flush();
		}
	}
}

/**
 * Update food groups.
 */
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

/**
 * Update DB version.
 */
function rp_update_130_db_version() {
	RP_Install::update_db_version( '1.3.0' );
}

/**
 * Update DB version.
 */
function rp_update_131_db_version() {
	RP_Install::update_db_version( '1.3.1' );
}

/**
 * Update DB version.
 */
function rp_update_132_db_version() {
	RP_Install::update_db_version( '1.3.2' );
}

/**
 * Update price.
 */
function rp_update_140_price() {
	global $wpdb;

	// Upgrade old style price to support formatted price.
	$existing_prices = $wpdb->get_results( "SELECT meta_value, post_id FROM {$wpdb->postmeta} WHERE meta_key = 'food_item_price' AND meta_value != '';" );

	if ( $existing_prices ) {

		foreach ( $existing_prices as $existing_price ) {

			$old_price = trim( $existing_price->meta_value );

			if ( ! empty( $old_price ) ) {
				$formatted_price = rp_format_decimal( $old_price );

				// Update key with formatted value.
				update_post_meta( $existing_price->post_id, '_price', $formatted_price );
				update_post_meta( $existing_price->post_id, '_regular_price', $formatted_price );

				// Delete unneeded old post meta value.
				delete_post_meta( $existing_price->post_id, 'food_item_price', $old_price );
			}
		}
	}
}

/**
 * Update chef badge meta key.
 */
function rp_update_140_chef_badge() {
	global $wpdb;

	// Update chef flash key.
	$wpdb->update(
		$wpdb->postmeta,
		array(
			'meta_key' => '_chef_badge',
		),
		array(
			'meta_key' => 'chef_badge_item',
		)
	);
}

/**
 * Update lightbox options.
 */
function rp_update_140_options() {
	$restaurantpress_enable_lightbox = get_option( 'restaurantpress_enable_lightbox' );
	if ( $restaurantpress_enable_lightbox ) {
		update_option( 'restaurantpress_enable_gallery_lightbox', $restaurantpress_enable_lightbox );
		delete_option( 'restaurantpress_enable_lightbox' );
	}
}

/**
 * Update DB version.
 */
function rp_update_140_db_version() {
	RP_Install::update_db_version( '1.4.0' );
}

/**
 * Update DB version.
 */
function rp_update_141_db_version() {
	RP_Install::update_db_version( '1.4.1' );
}

/**
 * Update DB version.
 */
function rp_update_142_db_version() {
	RP_Install::update_db_version( '1.4.2' );
}

/**
 * Update DB version.
 */
function rp_update_150_db_version() {
	RP_Install::update_db_version( '1.5.0' );
}
