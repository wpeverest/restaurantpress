<?php
/**
 * RestaurantPress Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include core functions (available in both admin and frontend).
include( RP_ABSPATH . 'includes/functions-rp-term.php' );
include( RP_ABSPATH . 'includes/functions-rp-deprecated.php' );
include( RP_ABSPATH . 'includes/functions-rp-widget.php' );

/**
 * Clean variables using sanitize_text_field
 * @param  string|array $var
 * @return string
 */
function rp_clean( $var ) {
	return is_array( $var ) ? array_map( 'rp_clean', $var ) : sanitize_text_field( $var );
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since  1.0.0  Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 * @param  string $var
 * @return string
 */
function rp_sanitize_tooltip( $var ) {
	return htmlspecialchars( wp_kses( html_entity_decode( $var ), array(
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'small'  => array(),
		'span'   => array(),
		'ul'     => array(),
		'li'     => array(),
		'ol'     => array(),
		'p'      => array(),
	) ) );
}

/**
 * Queue some JavaScript code to be output in the footer.
 * @param string $code
 */
function rp_enqueue_js( $code ) {
	global $rp_queued_js;

	if ( empty( $rp_queued_js ) ) {
		$rp_queued_js = '';
	}

	$rp_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function rp_print_js() {
	global $rp_queued_js;

	if ( ! empty( $rp_queued_js ) ) {
		// Sanitize.
		$rp_queued_js = wp_check_invalid_utf8( $rp_queued_js );
		$rp_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $rp_queued_js );
		$rp_queued_js = str_replace( "\r", '', $rp_queued_js );

		$js = "<!-- RestaurantPress JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $rp_queued_js });\n</script>\n";

		/**
		 * restaurantpress_queued_js filter.
		 * @param string $js JavaScript code.
		 */
		echo apply_filters( 'restaurantpress_queued_js', $js );

		unset( $rp_queued_js );
	}
}

/**
 * Get an image size.
 *
 * Variable is filtered by restaurantpress_get_image_size_{image_size}.
 *
 * @param  mixed $image_size
 * @return array
 */
function rp_get_image_size( $image_size ) {
	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => $crop
		);

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'food_grid' ) ) ) {
		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '370';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '245';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;

	} elseif ( in_array( $image_size, array( 'food_thumbnail' ) ) ) {
		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '100';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '100';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;

	} else {
		$size = array(
			'width'  => '100',
			'height' => '100',
			'crop'   => 0
		);
	}

	return apply_filters( 'restaurantpress_get_image_size_' . $image_size, $size );
}

/**
 * Get the placeholder image URL.
 * @return string
 */
function rp_placeholder_img_src( $thumb_size = 'small' ) {
	return apply_filters( 'restaurantpress_placeholder_img_src', RP()->plugin_url() . '/assets/images/placeholder-' . $thumb_size . '.jpg' );
}

/**
 * Get the placeholder image.
 * @return string
 */
function rp_placeholder_img( $size = 'food_thumbnail' ) {
	$dimensions = rp_get_image_size( $size );
	$thumb_size = $dimensions['width'] == 100 ? 'small' : 'large';

	return apply_filters( 'restaurantpress_placeholder_img', '<img src="' . rp_placeholder_img_src( $thumb_size ) . '" alt="' . esc_attr__( 'Placeholder', 'restaurantpress' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="restaurantpress-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
}

/**
 * RestaurantPress Core Supported Themes.
 * @return string[]
 */
function rp_get_core_supported_themes() {
	return array( 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve','twentyeleven', 'twentyten' );
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function rp_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}

/**
 * Display a RestaurantPress help tip.
 *
 * @param  string $tip Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
function rp_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = rp_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="restaurantpress-help-tip" data-tip="' . $tip . '"></span>';
}
