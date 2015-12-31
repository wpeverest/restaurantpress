<?php
/**
 * Group Menu Widget
 *
 * Displays Group Menu widget.
 *
 * @extends  RP_Widget
 * @version  1.0.0
 * @package  RestaurantPress/Widgets
 * @category Widgets
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Widget_Menu Class
 */
class RP_Widget_Menu extends RP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'restaurantpress widget_menu';
		$this->widget_description = __( 'Displays RestaurantPress Menu.', 'restaurantpress' );
		$this->widget_id          = 'restaurantpress_widget_menu';
		$this->widget_name        = __( 'RestaurantPress Menu', 'restaurantpress' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Group Menu', 'restaurantpress' ),
				'label' => __( 'Title', 'restaurantpress' )
			),
		);
		parent::__construct();
	}
}
