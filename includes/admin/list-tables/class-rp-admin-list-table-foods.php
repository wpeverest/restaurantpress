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

if ( ! class_exists( 'RP_Admin_List_Table', false ) ) {
	include_once( 'abstract-class-rp-admin-list-table.php' );
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
}

new RP_Admin_List_Table_Foods();
