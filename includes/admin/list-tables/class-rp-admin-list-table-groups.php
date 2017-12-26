<?php
/**
 * List tables: groups.
 *
 * @author   WPEverest
 * @category Admin
 * @package  RestaurantPress/Admin
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'RP_Admin_List_Table_Groups', false ) ) {
	new RP_Admin_List_Table_Groups();
	return;
}

/**
 * RP_Admin_List_Table_Groups Class.
 */
class RP_Admin_List_Table_Groups extends RP_Admin_List_Table {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $list_table_type = 'food_group';

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Render blank state.
	 */
	protected function render_blank_state() {
		echo '<div class="restaurantpress-BlankState">';
		echo '<h2 class="restaurantpress-BlankState-message">' . esc_html__( 'Groups are a great way to organize and categorize your food items. They will appear here once created.', 'restaurantpress' ) . '</h2>';
		echo '<a class="restaurantpress-BlankState-cta button-primary button" href="' . esc_url( admin_url( 'post-new.php?post_type=food_group' ) ) . '">' . esc_html__( 'Create your first food group!', 'restaurantpress' ) . '</a>';
		echo '</div>';
	}

	/**
	 * Define primary column.
	 *
	 * @return array
	 */
	protected function get_primary_column() {
		return 'name';
	}

	/**
	 * Get row actions to show in the list table.
	 *
	 * @param array   $actions Array of actions.
	 * @param WP_Post $post Current post object.
	 * @return array
	 */
	protected function get_row_actions( $actions, $post ) {
		unset( $actions['inline hide-if-no-js'] );
		return $actions;
	}

	/**
	 * Define which columns are sortable.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_sortable_columns( $columns ) {
		$custom = array(
			'name'     => 'title',
			'group_id' => 'group_id',
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_columns( $columns ) {
		$show_columns                = array();
		$show_columns['cb']          = $columns['cb'];
		$show_columns['name']        = __( 'Name', 'restaurantpress' );
		$show_columns['group_id']    = __( 'Group ID', 'restaurantpress' );
		$show_columns['description'] = __( 'Description', 'restaurantpress' );

		return $show_columns;
	}

	/**
	 * Pre-fetch any data for the row each column has access to it. the_food global is there for bw compat.
	 *
	 * @param int $post_id Post ID being shown.
	 */
	protected function prepare_row_data( $post_id ) {
		global $post, $the_group;

		if ( empty( $this->object ) || $this->object->ID !== $post_id ) {
			$this->object = $the_group = $post;
		}
	}

	/**
	 * Render columm: name.
	 */
	protected function render_name_column() {
		global $post;

		$edit_link = get_edit_post_link( $this->object->ID );
		$title     = _draft_or_post_title();

		echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';
		_post_states( $post );
		echo '</strong>';
	}

	/**
	 * Render columm: group_id.
	 */
	protected function render_group_id_column() {
		echo '<span>' . $this->object->ID . '</span>';
	}

	/**
	 * Render columm: description.
	 */
	protected function render_description_column() {
		echo wp_kses_post( $this->object->post_excerpt ? $this->object->post_excerpt : '&ndash;' );
	}
}

new RP_Admin_List_Table_Groups();
