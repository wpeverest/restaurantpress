<?php
/**
 * Installation related functions and actions.
 *
 * @class    RP_Install
 * @version  1.0.0
 * @package  RestaurantPress/Classes
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Install Class
 */
class RP_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'in_plugin_update_message-restaurantpress/restaurantpress.php', array( __CLASS__, 'in_plugin_update_message' ) );
		add_filter( 'plugin_action_links_' . RP_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Install RP
	 */
	public static function install() {
		self::create_tables();
		self::create_roles();

		// Register post types
		RP_Post_Types::register_post_types();
		RP_Post_Types::register_taxonomies();
	}

	/**
	 * Set up the database table which the plugin need to function.
	 *
	 * Tables:
	 *    restaurantpress_termmeta - Term meta table - sadly WordPress does not have termmeta so we need our own
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/**
		 * Updating with DBDELTA.
		 */
		dbDelta( self::get_schema() );
	}

	/**
	 * Get Table schema.
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$tables = '';

		// Term meta is only needed for old installs.
		if ( get_option( 'db_version' ) < 34370 || ! function_exists( 'get_term_meta' ) ) {
			$tables .= "
CREATE TABLE {$wpdb->prefix}restaurantpress_termmeta (
  meta_id bigint(20) NOT NULL auto_increment,
  restaurantpress_term_id bigint(20) NOT NULL,
  meta_key varchar(255) NULL,
  meta_value longtext NULL,
  PRIMARY KEY  (meta_id),
  KEY restaurantpress_term_id (restaurantpress_term_id),
  KEY meta_key (meta_key)
) $collate;
			";
		}

		return $tables;
	}

	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Get capabilities for RestaurantPress.
	 * @return array
	 */
	 private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_restaurantpress'
		);

		$capability_types = array( 'food_menu', 'food_group' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}

	/**
	 * restaurantpress_remove_roles function.
	 */
	public static function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 */
	public static function in_plugin_update_message( $args ) {
		$transient_name = 'rp_upgrade_notice_' . $args['Version'];

		if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {
			$response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/restaurantpress/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$upgrade_notice = self::parse_update_notice( $response['body'] );
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		echo wp_kses_post( $upgrade_notice );
	}

	/**
	 * Parse update notice from readme file
	 * @param  string $content
	 * @return string
	 */
	private static function parse_update_notice( $content ) {
		// Output Upgrade Notice
		$matches        = null;
		$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( RP_VERSION ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );

			if ( version_compare( RP_VERSION, $version, '<' ) ) {

				$upgrade_notice .= '<div class="rp_plugin_upgrade_notice">';

				foreach ( $notices as $index => $line ) {
					$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) );
				}

				$upgrade_notice .= '</div> ';
			}
		}

		return wp_kses_post( $upgrade_notice );
	}

	/**
	 * Show action links on the plugin screen.
	 * @param  mixed $links Plugin Action links
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		return $links;
	}

	/**
	 * Show row meta on the plugin screen.
	 * @param  mixed $links Plugin Row Meta
	 * @param  mixed $file  Plugin Base file
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( $file == RP_PLUGIN_BASENAME ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'restaurantpress_docs_url', 'http://themegrill.com/docs/restaurantpress/' ) ) . '" title="' . esc_attr( __( 'View RestaurantPress Documentation', 'restaurantpress' ) ) . '">' . __( 'Docs', 'restaurantpress' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'restaurantpress_support_url', 'http://themegrill.com/support-forum/' ) ) . '" title="' . esc_attr( __( 'Free Support Forum', 'restaurantpress' ) ) . '">' . __( 'Free Support', 'restaurantpress' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}

RP_Install::init();
