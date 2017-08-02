<?php
/**
 * RestaurantPress RP_AJAX
 *
 * AJAX Event Handler
 *
 * @class    RP_AJAX
 * @version  1.0.0
 * @package  RestaurantPress/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_AJAX Class.
 */
class RP_AJAX {

	/**
	 * Hooks in ajax handlers
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax)
	 */
	public static function add_ajax_events() {
		// restaurantpress_EVENT => nopriv
		$ajax_events = array(
			'rated' => false
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_restaurantpress_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_restaurantpress_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function rated() {
		if ( ! current_user_can( 'manage_restaurantpress' ) ) {
			die(-1);
		}

		update_option( 'restaurantpress_admin_footer_text_rated', 1 );
		die();
	}
}

RP_AJAX::init();
