<?php
/**
 * Admin View: Notice - Updating
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated restaurantpress-message rp-connect">
	<p><strong><?php _e( 'RestaurantPress data update', 'restaurantpress' ); ?></strong> &#8211; <?php _e( 'Your database is being updated in the background.', 'restaurantpress' ); ?> <a href="<?php echo esc_url( add_query_arg( 'force_update_restaurantpress', 'true', admin_url( 'admin.php?page=rp-settings' ) ) ); ?>"><?php _e( 'Taking a while? Click here to run it now.', 'restaurantpress' ); ?></a></p>
</div>
