<?php
/**
 * Admin View: Notice - Theme Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated restaurantpress-message rp-connect">
	<a class="restaurantpress-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'rp-hide-notice', 'theme_support' ), 'restaurantpress_hide_notices_nonce', '_rp_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'restaurantpress' ); ?></a>

	<p><?php printf( __( '<strong>Your theme does not declare RestaurantPress support</strong> &#8211; Please read our <a href="%1$s" target="_blank">integration</a> guide or check out our <a href="%2$s" target="_blank">FoodHunt</a> theme which is totally free to download and designed specifically for use with RestaurantPress.', 'restaurantpress' ), esc_url( apply_filters( 'restaurantpress_docs_url', 'https://docs.wpeverest.com/docs/restaurantpress/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ), esc_url( admin_url( 'theme-install.php?theme=foodhunt' ) ) ); ?></p>
	<p class="submit">
		<a href="https://themegrill.com/themes/foodhunt/?utm_source=wpadmin&amp;utm_medium=notice&amp;utm_campaign=FoodHunt" class="button-primary" target="_blank"><?php _e( 'Read more about FoodHunt', 'restaurantpress' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'restaurantpress_docs_url', 'https://docs.wpeverest.com/docs/restaurantpress/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ); ?>" class="button-secondary" target="_blank"><?php _e( 'Theme integration guide', 'restaurantpress' ); ?></a>
	</p>
</div>
