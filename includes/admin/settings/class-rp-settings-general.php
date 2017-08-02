<?php
/**
 * RestaurantPress General Settings
 *
 * @class    RP_Settings_General
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RP_Settings_General' ) ) :

/**
 * RP_Settings_General Class
 */
class RP_Settings_General extends RP_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'general';
		$this->label = __( 'General', 'restaurantpress' );

		add_filter( 'restaurantpress_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'restaurantpress_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'restaurantpress_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings
	 * @return array
	 */
	public function get_settings() {

		$settings = apply_filters( 'restaurantpress_general_settings', array(

			array(
				'title' => __( 'Color Options', 'restaurantpress' ),
				'type'  => 'title',
				'desc'  => __( 'This section lets you customize the color for all the group layouts.', 'restaurantpress' ),
				'id'    => 'chef_badge_options'
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

			array( 'type' => 'sectionend', 'id' => 'chef_badge_options' ),

			array(
				'title' => __( 'Food Item Images', 'restaurantpress' ),
				'type'  => 'title',
				'desc' 	=> sprintf( __( 'These settings affect the display and dimensions of images in your menu - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'restaurantpress' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ),
				'id'    => 'image_options'
			),

			array(
				'title'    => __( 'Food Grid Images', 'restaurantpress' ),
				'desc'     => __( 'This size is usually used in food grid listings', 'restaurantpress' ),
				'id'       => 'food_grid_image_size',
				'css'      => '',
				'type'     => 'image_width',
				'default'  => array(
					'width'  => '370',
					'height' => '245',
					'crop'   => 1
				),
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Food Thumbnails', 'restaurantpress' ),
				'desc'     => __( 'This size is usually used for the images on the food menu page.', 'restaurantpress' ),
				'id'       => 'food_thumbnail_image_size',
				'css'      => '',
				'type'     => 'image_width',
				'default'  => array(
					'width'  => '100',
					'height' => '100',
					'crop'   => 1
				),
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Food Image Lightbox', 'restaurantpress' ),
				'desc'     => __( 'Enable Lightbox for food images', 'restaurantpress' ),
				'id'       => 'restaurantpress_enable_lightbox',
				'default'  => 'yes',
				'desc_tip' => __( 'Include RestaurantPress\'s lightbox. Food images will open in a lightbox.', 'restaurantpress' ),
				'type'     => 'checkbox'
			),

			array( 'type' => 'sectionend', 'id' => 'image_options' )

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

return new RP_Settings_General();
