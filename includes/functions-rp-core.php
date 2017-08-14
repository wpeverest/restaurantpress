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
include( RP_ABSPATH . 'includes/functions-rp-deprecated.php' );
include( RP_ABSPATH . 'includes/functions-rp-formatting.php' );
include( RP_ABSPATH . 'includes/functions-rp-term.php' );
include( RP_ABSPATH . 'includes/functions-rp-widget.php' );

/**
 * Get template part (for templates like the layout-loop).
 *
 * RP_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
 *
 * @param mixed  $slug
 * @param string $name (default: '')
 */
function rp_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/restaurantpress/slug-name.php
	if ( $name && ! RP_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}-{$name}.php", RP()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( RP()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = RP()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/restaurantpress/slug.php
	if ( ! $template && ! RP_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}.php", RP()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'rp_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. layout attributes) passing attributes and including the file.
 *
 * @param string $template_name
 * @param array  $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function rp_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = rp_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'rp_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'restaurantpress_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'restaurantpress_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like rp_get_template, but returns the HTML instead of outputting.
 *
 * @see   rp_get_template
 * @since 1.4.0
 * @param string $template_name
 * @param array  $args
 * @param string $template_path
 * @param string $default_path
 *
 * @return string
 */
function rp_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	rp_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path   /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @param  string $template_name
 * @param  string $template_path (default: '')
 * @param  string $default_path (default: '')
 * @return string
 */
function rp_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = RP()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = RP()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/
	if ( ! $template || RP_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'restaurantpress_locate_template', $template, $template_name, $template_path );
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
	return array( 'twentyseventeen', 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve','twentyeleven', 'twentyten' );
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

/**
 * Prints human-readable information about a variable.
 *
 * Some server environments blacklist some debugging functions. This function provides a safe way to
 * turn an expression into a printable, readable form without calling blacklisted functions.
 *
 * @since 1.4
 *
 * @param mixed $expression The expression to be printed.
 * @param bool  $return Optional. Default false. Set to true to return the human-readable string.
 * @return string|bool False if expression could not be printed. True if the expression was printed.
 *     If $return is true, a string representation will be returned.
 */
function rp_print_r( $expression, $return = false ) {
	$alternatives = array(
		array( 'func' => 'print_r', 'args' => array( $expression, true ) ),
		array( 'func' => 'var_export', 'args' => array( $expression, true ) ),
		array( 'func' => 'json_encode', 'args' => array( $expression ) ),
		array( 'func' => 'serialize', 'args' => array( $expression ) ),
	);

	$alternatives = apply_filters( 'restaurantpress_print_r_alternatives', $alternatives, $expression );

	foreach ( $alternatives as $alternative ) {
		if ( function_exists( $alternative['func'] ) ) {
			$res = call_user_func_array( $alternative['func'], $alternative['args'] );
			if ( $return ) {
				return $res;
			} else {
				echo $res;
				return true;
			}
		}
	}

	return false;
}
