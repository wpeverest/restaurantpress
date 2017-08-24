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
 * Converts a string (e.g. yes or no) to a bool.
 * @since  1.4.0
 * @param  string $string
 * @return bool
 */
function rp_string_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Converts a bool to a string.
 * @since  1.4.0
 * @param  bool $bool
 * @return string yes or no
 */
function rp_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = rp_string_to_bool( $bool );
	}
	return true === $bool ? 'yes' : 'no';
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 * @param string|array $var
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
 * Trim trailing zeros off prices.
 *
 * @param  mixed $price
 * @return string
 */
function rp_trim_zeros( $price ) {
	return preg_replace( '/' . preg_quote( rp_get_price_decimal_separator(), '/' ) . '0++$/', '', $price );
}

/**
 * Run rp_clean over posted textarea but maintain line breaks.
 * @since  1.4.0
 * @param  string $var
 * @return string
 */
function rp_sanitize_textarea( $var ) {
	return implode( "\n", array_map( 'rp_clean', explode( "\n", $var ) ) );
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since 1.0.0 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 * @param string $var
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
 * @since  1.4.0
 * @return string
 */
function rp_get_price_thousand_separator() {
	$separator = apply_filters( 'rp_get_price_thousand_separator', get_option( 'restaurantpress_price_thousand_sep' ) );
	return stripslashes( $separator );
}

/**
 * Return the decimal separator for prices.
 * @since  1.4.0
 * @return string
 */
function rp_get_price_decimal_separator() {
	$separator = apply_filters( 'rp_get_price_decimal_separator', get_option( 'restaurantpress_price_decimal_sep' ) );
	return $separator ? stripslashes( $separator ) : '.';
}

/**
 * Return the number of decimals after the decimal point.
 * @since  1.4
 * @return int
 */
function rp_get_price_decimals() {
	$decimals = apply_filters( 'rp_get_price_decimals', get_option( 'restaurantpress_price_num_decimals', 2 ) );
	return absint( $decimals );
}

/**
 * Format the price with a currency symbol.
 *
 * @param float $price
 * @param array $args (default: array())
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

	$negative = $price < 0;
	$price    = apply_filters( 'raw_restaurantpress_price', floatval( $negative ? $price * -1 : $price ) );
	$price    = apply_filters( 'formatted_restaurantpress_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

	if ( apply_filters( 'restaurantpress_price_trim_zeros', false ) && $decimals > 0 ) {
		$price = rp_trim_zeros( $price );
	}

	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<span class="restaurantpress-Price-currencySymbol">' . get_restaurantpress_currency_symbol( $currency ) . '</span>', $price );
	$return          = '<span class="restaurantpress-Price-amount amount">' . $formatted_price . '</span>';

	return apply_filters( 'rp_price', $return, $price, $args );
}

/**
 * Format a sale price for display.
 *
 * @param  string $regular_price
 * @param  string $sale_price
 * @return string
 */
function rp_format_sale_price( $regular_price, $sale_price ) {
	$price = '<del>' . ( is_numeric( $regular_price ) ? rp_price( $regular_price ) : $regular_price ) . '</del> <ins>' . ( is_numeric( $sale_price ) ? rp_price( $sale_price ) : $sale_price ) . '</ins>';
	return apply_filters( 'restaurantpress_format_sale_price', $price, $regular_price, $sale_price );
}

/**
 * Format a price with RP Currency Locale settings.
 * @param  string $value
 * @return string
 */
function rp_format_localized_price( $value ) {
	return apply_filters( 'restaurantpress_format_localized_price', str_replace( '.', rp_get_price_decimal_separator(), strval( $value ) ), $value );
}
