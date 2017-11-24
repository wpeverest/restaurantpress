<?php
/**
 * Abstract Integration Class
 *
 * Extended by individual integrations to offer additional functionality.
 *
 * @class    RP_Integration
 * @extends  RP_Settings_API
 * @version  1.6.0
 * @package  RestaurantPress/Abstracts
 * @category Abstract Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Integration class.
 */
abstract class RP_Integration extends RP_Settings_API {

	/**
	 * yes or no based on whether the integration is enabled.
	 *
	 * @var string
	 */
	public $enabled = 'yes';

	/**
	 * Integration title.
	 *
	 * @var string
	 */
	public $method_title = '';

	/**
	 * Integration description.
	 *
	 * @var string
	 */
	public $method_description = '';

	/**
	 * Return the title for admin screens.
	 *
	 * @return string
	 */
	public function get_method_title() {
		return apply_filters( 'restaurantpress_integration_title', $this->method_title, $this );
	}

	/**
	 * Return the description for admin screens.
	 * @return string
	 */
	public function get_method_description() {
		return apply_filters( 'restaurantpress_integration_description', $this->method_description, $this );
	}

	/**
	 * Output the gateway settings screen.
	 */
	public function admin_options() {
		echo '<h2>' . esc_html( $this->get_method_title() ) . '</h2>';
		echo wp_kses_post( wpautop( $this->get_method_description() ) );
		echo '<div><input type="hidden" name="section" value="' . esc_attr( $this->id ) . '" /></div>';
		parent::admin_options();
	}

	/**
	 * Init settings for integrations.
	 */
	public function init_settings() {
		parent::init_settings();
		$this->enabled  = ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'] ? 'yes' : 'no';
	}
}
