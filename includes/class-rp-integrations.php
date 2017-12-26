<?php
/**
 * RestaurantPress Integrations class
 *
 * Loads Integrations into RestaurantPress.
 *
 * @class    RP_Integrations
 * @version  1.6.0
 * @package  RestaurantPress/Classes/Integrations
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Integrations Class.
 */
class RP_Integrations {

	/**
	 * Array of integrations.
	 *
	 * @var array
	 */
	public $integrations = array();

	/**
	 * Initialize integrations.
	 */
	public function __construct() {

		do_action( 'restaurantpress_integrations_init' );

		$load_integrations = apply_filters( 'restaurantpress_integrations', array() );

		// Load integration classes.
		foreach ( $load_integrations as $integration ) {

			$load_integration = new $integration();

			$this->integrations[ $load_integration->id ] = $load_integration;
		}
	}

	/**
	 * Return loaded integrations.
	 *
	 * @return array
	 */
	public function get_integrations() {
		return $this->integrations;
	}
}
