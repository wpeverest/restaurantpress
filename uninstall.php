<?php
/**
 * RestaurantPress Uninstall
 *
 * Uninstalls the plugin deletes user roles, tables, and options.
 *
 * @package RestaurantPress\Uninstaller
 * @version 1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb, $wp_version;

wp_clear_scheduled_hook( 'restaurantpress_cleanup_sessions' );

/*
 * Only remove ALL plugin data if RP_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'RP_REMOVE_ALL_DATA' ) && true === RP_REMOVE_ALL_DATA ) {
	include_once dirname( __FILE__ ) . '/includes/class-rp-install.php';

	// Roles + caps.
	RP_Install::remove_roles();

	// Tables.
	RP_Install::drop_tables();

	// Delete options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'restaurantpress\_%';" );

	// Delete posts + data.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'food_menu', 'food_group' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

	// Delete terms if > WP 4.2 (term splitting was added in 4.2).
	if ( version_compare( $wp_version, '4.2', '>=' ) ) {
		// Delete term taxonomies.
		foreach ( array( 'food_menu_cat' ) as $taxonomy ) {
			$wpdb->delete(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => $taxonomy,
				)
			);
		}

		// Delete orphan relationships.
		$wpdb->query( "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;" );

		// Delete orphan terms.
		$wpdb->query( "DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL;" );

		// Delete orphan term meta.
		if ( ! empty( $wpdb->termmeta ) ) {
			$wpdb->query( "DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL;" );
		}
	}

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
