<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/emails/email-styles.php.
 *
 * HOWEVER, on occasion RestaurantPress will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.wpeverest.com/docs/restaurantpress/template-structure/
 * @author  WPEverest
 * @package RestaurantPress/Templates/Emails
 * @version 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load colors.
$bg              = get_option( 'restaurantpress_email_background_color' );
$body            = get_option( 'restaurantpress_email_body_background_color' );
$base            = get_option( 'restaurantpress_email_base_color' );
$base_text       = rp_light_or_dark( $base, '#202020', '#ffffff' );
$text            = get_option( 'restaurantpress_email_text_color' );

$bg_darker_10    = rp_hex_darker( $bg, 10 );
$body_darker_10  = rp_hex_darker( $body, 10 );
$base_lighter_20 = rp_hex_lighter( $base, 20 );
$base_lighter_40 = rp_hex_lighter( $base, 40 );
$text_lighter_20 = rp_hex_lighter( $text, 20 );

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
?>
#wrapper {
	background-color: <?php echo esc_attr( $bg ); ?>;
	margin: 0;
	padding: 70px 0 70px 0;
	-webkit-text-size-adjust: none !important;
	width: 100%;
}

#template_container {
	background-color: <?php echo esc_attr( $body ); ?>;
	border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>;
	border-radius: 3px !important;
}

#template_header {
	background-color: <?php echo esc_attr( $base ); ?>;
	color: <?php echo esc_attr( $base_text ); ?>;
	border-bottom: 0;
	font-weight: bold;
	line-height: 100%;
	vertical-align: middle;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
}

#template_header h1,
#template_header h1 a {
	color: <?php echo esc_attr( $base_text ); ?>;
}

#template_footer {
    background: <?php echo esc_attr( $base ); ?>;
}

#template_footer td {
	padding: 0;
}

#template_footer #credit {
	border: 0;
	color: <?php echo esc_attr( $base_text ); ?>;
	font-family: Arial;
	font-size:12px;
	line-height:125%;
	text-align:center;
	padding: 10px;
}

#body_content {
	background-color: <?php echo esc_attr( $body ); ?>;
}

#body_content table td {
	padding: 48px 48px 0;
}

#body_content table td td {
	padding: 12px;
}

#body_content table td th {
	padding: 12px;
}

#body_content p {
	margin: 0 0 16px;
}

#body_content_inner {
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 14px;
	line-height: 150%;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

#body_content_inner p {
    line-height: 1.7em;
}

#body_content_inner h2 {
    font-size: 14px;
}

#body_content_inner h2 .link {
	font-weight: bold;
}

#body_content_inner #addresses h2,
#body_content_inner #addresses td {
    text-align: center !important;
}

#body_content_inner #addresses h2 {
	text-align: center;
	border-left: 3px solid;
	display: inline-block;
	border-right: 3px solid;
	padding: 0 10px;
	position: relative;
}

#body_content_inner tr td {
	border-left: none !important;
	border-right: none !important;
}

#body_content_inner th.td,
#body_content_inner tfoot .td {
	border: none !important;
}

.td {
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
}

.address {
	padding: 12px 12px 0;
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
}

.text {
	color: <?php echo esc_attr( $text ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
}

.link {
	color: <?php echo esc_attr( $base ); ?>;
}

#header_wrapper {
	padding: 24px 36px;
	display: block;
}

#header_wrapper h1 {
	font-weight: 600;
	text-align: center;
}

h1 {
	color: <?php echo esc_attr( $base ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 24px;
	font-weight: 300;
	line-height: 150%;
	margin: 0;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>;
	-webkit-font-smoothing: antialiased;
}

h2 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 18px;
	font-weight: bold;
	line-height: 130%;
	margin: 0 0 18px;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

h3 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 16px;
	font-weight: bold;
	line-height: 130%;
	margin: 16px 0 8px;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

a {
	color: <?php echo esc_attr( $base_text ); ?>;
	font-weight: normal;
	text-decoration: underline;
}

img {
	border: none;
	display: inline;
	font-size: 14px;
	font-weight: bold;
	height: auto;
	line-height: 100%;
	outline: none;
	text-decoration: none;
	text-transform: capitalize;
}
<?php
