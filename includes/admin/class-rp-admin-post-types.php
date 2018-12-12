<?php
/**
 * Post Types Admin
 *
 * @class    RP_Admin_Post_Types
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'RP_Admin_Post_Types', false ) ) {
	new RP_Admin_Post_Types();
	return;
}

/**
 * RP_Admin_Post_Types Class
 *
 * Handles the edit posts views and some functionality on the edit post screen for RP post types.
 */
class RP_Admin_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		include_once( dirname( __FILE__ ) . '/class-rp-admin-meta-boxes.php' );

		if ( ! class_exists( 'RP_Admin_List_Table', false ) ) {
			include_once( dirname( __FILE__ ) . '/list-tables/abstract-class-rp-admin-list-table.php' );
		}

		// Load correct list table classes for current screen.
		add_action( 'current_screen', array( $this, 'setup_screen' ) );
		add_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );

		// Admin notices.
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// Extra post data and screen elements.
		add_action( 'edit_form_top', array( $this, 'edit_form_top' ) );
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_action( 'current_screen', array( $this, 'edit_group_form_after_title' ) );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );
		add_action( 'post_submitbox_misc_actions', array( $this, 'food_data_visibility' ) );

		// Add a post display state for special RP pages.
		add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
	}

	/**
	 * Looks at the current screen and loads the correct list table handler.
	 *
	 * @since 1.6.0
	 */
	public function setup_screen() {
		$screen_id = false;

		if ( function_exists( 'get_current_screen' ) ) {
			$screen    = get_current_screen();
			$screen_id = isset( $screen, $screen->id ) ? $screen->id : '';
		}

		if ( ! empty( $_REQUEST['screen'] ) ) { // WPCS: input var ok.
			$screen_id = rp_clean( wp_unslash( $_REQUEST['screen'] ) ); // WPCS: input var ok, sanitization ok.
		}

		switch ( $screen_id ) {
			case 'edit-food_menu' :
				include_once( 'list-tables/class-rp-admin-list-table-foods.php' );
				break;
			case 'edit-food_group' :
				include_once( 'list-tables/class-rp-admin-list-table-groups.php' );
				break;
		}

		do_action( 'restaurantpress_admin_list_tables_setup_screen', $screen_id );
	}

	/**
	 * Change messages when a post type is updated.
	 *
	 * @param  array $messages Array of messages.
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_type_object, $post_ID;

		$preview_menu_item_link_html = $scheduled_menu_item_link_html = $view_menu_item_link_html = '';

		$viewable = is_post_type_viewable( $post_type_object );

		if ( $viewable ) {

			// Preview menu item link.
			$preview_menu_item_link_html = sprintf( ' <a target="_blank" href="%1$s">%2$s</a>',
				esc_url( get_preview_post_link( $post ) ),
				__( 'Preview menu item', 'restaurantpress' )
			);

			// Scheduled menu item preview link.
			$scheduled_menu_item_link_html = sprintf( ' <a target="_blank" href="%1$s">%2$s</a>',
				esc_url( get_permalink( $post_ID ) ),
				__( 'Preview menu item', 'restaurantpress' )
			);

			// View menu item link.
			$view_menu_item_link_html = sprintf( ' <a href="%1$s">%2$s</a>',
				esc_url( get_permalink( $post_ID ) ),
				__( 'View menu item', 'restaurantpress' )
			);
		}

		/* translators: Publish box date format, see https://secure.php.net/date */
		$scheduled_date = date_i18n( __( 'M j, Y @ H:i', 'restaurantpress' ), strtotime( $post->post_date ) );

		$messages['food_menu'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Menu Item updated.', 'restaurantpress' ) . $view_menu_item_link_html,
			2 => __( 'Custom field updated.', 'restaurantpress' ),
			3 => __( 'Custom field deleted.', 'restaurantpress' ),
			4 => __( 'Menu Item updated.', 'restaurantpress' ),
			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Menu Item restored to revision from %s', 'restaurantpress' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Menu Item published.', 'restaurantpress' ) . $view_menu_item_link_html,
			7 => __( 'Menu Item saved.', 'restaurantpress' ),
			8 => __( 'Menu Item submitted.', 'restaurantpress' ) . $preview_menu_item_link_html,
			9 => sprintf( __( 'Menu Item scheduled for: %s.', 'restaurantpress' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_menu_item_link_html,
			10 => __( 'Menu Item draft updated.', 'restaurantpress' ) . $preview_menu_item_link_html,
		);

		$messages['food_group'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Group updated.', 'restaurantpress' ),
			2 => __( 'Custom field updated.', 'restaurantpress' ),
			3 => __( 'Custom field deleted.', 'restaurantpress' ),
			4 => __( 'Group updated.', 'restaurantpress' ),
			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Group restored to revision from %s', 'restaurantpress' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Group updated.', 'restaurantpress' ),
			7 => __( 'Group saved.', 'restaurantpress' ),
			8 => __( 'Group submitted.', 'restaurantpress' ),
			9 => sprintf( __( 'Group scheduled for: %s.', 'restaurantpress' ), '<strong>' . $scheduled_date . '</strong>' ),
			10 => __( 'Group draft updated.', 'restaurantpress' ),
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 *
	 * @param  array $bulk_messages Array of messages.
	 * @param  array $bulk_counts Array of how many objects were updated.
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		$bulk_messages['food_menu'] = array(
			'updated'   => _n( '%s menu item updated.', '%s menu items updated.', $bulk_counts['updated'], 'restaurantpress' ),
			'locked'    => _n( '%s menu item not updated, somebody is editing it.', '%s menu items not updated, somebody is editing them.', $bulk_counts['locked'], 'restaurantpress' ),
			'deleted'   => _n( '%s menu item permanently deleted.', '%s menu items permanently deleted.', $bulk_counts['deleted'], 'restaurantpress' ),
			'trashed'   => _n( '%s menu item moved to the Trash.', '%s menu items moved to the Trash.', $bulk_counts['trashed'], 'restaurantpress' ),
			'untrashed' => _n( '%s menu item restored from the Trash.', '%s menu items restored from the Trash.', $bulk_counts['untrashed'], 'restaurantpress' ),
		);

		$bulk_messages['food_group'] = array(
			'updated'   => _n( '%s group updated.', '%s groups updated.', $bulk_counts['updated'], 'restaurantpress' ),
			'locked'    => _n( '%s group not updated, somebody is editing it.', '%s groups not updated, somebody is editing them.', $bulk_counts['locked'], 'restaurantpress' ),
			'deleted'   => _n( '%s group permanently deleted.', '%s groups permanently deleted.', $bulk_counts['deleted'], 'restaurantpress' ),
			'trashed'   => _n( '%s group moved to the Trash.', '%s groups moved to the Trash.', $bulk_counts['trashed'], 'restaurantpress' ),
			'untrashed' => _n( '%s group restored from the Trash.', '%s groups restored from the Trash.', $bulk_counts['untrashed'], 'restaurantpress' ),
		);

		return $bulk_messages;
	}

	/**
	 * Output extra data on post forms.
	 *
	 * @param WP_Post $post
	 */
	public function edit_form_top( $post ) {
		echo '<input type="hidden" id="original_post_title" name="original_post_title" value="' . esc_attr( $post->post_title ) . '" />';
	}

	/**
	 * Change title boxes in admin.
	 *
	 * @param string  $text Text to shown.
	 * @param WP_Post $post Current post object.
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'food_menu' :
				$text = __( 'Menu item name', 'restaurantpress' );
			break;
			case 'food_group':
				$text = __( 'Group name', 'restaurantpress' );
			break;
		}

		return $text;
	}

	/**
	 * Looks at the current screen and adds an action on group edit screen to print group description.
	 *
	 * @since 1.8.0
	 */
	public function edit_group_form_after_title() {
		$screen_id = false;

		if ( function_exists( 'get_current_screen' ) ) {
			$screen    = get_current_screen();
			$screen_id = isset( $screen, $screen->id ) ? $screen->id : '';
		}

		if ( 'food_group' == $screen_id ) {
			add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		}
	}

	/**
	 * Print group description textarea field.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function edit_form_after_title( $post ) {
		?>
		<textarea id="restaurantpress-group-description" name="excerpt" cols="5" rows="2" placeholder="<?php esc_attr_e( 'Description (optional)', 'restaurantpress' ); ?>"><?php echo $post->post_excerpt; // WPCS: XSS ok. ?></textarea>
		<?php
	}

	/**
	 * Hidden default Meta-Boxes.
	 *
	 * @param  array  $hidden Hidden boxes.
	 * @param  object $screen Current screen.
	 * @return array
	 */
	public function hidden_meta_boxes( $hidden, $screen ) {
		if ( 'food_menu' === $screen->post_type && 'post' === $screen->base ) {
			$hidden = array_merge( $hidden, array( 'postcustom' ) );
		}

		return $hidden;
	}

	/**
	 * Output food visibility options.
	 */
	public function food_data_visibility() {
		global $post, $thepostid, $food_object;

		if ( 'food_menu' !== $post->post_type ) {
			return;
		}

		$thepostid          = $post->ID;
		$food_object        = $thepostid ? rp_get_food( $thepostid ) : new RP_Food;
		$current_featured   = rp_bool_to_string( $food_object->get_featured() );
		?>
		<div class="misc-pub-section" id="featured-visibility">
			<?php _e( 'Featured:', 'restaurantpress' ); ?> <strong id="featured-visibility-display"><?php
				echo 'yes' === $current_featured ? __( 'Yes', 'restaurantpress' ) : __( 'No', 'restaurantpress' );
			?></strong>

			<a href="#featured-visibility" class="edit-featured-visibility hide-if-no-js"><?php esc_html_e( 'Edit', 'restaurantpress' ); ?></a>

			<div id="featured-visibility-select" class="hide-if-js">
				<input type="hidden" name="current_featured" id="current_featured" value="<?php echo esc_attr( $current_featured ); ?>" />
				<?php echo '<br /><input type="checkbox" name="_featured" id="_featured" ' . checked( $current_featured, 'yes', false ) . ' data-yes="' . esc_attr( 'Yes', 'restaurantpress' ) . '" data-no="' . esc_attr( 'No', 'restaurantpress' ) . '" /> <label for="_featured">' . esc_html__( 'This is a featured food', 'restaurantpress' ) . '</label><br />'; ?>
				<p>
					<a href="#featured-visibility" class="save-post-visibility hide-if-no-js button"><?php esc_html_e( 'OK', 'restaurantpress' ); ?></a>
					<a href="#featured-visibility" class="cancel-post-visibility hide-if-no-js"><?php esc_html_e( 'Cancel', 'restaurantpress' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Add a post display state for special RP pages in the page list table.
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 */
	public function add_display_post_states( $post_states, $post ) {
		if ( has_shortcode( $post->post_content, 'restaurantpress_menu' ) ) {
			$post_states['rp_page_for_group'] = __( 'Group Page', 'restaurantpress' );
		}

		return $post_states;
	}
}

new RP_Admin_Post_Types();
