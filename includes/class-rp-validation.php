<?php
/**
 * Contains Validation functions
 *
 * @class    RP_Validation
 * @version  1.5.0
 * @package  RestaurantPress/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Validation Class.
 */
class RP_Validation {

	/**
	 * Validates an email using WordPress native is_email function.
	 *
	 * @param  string $email Email address to validate.
	 * @return bool
	 */
	public static function is_email( $email ) {
		return is_email( $email );
	}

	/**
	 * Validates a phone number using a regular expression.
	 *
	 * @param  string $phone Phone number to validate.
	 * @return bool
	 */
	public static function is_phone( $phone ) {
		if ( 0 < strlen( trim( preg_replace( '/[\s\#0-9_\-\+\/\(\)]/', '', $phone ) ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Format the phone number.
	 *
	 * @param  mixed $tel Phone number to format.
	 * @return string
	 */
	public static function format_phone( $tel ) {
		return rp_format_phone_number( $tel );
	}
}
