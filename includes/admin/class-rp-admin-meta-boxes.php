<?php
/**
 * RestaurantPress Meta Boxes
 *
 * Sets up the write panels used by custom post types.
 *
 * @class    RP_Admin_Meta_Boxes
 * @version  1.0.0
 * @package  RestaurantPress/Admin/Meta Boxes
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Admin_Meta_Boxes Class
 */
class RP_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors  = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Food Meta Boxes
		add_action( 'restaurantpress_process_food_menu_meta', 'RP_Meta_Box_Food_Data::save', 10, 2 );
		add_action( 'restaurantpress_process_food_menu_meta', 'RP_Meta_Box_Food_Images::save', 20, 2 );

		// Save Group Meta Boxes
		add_action( 'restaurantpress_process_food_group_meta', 'RP_Meta_Box_Group_Data::save', 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load).
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'restaurantpress_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = array_filter( (array) get_option( 'restaurantpress_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="restaurantpress_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'restaurantpress_meta_box_errors' );
		}
	}

	/**
	 * Add RP Meta boxes.
	 */
	public function add_meta_boxes() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Foods
		add_meta_box( 'postexcerpt', __( 'Food short description', 'restaurantpress' ), 'RP_Meta_Box_Food_Short_Description::output', 'food_menu', 'normal' );
		add_meta_box( 'restaurantpress-food-data', __( 'Food data', 'restaurantpress' ), 'RP_Meta_Box_Food_Data::output', 'food_menu', 'normal', 'high' );
		add_meta_box( 'restaurantpress-food-images', __( 'Food gallery', 'restaurantpress' ), 'RP_Meta_Box_Food_Images::output', 'food_menu', 'side', 'low' );

		// Groups.
		add_meta_box( 'restaurantpress-group-data', __( 'Group data', 'restaurantpress' ), 'RP_Meta_Box_Group_Data::output', 'food_group', 'normal', 'high' );
	}

	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'postexcerpt', 'food_menu', 'normal' );
		remove_meta_box( 'commentsdiv', 'food_menu', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'food_menu', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'food_menu', 'side' );
		remove_meta_box( 'commentstatusdiv', 'food_group', 'normal' );
		remove_meta_box( 'slugdiv', 'food_group', 'normal' );
	}

	/**
	 * Rename core meta boxes.
	 */
	public function rename_meta_boxes() {
		global $post;

		// Comments/Reviews
		if ( isset( $post ) && ( 'publish' == $post->post_status || 'private' == $post->post_status ) && post_type_supports( 'food_menu', 'comments' ) ) {
			remove_meta_box( 'commentsdiv', 'food_menu', 'normal' );
		}
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 * @param int $post_id
	 * @param object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Don't save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['restaurantpress_meta_nonce'] ) || ! wp_verify_nonce( $_POST['restaurantpress_meta_nonce'], 'restaurantpress_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		self::$saved_meta_boxes = true;

		// Check the post type
		if ( in_array( $post->post_type, array( 'food_menu', 'food_group' ) ) ) {
			do_action( 'restaurantpress_process_' . $post->post_type . '_meta', $post_id, $post );
		}
	}
}

new RP_Admin_Meta_Boxes();
