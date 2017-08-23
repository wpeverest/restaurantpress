<?php
/**
 * RestaurantPress Admin.
 *
 * @class    RP_Admin
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Admin Class
 */
class RP_Admin {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_footer', 'rp_print_js', 25 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once( dirname( __FILE__ ) . '/rp-admin-functions.php' );
		include_once( dirname( __FILE__ ) . '/rp-meta-box-functions.php' );
		include_once( dirname( __FILE__ ) . '/class-rp-admin-post-types.php' );
		include_once( dirname( __FILE__ ) . '/class-rp-admin-taxonomies.php' );
		include_once( dirname( __FILE__ ) . '/class-rp-admin-menus.php' );
		include_once( dirname( __FILE__ ) . '/class-rp-admin-notices.php' );
		include_once( dirname( __FILE__ ) . '/class-rp-admin-assets.php' );
		include_once( dirname( __FILE__ ) . '/class-rp-admin-tinymce.php' );
		include_once( dirname( __FILE__ ) . '/class-rp-admin-pointers.php' );
	}

	/**
	 * Change the admin footer text on RestaurantPress admin pages.
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_restaurantpress' ) ) {
			return;
		}
		$current_screen = get_current_screen();
		$rp_pages       = rp_get_screen_ids();

		// Check to make sure we're on a RestaurantPress admin page
		if ( isset( $current_screen->id ) && apply_filters( 'restaurantpress_display_admin_footer_text', in_array( $current_screen->id, $rp_pages ) ) ) {
			// Change the footer text
			if ( ! get_option( 'restaurantpress_admin_footer_text_rated' ) ) {
				$footer_text = sprintf( __( 'If you like <strong>RestaurantPress</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thanks in advance!', 'restaurantpress' ), '<a href="https://wordpress.org/support/plugin/restaurantpress/reviews?rate=5#new-post" target="_blank" class="rp-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'restaurantpress' ) . '">', '</a>' );
				rp_enqueue_js( "
					jQuery( 'a.rp-rating-link' ).click( function() {
						jQuery.post( '" . RP()->ajax_url() . "', { action: 'restaurantpress_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});
				" );
			} else {
				$footer_text = __( 'Thank you for creating with RestaurantPress.', 'restaurantpress' );
			}
		}

		return $footer_text;
	}
}

new RP_Admin();
