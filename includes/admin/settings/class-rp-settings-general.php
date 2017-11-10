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

if ( ! class_exists( 'RP_Settings_General', false ) ) {
	return new RP_Settings_General();
}

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

		return apply_filters( 'restaurantpress_get_sections_' . $this->id, $sections );
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
	 * @param string $current_section Current section name.
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		$currency_code_options = get_restaurantpress_currencies();

		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $name . ' (' . get_restaurantpress_currency_symbol( $code ) . ')';
		}

		if ( 'display' === $current_section ) {
			$settings = array(
				array(
					'title' => __( 'Single food page', 'restaurantpress' ),
					'type' 	=> 'title',
					'id' 	=> 'single_page_options',
				),
				array(
					'title'   => __( 'Food page display', 'restaurantpress' ),
					'desc'    => __( 'Enable single food page display', 'restaurantpress' ),
					'id'      => 'restaurantpress_single_page_display',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'single_page_options',
				),
			);

			$theme_support           = get_theme_support( 'restaurantpress' );
			$theme_support           = is_array( $theme_support ) ? $theme_support[0]: false;
			$image_settings          = array(
				array(
					'title' => __( 'Food images', 'restaurantpress' ),
					'type' 	=> 'title',
					'desc' 	=> __( 'These settings change how food images are displayed in your catalog.', 'restaurantpress' ),
					'id' 	=> 'image_options',
				),
				'single_image_width' => array(
					'title'    => __( 'Main image width', 'restaurantpress' ),
					'desc'     => __( 'This is the width used by the main image on single food pages. These images will be uncropped.', 'restaurantpress' ),
					'id'       => 'restaurantpress_single_image_width',
					'css'      => '',
					'type'     => 'text',
					'custom_attributes' => array(
						'size' => 3,
					),
					'suffix'   => 'px',
					'default'  => 600,
					'desc_tip' => true,
				),
				'thumbnail_image_width' => array(
					'title'    => __( 'Thumbnail width', 'restaurantpress' ),
					'desc'     => __( 'This size is used for food archives and food listings.', 'restaurantpress' ),
					'id'       => 'restaurantpress_thumbnail_image_width',
					'css'      => '',
					'type'     => 'text',
					'custom_attributes' => array(
						'size' => 3,
					),
					'suffix'   => 'px',
					'default'  => 300,
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Thumbnail cropping', 'restaurantpress' ),
					'desc'     => __( 'This determines how thumbnails appear. Widths will be fixed, whilst heights may vary.', 'restaurantpress' ),
					'id'       => 'restaurantpress_thumbnail_cropping',
					'css'      => '',
					'type'     => 'thumbnail_cropping',
					'default'  => '1:1',
					'desc_tip' => false,
				),
				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'image_options',
				),
			);

			if ( isset( $theme_support['single_image_width'] ) ) {
				unset( $image_settings['single_image_width'] );
			}

			if ( isset( $theme_support['thumbnail_image_width'] ) ) {
				unset( $image_settings['thumbnail_image_width'] );
			}

			$settings = apply_filters( 'restaurantpress_food_settings', array_merge( $settings, $image_settings ) );

		} else {
			$settings = apply_filters( 'restaurantpress_general_settings', array(

				array(
					'title' => __( 'Color options', 'restaurantpress' ),
					'type'  => 'title',
					'desc'  => __( 'This section lets you customize the color for all the group layouts.', 'restaurantpress' ),
					'id'    => 'color_options',
				),

				array(
					'title'    => __( 'Primary Color', 'restaurantpress' ),
					'desc'     => __( 'The primary color for RestaurantPress group layouts. Default <code>#ff0033</code>.', 'restaurantpress' ),
					'id'       => 'restaurantpress_primary_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#ff0033',
					'autoload' => false,
					'desc_tip' => true,
				),

				array( 'type' => 'sectionend', 'id' => 'color_options' ),

				array( 'title' => __( 'Currency options', 'restaurantpress' ), 'type' => 'title', 'desc' => __( 'The following options affect how prices are displayed on the frontend.', 'restaurantpress' ), 'id' => 'pricing_options' ),

				array(
					'title'    => __( 'Currency', 'restaurantpress' ),
					'desc'     => __( 'This controls what currency prices are listed at in the menu catalog.', 'restaurantpress' ),
					'id'       => 'restaurantpress_currency',
					'default'  => 'USD',
					'type'     => 'select',
					'class'    => 'rp-enhanced-select',
					'desc_tip' => true,
					'options'  => $currency_code_options,
				),

				array(
					'title'    => __( 'Currency position', 'restaurantpress' ),
					'desc'     => __( 'This controls the position of the currency symbol.', 'restaurantpress' ),
					'id'       => 'restaurantpress_currency_pos',
					'class'    => 'rp-enhanced-select',
					'default'  => 'left',
					'type'     => 'select',
					'options'  => array(
						'left'        => __( 'Left', 'restaurantpress' ) . ' (' . get_restaurantpress_currency_symbol() . '&#x200e;99.99)',
						'right'       => __( 'Right', 'restaurantpress' ) . ' (99.99' . get_restaurantpress_currency_symbol() . '&#x200f;)',
						'left_space'  => __( 'Left with space', 'restaurantpress' ) . ' (' . get_restaurantpress_currency_symbol() . '&#x200e;&nbsp;99.99)',
						'right_space' => __( 'Right with space', 'restaurantpress' ) . ' (99.99&nbsp;' . get_restaurantpress_currency_symbol() . '&#x200f;)',
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Thousand separator', 'restaurantpress' ),
					'desc'     => __( 'This sets the thousand separator of displayed prices.', 'restaurantpress' ),
					'id'       => 'restaurantpress_price_thousand_sep',
					'css'      => 'width:50px;',
					'default'  => ',',
					'type'     => 'text',
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Decimal separator', 'restaurantpress' ),
					'desc'     => __( 'This sets the decimal separator of displayed prices.', 'restaurantpress' ),
					'id'       => 'restaurantpress_price_decimal_sep',
					'css'      => 'width:50px;',
					'default'  => '.',
					'type'     => 'text',
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Number of decimals', 'restaurantpress' ),
					'desc'     => __( 'This sets the number of decimal points shown in displayed prices.', 'restaurantpress' ),
					'id'       => 'restaurantpress_price_num_decimals',
					'css'      => 'width:50px;',
					'default'  => '2',
					'desc_tip' => true,
					'type'     => 'number',
					'custom_attributes' => array(
						'min'  => 0,
						'step' => 1,
					),
				),

				array( 'type' => 'sectionend', 'id' => 'pricing_options' ),

				array(
					'title' => __( 'Gallery options', 'restaurantpress' ),
					'type'  => 'title',
					'id'    => 'gallery_options',
				),

				array(
					'title'           => __( 'Enable features', 'restaurantpress' ),
					'desc'            => __( 'Enable gallery zoom', 'restaurantpress' ),
					'id'              => 'restaurantpress_enable_gallery_zoom',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => 'start',
					'show_if_checked' => 'option',
				),

				array(
					'desc'            => __( 'Enable gallery slider', 'restaurantpress' ),
					'id'              => 'restaurantpress_enable_gallery_slider',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => '',
					'autoload'        => false,
				),

				array(
					'desc'            => __( 'Enable gallery lightbox', 'restaurantpress' ),
					'id'              => 'restaurantpress_enable_gallery_lightbox',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => 'end',
					'autoload'        => false,
				),

				array( 'type' => 'sectionend', 'id' => 'gallery_options' ),

			) );
		}

		return apply_filters( 'restaurantpress_get_settings_' . $this->id, $settings, $current_section );
	}
}

return new RP_Settings_General();
