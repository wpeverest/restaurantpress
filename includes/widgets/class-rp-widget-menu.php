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
 * @author   WPEverest
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
		$this->settings           = apply_filters( 'restaurantpress_widget_menu_settings', array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Group Menu', 'restaurantpress' ),
				'label' => __( 'Title', 'restaurantpress' )
			),
			'description'  => array(
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Description', 'restaurantpress' )
			),
			'group' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => '',
				'label' => __( 'Group ID', 'restaurantpress' )
			),
			'orderby' => array(
				'type'  => 'select',
				'std'   => 'date',
				'label' => __( 'Order by', 'restaurantpress' ),
				'options' => array(
					'date'       => __( 'Date', 'restaurantpress' ),
					'title'      => __( 'Title', 'restaurantpress' ),
					'rand'       => __( 'Random', 'restaurantpress' ),
					'menu_order' => __( 'Menu Order', 'restaurantpress' ),
					'none'       => __( 'None', 'restaurantpress' ),
				)
			),
			'order' => array(
				'type'  => 'select',
				'std'   => 'desc',
				'label' => _x( 'Order', 'Sorting order', 'restaurantpress' ),
				'options' => array(
					'asc'  => __( 'ASC', 'restaurantpress' ),
					'desc' => __( 'DESC', 'restaurantpress' ),
				)
			)
		) );
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		do_action( 'restaurantpress_widget_menu_before', $args, $instance );

		$this->widget_start( $args, $instance );

		if ( ! empty( $instance['description'] ) ) {
			echo '<p class="sub-title">' . $instance['description'] . '</p>';
		}

		echo do_shortcode( '[restaurantpress_menu id=' . $instance['group'] . ' orderby=' . $instance['orderby'] . ' order=' . $instance['order'] . ']' );

		$this->widget_end( $args );

		do_action( 'restaurantpress_widget_menu_after', $args, $instance );
	}
}
