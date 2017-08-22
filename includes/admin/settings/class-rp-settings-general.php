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

if ( ! class_exists( 'RP_Settings_General', false ) ) :

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
	 * Get settings
	 * @return array
	 */
	public function get_settings() {

		$currency_code_options = get_restaurantpress_currencies();

		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $name . ' (' . get_restaurantpress_currency_symbol( $code ) . ')';
		}

		$settings = apply_filters( 'restaurantpress_general_settings', array(

			array( 'title' => __( 'Currency options', 'restaurantpress' ), 'type' => 'title', 'desc' => __( 'The following options affect how prices are displayed on the frontend.', 'restaurantpress' ), 'id' => 'pricing_options' ),

			array(
				'title'    => __( 'Currency', 'restaurantpress' ),
				'desc'     => __( 'This controls what currency prices are listed at in the menu catalog.', 'restaurantpress' ),
				'id'       => 'restaurantpress_currency',
				'default'  => 'NPR',
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
					'left'        => __( 'Left', 'restaurantpress' ) . ' (' . get_restaurantpress_currency_symbol() . '99.99)',
					'right'       => __( 'Right', 'restaurantpress' ) . ' (99.99' . get_restaurantpress_currency_symbol() . ')',
					'left_space'  => __( 'Left with space', 'restaurantpress' ) . ' (' . get_restaurantpress_currency_symbol() . ' 99.99)',
					'right_space' => __( 'Right with space', 'restaurantpress' ) . ' (99.99 ' . get_restaurantpress_currency_symbol() . ')',
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
