<?php
/**
 * Food Data
 *
 * Displays the food data box, tabbed, with several panels covering price, details etc.
 *
 * @class    RP_Meta_Box_Food_Data
 * @version  1.4.0
 * @package  RestaurantPress/Admin/Meta Boxes
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Meta_Box_Food_Data Class
 */
class RP_Meta_Box_Food_Data {

	/**
	 * Output the meta box.
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		wp_nonce_field( 'restaurantpress_save_data', 'restaurantpress_meta_nonce' );

		?>
		<div class="panel-wrap food_data">
			<ul class="food_data_tabs rp-tabs">
				<?php foreach ( self::get_food_data_tabs() as $key => $tab ) : ?>
					<li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , (array) $tab['class'] ); ?>">
						<a href="#<?php echo $tab['target']; ?>"><span><?php echo esc_html( $tab['label'] ); ?></span></a>
					</li>
				<?php endforeach; ?>
				<?php do_action( 'restaurantpress_food_write_panel_tabs' ); ?>
			</ul>
			<div id="general_food_data" class="panel restaurantpress_options_panel">

				<div class="options_group pricing">
					<?php
						restaurantpress_wp_text_input( array(
							'id'        => '_regular_price',
							'label'     => __( 'Regular price', 'restaurantpress' ) . ' (Rs)',
							'data_type' => 'price',
						) );

						restaurantpress_wp_text_input( array(
							'id'          => '_sale_price',
							'label'       => __( 'Sale price', 'restaurantpress' ) . ' (Rs)',
							'data_type'   => 'price',
						) );

						do_action( 'restaurantpress_food_options_pricing' );
					?>
				</div>

				<div class="options_group">
					<?php
						restaurantpress_wp_checkbox( array(
							'id'      => 'chef_badge_item',
							'label'   => __( 'Enable chef flash', 'restaurantpress' ),
							'cbvalue' => 'yes',
						) );
					?>
				</div>

				<?php do_action( 'restaurantpress_food_options_general' ); ?>
			</div>
			<div id="advanced_food_data" class="panel restaurantpress_options_panel hidden">

				<div class="options_group">
					<?php
						restaurantpress_wp_text_input( array(
							'id'                => 'menu_order',
							'label'             => __( 'Menu order', 'restaurantpress' ),
							'desc_tip'          => true,
							'description'       => __( 'Custom ordering position.', 'restaurantpress' ),
							'type'              => 'number',
							'custom_attributes' => array(
								'step' 	=> '1',
							),
						) );
					?>
				</div>

				<?php do_action( 'restaurantpress_food_options_advanced' ); ?>
			</div>
			<?php do_action( 'restaurantpress_food_data_panels' ); ?>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Returns array of tabs to show.
	 * @return array
	 */
	private static function get_food_data_tabs() {
		$tabs = apply_filters( 'restaurantpress_food_data_tabs', array(
			'general' => array(
				'label'    => __( 'General', 'restaurantpress' ),
				'target'   => 'general_food_data',
				'class'    => array(),
				'priority' => 10,
			),
			'advanced' => array(
				'label'    => __( 'Advanced', 'restaurantpress' ),
				'target'   => 'advanced_food_data',
				'class'    => array(),
				'priority' => 20,
			),
		) );

		// Sort tabs based on priority.
		uasort( $tabs, array( __CLASS__, 'food_data_tabs_sort' ) );

		return $tabs;
	}

	/**
	 * Callback to sort food data tabs on priority.
	 *
	 * @since 1.4.0
	 * @param int $a First item.
	 * @param int $b Second item.
	 *
	 * @return bool
	 */
	private static function food_data_tabs_sort( $a, $b ) {
		if ( ! isset( $a['priority'], $b['priority'] ) ) {
			return -1;
		}

		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}

		return $a['priority'] < $b['priority'] ? -1 : 1;
	}

	/**
	 * Save meta box data.
	 * @param int $post_id
	 */
	public static function save( $post_id ) {
		// Add/Replace data to array
		$menu_order      = rp_clean( $_POST['menu_order'] );
		$sale_price      = rp_clean( $_POST['_sale_price'] );
		$regular_price   = rp_clean( $_POST['_regular_price'] );
		$chef_item_badge = isset( $_POST['chef_badge_item'] ) ? 'yes' : 'no';

		// Prevent regular price being lower.
		if ( $sale_price >= $regular_price ) {
			$sale_price = '';
		}

		// Save
		update_post_meta( $post_id, '_price', $sale_price ? $sale_price : $regular_price );
		update_post_meta( $post_id, '_regular_price', $regular_price );
		update_post_meta( $post_id, '_sale_price', $sale_price );
		update_post_meta( $post_id, 'menu_order', $menu_order );
		update_post_meta( $post_id, 'chef_badge_item', $chef_item_badge );
	}
}
