<?php
/**
 * Post Data
 *
 * Standardises certain post data on save.
 *
 * @class    RP_Post_Data
 * @version  1.4.2
 * @package  RestaurantPress/Classes/Data
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Post_Data Class.
 */
class RP_Post_Data {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_filter( 'oembed_response_data', array( __CLASS__, 'filter_oembed_response_data' ), 10, 2 );
	}

	/**
	 * Change embed data for certain post types.
	 *
	 * @since  1.4.2
	 * @param  array   $data The response data.
	 * @param  WP_Post $post The post object.
	 * @return array
	 */
	public static function filter_oembed_response_data( $data, $post ) {
		if ( in_array( $post->post_type, array( 'food_group' ) ) ) {
			return array();
		}
		return $data;
	}
}

RP_Post_Data::init();
