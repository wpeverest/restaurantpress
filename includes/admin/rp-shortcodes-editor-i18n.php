<?php
/**
 * TinyMCE i18n
 *
 * @package  RestaurantPress/i18n
 * @category i18n
 * @author   WPEverest
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
	require( ABSPATH . WPINC . '/class-wp-editor.php' );
}

if ( ! function_exists( 'restaurantpress_tinymce_plugin_translation' ) ) :

/**
 * TinyMCE Plugin Translation.
 * @return string $translated TinyMCE language strings.
 */
function restaurantpress_tinymce_plugin_translation() {

	// Default TinyMCE strings.
	$mce_translation = array(
		'id'              => __( 'Group ID', 'restaurantpress' ),
		'orderby'         => __( 'Order BY', 'restaurantpress' ),
		'date'            => __( 'Date', 'restaurantpress' ),
		'rand'            => __( 'Random', 'restaurantpress' ),
		'title'           => __( 'Title', 'restaurantpress' ),
		'menu_order'      => __( 'Menu Order', 'restaurantpress' ),
		'none'            => __( 'None', 'restaurantpress' ),
		'order'           => __( 'Order', 'restaurantpress' ),
		'asc'             => __( 'ASC', 'restaurantpress' ),
		'desc'            => __( 'DESC', 'restaurantpress' ),
		'shortcode_title' => __( 'Insert Food Menu', 'restaurantpress' ),
		'need_group_id'   => __( 'You need to use a Group ID!', 'restaurantpress' )
	);

	/**
	 * Filter translated strings prepared for TinyMCE.
	 * @param array $mce_translation Key/value pairs of strings.
	 * @since 1.0.0
	 */
	$mce_translation = apply_filters( 'restaurantpress_mce_translations', $mce_translation );

	$mce_locale = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $mce_locale . '.restaurantpress_shortcodes", ' . json_encode( $mce_translation ) . ");\n";

	return $translated;
}

endif;

$strings = restaurantpress_tinymce_plugin_translation();
