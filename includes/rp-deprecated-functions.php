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
 * Runs a deprecated action with notice only if used.
 *
 * @since 1.5.0
 * @param string $action
 * @param array  $args
 * @param string $deprecated_in
 * @param string $replacement
 */
function rp_do_deprecated_action( $action, $args, $deprecated_in, $replacement ) {
	if ( has_action( $action ) ) {
		rp_deprecated_function( 'Action: ' . $action, $deprecated_in, $replacement );
		do_action_ref_array( $action, $args );
	}
}

/**
 * Wrapper for deprecated functions so we can apply some extra logic.
 *
 * @since 1.5.0
 * @param string $function
 * @param string $version
 * @param string $replacement
 */
function rp_deprecated_function( $function, $version, $replacement = null ) {
	if ( is_ajax() ) {
		do_action( 'deprecated_function_run', $function, $replacement, $version );
		$log_string  = "The {$function} function is deprecated since version {$version}.";
		$log_string .= $replacement ? " Replace with {$replacement}." : '';
		error_log( $log_string );
	} else {
		_deprecated_function( $function, $version, $replacement );
	}
}

/**
 * Wrapper for rp_doing_it_wrong.
 *
 * @since 1.5.0
 * @param string $function
 * @param string $version
 * @param string $replacement
 */
function rp_doing_it_wrong( $function, $message, $version ) {
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( is_ajax() ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}
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
 * @deprecated
 */
function rp_taxonomy_metadata_wpdbfix() {
	rp_deprecated_function( __FUNCTION__, '1.3.2' );
}
