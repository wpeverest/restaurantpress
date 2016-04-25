<?php
/**
 * Admin View: Custom Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated restaurantpress-message">
	<a class="restaurantpress-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'rp-hide-notice', $notice ), 'restaurantpress_hide_notices_nonce', '_rp_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'restaurantpress' ); ?></a>
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>

