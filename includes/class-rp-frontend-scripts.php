<?php
/**
 * Handle frontend scripts.
 *
 * @package RestaurantPress\Classes
 * @version 1.0.0
 * @since   1.0.0
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
	 *
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by RP.
	 *
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized by RP.
	 *
	 * @var array
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Hooks in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'customize_preview_init', array( __CLASS__, 'customizer_live_preview' ) );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	/**
	 * Get styles for the frontend.
	 *
	 * @access private
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'restaurantpress_enqueue_styles', array(
			'restaurantpress-layout' => array(
				'src'     => self::get_asset_url( 'assets/css/restaurantpress-layout.css' ),
				'deps'    => '',
				'version' => RP_VERSION,
				'media'   => 'all',
				'has_rtl' => true,
			),
			'restaurantpress-smallscreen' => array(
				'src'     => self::get_asset_url( 'assets/css/restaurantpress-smallscreen.css' ),
				'deps'    => 'restaurantpress-layout',
				'version' => RP_VERSION,
				'media'   => 'only screen and (max-width: ' . apply_filters( 'restaurantpress_style_smallscreen_breakpoint', '768px' ) . ')',
				'has_rtl' => true,
			),
			'restaurantpress-general' => array(
				'src'     => self::get_asset_url( 'assets/css/restaurantpress.css' ),
				'deps'    => '',
				'version' => RP_VERSION,
				'media'   => 'all',
				'has_rtl' => true,
			),
		) );
	}

	/**
	 * Return asset URL.
	 *
	 * @param  string $path
	 * @return string
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'restaurantpress_get_asset_url', plugins_url( $path, RP_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
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
	 * Register all RP scripts.
	 */
	private static function register_scripts() {
		$suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$register_scripts = array(
			'flexslider' => array(
				'src'     => self::get_asset_url( 'assets/js/flexslider/jquery.flexslider' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '2.7.0',
			),
			'jquery-blockui' => array(
				'src'     => self::get_asset_url( 'assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '2.70',
			),
			'photoswipe' => array(
				'src'     => self::get_asset_url( 'assets/js/photoswipe/photoswipe' . $suffix . '.js' ),
				'deps'    => array(),
				'version' => '4.1.1',
			),
			'photoswipe-ui-default'  => array(
				'src'     => self::get_asset_url( 'assets/js/photoswipe/photoswipe-ui-default' . $suffix . '.js' ),
				'deps'    => array( 'photoswipe' ),
				'version' => '4.1.1',
			),
			'selectWoo' => array(
				'src'     => self::get_asset_url( 'assets/js/selectWoo/selectWoo.full' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '1.0.2',
			),
			'rp-single-food' => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/single-food' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => RP_VERSION,
			),
			'restaurantpress' => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/restaurantpress' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => RP_VERSION,
			),
			'zoom' => array(
				'src'     => self::get_asset_url( 'assets/js/zoom/jquery.zoom' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '1.7.15',
			),
		);
		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

	/**
	 * Register all RP styles.
	 */
	private static function register_styles() {
		$register_styles = array(
			'photoswipe' => array(
				'src'     => self::get_asset_url( 'assets/css/photoswipe/photoswipe.css' ),
				'deps'    => array(),
				'version' => RP_VERSION,
				'has_rtl' => false,
			),
			'photoswipe-default-skin' => array(
				'src'     => self::get_asset_url( 'assets/css/photoswipe/default-skin/default-skin.css' ),
				'deps'    => array( 'photoswipe' ),
				'version' => RP_VERSION,
				'has_rtl' => false,
			),
		);
		foreach ( $register_styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
		}
	}

	/**
	 * Register/enqueue frontend scripts.
	 */
	public static function load_scripts() {
		global $post;

		if ( ! did_action( 'before_restaurantpress_init' ) ) {
			return;
		}

		self::register_scripts();
		self::register_styles();

		// Load gallery scripts on food pages only if supported.
		if ( is_restaurantpress() || is_group_menu_page() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[restaurantpress_menu' ) ) ) {
			if ( 'yes' === get_option( 'restaurantpress_enable_gallery_zoom' ) ) {
				self::enqueue_script( 'zoom' );
			}
			if ( 'yes' === get_option( 'restaurantpress_enable_gallery_slider' ) ) {
				self::enqueue_script( 'flexslider' );
			}
			if ( 'yes' === get_option( 'restaurantpress_enable_gallery_lightbox' ) ) {
				self::enqueue_script( 'photoswipe-ui-default' );
				self::enqueue_style( 'photoswipe-default-skin' );
				add_action( 'wp_footer', 'restaurantpress_photoswipe' );
			}
			self::enqueue_script( 'rp-single-food' );
		}

		// Global frontend scripts.
		self::enqueue_script( 'restaurantpress' );

		// CSS Styles.
		$enqueue_styles = self::get_styles();

		if ( $enqueue_styles ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				if ( ! isset( $args['has_rtl'] ) ) {
					$args['has_rtl'] = false;
				}

				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
			}
		}

		// Custom RP colors inline style.
		wp_add_inline_style( 'restaurantpress-general', get_option( 'restaurantpress_colors_css' ) );
	}

	/**
	 * Register/enqueue customizer live preview.
	 */
	public static function customizer_live_preview() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'restaurantpress-customizer', self::get_asset_url( '/assets/js/frontend/customizer' . $suffix . '.js' ), array( 'jquery', 'customize-preview' ), RP_VERSION, true );
	}

	/**
	 * Localize a RP script once.
	 *
	 * @access private
	 * @since  1.4.0 this needs less wp_script_is() calls due to https://core.trac.wordpress.org/ticket/28404 being added in WP 4.0.
	 * @param  string $handle
	 */
	private static function localize_script( $handle ) {
		$data = self::get_script_data( $handle );

		if ( ! in_array( $handle, self::$wp_localize_scripts ) && wp_script_is( $handle ) && $data ) {
			$name                        = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Return data for script handles.
	 *
	 * @access private
	 * @param  string $handle
	 * @return array|bool
	 */
	private static function get_script_data( $handle ) {
		switch ( $handle ) {
			case 'rp-single-food':
				$params = array(
					'flexslider'         => apply_filters( 'restaurantpress_single_food_carousel_options', array(
						'rtl'            => is_rtl(),
						'animation'      => 'slide',
						'smoothHeight'   => true,
						'directionNav'   => false,
						'controlNav'     => 'thumbnails',
						'slideshow'      => false,
						'animationSpeed' => 500,
						'animationLoop'  => false, // Breaks photoswipe pagination if true.
						'allowOneSlide'  => false,
					) ),
					'zoom_enabled'       => apply_filters( 'restaurantpress_single_food_zoom_enabled', 'yes' === get_option( 'restaurantpress_enable_gallery_zoom' ) ? 1 : 0 ),
					'zoom_options'       => apply_filters( 'restaurantpress_single_food_zoom_options', array() ),
					'photoswipe_enabled' => apply_filters( 'restaurantpress_single_food_photoswipe_enabled', 'yes' === get_option( 'restaurantpress_enable_gallery_lightbox' ) ? 1 : 0 ),
					'photoswipe_options' => apply_filters( 'restaurantpress_single_food_photoswipe_options', array(
						'shareEl'               => false,
						'closeOnScroll'         => false,
						'history'               => false,
						'hideAnimationDuration' => 0,
						'showAnimationDuration' => 0,
					) ),
					'flexslider_enabled' => apply_filters( 'restaurantpress_single_food_flexslider_enabled', 'yes' === get_option( 'restaurantpress_enable_gallery_slider' ) ? 1 : 0 ),
				);
				break;
			default:
				$params = false;
				break;
		}

		return apply_filters( 'restaurantpress_get_script_data', $params, $handle );
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}
}

RP_Frontend_Scripts::init();
