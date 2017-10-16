<?php
/**
 * Plugin Name: RestaurantPress
 * Plugin URI: https://wpeverest.com/wordpress-plugins/restaurantpress/
 * Description: Allows you to create awesome restaurant menus for restaurants, bars, and cafes in no time. Smartly :)
 * Version: 1.5.0
 * Author: WPEverest
 * Author URI: https://wpeverest.com
 * Requires at least: 4.4
 * Tested up to: 4.9
 *
 * Text Domain: restaurantpress
 * Domain Path: /languages/
 *
 * @package  RestaurantPress
 * @category Core
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define RP_PLUGIN_FILE.
if ( ! defined( 'RP_PLUGIN_FILE' ) ) {
	define( 'RP_PLUGIN_FILE', __FILE__ );
}

// Include the main RestaurantPress class.
if ( ! class_exists( 'RestaurantPress' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-restaurantpress.php';
}

/**
 * Main instance of RestaurantPress.
 *
 * Returns the main instance of RP to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return RestaurantPress
 */
function RP() {
	return RestaurantPress::get_instance();
}

// Global for backwards compatibility.
$GLOBALS['restaurantpress'] = RP();
