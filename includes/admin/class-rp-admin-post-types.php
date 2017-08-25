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

if ( ! class_exists( 'RP_Admin_Post_Types', false ) ) :

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

		// Extra post data.
		add_action( 'edit_form_top', array( $this, 'edit_form_top' ) );

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
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );
		add_action( 'post_submitbox_misc_actions', array( $this, 'food_data_visibility' ) );

		// Meta-Box Class
		include_once( dirname( __FILE__ ) . '/class-rp-admin-meta-boxes.php' );

		// Disable DFW feature pointer
		add_action( 'admin_footer', array( $this, 'disable_dfw_feature_pointer' ) );

		// Disable post type view mode options
		add_filter( 'view_mode_post_types', array( $this, 'disable_view_mode_options' ) );

		// Show blank state
		add_action( 'manage_posts_extra_tablenav', array( $this, 'maybe_render_blank_state' ) );

		// Add a post display state for special RP pages.
		add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
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
		$columns['food_menu_tag'] = __( 'Tags', 'restaurantpress' );
		$columns['featured']      = '<span class="rp-featured parent-tips" data-tip="' . esc_attr__( 'Featured', 'restaurantpress' ) . '">' . __( 'Featured', 'restaurantpress' ) . '</span>';
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
	 * Output custom columns for menu.
	 * @param string $column
	 */
	public function render_food_menu_columns( $column ) {
		global $post, $the_food;

		if ( empty( $the_food ) || $the_food->get_id() != $post->ID ) {
			$the_food = rp_get_food( $post );
		}

		// Only continue if we have a product.
		if ( empty( $the_food ) ) {
			return;
		}

		switch ( $column ) {
			case 'thumb' :
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . $the_food->get_image( 'thumbnail' ) . '</a>';
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

				get_inline_data( $post );
			break;
			case 'price' :
				echo $the_food->get_price_html() ? $the_food->get_price_html() : '<span class="na">&ndash;</span>';
				break;
			case 'food_menu_cat' :
			case 'food_menu_tag' :
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
			case 'featured' :
				$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=restaurantpress_feature_food&food_id=' . $post->ID ), 'restaurantpress-feature-food' );
				echo '<a href="' . esc_url( $url ) . '" aria-label="' . __( 'Toggle featured', 'restaurantpress' ) . '">';
				if ( $the_food->is_featured() ) {
					echo '<span class="rp-featured tips" data-tip="' . esc_attr__( 'Yes', 'restaurantpress' ) . '">' . __( 'Yes', 'restaurantpress' ) . '</span>';
				} else {
					echo '<span class="rp-featured not-featured tips" data-tip="' . esc_attr__( 'No', 'restaurantpress' ) . '">' . __( 'No', 'restaurantpress' ) . '</span>';
				}
				echo '</a>';
				break;
			default:
				break;
		}
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
	 * Set row actions for food and groups.
	 *
	 * @param  array $actions
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public function row_actions( $actions, $post ) {
		if ( 'food_menu' === $post->post_type ) {
			return array_merge( array( 'id' => 'ID: ' . $post->ID ), $actions );
		}

		if ( 'food_group' === $post->post_type ) {
			if ( isset( $actions['inline hide-if-no-js'] ) ) {
				unset( $actions['inline hide-if-no-js'] );
			}
		}

		return $actions;
	}

	/**
	 * Output extra data on post forms.
	 * @param WP_Post $post
	 */
	public function edit_form_top( $post ) {
		echo '<input type="hidden" id="original_post_title" name="original_post_title" value="' . esc_attr( $post->post_title ) . '" />';
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
				$text = __( 'Menu item name', 'restaurantpress' );
			break;
			case 'food_group':
				$text = __( 'Group name', 'restaurantpress' );
			break;
		}

		return $text;
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

			<a href="#featured-visibility" class="edit-featured-visibility hide-if-no-js"><?php _e( 'Edit', 'restaurantpress' ); ?></a>

			<div id="featured-visibility-select" class="hide-if-js">
				<input type="hidden" name="current_featured" id="current_featured" value="<?php echo esc_attr( $current_featured ); ?>" />
				<?php echo '<br /><input type="checkbox" name="_featured" id="_featured" ' . checked( $current_featured, 'yes', false ) . ' data-yes="' . esc_attr( 'Yes', 'restaurantpress' ) . '" data-no="' . esc_attr( 'No', 'restaurantpress' ) . '" /> <label for="_featured">' . __( 'This is a featured food', 'restaurantpress' ) . '</label><br />'; ?>
				<p>
					<a href="#featured-visibility" class="save-post-visibility hide-if-no-js button"><?php _e( 'OK', 'restaurantpress' ); ?></a>
					<a href="#featured-visibility" class="cancel-post-visibility hide-if-no-js"><?php _e( 'Cancel', 'restaurantpress' ); ?></a>
				</p>
			</div>
		</div>
		<?php
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

	/**
	 * Show blank slate.
	 *
	 * @param string $which
	 */
	public function maybe_render_blank_state( $which ) {
		global $post_type;

		if ( in_array( $post_type, array( 'food_group', 'food_menu' ) ) && 'bottom' === $which ) {
			$counts = (array) wp_count_posts( $post_type );
			unset( $counts['auto-draft'] );
			$count  = array_sum( $counts );

			if ( 0 < $count ) {
				return;
			}

			echo '<div class="restaurantpress-BlankState">';

			switch ( $post_type ) {
				case 'food_group' :
					?>
					<h2 class="restaurantpress-BlankState-message"><?php _e( 'Groups are a great way to organize and categorize your food items. They will appear here once created.', 'restaurantpress' ); ?></h2>
					<a class="restaurantpress-BlankState-cta button-primary button" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=food_group' ) ); ?>"><?php _e( 'Create your first food group!', 'restaurantpress' ); ?></a>
					<?php
				break;
				case 'food_menu' :
					?>
					<h2 class="restaurantpress-BlankState-message"><?php _e( 'Create elegant food/restaurant menus!', 'restaurantpress' ); ?></h2>
					<a class="restaurantpress-BlankState-cta button-primary button" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=food_menu&tutorial=true' ) ); ?>"><?php _e( 'Add your first menu item!', 'restaurantpress' ); ?></a>
					<?php
				break;
			}

			echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub  { display: none; } </style></div>';
		}
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

endif;

return new RP_Admin_Post_Types();
