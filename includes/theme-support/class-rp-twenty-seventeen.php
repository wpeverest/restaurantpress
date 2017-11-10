<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Twenty Seventeen support.
 *
 * @class   RP_Twenty_Seventeen
 * @since   1.7.0
 * @package RestaurantPress/Classes
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

		// Declare theme support for features.
		add_theme_support( 'restaurantpress', array(
			'thumbnail_image_width' => 150,
			'single_image_width'    => 322,
		) );
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
}

RP_Twenty_Seventeen::init();
