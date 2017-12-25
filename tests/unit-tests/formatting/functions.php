<?php
/**
 * Formatting functions
 *
 * @package RestaurantPress\Tests\Formatting
 */

/**
 * Class Functions.
 *
 * @since 1.7
 */
class RP_Tests_Formatting_Functions extends RP_Unit_Test_Case {

	public function tearDown() {
		update_option( 'restaurantpress_price_num_decimals', '2' );
		update_option( 'restaurantpress_price_decimal_sep', '.' );
		update_option( 'restaurantpress_price_thousand_sep', ',' );
	}

	/**
	 * Test rp_string_to_bool().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_string_to_bool() {
		$this->assertTrue( rp_string_to_bool( 1 ) );
		$this->assertTrue( rp_string_to_bool( 'yes' ) );
		$this->assertTrue( rp_string_to_bool( 'true' ) );
		$this->assertFalse( rp_string_to_bool( 0 ) );
		$this->assertFalse( rp_string_to_bool( 'no' ) );
		$this->assertFalse( rp_string_to_bool( 'false' ) );
	}

	/**
	 * Test rp_bool_to_string().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_bool_to_string() {
		$this->assertEquals( array( 'yes', 'no' ), array( rp_bool_to_string( true ), rp_bool_to_string( false )	) );
	}

	/**
	 * Test rp_sanitize_permalink().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_sanitize_permalink() {
		$this->assertEquals( 'foo.com/bar', rp_sanitize_permalink( 'http://foo.com/bar' ) );
	}

	/**
	 * Test rp_trim_zeros().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_trim_zeros() {
		$this->assertEquals( '$1', rp_trim_zeros( '$1.00' ) );
		$this->assertEquals( '$1.10', rp_trim_zeros( '$1.10' ) );
	}

	/**
	 * Test rp_format_decimal().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_format_decimal() {
		// Given string.
		$this->assertEquals( '9.99', rp_format_decimal( '9.99' ) );

		// Float.
		$this->assertEquals( '9.99', rp_format_decimal( 9.99 ) );

		// DP false, no rounding.
		$this->assertEquals( '9.9999', rp_format_decimal( 9.9999 ) );

		// DP when empty string uses default as 2.
		$this->assertEquals( '9.99', rp_format_decimal( 9.9911, '' ) );

		// DP use default as 2 and round.
		$this->assertEquals( '10.00', rp_format_decimal( 9.9999, '' ) );

		// DP use custom.
		$this->assertEquals( '9.991', rp_format_decimal( 9.9912, 3 ) );

		// Trim zeros.
		$this->assertEquals( '9', rp_format_decimal( 9.00, false, true ) );

		// Trim zeros and round.
		$this->assertEquals( '10', rp_format_decimal( 9.9999, '', true ) );

		// Given string with thousands in german format.
		update_option( 'restaurantpress_price_decimal_sep', ',' );
		update_option( 'restaurantpress_price_thousand_sep', '.' );

		// Given string.
		$this->assertEquals( '9.99', rp_format_decimal( '9.99' ) );

		// Float.
		$this->assertEquals( '9.99', rp_format_decimal( 9.99 ) );

		// DP false, no rounding.
		$this->assertEquals( '9.9999', rp_format_decimal( 9.9999 ) );

		// DP when empty string uses default as 2.
		$this->assertEquals( '9.99', rp_format_decimal( 9.9911, '' ) );

		// DP use default as 2 and round.
		$this->assertEquals( '10.00', rp_format_decimal( 9.9999, '' ) );

		// DP use custom.
		$this->assertEquals( '9.991', rp_format_decimal( 9.9912, 3 ) );

		// Trim zeros.
		$this->assertEquals( '9', rp_format_decimal( 9.00, false, true ) );

		// Trim zeros and round.
		$this->assertEquals( '10', rp_format_decimal( 9.9999, '', true ) );

		update_option( 'restaurantpress_price_num_decimals', '8' );

		// Floats.
		$this->assertEquals( '0.00001', rp_format_decimal( 0.00001 ) );
		$this->assertEquals( '0.22222222', rp_format_decimal( 0.22222222 ) );

		update_option( 'restaurantpress_price_num_decimals', '2' );
		update_option( 'restaurantpress_price_decimal_sep', '.' );
		update_option( 'restaurantpress_price_thousand_sep', ',' );
	}

	/**
	 * Test rp_format_localized_price().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_format_localized_price() {
		// Save default.
		$decimal_sep = get_option( 'restaurantpress_price_decimal_sep' );
		update_option( 'restaurantpress_price_decimal_sep', ',' );

		$this->assertEquals( '1,17', rp_format_localized_price( '1.17' ) );

		// Restore default.
		update_option( 'restaurantpress_price_decimal_sep', $decimal_sep );
	}

	/**
	 * Test rp_clean().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_clean() {
		$this->assertEquals( 'cleaned', rp_clean( '<script>alert();</script>cleaned' ) );
		$this->assertEquals( array( 'cleaned', 'foo' ), rp_clean( array( '<script>alert();</script>cleaned', 'foo' ) ) );
	}

	/**
	 * Test rp_sanitize_textarea().
	 *
	 * @since 3.3.0
	 */
	public function test_rp_sanitize_textarea() {
		$this->assertEquals( "foo\ncleaned\nbar", rp_sanitize_textarea( "foo\n<script>alert();</script>cleaned\nbar" ) );
	}

