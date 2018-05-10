<?php
/**
 * RestaurantPress Template Hooks
 *
 * Action/filter hooks used for RestaurantPress functions/templates.
 *
 * @package RestaurantPress/Templates
 * @version 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'body_class', 'rp_body_class' );
add_filter( 'post_class', 'rp_food_post_class', 20, 3 );

/**
 * WP Header.
 *
 * @see rp_generator_tag()
 */
add_filter( 'get_the_generator_html', 'rp_generator_tag', 10, 2 );
add_filter( 'get_the_generator_xhtml', 'rp_generator_tag', 10, 2 );

/**
 * Content Wrappers.
 *
 * @see restaurantpress_output_content_wrapper()
 * @see restaurantpress_output_content_wrapper_end()
 */
add_action( 'restaurantpress_before_main_content', 'restaurantpress_output_content_wrapper', 10 );
add_action( 'restaurantpress_after_main_content', 'restaurantpress_output_content_wrapper_end', 10 );

/**
 * Chef badges.
 *
 * @see restaurantpress_show_food_loop_chef_badge()
 * @see restaurantpress_show_food_chef_badge()
 */
add_action( 'restaurantpress_before_menu_loop_item_summary', 'restaurantpress_show_food_loop_chef_badge', 10 );
add_action( 'restaurantpress_before_single_food_summary', 'restaurantpress_show_food_chef_badge', 10 );

/**
 * Sidebar.
 *
 * @see restaurantpress_get_sidebar()
 */
add_action( 'restaurantpress_sidebar', 'restaurantpress_get_sidebar', 10 );

/**
 * Archive descriptions.
 *
 * @see restaurantpress_taxonomy_archive_description()
 */
add_action( 'restaurantpress_archive_description', 'restaurantpress_taxonomy_archive_description', 10 );

/**
 * Foods Loop.
 *
 * @see rp_no_foods_found()
 */
add_action( 'restaurantpress_no_foods_found', 'rp_no_foods_found' );

/**
 * Food Loop Items.
 *
 * @see restaurantpress_template_loop_food_thumbnail()
 * @see restaurantpress_template_loop_food_title()
 * @see restaurantpress_template_loop_price()
 * @see restaurantpress_template_loop_excerpt()
 */
add_action( 'restaurantpress_before_menu_loop_item_summary', 'restaurantpress_template_loop_food_thumbnail', 10 );
add_action( 'restaurantpress_menu_loop_item_summary', 'restaurantpress_template_loop_food_title', 10 );
add_action( 'restaurantpress_menu_loop_item_summary', 'restaurantpress_template_loop_price', 20 );
add_action( 'restaurantpress_menu_loop_item_summary', 'restaurantpress_template_loop_excerpt', 30 );

/**
 * Before Single Products Summary Div.
 *
 * @see restaurantpress_show_food_images()
 * @see restaurantpress_show_food_thumbnails()
 */
add_action( 'restaurantpress_before_single_food_summary', 'restaurantpress_show_food_images', 20 );
add_action( 'restaurantpress_food_thumbnails', 'restaurantpress_show_food_thumbnails', 20 );

/**
 * After Single Products Summary Div.
 *
 * @see restaurantpress_output_food_data_tabs()
 */
add_action( 'restaurantpress_after_single_food_summary', 'restaurantpress_output_food_data_tabs', 10 );

/**
 * Food Summary Box.
 *
 * @see restaurantpress_template_single_title()
 * @see restaurantpress_template_single_price()
 * @see restaurantpress_template_single_excerpt()
 * @see restaurantpress_template_single_contact()
 * @see restaurantpress_template_single_meta()
 * @see restaurantpress_template_single_sharing()
 */
add_action( 'restaurantpress_single_food_summary', 'restaurantpress_template_single_title', 5 );
add_action( 'restaurantpress_single_food_summary', 'restaurantpress_template_single_price', 10 );
add_action( 'restaurantpress_single_food_summary', 'restaurantpress_template_single_excerpt', 20 );
add_action( 'restaurantpress_single_food_summary', 'restaurantpress_template_single_contact', 30 );
add_action( 'restaurantpress_single_food_summary', 'restaurantpress_template_single_meta', 40 );
add_action( 'restaurantpress_single_food_summary', 'restaurantpress_template_single_sharing', 50 );

/**
 * Pagination after menu loops.
 *
 * @see restaurantpress_pagination()
 */
add_action( 'restaurantpress_after_menu_loop', 'restaurantpress_pagination', 10 );

/**
 * Food page tabs.
 */
add_filter( 'restaurantpress_food_tabs', 'restaurantpress_default_food_tabs' );
add_filter( 'restaurantpress_food_tabs', 'restaurantpress_sort_food_tabs', 99 );

/**
 * Footer.
 *
 * @see rp_print_js()
 */
add_action( 'wp_footer', 'rp_print_js', 25 );
