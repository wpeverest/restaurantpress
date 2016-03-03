<?php
/**
 * Admin View: Notice - Theme Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated restaurantpress-message rp-connect">
	<p><?php printf( __( '<strong>Your theme does not declare RestaurantPress support</strong> &#8211; Please read our %sintegration%s guide or check out our %sFoodHunt%s theme which is totally free to download and designed specifically for use with RestaurantPress.', 'restaurantpress' ), '<a href="' . esc_url( apply_filters( 'restaurantpress_docs_url', 'http://themegrill.com/docs/restaurantpress/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ) . '">', '</a>', '<a href="' . esc_url( 'http://themegrill.com/themes/foodhunt/' ) . '">', '</a>' ); ?></p>
	<p class="submit">
		<a href="http://themegrill.com/themes/foodhunt/?utm_source=wpadmin&amp;utm_medium=notice&amp;utm_campaign=FoodHunt" class="button-primary" target="_blank"><?php _e( 'Read More About FoodHunt', 'restaurantpress' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'axiscomposer_docs_url', 'http://themegrill.com/docs/restaurantpress/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ); ?>" class="button-secondary" target="_blank"><?php _e( 'Theme Integration Guide', 'restaurantpress' ); ?></a>
		<a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'rp-hide-notice', 'theme_support' ), 'restaurantpress_hide_notices_nonce', '_rp_notice_nonce' ) ); ?>"><?php _e( 'Hide This Notice', 'restaurantpress' ); ?></a>
	</p>
</div>
