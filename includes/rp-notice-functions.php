<?php
/**
 * RestaurantPress Message Functions
 *
 * Functions for error/message handling and display.
 *
 * @package RestaurantPress/Functions
 * @version 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 *
 * @since  1.5.0
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return int
 */
function rp_notice_count( $notice_type = '' ) {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		rp_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before restaurantpress_init.', 'restaurantpress' ), '1.5' );
		return;
	}

	$notice_count = 0;
	$all_notices  = RP()->session->get( 'rp_notices', array() );

	if ( isset( $all_notices[ $notice_type ] ) ) {

		$notice_count = count( $all_notices[ $notice_type ] );

	} elseif ( empty( $notice_type ) ) {

		foreach ( $all_notices as $notices ) {
			$notice_count += count( $notices );
		}
	}

	return $notice_count;
}

/**
 * Check if a notice has already been added.
 *
 * @since  1.5.0
 * @param  string $message The text to display in the notice.
 * @param  string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 * @return bool
 */
function rp_has_notice( $message, $notice_type = 'success' ) {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		rp_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before restaurantpress_init.', 'restaurantpress' ), '1.5' );
		return;
	}

	$notices = RP()->session->get( 'rp_notices', array() );
	$notices = isset( $notices[ $notice_type ] ) ? $notices[ $notice_type ] : array();
	return array_search( $message, $notices, true ) !== false;
}

/**
 * Add and store a notice.
 *
 * @since 1.5.0
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 */
function rp_add_notice( $message, $notice_type = 'success' ) {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		rp_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before restaurantpress_init.', 'restaurantpress' ), '1.5' );
		return;
	}

	$notices = RP()->session->get( 'rp_notices', array() );

	// Backward compatibility.
	if ( 'success' === $notice_type ) {
		$message = apply_filters( 'restaurantpress_add_message', $message );
	}

	$notices[ $notice_type ][] = apply_filters( 'restaurantpress_add_' . $notice_type, $message );

	RP()->session->set( 'rp_notices', $notices );
}

/**
 * Set all notices at once.
 *
 * @since 1.5.0
 * @param mixed $notices Array of notices.
 */
function rp_set_notices( $notices ) {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		rp_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before restaurantpress_init.', 'restaurantpress' ), '1.5' );
		return;
	}
	RP()->session->set( 'rp_notices', $notices );
}

/**
 * Unset all notices.
 *
 * @since 1.5.0
 */
function rp_clear_notices() {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		rp_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before restaurantpress_init.', 'restaurantpress' ), '1.5' );
		return;
	}
	RP()->session->set( 'rp_notices', null );
}

/**
 * Prints messages and errors which are stored in the session, then clears them.
 *
 * @since 1.5.0
 */
function rp_print_notices() {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		rp_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before restaurantpress_init.', 'restaurantpress' ), '1.5' );
		return;
	}

	$all_notices  = RP()->session->get( 'rp_notices', array() );
	$notice_types = apply_filters( 'restaurantpress_notice_types', array( 'error', 'success', 'notice' ) );

	foreach ( $notice_types as $notice_type ) {
		if ( rp_notice_count( $notice_type ) > 0 ) {
			rp_get_template( "notices/{$notice_type}.php", array(
				'messages' => array_filter( $all_notices[ $notice_type ] ),
			) );
		}
	}

	rp_clear_notices();
}
add_action( 'restaurantpress_before_menu_loop', 'rp_print_notices', 10 );
add_action( 'restaurantpress_before_single_food', 'rp_print_notices', 10 );

/**
 * Print a single notice immediately.
 *
 * @since 2.1
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 */
function rp_print_notice( $message, $notice_type = 'success' ) {
	if ( 'success' === $notice_type ) {
		$message = apply_filters( 'restaurantpress_add_message', $message );
	}

	rp_get_template( "notices/{$notice_type}.php", array(
		'messages' => array( apply_filters( 'restaurantpress_add_' . $notice_type, $message ) ),
	) );
}

/**
 * Returns all queued notices, optionally filtered by a notice type.
 *
 * @since  1.5.0
 * @param  string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 * @return array|mixed
 */
function rp_get_notices( $notice_type = '' ) {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		rp_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before restaurantpress_init.', 'restaurantpress' ), '1.5' );
		return;
	}

	$all_notices = RP()->session->get( 'rp_notices', array() );

	if ( empty( $notice_type ) ) {
		$notices = $all_notices;
	} elseif ( isset( $all_notices[ $notice_type ] ) ) {
		$notices = $all_notices[ $notice_type ];
	} else {
		$notices = array();
	}

	return $notices;
}

/**
 * Add notices for WP Errors.
 *
 * @param WP_Error $errors Errors.
 */
function rp_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			rp_add_notice( $error, 'error' );
		}
	}
}
