<?php
/**
 * Class for displaying plugin warning notifications and determining 3rd party plugin compatibility.
 *
 * @author   WPEverest
 * @category Admin
 * @package  RestaurantPress/Admin
 * @version  1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Plugin_Updates Class.
 */
class RP_Plugin_Updates {

	/**
	 * This is the header used by extensions to show requirements.
	 * @var string
	 */
	const VERSION_REQUIRED_HEADER = 'RP requires at least';

	/**
	 * This is the header used by extensions to show testing.
	 * @var string
	 */
	const VERSION_TESTED_HEADER = 'RP tested up to';

	/*
	|--------------------------------------------------------------------------
	| Data Helpers
	|--------------------------------------------------------------------------
	|
	| Methods for getting & manipulating data.
	*/

	/**
	 * Get plugins that have a valid value for a specific header.
	 *
	 * @param string $header
	 * @return array of plugin info arrays
	 */
	protected function get_plugins_with_header( $header ) {
		$plugins = get_plugins();
		$matches = array();

		foreach ( $plugins as $file => $plugin ) {
			if ( ! empty( $plugin[ $header ] ) ) {
				$matches[ $file ] = $plugin;
			}
		}

		return apply_filters( 'restaurantpress_get_plugins_with_header', $matches, $header, $plugins );
	}

	/**
	 * Get plugins which "maybe" are for RestaurantPress.
	 *
	 * @return array of plugin info arrays
	 */
	protected function get_plugins_for_restaurantpress() {
		$plugins = get_plugins();
		$matches = array();

		foreach ( $plugins as $file => $plugin ) {
			if ( $plugin['Name'] !== 'RestaurantPress' && ( stristr( $plugin['Name'], 'restaurantpress' ) || stristr( $plugin['Description'], 'restaurantpress' ) ) ) {
				$matches[ $file ] = $plugin;
			}
		}

		return apply_filters( 'restaurantpress_get_plugins_for_restaurantpress', $matches, $plugins );
	}
}