	/**
	 * Test rp_sanitize_tooltip().
	 *
	 * Note this is a basic type test as WP core already has coverage for wp_kses().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_sanitize_tooltip() {
		$this->assertEquals( 'alert();cleaned&lt;p&gt;foo&lt;/p&gt;&lt;span&gt;bar&lt;/span&gt;', rp_sanitize_tooltip( '<script>alert();</script>cleaned<p>foo</p><span>bar</span>' ) );
	}

	/**
	 * Test get_restaurantpress_price_format().
	 *
	 * @since 1.7.0
	 */
	public function test_get_restaurantpress_price_format() {
		// Save default.
		$currency_pos = get_option( 'restaurantpress_currency_pos' );

		// Default format (left).
		$this->assertEquals( '%1$s&#x200e;%2$s', get_restaurantpress_price_format() );

		// Right.
		update_option( 'restaurantpress_currency_pos', 'right' );
		$this->assertEquals( '%2$s%1$s&#x200f;', get_restaurantpress_price_format() );

		// Left space.
		update_option( 'restaurantpress_currency_pos', 'left_space' );
		$this->assertEquals( '%1$s&#x200e;&nbsp;%2$s', get_restaurantpress_price_format() );

		// Right space.
		update_option( 'restaurantpress_currency_pos', 'right_space' );
		$this->assertEquals( '%2$s&nbsp;%1$s&#x200f;', get_restaurantpress_price_format() );

		// Restore default.
		update_option( 'restaurantpress_currency_pos', $currency_pos );
	}

