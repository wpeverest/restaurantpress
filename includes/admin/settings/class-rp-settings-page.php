<?php
/**
 * RestaurantPress Settings Page/Tab
 *
 * @class    RP_Settings_Page
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RP_Settings_Page', false ) ) :

/**
 * RP_Settings_Page Abstract
 */
abstract class RP_Settings_Page {

	/**
	 * Setting page id.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Setting page label.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'restaurantpress_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'restaurantpress_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'restaurantpress_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'restaurantpress_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Add this page to settings
	 */
	public function add_settings_page( $pages ) {
		$pages[ $this->id ] = $this->label;

		return $pages;
	}

	/**
	 * Get settings
	 * @return array
	 */
	public function get_settings() {
		return apply_filters( 'restaurantpress_get_settings_' . $this->id, array() );
	}

	/**
	 * Get sections
	 * @return array
	 */
	public function get_sections() {
		return apply_filters( 'restaurantpress_get_sections_' . $this->id, array() );
	}

	/**
	 * Output sections
	 */
	public function output_sections() {
		global $current_section;

		$sections = $this->get_sections();

		if ( empty( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=rp-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}

	/**
	 * Output the settings
	 */
	public function output() {
		$settings = $this->get_settings();
		RP_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings();
		RP_Admin_Settings::save_fields( $settings );

		if ( $current_section ) {
			do_action( 'restaurantpress_update_options_' . $this->id . '_' . $current_section );
		}
	}
}

endif;
