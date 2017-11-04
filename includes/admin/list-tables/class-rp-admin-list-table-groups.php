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

if ( ! class_exists( 'RP_Admin_List_Table', false ) ) {
	include_once( 'abstract-class-rp-admin-list-table.php' );
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
}

new RP_Admin_List_Table_Groups();