	/**
	 * Test rp_get_price_thousand_separator().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_get_price_thousand_separator() {
		$separator = get_option( 'restaurantpress_price_thousand_sep' );

		// Default value.
		$this->assertEquals( ',', rp_get_price_thousand_separator() );

		update_option( 'restaurantpress_price_thousand_sep', '.' );
		$this->assertEquals( '.', rp_get_price_thousand_separator() );

		update_option( 'restaurantpress_price_thousand_sep', '&lt;.&gt;' );
		$this->assertEquals( '&lt;.&gt;', rp_get_price_thousand_separator() );

		update_option( 'restaurantpress_price_thousand_sep', $separator );
	}

	/**
	 * Test rp_get_price_decimal_separator().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_get_price_decimal_separator() {
		$separator = get_option( 'restaurantpress_price_decimal_sep' );

		// Default value.
		$this->assertEquals( '.', rp_get_price_decimal_separator() );

		update_option( 'restaurantpress_price_decimal_sep', ',' );
		$this->assertEquals( ',', rp_get_price_decimal_separator() );

		update_option( 'restaurantpress_price_decimal_sep', '&lt;.&gt;' );
		$this->assertEquals( '&lt;.&gt;', rp_get_price_decimal_separator() );

		update_option( 'restaurantpress_price_decimal_sep', $separator );
	}

	/**
	 * Test rp_get_price_decimals().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_get_price_decimals() {
		$decimals = get_option( 'restaurantpress_price_num_decimals' );

		// Default value.
		$this->assertEquals( 2, rp_get_price_decimals() );

		update_option( 'restaurantpress_price_num_decimals', '1' );
		$this->assertEquals( 1, rp_get_price_decimals() );

		update_option( 'restaurantpress_price_num_decimals', '-2' );
		$this->assertEquals( 2, rp_get_price_decimals() );

		update_option( 'restaurantpress_price_num_decimals', '2.50' );
		$this->assertEquals( 2, rp_get_price_decimals() );

		update_option( 'restaurantpress_price_num_decimals', $decimals );
	}

	/**
	 * Test rp_price().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_price() {
		// Common prices.
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;1.00</span>', rp_price( 1 ) );
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;1.10</span>', rp_price( 1.1 ) );
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;1.17</span>', rp_price( 1.17 ) );
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;1,111.17</span>', rp_price( 1111.17 ) );
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;0.00</span>', rp_price( 0 ) );

		// Different currency.
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&#36;</span>&#x200e;1,111.17</span>', rp_price( 1111.17, array( 'currency' => 'USD' ) ) );

		// Negative price.
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount">-<span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;1.17</span>', rp_price( -1.17 ) );

		// Bogus prices.
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;0.00</span>', rp_price( null ) );
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;0.00</span>', rp_price( 'Q' ) );
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;0.00</span>', rp_price( 'ಠ_ಠ' ) );

		// Trim zeros.
		add_filter( 'restaurantpress_price_trim_zeros', '__return_true' );
		$this->assertEquals( '<span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;1</span>', rp_price( 1.00 ) );
		remove_filter( 'restaurantpress_price_trim_zeros', '__return_true' );
	}

	/**
	 * Test rp_date_format().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_date_format() {
		$this->assertEquals( get_option( 'date_format' ), rp_date_format() );
	}

	/**
	 * Test rp_time_format().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_time_format() {
		$this->assertEquals( get_option( 'time_format' ), rp_time_format() );
	}

	/**
	 * Test rp_format_phone_number().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_format_phone_number() {
		$this->assertEquals( '1-610-385-0000', rp_format_phone_number( '1.610.385.0000' ) );
	}

	/**
	 * Test rp_trim_string().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_trim_string() {
		$this->assertEquals( 'string', rp_trim_string( 'string' ) );
		$this->assertEquals( 's...',   rp_trim_string( 'string', 4 ) );
		$this->assertEquals( 'st.',    rp_trim_string( 'string', 3, '.' ) );
		$this->assertEquals( 'string¥', rp_trim_string( 'string¥', 7, '' ) );
	}

	/**
	 * Test rp_format_content().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_format_content() {
		$this->assertEquals( "<p>foo</p>\n", rp_format_content( 'foo' ) );
	}

	/**
	 * Test rp_format_sale_price().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_format_sale_price() {
		$this->assertEquals( '<del><span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;10.00</span></del> <ins><span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">&pound;</span>&#x200e;5.00</span></ins>', rp_format_sale_price( '10', '5' ) );
	}

	/**
	 * Test rp_do_oembeds().
	 *
	 * @since 1.7.0
	 */
	public function test_rp_do_oembeds() {
		// In this case should only return the URL back, since oEmbed will run other actions on frontend.
		$this->assertEquals( "<iframe width='500' height='281' src='https://videopress.com/embed/9sRCUigm?hd=0' frameborder='0' allowfullscreen></iframe><script src='https://v0.wordpress.com/js/next/videopress-iframe.js?m=1435166243'></script>", rp_do_oembeds( 'https://wordpress.tv/2015/10/19/mike-jolley-user-onboarding-for-wordpress-plugins/' ) );
	}
}
