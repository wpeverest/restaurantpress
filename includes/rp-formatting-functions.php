<?php
/**
 * RestaurantPress Formatting
 *
 * Functions for formatting data.
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
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since  1.4.0
 * @param  string $string String to convert.
 * @return bool
 */
function rp_string_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since  1.4.0
 * @param  bool $bool String to convert.
 * @return string
 */
function rp_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = rp_string_to_bool( $bool );
	}
	return true === $bool ? 'yes' : 'no';
}

/**
 * Trim trailing zeros off prices.
 *
 * @param  string|float|int $price Price.
 * @return string
 */
function rp_trim_zeros( $price ) {
	return preg_replace( '/' . preg_quote( rp_get_price_decimal_separator(), '/' ) . '0++$/', '', $price );
}

/**
 * Format decimal numbers ready for DB storage.
 *
 * Sanitize, remove decimals, and optionally round + trim off zeros.
 *
 * This function does not remove thousands - this should be done before passing a value to the function.
 *
 * @param  float|string $number     Expects either a float or a string with a decimal separator only (no thousands).
 * @param  mixed        $dp number  Number of decimal points to use, blank to use restaurantpress_price_num_decimals, or false to avoid all rounding.
 * @param  bool         $trim_zeros From end of string.
 * @return string
 */
