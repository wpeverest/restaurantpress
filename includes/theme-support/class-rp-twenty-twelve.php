<?php
/**
 * Twenty Twelve support.
 *
 * @package RestaurantPress\Classes
 * @since   1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Twenty_Twelve Class.
 */
class RP_Twenty_Twelve {

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
			'thumbnail_image_width' => 140,
			'single_image_width'    => 300,
		) );
	}

	/**
	 * Open wrappers.
	 */
	public static function output_content_wrapper() {
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
	}

	/**
	 * Close wrappers.
	 */
	public static function output_content_wrapper_end() {
		echo '</div></div>';
	}
}

RP_Twenty_Twelve::init();
