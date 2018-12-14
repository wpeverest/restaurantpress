<?php
/**
 * RestaurantPress Gutenberg blocks
 *
 * @package EverstForms\Class
 * @version 1.3.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Guten Block Class.
 */
class RP_Group_Block {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register the block and its scripts.
	 */
	public function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type( 'restaurantpress/group-selector', array(
			'attributes' => array(
				'groupId'      => array(
					'type' => 'string',
				),
				'orderBy'      => array(
					'type' => 'string',
				),
				'displayOrder' => array(
					'type' => 'boolean',
				),
			),
			'editor_style'    => 'restaurantpress-group-block-editor',
			'editor_script'   => 'restaurantpress-group-block-editor',
			'render_callback' => array( $this, 'get_group_html' ),
		) );
	}

	/**
	 * Load Gutenberg block scripts.
	 */
	public function enqueue_editor_assets() {
		wp_enqueue_style(
			'restaurantpress-layout',
			RP()->plugin_url() . '/assets/css/restaurantpress-layout.css',
			array( 'wp-edit-blocks' ),
			defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( RP()->plugin_path() . '/assets/css/restaurantpress-layout.css' ) : RP_VERSION
		);
	}

	/**
	 * Load Gutenberg block scripts.
	 */
	public function enqueue_block_editor_assets() {
		wp_register_style(
			'restaurantpress-group-block-editor',
			RP()->plugin_url() . '/assets/css/restaurantpress.css',
			array( 'wp-edit-blocks' ),
			defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( RP()->plugin_path() . '/assets/css/restaurantpress.css' ) : RP_VERSION
		);

		wp_register_script(
			'restaurantpress-group-block-editor',
			RP()->plugin_url() . '/assets/js/admin/gutenberg/group-block.min.js',
			array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-editor', 'wp-components' ),
			defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( RP()->plugin_path() . '/assets/js/admin/gutenberg/group-block.min.js' ) : RP_VERSION,
			true
		);

		$group_block_data = array(
			'groups' => get_posts(
				array(
					'post_type'     => 'food_group',
					'orderby'       => 'id',
					'order'         => 'DESC',
					'no_found_rows' => true,
					'nopaging'      => true,
				)
			),
			'orderby'  => array(
				'date'       => __( 'Date', 'restaurantpress' ),
				'title'      => __( 'Title', 'restaurantpress' ),
				'rand'       => __( 'Random', 'restaurantpress' ),
				'menu_order' => __( 'Menu Order', 'restaurantpress' ),
				'none'       => __( 'None', 'restaurantpress' ),
			),
			'i18n'   => array(
				'title'            => __( 'RestaurantPress Group', 'restaurantpress' ),
				'description'      => __( 'Select & display one of your food group.', 'restaurantpress' ),
				'group_select'     => __( 'Select a Group', 'restaurantpress' ),
				'group_selected'   => __( 'Group', 'restaurantpress' ),
				'group_settings'   => __( 'Group Settings', 'restaurantpress' ),
				'order_select'     => __( 'Select a order by', 'restaurantpress' ),
				'orderby_selected' => __( 'Order BY', 'restaurantpress' ),
				'order_toogle'     => __( 'Order', 'restaurantpress' ),
				'order_toogleHelp' => __( 'Toggle to display in ascending order.', 'restaurantpress' ),
			)
		);
		wp_localize_script( 'restaurantpress-group-block-editor', 'rp_group_block_data', $group_block_data );
	}

	/**
	 * Get form HTML to display in a Gutenberg block.
	 *
	 * @param  array $attr Attributes passed by Gutenberg block.
	 * @return string
	 */
	public function get_group_html( $attr ) {
		$group_id = ! empty( $attr['groupId'] ) ? absint( $attr['groupId'] ) : 0;
		$order_by = ! empty( $attr['orderBy'] ) ? rp_clean( $attr['orderBy'] ) : 'date';
		$is_order = isset( $attr['displayOrder'] ) && rp_string_to_bool( $attr['displayOrder'] ) ? 'ASC' : 'DESC';

		if ( empty( $group_id ) ) {
			return '';
		}

		$shortcode = RP_Shortcodes::menu(
			array(
				'id'      => $group_id,
				'orderby' => $order_by,
				'order'   => $is_order,
				'class'   => 'restaurantpress-group'
			)
		);

		return str_replace( array( '\n', '\r' ), '', $shortcode );
	}
}

new RP_Group_Block();