function rp_format_decimal( $number, $dp = false, $trim_zeros = false ) {
	$locale   = localeconv();
	$decimals = array( rp_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'] );

	// Remove locale from string.
	if ( ! is_float( $number ) ) {
		$number = str_replace( $decimals, '.', $number );
		$number = preg_replace( '/[^0-9\.,-]/', '', rp_clean( $number ) );
	}

	if ( false !== $dp ) {
		$dp     = intval( '' == $dp ? rp_get_price_decimals() : $dp );
		$number = number_format( floatval( $number ), $dp, '.', '' );
	} elseif ( is_float( $number ) ) {
		// DP is false - don't use number format, just return a string in our format.
		$number = rp_clean( str_replace( $decimals, '.', strval( $number ) ) );
	}

	if ( $trim_zeros && strstr( $number, '.' ) ) {
		$number = rtrim( rtrim( $number, '0' ), '.' );
	}

	return $number;
}

/**
 * Format a price with RP Currency Locale settings.
 *
 * @param  string $value Price to localize.
 * @return string
 */
function rp_format_localized_price( $value ) {
	return apply_filters( 'restaurantpress_format_localized_price', str_replace( '.', rp_get_price_decimal_separator(), strval( $value ) ), $value );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param  string|array $var Data to sanitize.
 * @return string|array
 */
function rp_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'rp_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Run rp_clean over posted textarea but maintain line breaks.
 *
 * @since  1.4.0
 * @param  string $var Data to sanitize.
 * @return string
 */
function rp_sanitize_textarea( $var ) {
	return implode( "\n", array_map( 'rp_clean', explode( "\n", $var ) ) );
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since  1.0.0 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 * @param  string $var Data to sanitize.
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
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function get_restaurantpress_price_format() {
	$currency_pos = get_option( 'restaurantpress_currency_pos' );
	$format = '%1$s%2$s';

	switch ( $currency_pos ) {
		case 'left' :
			$format = '%1$s%2$s';
		break;
		case 'right' :
			$format = '%2$s%1$s';
		break;
		case 'left_space' :
			$format = '%1$s&nbsp;%2$s';
		break;
		case 'right_space' :
			$format = '%2$s&nbsp;%1$s';
		break;
	}

	return apply_filters( 'restaurantpress_price_format', $format, $currency_pos );
}

/**
 * Return the thousand separator for prices.
 *
 * @since  1.4.0
 * @return string
 */
function rp_get_price_thousand_separator() {
	return stripslashes( apply_filters( 'rp_get_price_thousand_separator', get_option( 'restaurantpress_price_thousand_sep' ) ) );
}

/**
 * Return the decimal separator for prices.
 *
 * @since  1.4.0
 * @return string
 */
function rp_get_price_decimal_separator() {
	$separator = apply_filters( 'rp_get_price_decimal_separator', get_option( 'restaurantpress_price_decimal_sep' ) );
	return $separator ? stripslashes( $separator ) : '.';
}

/**
 * Return the number of decimals after the decimal point.
 *
 * @since  1.4
 * @return int
 */
function rp_get_price_decimals() {
	return absint( apply_filters( 'rp_get_price_decimals', get_option( 'restaurantpress_price_num_decimals', 2 ) ) );
}

/**
 * Format the price with a currency symbol.
 *
 * @param float $price
 * @param array $args (default: array())
 * @return string
 */
/**
 * Format the price with a currency symbol.
 *
 * @param  float $price Raw price.
 * @param  array $args  Arguments to format a price {
 *     Array of arguments.
 *     Defaults to empty array.
 *
 *     @type string $currency           Currency code.
 *                                      Defaults to empty string (Use the result from get_restaurantpress_currency()).
 *     @type string $decimal_separator  Decimal separator.
 *                                      Defaults the result of rp_get_price_decimal_separator().
 *     @type string $thousand_separator Thousand separator.
 *                                      Defaults the result of rp_get_price_thousand_separator().
 *     @type string $decimals           Number of decimals.
 *                                      Defaults the result of rp_get_price_decimals().
 *     @type string $price_format       Price format depending on the currency position.
 *                                      Defaults the result of get_restaurantpress_price_format().
 * }
 * @return string
 */
function rp_price( $price, $args = array() ) {
	extract( apply_filters( 'rp_price_args', wp_parse_args( $args, array(
		'currency'           => '',
		'decimal_separator'  => rp_get_price_decimal_separator(),
		'thousand_separator' => rp_get_price_thousand_separator(),
		'decimals'           => rp_get_price_decimals(),
		'price_format'       => get_restaurantpress_price_format(),
	) ) ) );

	$unformatted_price = $price;
	$negative          = $price < 0;
	$price             = apply_filters( 'raw_restaurantpress_price', floatval( $negative ? $price * -1 : $price ) );
	$price             = apply_filters( 'formatted_restaurantpress_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

	if ( apply_filters( 'restaurantpress_price_trim_zeros', false ) && $decimals > 0 ) {
		$price = rp_trim_zeros( $price );
	}

	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<span class="restaurantpress-Price-currencySymbol">' . get_restaurantpress_currency_symbol( $currency ) . '</span>', $price );
	$return          = '<span class="restaurantpress-Price-amount amount">' . $formatted_price . '</span>';

	/**
	 * Filters the string of price markup.
	 *
	 * @param string $return 			Price HTML markup.
	 * @param string $price	            Formatted price.
	 * @param array  $args     			Pass on the args.
	 * @param float  $unformatted_price	Price as float to allow plugins custom formatting.
	 */
	return apply_filters( 'rp_price', $return, $price, $args, $unformatted_price );
}

/**
 * RestaurantPress Date Format - Allows to change date format for everything RestaurantPress.
 *
 * @return string
 */
function rp_date_format() {
	return apply_filters( 'restaurantpress_date_format', get_option( 'date_format' ) );
}

/**
 * RestaurantPress Time Format - Allows to change time format for everything RestaurantPress.
 *
 * @return string
 */
function rp_time_format() {
	return apply_filters( 'restaurantpress_time_format', get_option( 'time_format' ) );
}

/**
 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
 *
 * Based on wcs_strtotime_dark_knight() from WC Subscriptions by Prospress.
 *
 * @since  1.5.1
 *
 * @param string $time_string
 * @param int|null $from_timestamp
 *
 * @return int
 */
function rp_string_to_timestamp( $time_string, $from_timestamp = null ) {
	$original_timezone = date_default_timezone_get();

	// @codingStandardsIgnoreStart
	date_default_timezone_set( 'UTC' );

	if ( null === $from_timestamp ) {
		$next_timestamp = strtotime( $time_string );
	} else {
		$next_timestamp = strtotime( $time_string, $from_timestamp );
	}

	date_default_timezone_set( $original_timezone );
	// @codingStandardsIgnoreEnd

	return $next_timestamp;
}

/**
 * Convert a date string to a RP_DateTime.
 *
 * @since  1.5.1
 * @param  string $time_string
 * @return RP_DateTime
 */
function rp_string_to_datetime( $time_string ) {
	// Strings are defined in local WP timezone. Convert to UTC.
	if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $time_string, $date_bits ) ) {
		$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : rp_timezone_offset();
		$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
	} else {
		$timestamp = rp_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', rp_string_to_timestamp( $time_string ) ) ) );
	}
	$datetime  = new RP_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

	// Set local timezone or offset.
	if ( get_option( 'timezone_string' ) ) {
		$datetime->setTimezone( new DateTimeZone( rp_timezone_string() ) );
	} else {
		$datetime->set_utc_offset( rp_timezone_offset() );
	}

	return $datetime;
}

/**
 * RestaurantPress Timezone - helper to retrieve the timezone string for a site until.
 * a WP core method exists (see https://core.trac.wordpress.org/ticket/24730).
 *
 * Adapted from https://secure.php.net/manual/en/function.timezone-name-from-abbr.php#89155.
 *
 * @since  1.5.1
 * @return string PHP timezone string for the site
 */
function rp_timezone_string() {

	// if site timezone string exists, return it
	if ( $timezone = get_option( 'timezone_string' ) ) {
		return $timezone;
	}

	// get UTC offset, if it isn't set then return UTC
	if ( 0 === ( $utc_offset = intval( get_option( 'gmt_offset', 0 ) ) ) ) {
		return 'UTC';
	}

	// adjust UTC offset from hours to seconds
	$utc_offset *= 3600;

	// attempt to guess the timezone string from the UTC offset
	if ( $timezone = timezone_name_from_abbr( '', $utc_offset ) ) {
		return $timezone;
	}

	// last try, guess timezone string manually
	foreach ( timezone_abbreviations_list() as $abbr ) {
		foreach ( $abbr as $city ) {
			if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
				return $city['timezone_id'];
			}
		}
	}

	// fallback to UTC
	return 'UTC';
}

/**
 * Get timezone offset in seconds.
 *
 * @since  1.5.0
 * @return float
 */
function rp_timezone_offset() {
	if ( $timezone = get_option( 'timezone_string' ) ) {
		$timezone_object = new DateTimeZone( $timezone );
		return $timezone_object->getOffset( new DateTime( 'now' ) );
	} else {
		return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
	}
}

if ( ! function_exists( 'rp_rgb_from_hex' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colors.
	 *
	 * @param  mixed $color
	 * @return array
	 */
	function rp_rgb_from_hex( $color ) {
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb      = array();
		$rgb['R'] = hexdec( $color{0} . $color{1} );
		$rgb['G'] = hexdec( $color{2} . $color{3} );
		$rgb['B'] = hexdec( $color{4} . $color{5} );

		return $rgb;
	}
}

if ( ! function_exists( 'rp_hex_darker' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colors.
	 *
	 * @param  mixed $color
	 * @param  int   $factor (default: 30)
	 * @return string
	 */
	function rp_hex_darker( $color, $factor = 30 ) {
		$base  = rp_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = $v / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = "0" . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}
}

if ( ! function_exists( 'rp_hex_lighter' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colors.
	 *
	 * @param  mixed $color
	 * @param  int   $factor (default: 30)
	 * @return string
	 */
	function rp_hex_lighter( $color, $factor = 30 ) {
		$base  = rp_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = 255 - $v;
			$amount      = $amount / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v + $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = "0" . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}
}

if ( ! function_exists( 'rp_light_or_dark' ) ) {

	/**
	 * Detect if we should use a light or dark color on a background color.
	 *
	 * @param  mixed  $color
	 * @param  string $dark (default: '#000000')
	 * @param  string $light (default: '#FFFFFF')
	 * @return string
	 */
	function rp_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {

		$hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155 ? $dark : $light;
	}
}

if ( ! function_exists( 'rp_format_hex' ) ) {

	/**
	 * Format string as hex.
	 *
	 * @param  string $hex
	 * @return string
	 */
	function rp_format_hex( $hex ) {

		$hex = trim( str_replace( '#', '', $hex ) );

		if ( strlen( $hex ) == 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return $hex ? '#' . $hex : null;
	}
}

/**
 * Format phone numbers.
 *
 * @param  string $phone Phone number.
 * @return string
 */
function rp_format_phone_number( $phone ) {
	return str_replace( '.', '-', $phone );
}

/**
 * Trim a string and append a suffix.
 *
 * @param  string  $string String to trim.
 * @param  integer $chars  Amount of characters.
 *                         Defaults to 200.
 * @param  string  $suffix Suffix.
 *                         Defaults to '...'.
 * @return string
 */
function rp_trim_string( $string, $chars = 200, $suffix = '...' ) {
	if ( strlen( $string ) > $chars ) {
		if ( function_exists( 'mb_substr' ) ) {
			$string = mb_substr( $string, 0, ( $chars - mb_strlen( $suffix ) ) ) . $suffix;
		} else {
			$string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
		}
	}
	return $string;
}

/**
 * Format content to display shortcodes.
 *
 * @since  1.5.0
 * @param  string $raw_string Raw string.
 * @return string
 */
function rp_format_content( $raw_string ) {
	return apply_filters( 'restaurantpress_format_content', apply_filters( 'restaurantpress_short_description', $raw_string ), $raw_string );
}

/**
 * Format product short description.
 * Adds support for Jetpack Markdown.
 *
 * @codeCoverageIgnore
 * @since  1.5.0
 * @param  string $content Food short description.
 * @return string
 */
function rp_format_food_short_description( $content ) {
	// Add support for Jetpack Markdown.
	if ( class_exists( 'WPCom_Markdown' ) ) {
		$markdown = WPCom_Markdown::get_instance();

		return wpautop( $markdown->transform( $content, array(
			'unslash' => false,
		) ) );
	}

	return $content;
}

/**
 * Formats curency symbols when saved in settings.
 *
 * @codeCoverageIgnore
 * @param  string $value     Option value.
 * @param  array  $option    Option name.
 * @param  string $raw_value Raw value.
 * @return string
 */
function rp_format_option_price_separators( $value, $option, $raw_value ) {
	return wp_kses_post( $raw_value );
}
add_filter( 'restaurantpress_admin_settings_sanitize_option_restaurantpress_price_decimal_sep', 'rp_format_option_price_separators', 10, 3 );
add_filter( 'restaurantpress_admin_settings_sanitize_option_restaurantpress_price_thousand_sep', 'rp_format_option_price_separators', 10, 3 );

/**
 * Formats decimals when saved in settings.
 *
 * @codeCoverageIgnore
 * @param  string $value     Option value.
 * @param  array  $option    Option name.
 * @param  string $raw_value Raw value.
 * @return string
 */
function rp_format_option_price_num_decimals( $value, $option, $raw_value ) {
	return is_null( $raw_value ) ? 2 : absint( $raw_value );
}
add_filter( 'restaurantpress_admin_settings_sanitize_option_restaurantpress_price_num_decimals', 'rp_format_option_price_num_decimals', 10, 3 );

/**
 * Format a sale price for display.
 *
 * @param  string $regular_price Regular price.
 * @param  string $sale_price    Sale price.
 * @return string
 */
function rp_format_sale_price( $regular_price, $sale_price ) {
	$price = '<del>' . ( is_numeric( $regular_price ) ? rp_price( $regular_price ) : $regular_price ) . '</del> <ins>' . ( is_numeric( $sale_price ) ? rp_price( $sale_price ) : $sale_price ) . '</ins>';
	return apply_filters( 'restaurantpress_format_sale_price', $price, $regular_price, $sale_price );
}

/**
 * Format a date for output.
 *
 * @since  1.5.1
 * @param  RP_DateTime $date
 * @param  string $format Defaults to the rp_date_format function if not set.
 * @return string
 */
function rp_format_datetime( $date, $format = '' ) {
	if ( ! $format ) {
		$format = rp_date_format();
	}
	if ( ! is_a( $date, 'RP_DateTime' ) ) {
		return date_i18n( $format );
	}
	return $date->date_i18n( $format );
}

/**
 * Process oEmbeds.
 *
 * @since  1.5.0
 * @param  string $content Content.
 * @return string
 */
function rp_do_oembeds( $content ) {
	global $wp_embed;

	$content = $wp_embed->autoembed( $content );

	return $content;
}
