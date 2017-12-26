<?php
/**
 * List tables: foods.
 *
 * @author   WPEverest
 * @category Admin
 * @package  RestaurantPress/Admin
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'RP_Admin_List_Table_Foods', false ) ) {
	new RP_Admin_List_Table_Foods();
	return;
}

/**
 * RP_Admin_List_Table_Foods Class.
 */
class RP_Admin_List_Table_Foods extends RP_Admin_List_Table {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $list_table_type = 'food_menu';

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
		echo '<h2 class="restaurantpress-BlankState-message">' . esc_html__( 'Create elegant food/restaurant menus!', 'restaurantpress' ) . '</h2>';
		echo '<a class="restaurantpress-BlankState-cta button-primary button" href="' . esc_url( admin_url( 'post-new.php?post_type=food_menu' ) ) . '">' . esc_html__( 'Add your first menu item!', 'restaurantpress' ) . '</a>';
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
	 * Define which columns are sortable.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_sortable_columns( $columns ) {
		$custom = array(
			'name'     => 'title',
			'price'    => 'price',
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
		if ( empty( $columns ) && ! is_array( $columns ) ) {
			$columns = array();
		}

		unset( $columns['title'], $columns['comments'], $columns['date'] );

		$show_columns                  = array();
		$show_columns['cb']            = '<input type="checkbox" />';
		$show_columns['thumb']         = '<span class="rp-image tips" data-tip="' . esc_attr__( 'Image', 'restaurantpress' ) . '">' . __( 'Image', 'restaurantpress' ) . '</span>';
		$show_columns['name']          = __( 'Name', 'restaurantpress' );
		$show_columns['price']         = __( 'Price', 'restaurantpress' );
		$show_columns['food_menu_cat'] = __( 'Categories', 'restaurantpress' );
		$show_columns['food_menu_tag'] = __( 'Tags', 'restaurantpress' );
		$show_columns['featured']      = '<span class="rp-featured parent-tips" data-tip="' . esc_attr__( 'Featured', 'restaurantpress' ) . '">' . __( 'Featured', 'restaurantpress' ) . '</span>';
		$show_columns['date']          = __( 'Date', 'restaurantpress' );

		return array_merge( $show_columns, $columns );
	}

	/**
	 * Pre-fetch any data for the row each column has access to it. the_food global is there for bw compat.
	 *
	 * @param int $post_id Post ID being shown.
	 */
	protected function prepare_row_data( $post_id ) {
		global $the_food;

		if ( empty( $this->object ) || $this->object->get_id() !== $post_id ) {
			$this->object = $the_food = rp_get_food( $post_id );
		}
	}

	/**
	 * Render columm: thumb.
	 */
	protected function render_thumb_column() {
		echo '<a href="' . esc_url( get_edit_post_link( $this->object->get_id() ) ) . '">' . $this->object->get_image( 'thumbnail' ) . '</a>';
	}

	/**
	 * Render columm: name.
	 */
	protected function render_name_column() {
		global $post;

		$edit_link = get_edit_post_link( $this->object->get_id() );
		$title     = _draft_or_post_title();

		echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

		_post_states( $post );

		echo '</strong>';

		if ( $post->post_parent > 0 ) {
			echo '&nbsp;&nbsp;&larr; <a href="' . get_edit_post_link( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . '</a>';
		}

		get_inline_data( $post );

		/* Custom inline data for restaurantpress. */
		echo '
			<div class="hidden" id="restaurantpress_inline_' . absint( $this->object->get_id() ) . '">
				<div class="regular_price">' . esc_html( $this->object->get_regular_price() ) . '</div>
				<div class="sale_price">' . esc_html( $this->object->get_sale_price() ) . '</div>
				<div class="featured">' . esc_html( rp_bool_to_string( $this->object->get_featured() ) ) . '</div>
			</div>
		';
	}

	/**
	 * Render columm: price.
	 */
	protected function render_price_column() {
		echo $this->object->get_price_html() ? wp_kses_post( $this->object->get_price_html() ) : '<span class="na">&ndash;</span>';
	}

	/**
	 * Render columm: food_menu_cat.
	 */
	protected function render_food_menu_cat_column() {
		if ( ! $terms = get_the_terms( $this->object->get_id(), 'food_menu_cat' ) ) {
			echo '<span class="na">&ndash;</span>';
		} else {
			$termlist = array();
			foreach ( $terms as $term ) {
				$termlist[] = '<a href="' . esc_url( admin_url( 'edit.php?food_menu_cat=' . $term->slug . '&post_type=food_menu' ) ) . ' ">' . esc_html( $term->name ) . '</a>';
			}

			echo implode( ', ', $termlist ); // WPCS: XSS ok.
		}
	}

	/**
	 * Render columm: food_menu_tag.
	 */
	protected function render_food_menu_tag_column() {
		if ( ! $terms = get_the_terms( $this->object->get_id(), 'food_menu_tag' ) ) {
			echo '<span class="na">&ndash;</span>';
		} else {
			$termlist = array();
			foreach ( $terms as $term ) {
				$termlist[] = '<a href="' . esc_url( admin_url( 'edit.php?food_menu_tag=' . $term->slug . '&post_type=food_menu' ) ) . ' ">' . esc_html( $term->name ) . '</a>';
			}

			echo implode( ', ', $termlist ); // WPCS: XSS ok.
		}
	}

	/**
	 * Render columm: featured.
	 */
	protected function render_featured_column() {
		$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=restaurantpress_feature_food&food_id=' . $this->object->get_id() ), 'restaurantpress-feature-food' );
		echo '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr__( 'Toggle featured', 'restaurantpress' ) . '">';
		if ( $this->object->is_featured() ) {
			echo '<span class="rp-featured tips" data-tip="' . esc_attr__( 'Yes', 'restaurantpress' ) . '">' . esc_html__( 'Yes', 'restaurantpress' ) . '</span>';
		} else {
			echo '<span class="rp-featured not-featured tips" data-tip="' . esc_attr__( 'No', 'restaurantpress' ) . '">' . esc_html__( 'No', 'restaurantpress' ) . '</span>';
		}
		echo '</a>';
	}
}

new RP_Admin_List_Table_Foods();
