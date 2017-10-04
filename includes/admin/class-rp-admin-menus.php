<?php
/**
 * Setup menus in WP admin.
 *
 * @class    RP_Admin_Menu
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RP_Admin_Menu', false ) ) :

/**
 * RP_Admin_Menu Class
 */
class RP_Admin_Menu {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );

		if ( apply_filters( 'restaurantpress_show_addons_page', true ) ) {
			add_action( 'admin_menu', array( $this, 'addons_menu' ), 70 );
		}

		add_action( 'admin_head', array( $this, 'menu_unset' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		global $menu;

		if ( current_user_can( 'manage_restaurantpress' ) ) {
			$menu[] = array( '', 'read', 'separator-restaurantpress', '', 'wp-menu-separator restaurantpress' );
		}

		add_menu_page( __( 'RestaurantPress', 'restaurantpress' ), __( 'RestaurantPress', 'restaurantpress' ), 'manage_restaurantpress', 'restaurantpress', null, null, '57.5' );
	}

	/**
	 * Add menu item.
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'restaurantpress', __( 'RestaurantPress Settings', 'restaurantpress' ),  __( 'Settings', 'restaurantpress' ) , 'manage_restaurantpress', 'rp-settings', array( $this, 'settings_page' ) );

		add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
	}

	/**
	 * Loads gateways and shipping methods into memory for use within settings.
	 */
	public function settings_page_init() {
		global $current_tab, $current_section;

		// Include settings pages
		RP_Admin_Settings::get_settings_pages();

		// Get current tab/section
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] );

		// Save settings if data has been posted
		if ( ! empty( $_POST ) ) {
			RP_Admin_Settings::save();
		}

		// Add any posted messages
		if ( ! empty( $_GET['rp_error'] ) ) {
			RP_Admin_Settings::add_error( stripslashes( $_GET['rp_error'] ) );
		}

		if ( ! empty( $_GET['rp_message'] ) ) {
			RP_Admin_Settings::add_message( stripslashes( $_GET['rp_message'] ) );
		}
	}

	/**
	 * Addons menu item.
	 */
	public function addons_menu() {
		add_submenu_page( 'restaurantpress', __( 'RestaurantPress extensions', 'restaurantpress' ),  __( 'Extensions', 'restaurantpress' ) , 'manage_restaurantpress', 'rp-addons', array( $this, 'addons_page' ) );
	}

	/**
	 * Remove the RP menu item in admin.
	 */
	public function menu_unset() {
		global $submenu;

		// Remove 'RestaurantPress' sub menu item
		if ( isset( $submenu['restaurantpress'] ) ) {
			unset( $submenu['restaurantpress'][0] );
		}
	}

	/**
	 * Reorder the RP menu items in admin.
	 * @param  mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$restaurantpress_menu_order = array();

		// Get the index of our custom separator
		$restaurantpress_separator = array_search( 'separator-restaurantpress', $menu_order );

		// Get index of food_menu menu
		$restaurantpress_food_menu = array_search( 'edit.php?post_type=food_menu', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) {

			if ( ( ( 'restaurantpress' ) == $item ) ) {
				$restaurantpress_menu_order[] = 'separator-restaurantpress';
				$restaurantpress_menu_order[] = $item;
				$restaurantpress_menu_order[] = 'edit.php?post_type=food_menu';
				unset( $menu_order[ $restaurantpress_separator ] );
				unset( $menu_order[ $restaurantpress_food_menu ] );
			} elseif ( ! in_array( $item, array( 'separator-restaurantpress' ) ) ) {
				$restaurantpress_menu_order[] = $item;
			}
		}

		// Return order
		return $restaurantpress_menu_order;
	}

	/**
	 * Custom menu order.
	 * @return bool
	 */
	public function custom_menu_order() {
		return current_user_can( 'manage_restaurantpress' );
	}

	/**
	 * Init the settings page.
	 */
	public function settings_page() {
		RP_Admin_Settings::output();
	}

	/**
	 * Init the addons page.
	 */
	public function addons_page() {
		RP_Admin_Addons::output();
	}
}

endif;

return new RP_Admin_Menu();
