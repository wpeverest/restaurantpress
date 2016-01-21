<?php
/**
 * RestaurantPress Admin Assets
 *
 * Load Admin Assets.
 *
 * @class    RP_Admin_Assets
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Admin_Assets Class
 */
class RP_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts;

		$screen         = get_current_screen();
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		// Register admin styles
		wp_register_style( 'restaurantpress-menu', RP()->plugin_url() . '/assets/css/menu.css', array(), RP_VERSION );
		wp_register_style( 'restaurantpress-admin', RP()->plugin_url() . '/assets/css/admin.css', array(), RP_VERSION );
		wp_register_style( 'restaurantpress-admin-widgets', RP()->plugin_url() . '/assets/css/widgets.css', array(), AC_VERSION );
		wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );

		// Sitewide menu CSS
		wp_enqueue_style( 'restaurantpress-menu' );

		// Admin styles for RP pages only
		if ( in_array( $screen->id, rp_get_screen_ids() ) ) {
			wp_enqueue_style( 'restaurantpress-admin' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( in_array( $screen->id, array( 'widgets', 'customize' ) ) ) {
			wp_enqueue_style( 'restaurantpress-admin-widgets' );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register Scripts
		wp_register_script( 'restaurantpress-admin', RP()->plugin_url() . '/assets/js/admin/admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), RP_VERSION );
		wp_register_script( 'rp-admin-meta-boxes', RP()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'jquery-tiptip', 'rp-enhanced-select' ), RP_VERSION );
		wp_register_script( 'jquery-tiptip', RP()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), RP_VERSION, true );
		wp_register_script( 'select2', RP()->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array( 'jquery' ), '3.5.4' );
		wp_register_script( 'rp-enhanced-select', RP()->plugin_url() . '/assets/js/admin/enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), RP_VERSION );
		wp_localize_script( 'rp-enhanced-select', 'rp_enhanced_select_params', array(
			'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'restaurantpress' ),
			'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'restaurantpress' ),
			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'restaurantpress' ),
			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'restaurantpress' ),
			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'restaurantpress' ),
			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'restaurantpress' ),
			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'restaurantpress' ),
			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'restaurantpress' ),
			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'restaurantpress' ),
			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'restaurantpress' ),
			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'restaurantpress' ),
			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'restaurantpress' )
		) );

		// RestaurantPress admin pages
		if ( in_array( $screen->id, rp_get_screen_ids() ) ) {
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'restaurantpress-admin' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			$params = array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			);

			wp_localize_script( 'restaurantpress-admin', 'restaurantpress_admin', $params );
		}

		// Edit food menu category pages
		if ( in_array( $screen->id, array( 'edit-food_menu_cat' ) ) ) {
			wp_enqueue_media();
		}

		// Meta boxes
		if ( in_array( $screen->id, array( 'food_group', 'edit-food_group' ) ) ) {
			wp_register_script( 'rp-admin-group-meta-boxes', RP()->plugin_url() . '/assets/js/admin/meta-boxes-group' . $suffix . '.js', array( 'rp-admin-meta-boxes' ), RP_VERSION );
			wp_enqueue_script( 'rp-admin-group-meta-boxes' );
		}

		// Widgets Specific
		if ( in_array( $screen->id, array( 'widgets', 'customize' ) ) ) {
			wp_register_script( 'rp-admin-widgets', RP()->plugin_url() . '/assets/js/admin/widgets' . $suffix . '.js', array( 'jquery', 'wp-color-picker' ), RP_VERSION );
			wp_enqueue_script( 'rp-admin-widgets' );
		}
	}
}

new RP_Admin_Assets();
