<?php
/**
 * RestaurantPress Food Settings
 *
 * @class    RP_Settings_Food
 * @version  1.4.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RP_Settings_Foods', false ) ) :

/**
 * RP_Settings_Foods.
 */
class RP_Settings_Foods extends RP_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'foods';
		$this->label = __( 'Foods', 'restaurantpress' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			''          	=> __( 'General', 'restaurantpress' ),
			'display'       => __( 'Display', 'restaurantpress' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );

		RP_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		RP_Admin_Settings::save_fields( $settings );
	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section
	 *
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		if ( 'display' == $current_section ) {

			$settings = apply_filters( 'restaurantpress_food_settings', array(

				array(
					'title' => __( 'Food images', 'restaurantpress' ),
					'type' 	=> 'title',
					'desc' 	=> sprintf( __( 'These settings affect the display and dimensions of images in your menu catalog - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a target="_blank" href="%s">regenerate your thumbnails</a>.', 'restaurantpress' ), 'https://wordpress.org/plugins/regenerate-thumbnails/' ),
					'id' 	=> 'image_options',
				),

				array(
					'title'    => __( 'Food grid images', 'restaurantpress' ),
					'desc'     => __( 'This size is usually used in food grid listings. (W x H)', 'woocommerce' ),
					'id'       => 'food_grid_image_size',
					'css'      => '',
					'type'     => 'image_width',
					'default'  => array(
						'width'  => '370',
						'height' => '245',
						'crop'   => 1,
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Single food image', 'restaurantpress' ),
					'desc'     => __( 'This is the size used by the main image on the food page. (W x H)', 'restaurantpress' ),
					'id'       => 'food_single_image_size',
					'css'      => '',
					'type'     => 'image_width',
					'default'  => array(
						'width'  => '600',
						'height' => '600',
						'crop'   => 1,
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Food thumbnails', 'restaurantpress' ),
					'desc'     => __( 'This size is usually used for the gallery of images on the food page. (W x H)', 'restaurantpress' ),
					'id'       => 'food_thumbnail_image_size',
					'css'      => '',
					'type'     => 'image_width',
					'default'  => array(
						'width'  => '180',
						'height' => '180',
						'crop'   => 1,
					),
					'desc_tip' => true,
				),

				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'image_options',
				),

			) );

		} else {
			$settings = apply_filters( 'restaurantpress_foods_general_settings', array(

				array(
					'title' => __( 'Color options', 'restaurantpress' ),
					'type'  => 'title',
					'desc'  => __( 'This section lets you customize the color for all the group layouts.', 'restaurantpress' ),
					'id'    => 'color_options'
				),

				array(
					'title'    => __( 'Primary Color', 'restaurantpress' ),
					'desc'     => __( 'The primary color for RestaurantPress group layouts. Default <code>#d60e10</code>.', 'restaurantpress' ),
					'id'       => 'restaurantpress_primary_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#d60e10',
					'autoload' => false,
					'desc_tip' => true
				),

				array( 'type' => 'sectionend', 'id' => 'color_options' ),

				array(
					'title' => __( 'Lightbox options', 'restaurantpress' ),
					'type'  => 'title',
					'id'    => 'image_options'
				),

				array(
					'title'    => __( 'Food Lightbox', 'restaurantpress' ),
					'desc'     => __( 'Enable Lightbox for food images', 'restaurantpress' ),
					'id'       => 'restaurantpress_enable_lightbox',
					'default'  => 'yes',
					'desc_tip' => __( 'Include RestaurantPress\'s lightbox. Food images will open in a lightbox.', 'restaurantpress' ),
					'type'     => 'checkbox'
				),

				array( 'type' => 'sectionend', 'id' => 'image_options' )

			) );
		}

		return apply_filters( 'restaurantpress_get_settings_' . $this->id, $settings, $current_section );
	}
}

endif;

return new RP_Settings_Foods();
