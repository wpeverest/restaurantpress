<?php
/**
 * RestaurantPress General Settings
 *
 * @class   RP_Settings_General
 * @version 1.0.0
 * @package RestaurantPress/Admin
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
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );

		$this->food_display_settings_moved_notice();

		RP_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Show a notice showing where some options have moved.
	 *
	 * @since 1.7.0
	 * @todo  remove in next major release.
	 */
	private function food_display_settings_moved_notice() {
		if ( get_user_meta( get_current_user_id(), 'dismissed_food_display_settings_moved_notice', true ) ) {
			return;
		}
		?>
		<div id="message" class="updated restaurantpress-message inline">
			<a class="restaurantpress-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'rp-hide-notice', 'food_display_settings_moved' ), 'restaurantpress_hide_notices_nonce', '_rp_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'restaurantpress' ); ?></a>

			<p><?php
			/* translators: %s: URL to customizer. */
			echo wp_kses( sprintf( __( 'Looking for the food display and color options? They can now be found in the Customizer. <a href="%s">Go see them in action here.</a>', 'restaurantpress' ), esc_url( add_query_arg( array(
				'autofocus' => array(
					'panel' => 'restaurantpress',
				),
			), admin_url( 'customize.php' ) ) ) ), array(
				'a' => array(
					'href'  => array(),
					'title' => array(),
				),
			) );
			?></p>
		</div>
		<?php
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

		$settings = apply_filters( 'restaurantpress_general_settings', array(

			array(
				'title' => __( 'Currency options', 'restaurantpress' ),
				'type'  => 'title',
				'desc'  => __( 'The following options affect how prices are displayed on the frontend.', 'restaurantpress' ),
				'id'    => 'pricing_options',
			),

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
					'left'        => __( 'Left', 'restaurantpress' ),
					'right'       => __( 'Right', 'restaurantpress' ),
					'left_space'  => __( 'Left with space', 'restaurantpress' ),
					'right_space' => __( 'Right with space', 'restaurantpress' ),
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

			array(
				'type' => 'sectionend',
				'id'   => 'pricing_options',
			),

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

			array(
				'type' => 'sectionend',
				'id'   => 'gallery_options',
			),

		) );

		return apply_filters( 'restaurantpress_get_settings_' . $this->id, $settings, $current_section );
	}
}

return new RP_Settings_General();
