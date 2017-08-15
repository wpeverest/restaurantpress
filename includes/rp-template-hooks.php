<?php
/**
 * RestaurantPress Template Hooks
 *
 * Action/filter hooks used for RestaurantPress functions/templates.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Templates
 * @version  1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'body_class', 'rp_body_class' );

/**
 * WP Header.
 *
 * @see rp_generator_tag()
 */
add_action( 'get_the_generator_html', 'rp_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'rp_generator_tag', 10, 2 );

/**
 * Content Wrappers.
 *
 * @see restaurantpress_output_content_wrapper()
 * @see restaurantpress_output_content_wrapper_end()
 */
add_action( 'restaurantpress_before_main_content', 'restaurantpress_output_content_wrapper', 10 );
add_action( 'restaurantpress_after_main_content', 'restaurantpress_output_content_wrapper_end', 10 );
