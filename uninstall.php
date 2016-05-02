<?php
/**
 * RestaurantPress Uninstall
 *
 * Uninstalls the plugin deletes user roles, tables, and options.
 *
 * @author   ThemeGrill
 * @category Core
 * @package  RestaurantPress/Uninstaller
 * @version  1.0.0
 */

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb, $wp_version;

$uninstall_data = apply_filters( 'restaurantpress_uninstall_data', false );

if ( $uninstall_data ) {
	// Roles + caps.
	include_once( 'includes/class-rp-install.php' );
	RP_Install::remove_roles();

	// Tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}restaurantpress_termmeta" );

	// Delete options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'restaurantpress\_%';" );

	// Delete posts + data.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'food_menu', 'food_group' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
	$wpdb->query( "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;" );

	// Delete terms if > WP 4.2 (term splitting was added in 4.2)
	if ( version_compare( $wp_version, '4.2', '>=' ) ) {
		// Delete term taxonomies
		$wpdb->delete(
			$wpdb->term_taxonomy,
			array(
				'taxonomy' => 'food_menu_cat',
			)
		);

		// Delete orphan relationships
		$wpdb->query( "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;" );

		// Delete orphan terms
		$wpdb->query( "DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL;" );

		// Delete orphan term meta
		if ( ! empty( $wpdb->termmeta ) ) {
			$wpdb->query( "DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL;" );
		}
	}

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
