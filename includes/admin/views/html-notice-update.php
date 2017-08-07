<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated restaurantpress-message rp-connect">
	<p><strong><?php _e( 'RestaurantPress data update', 'restaurantpress' ); ?></strong> &#8211; <?php _e( 'We need to update your site\'s database to the latest version.', 'restaurantpress' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_restaurantpress', 'true', admin_url( 'admin.php?page=rp-settings' ) ) ); ?>" class="rp-update-now button-primary"><?php _e( 'Run the updater', 'restaurantpress' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery( '.rp-update-now' ).click( 'click', function() {
		return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'restaurantpress' ) ); ?>' ); // jshint ignore:line
	});
</script>
