<?php
/**
 * Cache Helper Class
 *
 * @class    RP_Cache_Helper
 * @version  1.5.0
 * @package  RestaurantPress/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Cache_Helper Class.
 */
class RP_Cache_Helper {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_filter( 'nocache_headers', array( __CLASS__, 'set_nocache_constants' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
	}

	/**
	 * Get prefix for use with wp_cache_set. Allows all cache in a group to be invalidated at once.
	 *
	 * @param  string $group
	 * @return string
	 */
	public static function get_cache_prefix( $group ) {
		$prefix = wp_cache_get( 'rp_' . $group . '_cache_prefix', $group );

		if ( false === $prefix ) {
			$prefix = 1;
			wp_cache_set( 'rp_' . $group . '_cache_prefix', $prefix, $group );
		}

		return 'rp_cache_' . $prefix . '_';
	}

	/**
	 * Increment group cache prefix (invalidates cache).
	 *
	 * @param string $group
	 */
	public static function incr_cache_prefix( $group ) {
		wp_cache_incr( 'rp_' . $group . '_cache_prefix', 1, $group );
	}

	/**
	 * Set constants to prevent caching by some plugins.
	 *
	 * Hooked into nocache_headers filter but does not change headers.
	 *
	 * @param  array $value
	 * @return array
	 */
	public static function set_nocache_constants( $value ) {
		rp_maybe_define_constant( 'DONOTCACHEPAGE', true );
		rp_maybe_define_constant( 'DONOTCACHEOBJECT', true );
		rp_maybe_define_constant( 'DONOTCACHEDB', true );
		return $value;
	}

	/**
	 * W3 Total Cache notice.
	 */
	public static function notices() {
		if ( ! function_exists( 'w3tc_pgcache_flush' ) || ! function_exists( 'w3_instance' ) ) {
			return;
		}

		$config   = w3_instance( 'W3_Config' );
		$enabled  = $config->get_integer( 'dbcache.enabled' );
		$settings = array_map( 'trim', $config->get_array( 'dbcache.reject.sql' ) );

		if ( $enabled && ! in_array( '_rp_session_', $settings ) ) {
			?>
			<div class="error">
				<p><?php printf( __( 'In order for <strong>database caching</strong> to work with RestaurantPress you must add %1$s to the "Ignored Query Strings" option in <a href="%2$s">W3 Total Cache settings</a>.', 'restaurantpress' ), '<code>_rp_session_</code>', admin_url( 'admin.php?page=w3tc_dbcache' ) ); ?></p>
			</div>
			<?php
		}
	}
}

RP_Cache_Helper::init();
