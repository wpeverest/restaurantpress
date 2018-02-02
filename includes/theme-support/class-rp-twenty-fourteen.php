<?php
/**
 * Twenty Fourteen support.
 *
 * @package RestaurantPress\Classes
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * RP_Twenty_Fourteen class.
 */
class RP_Twenty_Fourteen {

	/**
	 * Theme init.
	 */
	public static function init() {
		// Remove default wrappers.
		remove_action( 'restaurantpress_before_main_content', 'restaurantpress_output_content_wrapper' );
		remove_action( 'restaurantpress_after_main_content', 'restaurantpress_output_content_wrapper_end' );

		// Add custom wrappers.
		add_action( 'restaurantpress_before_main_content', array( __CLASS__, 'output_content_wrapper' ) );
		add_action( 'restaurantpress_after_main_content', array( __CLASS__, 'output_content_wrapper_end' ) );

		// Declare theme support for features.
		add_theme_support( 'restaurantpress', array(
			'thumbnail_image_width' => 150,
			'single_image_width'    => 300,
		) );
	}

	/**
	 * Open wrappers.
	 */
	public static function output_content_wrapper() {
		echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfwc">';
	}

	/**
	 * Close wrappers.
	 */
	public static function output_content_wrapper_end() {
		echo '</div></div></div>';
		get_sidebar( 'content' );
	}
}

RP_Twenty_Fourteen::init();
