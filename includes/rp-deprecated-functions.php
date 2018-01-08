<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs a deprecated action with notice only if used.
 *
 * @since 1.6.0
 * @param string $tag         The name of the action hook.
 * @param array  $args        Array of additional function arguments to be passed to do_action().
 * @param string $version     The version of RestaurantPress that deprecated the hook.
 * @param string $replacement The hook that should have been used.
 * @param string $message     A message regarding the change.
 */
function rp_do_deprecated_action( $tag, $args, $version, $replacement = null, $message = null ) {
	if ( ! has_action( $tag ) ) {
		return;
	}

	rp_deprecated_hook( $tag, $version, $replacement, $message );
	do_action_ref_array( $tag, $args );
}

/**
 * Wrapper for deprecated functions so we can apply some extra logic.
 *
 * @since 1.5.0
 * @param string $function Function used.
 * @param string $version Version the message was added in.
 * @param string $replacement Replacement for the called function.
 */
function rp_deprecated_function( $function, $version, $replacement = null ) {
	// @codingStandardsIgnoreStart
	if ( is_ajax() ) {
		do_action( 'deprecated_function_run', $function, $replacement, $version );
		$log_string  = "The {$function} function is deprecated since version {$version}.";
		$log_string .= $replacement ? " Replace with {$replacement}." : '';
		error_log( $log_string );
	} else {
		_deprecated_function( $function, $version, $replacement );
	}
	// @codingStandardsIgnoreEnd
}

/**
 * Wrapper for deprecated hook so we can apply some extra logic.
 *
 * @since 1.6.0
 * @param string $hook        The hook that was used.
 * @param string $version     The version of WordPress that deprecated the hook.
 * @param string $replacement The hook that should have been used.
 * @param string $message     A message regarding the change.
 */
function rp_deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
	// @codingStandardsIgnoreStart
	if ( is_ajax() ) {
		do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

		$message    = empty( $message ) ? '' : ' ' . $message;
		$log_string = "{$hook} is deprecated since version {$version}";
		$log_string .= $replacement ? "! Use {$replacement} instead." : ' with no alternative available.';

		error_log( $log_string . $message );
	} else {
		_deprecated_hook( $hook, $version, $replacement, $message );
	}
	// @codingStandardsIgnoreEnd
}

/**
 * When catching an exception, this allows us to log it if unexpected.
 *
 * @since 1.6.0
 * @param Exception $exception_object The exception object.
 * @param string    $function The function which threw exception.
 * @param array     $args The args passed to the function.
 */
function rp_caught_exception( $exception_object, $function = '', $args = array() ) {
	// @codingStandardsIgnoreStart
	$message  = $exception_object->getMessage();
	$message .= '. Args: ' . print_r( $args, true ) . '.';

	do_action( 'restaurantpress_caught_exception', $exception_object, $function, $args );
	error_log( "Exception caught in {$function}. {$message}." );
	// @codingStandardsIgnoreEnd
}

/**
 * Wrapper for rp_doing_it_wrong.
 *
 * @since 1.5.0
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 */
function rp_doing_it_wrong( $function, $message, $version ) {
	// @codingStandardsIgnoreStart
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( is_ajax() ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}
	// @codingStandardsIgnoreEnd
}

/**
 * Wrapper for deprecated arguments so we can apply some extra logic.
 *
 * @since 1.5.0
 * @param string $argument
 * @param string $version
 * @param string $replacement
 */
function rp_deprecated_argument( $argument, $version, $message = null ) {
	if ( is_ajax() ) {
		do_action( 'deprecated_argument_run', $argument, $message, $version );
		error_log( "The {$argument} argument is deprecated since version {$version}. {$message}" );
	} else {
		_deprecated_argument( $argument, $version, $message );
	}
}

/**
 * @deprecated
 *
 * @param string $tag
 */
function rp_shortcode_tag( $tag = '' ) {
	rp_deprecated_function( 'rp_shortcode_tag', '1.3', 'rp_post_content_has_shortcode' );
	return rp_post_content_has_shortcode( $tag );
}

/**
 * @deprecated 1.3.2
 */
function rp_taxonomy_metadata_wpdbfix() {
	rp_deprecated_function( __FUNCTION__, '1.3.2' );
}

/**
 * RestaurantPress Core Supported Themes.
 *
 * @deprecated 1.7.0
 * @return string[]
 */
function rp_get_core_supported_themes() {
	rp_deprecated_function( 'rp_get_core_supported_themes()', '1.7' );
	return array( 'twentyseventeen', 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve','twentyeleven', 'twentyten' );
}
