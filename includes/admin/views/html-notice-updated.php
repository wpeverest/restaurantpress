<?php
/**
 * Admin View: Notice - Updated
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated restaurantpress-message rp-connect restaurantpress-message--success">
	<a class="restaurantpress-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'rp-hide-notice', 'update', remove_query_arg( 'do_update_restaurantpress' ) ), 'restaurantpress_hide_notices_nonce', '_rp_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'restaurantpress' ); ?></a>

	<p><?php printf( __( 'RestaurantPress data update complete. <strong>Please, update your restaurant %scurrency symbol%s now.</strong> Thank you for updating to the latest version!', 'restaurantpress' ), '<a href="' . admin_url( 'admin.php?page=rp-settings' ) . '">', '</a>' ); ?></p>
</div>
