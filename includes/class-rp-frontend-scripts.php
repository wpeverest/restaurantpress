<?php
/**
 * Handle frontend scripts.
 *
 * @class    RP_Frontend_Scripts
 * @version  1.0.0
 * @package  RestaurantPress/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Frontend_Scripts Class.
 */
class RP_Frontend_Scripts {

	/**
	 * Contains an array of script handles registered by RP.
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by RP.
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Hooks in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
	}

	/**
	 * Get styles for the frontend.
	 * @access private
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'restaurantpress_enqueue_styles', array(
			'restaurantpress-general' => array(
				'src'     => self::get_asset_url( 'assets/css/restaurantpress.css' ),
				'deps'    => '',
				'version' => RP_VERSION,
				'media'   => 'all',
				'has_rtl' => true,
			)
		) );
	}

	/**
	 * Return protocol relative asset URL.
	 *
	 * @param string $path
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'restaurantpress_get_asset_url', plugins_url( $path, RP_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
	 *
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = RP_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 *
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = RP_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @access private
	 *
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 * @param  boolean  $has_rtl
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = RP_VERSION, $media = 'all', $has_rtl = false ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );

		if ( $has_rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @access private
	 *
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 * @param  boolean  $has_rtl
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = RP_VERSION, $media = 'all', $has_rtl = false ) {
		if ( ! in_array( $handle, self::$styles ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Register/enqueue frontend scripts.
	 */
	public static function load_scripts() {
		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$lightbox_en          = 'yes' === get_option( 'restaurantpress_enable_lightbox' );
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', RP()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'js/frontend/';

		if ( apply_filters( 'restaurantpress_is_widget_menu_active', is_active_widget( false, false, 'restaurantpress_widget_menu', true ) ) || rp_post_content_has_shortcode( 'restaurantpress_menu' ) ) {

			// Register frontend scripts conditionally
			if ( $lightbox_en ) {
				self::enqueue_script( 'prettyPhoto', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.6', true );
				self::enqueue_script( 'prettyPhoto-init', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery','prettyPhoto' ) );
				self::enqueue_style( 'restaurantpress_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css' );
			}

			// Global frontend scripts
			self::enqueue_script( 'restaurantpress', $frontend_script_path . 'restaurantpress' . $suffix . '.js', array( 'jquery' ) );

			// CSS Styles
			if ( $enqueue_styles = self::get_styles() ) {
				foreach ( $enqueue_styles as $handle => $args ) {
					self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
				}
			}

			// Inline Styles
			self::create_primary_styles();
		}
	}

	/**
	 * Enqueues front-end CSS for primary color.
	 *
	 * @uses   wp_add_inline_style()
	 * @access private
	 * @param  string $default_color
	 */
	private static function create_primary_styles( $default_color = '#d60e10' ) {
		$primary_color = get_option( 'restaurantpress_primary_color' );

		// Check if the primary color is default?
		if ( $primary_color === $default_color ) {
			return;
		}

		$inline_css = '
			.restaurantpress .rp-chef-badge {
				background: %1$s !important;
			}

			.restaurantpress .rp-chef-badge:before,
			.restaurantpress .rp-chef-badge:after {
				border-top-color: %1$s !important;
			}

			.restaurantpress .rp-price {
				background: %1$s !important;
			}

			.restaurantpress .rp-price:before {
				border-right-color: %1$s !important;
			}

			.restaurantpress .rp-content-wrapper {
				border-bottom-color: %1$s !important;
			}

			.restaurantpress .image-magnify span:hover {
				background: %1$s !important;
				border-color: %1$s !important;
			}
		';

		wp_add_inline_style( 'restaurantpress-general', sprintf( $inline_css, esc_attr( $primary_color ) ) );
	}
}

RP_Frontend_Scripts::init();
