<?php
/**
 * RestaurantPress Template
 *
 * Functions for the templating system.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show the gallery if JS is disabled.
 *
 * @since 1.4.0
 */
function rp_gallery_noscript() {
	?>
	<noscript><style>.restaurantpress-food-gallery{ opacity: 1 !important; }</style></noscript>
	<?php
}
add_action( 'wp_head', 'rp_gallery_noscript' );

/**
 * When the_post is called, put food data into a global.
 *
 * @param mixed $post
 * @return RP_Food
 */
function rp_setup_food_data( $post ) {
	unset( $GLOBALS['food'] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'food_menu' ) ) ) {
		return;
	}

	$GLOBALS['food'] = rp_get_food( $post );

	return $GLOBALS['food'];
}
add_action( 'the_post', 'rp_setup_food_data' );

/**
 * Add body classes for RP pages.
 *
 * @param  array $classes
 * @return array
 */
function rp_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_restaurantpress() ) {

		$classes[] = 'restaurantpress';
		$classes[] = 'restaurantpress-page';

	} elseif ( is_group_menu_page() ) {

		$classes[] = 'restaurantpress-group';
		$classes[] = 'restaurantpress-page';

	}

	return array_unique( $classes );
}

/**
 * Output generator tag to aid debugging.
 *
 * @param string $gen
 * @param string $type
 *
 * @return string
 */
function rp_generator_tag( $gen, $type ) {
	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="RestaurantPress ' . esc_attr( RP_VERSION ) . '">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="RestaurantPress ' . esc_attr( RP_VERSION ) . '" />';
			break;
	}
	return $gen;
}

/** Global ****************************************************************/

if ( ! function_exists( 'restaurantpress_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 */
	function restaurantpress_output_content_wrapper() {
		rp_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'restaurantpress_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 */
	function restaurantpress_output_content_wrapper_end() {
		rp_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'restaurantpress_get_sidebar' ) ) {

	/**
	 * Get the food sidebar template.
	 *
	 */
	function restaurantpress_get_sidebar() {
		rp_get_template( 'global/sidebar.php' );
	}
}

/** Single Food ***********************************************************/

if ( ! function_exists( 'restaurantpress_show_food_chef_badge' ) ) {

	/**
	 * Output the food chef badge.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_show_food_chef_badge() {
		rp_get_template( 'single-food/chef-badge.php' );
	}
}
if ( ! function_exists( 'restaurantpress_show_food_images' ) ) {

	/**
	 * Output the food image before the single food summary.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_show_food_images() {
		rp_get_template( 'single-food/food-image.php' );
	}
}
if ( ! function_exists( 'restaurantpress_show_food_thumbnails' ) ) {

	/**
	 * Output the food thumbnails.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_show_food_thumbnails() {
		rp_get_template( 'single-food/food-thumbnails.php' );
	}
}

if ( ! function_exists( 'restaurantpress_photoswipe' ) ) {

	/**
	 * Get the photoswipe template.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_photoswipe() {
		if ( 'yes' === get_option( 'restaurantpress_enable_gallery_lightbox' ) ) {
			rp_get_template( 'single-food/photoswipe.php' );
		}
	}
}
if ( ! function_exists( 'restaurantpress_template_single_title' ) ) {

	/**
	 * Output the food title.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_title() {
		rp_get_template( 'single-food/title.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_price' ) ) {

	/**
	 * Output the food price.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_price() {
		rp_get_template( 'single-food/price.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_excerpt' ) ) {

	/**
	 * Output the food short description (excerpt).
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_excerpt() {
		rp_get_template( 'single-food/short-description.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_contact' ) ) {

	/**
	 * Output the food contact.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_contact() {
		rp_get_template( 'single-food/contact.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_meta' ) ) {

	/**
	 * Output the food meta.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_meta() {
		rp_get_template( 'single-food/meta.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_sharing' ) ) {

	/**
	 * Output the food sharing.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_sharing() {
		rp_get_template( 'single-food/share.php' );
	}
}
if ( ! function_exists( 'restaurantpress_output_food_data_tabs' ) ) {

	/**
	 * Output the food tabs.
	 *
	 * @subpackage Food/Tabs
	 */
	function restaurantpress_output_food_data_tabs() {
		rp_get_template( 'single-food/tabs/tabs.php' );
	}
}
if ( ! function_exists( 'restaurantpress_food_description_tab' ) ) {

	/**
	 * Output the description tab content.
	 *
	 * @subpackage Food/Tabs
	 */
	function restaurantpress_food_description_tab() {
		rp_get_template( 'single-food/tabs/description.php' );
	}
}

if ( ! function_exists( 'restaurantpress_default_food_tabs' ) ) {

	/**
	 * Add default food tabs to food pages.
	 *
	 * @param  array $tabs
	 * @return array
	 */
	function restaurantpress_default_food_tabs( $tabs = array() ) {
		global $post;

		// Description tab - shows product content
		if ( $post->post_content ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'restaurantpress' ),
				'priority' => 10,
				'callback' => 'restaurantpress_food_description_tab',
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'restaurantpress_sort_food_tabs' ) ) {

	/**
	 * Sort tabs by priority.
	 *
	 * @param  array $tabs
	 * @return array
	 */
	function restaurantpress_sort_food_tabs( $tabs = array() ) {

		// Make sure the $tabs parameter is an array
		if ( ! is_array( $tabs ) ) {
			trigger_error( "Function restaurantpress_sort_food_tabs() expects an array as the first parameter. Defaulting to empty array." );
			$tabs = array();
		}

		// Re-order tabs by priority
		if ( ! function_exists( '_sort_priority_callback' ) ) {
			function _sort_priority_callback( $a, $b ) {
				if ( $a['priority'] === $b['priority'] ) {
					return 0;
				}
				return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
			}
		}

		uasort( $tabs, '_sort_priority_callback' );

		return $tabs;
	}
}
