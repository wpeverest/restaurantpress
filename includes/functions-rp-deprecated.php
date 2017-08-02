<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @deprecated
 */
function rp_shortcode_tag( $tag = '' ) {
	_deprecated_function( 'rp_shortcode_tag', '1.3', 'rp_post_content_has_shortcode' );
	return rp_post_content_has_shortcode( $tag );
}

/**
 * @deprecated
 */
function rp_taxonomy_metadata_wpdbfix() {}
