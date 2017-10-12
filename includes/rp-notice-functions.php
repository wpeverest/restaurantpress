<?php
/**
 * RestaurantPress Message Functions
 *
 * Functions for error/message handling and display.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Functions
 * @version  1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add and store a notice.
 *
 * @since 2.1
 * @param string $message The text to display in the notice.
 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
 */
function rp_add_notice( $message, $notice_type = 'success' ) {
	if ( ! did_action( 'restaurantpress_init' ) ) {
		rp_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before restaurantpress_init.', 'restaurantpress' ), '1.5' );
		return;
	}

	echo '<pre>' . print_r( $message, true ) . '</pre>';
}
