<?php
/**
 * RestaurantPress Shortcode Settings
 *
 * @class    RP_Settings_Shortcode
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RP_Settings_Shortcode' ) ) :

/**
 * RP_Settings_General Class
 */
class RP_Settings_Shortcode extends RP_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'shortcode';
		$this->label = __( 'Shortcode', 'restaurantpress' );

		add_filter( 'restaurantpress_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'restaurantpress_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'restaurantpress_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings
	 * @return array
	 */
	public function get_settings() {

		$settings = apply_filters( 'restaurantpress_shortcode_settings', array(

			array(
				'title' => __( 'Generate Shortcode', 'restaurantpress' ),
				'type'  => 'title',
				'desc' 	=> __( 'This generator helps you to generate the group shortcode.', 'restaurantpress' ),
				'id'    => 'shortcode_options'
			),

			array( 'type' => 'sectionend', 'id' => 'shortcode_options' )

		) );

		return apply_filters( 'restaurantpress_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();
		RP_Admin_Settings::save_fields( $settings );
	}
}

endif;

return new RP_Settings_Shortcode();
