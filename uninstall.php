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
}
