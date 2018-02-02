<?php
/**
 * Twenty Seventeen support.
 *
 * @package RestaurantPress\Classes
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * RP_Twenty_Seventeen class.
 */
class RP_Twenty_Seventeen {

	/**
	 * Theme init.
	 */
	public static function init() {
		// Remove default wrappers.
		remove_action( 'restaurantpress_before_main_content', 'restaurantpress_output_content_wrapper', 10 );
		remove_action( 'restaurantpress_after_main_content', 'restaurantpress_output_content_wrapper_end', 10 );

		// Add custom wrappers.
		add_action( 'restaurantpress_before_main_content', array( __CLASS__, 'output_content_wrapper' ), 10 );
		add_action( 'restaurantpress_after_main_content', array( __CLASS__, 'output_content_wrapper_end' ), 10 );
		add_filter( 'restaurantpress_enqueue_styles', array( __CLASS__, 'enqueue_styles' ) );
		add_filter( 'twentyseventeen_custom_colors_css', array( __CLASS__, 'custom_colors_css' ), 10, 3 );

		// Declare theme support for features.
		add_theme_support( 'restaurantpress', array(
			'thumbnail_image_width' => 250,
			'single_image_width'    => 350,
		) );
	}

	/**
	 * Enqueue CSS for this theme.
	 *
	 * @param  array $styles Array of registered styles.
	 * @return array
	 */
	public static function enqueue_styles( $styles ) {
		$styles['restaurantpress-twenty-seventeen'] = array(
			'src'     => str_replace( array( 'http:', 'https:' ), '', RP()->plugin_url() ) . '/assets/css/twenty-seventeen.css',
			'deps'    => '',
			'version' => RP_VERSION,
			'media'   => 'all',
		);

		return apply_filters( 'restaurantpress_twenty_seventeen_styles', $styles );
	}

	/**
	 * Open the Twenty Seventeen wrapper.
	 */
	public static function output_content_wrapper() {
		echo '<div class="wrap">';
		echo '<div id="primary" class="content-area twentyseventeen">';
		echo '<main id="main" class="site-main" role="main">';
	}

	/**
	 * Close the Twenty Seventeen wrapper.
	 */
	public static function output_content_wrapper_end() {
		echo '</main>';
		echo '</div>';
		get_sidebar();
		echo '</div>';
	}


	/**
	 * Custom colors.
	 *
	 * @param  string $css Styles.
	 * @param  string $hue Color.
	 * @param  string $saturation Saturation.
	 * @return string
	 */
	public static function custom_colors_css( $css, $hue, $saturation ) {
		$css .= '
			.colors-custom .select2-container--default .select2-selection--single {
				border-color: hsl( ' . $hue . ', ' . $saturation . ', 73% );
			}
			.colors-custom .select2-container--default .select2-selection__rendered {
				color: hsl( ' . $hue . ', ' . $saturation . ', 40% );
			}
			.colors-custom .select2-container--default .select2-selection--single .select2-selection__arrow b {
				border-color: hsl( ' . $hue . ', ' . $saturation . ', 40% ) transparent transparent transparent;
			}
			.colors-custom .select2-container--focus .select2-selection {
				border-color: #000;
			}
			.colors-custom .select2-container--focus .select2-selection--single .select2-selection__arrow b {
				border-color: #000 transparent transparent transparent;
			}
			.colors-custom .select2-container--focus .select2-selection .select2-selection__rendered {
				color: #000;
			}
		';
		return $css;
	}
}

RP_Twenty_Seventeen::init();
