<?php
/**
 * RestaurantPress Integration Settings
 *
 * @class    RP_Settings_Integrations
 * @version  1.6.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RP_Settings_Integrations', false ) ) :

/**
 * RP_Settings_Integrations Class.
 */
class RP_Settings_Integrations extends RP_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'integration';
		$this->label = __( 'Integration', 'restaurantpress' );

		if ( isset( RP()->integrations ) && RP()->integrations->get_integrations() ) {
			parent::__construct();
		}
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		global $current_section;

		$sections = array();

		if ( ! defined( 'RP_INSTALLING' ) ) {
			$integrations = RP()->integrations->get_integrations();

			if ( ! $current_section && ! empty( $integrations ) ) {
				$current_section = current( $integrations )->id;
			}

			if ( sizeof( $integrations ) > 1 ) {
				foreach ( $integrations as $integration ) {
					$title = empty( $integration->method_title ) ? ucfirst( $integration->id ) : $integration->method_title;
					$sections[ strtolower( $integration->id ) ] = esc_html( $title );
				}
			}
		}

		return apply_filters( 'restaurantpress_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$integrations = RP()->integrations->get_integrations();

		if ( isset( $integrations[ $current_section ] ) ) {
			$integrations[ $current_section ]->admin_options();
		}
	}
}

endif;

return new RP_Settings_Integrations();
