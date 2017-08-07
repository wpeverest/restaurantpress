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
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// WP List table columns. Defined here so they are always available for events such as inline editing.
		add_filter( 'manage_food_menu_posts_columns', array( $this, 'food_menu_columns' ) );
		add_filter( 'manage_food_group_posts_columns', array( $this, 'food_group_columns' ) );

		add_action( 'manage_food_menu_posts_custom_column', array( $this, 'render_food_menu_columns' ), 2 );
		add_action( 'manage_food_group_posts_custom_column', array( $this, 'render_food_group_columns' ), 2 );

		add_filter( 'manage_edit-food_menu_sortable_columns', array( $this, 'food_menu_sortable_columns' ) );
		add_filter( 'manage_edit-food_group_sortable_columns', array( $this, 'food_group_sortable_columns' ) );

		add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 2, 100 );

		// Edit post screens
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'media_view_strings', array( $this, 'change_insert_into_post' ) );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );

		// Meta-Box Class
		include_once( dirname( __FILE__ ) . '/class-rp-admin-meta-boxes.php' );

		// Disable DFW feature pointer
		add_action( 'admin_footer', array( $this, 'disable_dfw_feature_pointer' ) );

		// Disable post type view mode options
		add_filter( 'view_mode_post_types', array( $this, 'disable_view_mode_options' ) );
	}

	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['food_menu'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Menu Item updated. <a href="%s">View Menu Item</a>', 'restaurantpress' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'restaurantpress' ),
			3 => __( 'Custom field deleted.', 'restaurantpress' ),
			4 => __( 'Menu Item updated.', 'restaurantpress' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Menu Item restored to revision from %s', 'restaurantpress' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Menu Item published. <a href="%s">View Menu Item</a>', 'restaurantpress' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Menu Item saved.', 'restaurantpress' ),
			8 => sprintf( __( 'Menu Item submitted. <a target="_blank" href="%s">Preview Menu Item</a>', 'restaurantpress' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Menu Item scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Menu Item</a>', 'restaurantpress' ),
			  date_i18n( __( 'M j, Y @ G:i', 'restaurantpress' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Menu Item draft updated. <a target="_blank" href="%s">Preview Menu Item</a>', 'restaurantpress' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		$messages['food_group'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Group updated.', 'restaurantpress' ),
			2 => __( 'Custom field updated.', 'restaurantpress' ),
			3 => __( 'Custom field deleted.', 'restaurantpress' ),
			4 => __( 'Group updated.', 'restaurantpress' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Group restored to revision from %s', 'restaurantpress' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Group updated.', 'restaurantpress' ),
			7 => __( 'Group saved.', 'restaurantpress' ),
			8 => __( 'Group submitted.', 'restaurantpress' ),
			9 => sprintf( __( 'Group scheduled for: <strong>%1$s</strong>.', 'restaurantpress' ),
			  date_i18n( __( 'M j, Y @ G:i', 'restaurantpress' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Group draft updated.', 'restaurantpress' )
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 * @param  array $bulk_messages
	 * @param  array $bulk_counts
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
	 * Define custom columns for menus.
	 * @param  array $existing_columns
	 * @return array
	 */
	public function food_menu_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns                  = array();
		$columns['cb']            = '<input type="checkbox" />';
		$columns['thumb']         = '<span class="rp-image tips" data-tip="' . esc_attr__( 'Image', 'restaurantpress' ) . '">' . __( 'Image', 'restaurantpress' ) . '</span>';
		$columns['name']          = __( 'Name', 'restaurantpress' );
		$columns['price']         = __( 'Price', 'restaurantpress' );
		$columns['food_menu_cat'] = __( 'Categories', 'restaurantpress' );
		$columns['date']          = __( 'Date', 'restaurantpress' );

		return array_merge( $columns, $existing_columns );
	}

	/**
	 * Define custom columns for groups.
	 * @param  array $existing_columns
	 * @return array
	 */
	public function food_group_columns( $existing_columns ) {
		$columns                = array();
		$columns['cb']          = $existing_columns['cb'];
		$columns['name']        = __( 'Name', 'restaurantpress' );
		$columns['group_id']    = __( 'Group ID', 'restaurantpress' );
		$columns['description'] = __( 'Description', 'restaurantpress' );

		return $columns;
	}

	/**
	 * Returns the main menu image.
	 * @param  string $size (default: 'food_thumbnail')
	 * @return string
	 */
	private function get_image( $size = 'food_thumbnail', $attr = array() ) {
		global $post;

		if ( has_post_thumbnail( $post->ID ) ) {
			$image = get_the_post_thumbnail( $post->ID, $size, $attr );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $post->ID ) ) && has_post_thumbnail( $parent_id ) ) {
			$image = get_the_post_thumbnail( $parent_id, $size, $attr );
		} else {
			$image = rp_placeholder_img( $size );
		}

		return $image;
	}

	/**
	 * Output custom columns for menu.
	 * @param string $column
	 */
	public function render_food_menu_columns( $column ) {
		global $post, $the_food_menu;

		switch ( $column ) {
			case 'thumb' :
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . $this->get_image( 'thumbnail' ) . '</a>';
			break;
			case 'name' :
				$edit_link = get_edit_post_link( $post->ID );
				$title     = _draft_or_post_title();

				echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

				_post_states( $post );

				echo '</strong>';

				if ( $post->post_parent > 0 ) {
					echo '&nbsp;&nbsp;&larr; <a href="'. get_edit_post_link( $post->post_parent ) .'">'. get_the_title( $post->post_parent ) .'</a>';
				}

				// Excerpt view
				if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
					echo apply_filters( 'the_excerpt', $post->post_excerpt );
				}

				$this->_render_food_menu_row_actions( $post, $title );

				get_inline_data( $post );
			break;
			case 'price' :
				$the_price = get_post_meta( $post->ID, 'food_item_price', true );
				echo $the_price ? '<span class="amount">' . $the_price . '</span>' : '<span class="na">&ndash;</span>';
			break;
			case 'food_menu_cat' :
				if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					$termlist = array();
					foreach ( $terms as $term ) {
						$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=food_menu' ) . ' ">' . $term->name . '</a>';
					}

					echo implode( ', ', $termlist );
				}
			break;
			default:
				break;
		}
	}

	/**
	 * Render food_menu row actions for old version of WordPress.
	 * Since WordPress 4.3 we don't have to build the row actions.
	 *
	 * @param WP_Post $post
	 * @param string  $title
	 */
	private function _render_food_menu_row_actions( $post, $title ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.3-beta', '>=' ) ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $post->ID );

		// Get actions
		$actions = array();

		if ( $can_edit_post && 'trash' != $post->post_status ) {
			$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item', 'restaurantpress' ) ) . '">' . __( 'Edit', 'restaurantpress' ) . '</a>';
			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline', 'restaurantpress' ) ) . '">' . __( 'Quick&nbsp;Edit', 'restaurantpress' ) . '</a>';
		}
		if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
			if ( 'trash' == $post->post_status ) {
				$actions['untrash'] = '<a title="' . esc_attr( __( 'Restore this item from the Trash', 'restaurantpress' ) ) . '" href="' . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . '">' . __( 'Restore', 'restaurantpress' ) . '</a>';
			} elseif ( EMPTY_TRASH_DAYS ) {
				$actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this item to the Trash', 'restaurantpress' ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', 'restaurantpress' ) . '</a>';
			}

			if ( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS ) {
				$actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this item permanently', 'restaurantpress' ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', 'restaurantpress' ) . '</a>';
			}
		}
		if ( $post_type_object->public ) {
			if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
				if ( $can_edit_post )
					$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'restaurantpress' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'restaurantpress' ) . '</a>';
			} elseif ( 'trash' != $post->post_status ) {
				$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'restaurantpress' ), $title ) ) . '" rel="permalink">' . __( 'View', 'restaurantpress' ) . '</a>';
			}
		}

		$actions = apply_filters( 'post_row_actions', $actions, $post );

		echo '<div class="row-actions">';

		$i = 0;
		$action_count = sizeof( $actions );

		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo '<span class="' . $action . '">' . $link . $sep . '</span>';
		}
		echo '</div>';
	}

	/**
	 * Output custom columns for groups.
	 * @param string $column
	 */
	public function render_food_group_columns( $column ) {
		global $post;

		switch ( $column ) {
			case 'name':
				$edit_link = get_edit_post_link( $post->ID );
				$title     = _draft_or_post_title();

				echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

				_post_states( $post );

				echo '</strong>';

				$this->_render_food_group_row_actions( $post, $title );
			break;
			case 'group_id' :
				echo '<span>' . $post->ID . '</span>';
			break;
			default:
				echo wp_kses_post( $post->post_excerpt );
			break;
		}
	}

	/**
	 * Render food_group row actions for old version of WordPress.
	 * Since WordPress 4.3 we don't have to build the row actions.
	 *
	 * @param WP_Post $post
	 * @param string  $title
	 */
	private function _render_food_group_row_actions( $post, $title ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.3-beta', '>=' ) ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );

		// Get actions
		$actions = array();

		if ( current_user_can( $post_type_object->cap->edit_post, $post->ID ) ) {
			$actions['edit'] = '<a href="' . admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=edit', $post->ID ) ) . '">' . __( 'Edit', 'restaurantpress' ) . '</a>';
		}

		if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {

			if ( 'trash' == $post->post_status ) {
				$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash', 'restaurantpress' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore', 'restaurantpress' ) . "</a>";
			} elseif ( EMPTY_TRASH_DAYS ) {
				$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash', 'restaurantpress' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash', 'restaurantpress' ) . "</a>";
			}

			if ( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS ) {
				$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently', 'restaurantpress' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently', 'restaurantpress' ) . "</a>";
			}
		}

		$actions = apply_filters( 'post_row_actions', $actions, $post );

		echo '<div class="row-actions">';

		$i = 0;
		$action_count = sizeof( $actions );

		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo "<span class='$action'>$link$sep</span>";
		}
		echo '</div>';
	}

	/**
	 * Make columns sortable - https://gist.github.com/906872
	 * @param  array $columns
	 * @return array
	 */
	public function food_menu_sortable_columns( $columns ) {
		$custom = array(
			'name'  => 'title',
			'price' => 'price'
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Make columns sortable - https://gist.github.com/906872
	 * @param  array $columns
	 * @return array
	 */
	public function food_group_sortable_columns( $columns ) {
		$custom = array(
			'name'     => 'title',
			'group_id' => 'group_id'
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Set list table primary column for food group
	 * Support for WordPress 4.3
	 *
	 * @param  string $default
	 * @param  string $screen_id
	 *
	 * @return string
	 */
	public function list_table_primary_column( $default, $screen_id ) {

		if ( 'edit-food_menu' === $screen_id ) {
			return 'name';
		}

		if ( 'edit-food_group' === $screen_id ) {
			return 'name';
		}

		return $default;
	}

	/**
	 * Set row actions for food group.
	 *
	 * @param  array $actions
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public function row_actions( $actions, $post ) {

		if ( 'food_group' === $post->post_type ) {
			if ( isset( $actions['inline hide-if-no-js'] ) ) {
				unset( $actions['inline hide-if-no-js'] );
			}
		}

		return $actions;
	}

	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'food_menu' :
				$text = __( 'Menu Item name', 'restaurantpress' );
			break;
			case 'food_group':
				$text = __( 'Group name', 'restaurantpress' );
			break;
		}

		return $text;
	}

	/**
	 * Change label for insert buttons.
	 * @param  array $strings
	 * @return array
	 */
	public function change_insert_into_post( $strings ) {
		global $post_type;

		if ( in_array( $post_type, array( 'food_menu' ) ) ) {
			$obj = get_post_type_object( $post_type );

			$strings['insertIntoPost']     = sprintf( __( 'Insert into %s', 'restaurantpress' ), $obj->labels->singular_name );
			$strings['uploadedToThisPost'] = sprintf( __( 'Uploaded to this %s', 'restaurantpress' ), $obj->labels->singular_name );
		}

		return $strings;
	}

	/**
	 * Print group description textarea field
	 * @param WP_Post $post
	 */
	public function edit_form_after_title( $post ) {
		if ( 'food_group' === $post->post_type ) {
			?>
			<textarea id="restaurantpress-group-description" name="excerpt" cols="5" rows="2" placeholder="<?php esc_attr_e( 'Description (optional)', 'restaurantpress' ); ?>"><?php echo $post->post_excerpt; ?></textarea>
			<?php
		}
	}

	/**
	 * Hidden default Meta-Boxes.
	 * @param  array  $hidden
	 * @param  object $screen
	 * @return array
	 */
	public function hidden_meta_boxes( $hidden, $screen ) {
		if ( 'food_menu' === $screen->post_type && 'post' === $screen->base ) {
			$hidden = array_merge( $hidden, array( 'postcustom' ) );
		}

		return $hidden;
	}

	/**
	 * Disable DFW feature pointer.
	 */
	public function disable_dfw_feature_pointer() {
		$screen = get_current_screen();

		if ( $screen && 'food_menu' === $screen->id && 'post' === $screen->base ) {
			remove_action( 'admin_print_footer_scripts', array( 'WP_Internal_Pointers', 'pointer_wp410_dfw' ) );
		}
	}

	/**
	 * Removes food menu and group from the list of post types that support "View Mode" switching.
	 * View mode is seen on posts where you can switch between list or excerpt. Our post types don't support
	 * it, so we want to hide the useless UI from the screen options tab.
	 *
	 * @param  array $post_types Array of post types supporting view mode
	 * @return array             Array of post types supporting view mode, without food menu and group
	 */
	public function disable_view_mode_options( $post_types ) {
		unset( $post_types['food_menu'], $post_types['food_group'] );
		return $post_types;
	}
}

new RP_Admin_Post_Types();
