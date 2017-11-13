<?php
/**
 * Setup menus in WP admin.
 *
 * @class    RP_Admin_Menus
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'RP_Admin_Menus', false ) ) {
	return new RP_Admin_Menus();
}

/**
 * RP_Admin_Menus Class.
 */
class RP_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );

		if ( apply_filters( 'restaurantpress_show_extensions_page', true ) ) {
			add_action( 'admin_menu', array( $this, 'extensions_menu' ), 70 );
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
			$menu[] = array( '', 'read', 'separator-restaurantpress', '', 'wp-menu-separator restaurantpress' ); // WPCS: override ok.
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
	 * Loads settings page.
	 */
	public function settings_page_init() {
		global $current_tab, $current_section;

		// Include settings pages.
		RP_Admin_Settings::get_settings_pages();

		// Get current tab/section.
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) ); // WPCS: input var okay, CSRF ok.
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) ); // WPCS: input var okay, CSRF ok.

		// Save settings if data has been posted.
		if ( apply_filters( '' !== $current_section ? "restaurantpress_save_settings_{$current_tab}_{$current_section}" : "restaurantpress_save_settings_{$current_tab}", ! empty( $_POST ) ) ) { // WPCS: input var okay, CSRF ok.
			RP_Admin_Settings::save();
		}

		// Add any posted messages.
		if ( ! empty( $_GET['rp_error'] ) ) { // WPCS: input var okay, CSRF ok.
			RP_Admin_Settings::add_error( wp_kses_post( wp_unslash( $_GET['rp_error'] ) ) ); // WPCS: input var okay, CSRF ok.
		}

		if ( ! empty( $_GET['rp_message'] ) ) { // WPCS: input var okay, CSRF ok.
			RP_Admin_Settings::add_message( wp_kses_post( wp_unslash( $_GET['rp_message'] ) ) ); // WPCS: input var okay, CSRF ok.
		}
	}

	/**
	 * Extensions menu item.
	 */
	public function extensions_menu() {
		add_submenu_page( 'restaurantpress', __( 'RestaurantPress extensions', 'restaurantpress' ),  __( 'Extensions', 'restaurantpress' ) , 'manage_restaurantpress', 'rp-extensions', array( $this, 'extensions_page' ) );
	}

	/**
	 * Remove the RP menu item in admin.
	 */
	public function menu_unset() {
		global $submenu;

		// Remove 'RestaurantPress' sub menu item.
		if ( isset( $submenu['restaurantpress'] ) ) {
			unset( $submenu['restaurantpress'][0] );
		}
	}

	/**
	 * Reorder the RP menu items in admin.
	 *
	 * @param  int $menu_order Menu Order.
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array.
		$restaurantpress_menu_order = array();

		// Get the index of our custom separator.
		$restaurantpress_separator = array_search( 'separator-restaurantpress', $menu_order, true );

		// Get index of food menu.
		$restaurantpress_food_menu = array_search( 'edit.php?post_type=food_menu', $menu_order, true );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {

			if ( 'restaurantpress' === $item ) {
				$restaurantpress_menu_order[] = 'separator-restaurantpress';
				$restaurantpress_menu_order[] = $item;
				$restaurantpress_menu_order[] = 'edit.php?post_type=food_menu';
				unset( $menu_order[ $restaurantpress_separator ] );
				unset( $menu_order[ $restaurantpress_food_menu ] );
			} elseif ( ! in_array( $item, array( 'separator-restaurantpress' ), true ) ) {
				$restaurantpress_menu_order[] = $item;
			}
		}

		// Return order.
		return $restaurantpress_menu_order;
	}

	/**
	 * Custom menu order.
	 *
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
	 * Init the extensions page.
	 */
	public function extensions_page() {
		RP_Admin_extensions::output();
	}
}

return new RP_Admin_Menus();
