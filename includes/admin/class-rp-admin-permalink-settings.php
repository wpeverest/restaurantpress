<?php
/**
 * Adds settings to the permalink admin settings page
 *
 * @class    RP_Admin_Permalink_Settings
 * @version  1.6.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'RP_Admin_Permalink_Settings', false ) ) {
	return new RP_Admin_Permalink_Settings();
}

/**
 * RP_Admin_Permalink_Settings Class.
 */
class RP_Admin_Permalink_Settings {

	/**
	 * Permalink settings.
	 *
	 * @var array
	 */
	private $permalinks = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		$this->settings_init();
		$this->settings_save();
	}

	/**
	 * Init our settings.
	 */
	public function settings_init() {
		add_settings_section( 'restaurantpress-permalink', __( 'Food permalinks', 'restaurantpress' ), array( $this, 'settings' ), 'permalink' );

		add_settings_field(
			'restaurantpress_food_category_slug',
			__( 'Food category base', 'restaurantpress' ),
			array( $this, 'food_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'restaurantpress_food_tag_slug',
			__( 'Food tag base', 'restaurantpress' ),
			array( $this, 'food_tag_slug_input' ),
			'permalink',
			'optional'
		);

		$this->permalinks = rp_get_permalink_structure();
	}

	/**
	 * Show a slug input box.
	 */
	public function food_category_slug_input() {
		?>
		<input name="restaurantpress_food_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['category_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'food-category', 'slug', 'restaurantpress' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function food_tag_slug_input() {
		?>
		<input name="restaurantpress_food_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['tag_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'food-tag', 'slug', 'restaurantpress' ); ?>" />
		<?php
	}

	/**
	 * Show the settings.
	 */
	public function settings() {
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your food URLs here. For example, using <code>menu</code> would make your food links like <code>%smenu/sample-food/</code>. This setting affects food URLs only, not things such as food categories.', 'restaurantpress' ), esc_url( home_url( '/' ) ) ) ) );

		$base_slug   = _x( 'menu', 'default-slug', 'restaurantpress' );
		$food_base   = _x( 'food', 'default-slug', 'restaurantpress' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $base_slug ),
			2 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%food_menu_cat%' ),
		);
		?>
		<table class="form-table rp-permalink-structure">
			<tbody>
				<tr>
					<th><label><input name="food_permalink" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="rptog" <?php checked( $structures[0], $this->permalinks['food_base'] ); ?> /> <?php esc_html_e( 'Default', 'restaurantpress' ); ?></label></th>
					<td><code class="default-example"><?php echo esc_html( home_url() ); ?>/?food=sample-food</code> <code class="non-default-example"><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $food_base ); ?>/sample-food/</code></td>
				</tr>
				<tr>
					<th><label><input name="food_permalink" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="rptog" <?php checked( $structures[1], $this->permalinks['food_base'] ); ?> /> <?php esc_html_e( 'Menu base', 'restaurantpress' ); ?></label></th>
					<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?>/sample-food/</code></td>
				</tr>
				<tr>
					<th><label><input name="food_permalink" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" class="rptog" <?php checked( $structures[2], $this->permalinks['food_base'] ); ?> /> <?php esc_html_e( 'Menu base with category', 'restaurantpress' ); ?></label></th>
					<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?>/food-category/sample-food/</code></td>
				</tr>
				<tr>
					<th><label><input name="food_permalink" id="restaurantpress_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $this->permalinks['food_base'], $structures, true ), false ); ?> />
						<?php esc_html_e( 'Custom base', 'restaurantpress' ); ?></label></th>
					<td>
						<input name="food_permalink_structure" id="restaurantpress_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['food_base'] ? trailingslashit( $this->permalinks['food_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'restaurantpress' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php wp_nonce_field( 'rp-permalinks', 'rp-permalinks-nonce' ); ?>
		<script type="text/javascript">
			jQuery( function() {
				jQuery( 'input.rptog' ).change(function() {
					jQuery( '#restaurantpress_permalink_structure' ).val( jQuery( this ).val() );
				});
				jQuery( '.permalink-structure input' ).change( function() {
					jQuery( '.rp-permalink-structure' ).find( 'code.non-default-example, code.default-example' ).hide();
					if ( jQuery( this ).val() ) {
						jQuery( '.rp-permalink-structure code.non-default-example' ).show();
						jQuery( '.rp-permalink-structure input' ).removeAttr( 'disabled' );
					} else {
						jQuery( '.rp-permalink-structure code.default-example' ).show();
						jQuery( '.rp-permalink-structure input:eq(0)' ).click();
						jQuery( '.rp-permalink-structure input' ).attr( 'disabled', 'disabled' );
					}
				});
				jQuery( '.permalink-structure input:checked' ).change();
				jQuery( '#restaurantpress_permalink_structure' ).focus( function(){
					jQuery( '#restaurantpress_custom_selection' ).click();
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Save the settings.
	 */
	public function settings_save() {
		if ( ! is_admin() ) {
			return;
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['rp-permalinks-nonce'], $_POST['restaurantpress_food_category_slug'], $_POST['restaurantpress_food_tag_slug'] ) && wp_verify_nonce( wp_unslash( $_POST['rp-permalinks-nonce'] ), 'rp-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			rp_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'restaurantpress_permalinks', array() );
			$permalinks['category_base']  = rp_sanitize_permalink( wp_unslash( $_POST['restaurantpress_food_category_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['tag_base']       = rp_sanitize_permalink( wp_unslash( $_POST['restaurantpress_food_tag_slug'] ) ); // WPCS: input var ok, sanitization ok.

			// Generate food base.
			$food_base = isset( $_POST['food_permalink'] ) ? rp_clean( wp_unslash( $_POST['food_permalink'] ) ) : ''; // WPCS: input var ok, sanitization ok.

			if ( 'custom' === $food_base ) {
				if ( isset( $_POST['food_permalink_structure'] ) ) { // WPCS: input var ok.
					$food_base = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', trim( wp_unslash( $_POST['food_permalink_structure'] ) ) ) ); // WPCS: input var ok, sanitization ok.
				} else {
					$food_base = '/';
				}

				// This is an invalid base structure and breaks pages.
				if ( '/%food_menu_cat%/' === trailingslashit( $food_base ) ) {
					$food_base = '/' . _x( 'food', 'slug', 'restaurantpress' ) . $food_base;
				}
			} elseif ( empty( $food_base ) ) {
				$food_base = _x( 'food', 'slug', 'restaurantpress' );
			}

			$permalinks['food_base'] = rp_sanitize_permalink( $food_base );

			update_option( 'restaurantpress_permalinks', $permalinks );
			rp_restore_locale();
		}
	}
}

return new RP_Admin_Permalink_Settings();
