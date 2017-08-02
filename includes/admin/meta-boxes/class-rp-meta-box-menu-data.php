<?php
/**
 * Food Menu Data
 *
 * @class    RP_Meta_Box_Menu_Data
 * @version  1.0.0
 * @package  RestaurantPress/Admin/Meta Boxes
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Meta_Box_Menu_Data Class
 */
class RP_Meta_Box_Menu_Data {

	/**
	 * Output the meta box.
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		wp_nonce_field( 'restaurantpress_save_data', 'restaurantpress_meta_nonce' );

		?>
		<ul class="food_menu_data">

			<?php
				do_action( 'restaurantpress_price_data_start', $post->ID );

				// Price Input
				restaurantpress_wp_text_input( array(
					'id'          => 'food_item_price',
					'type'        => 'text',
					'label'       => __( 'Set Price', 'restaurantpress' ),
					'placeholder' => __( 'Enter the item Price&hellip;', 'restaurantpress' )
				) );

				// Enable Chef Recommended Badge
				restaurantpress_wp_checkbox( array( 'id' => 'chef_badge_item', 'label' => '', 'description' => __( 'Enable Chef Recommended Badge.', 'restaurantpress' ) ) );

				do_action( 'restaurantpress_price_data_end', $post->ID );
			?>
		</ul>
		<?php
	}

	/**
	 * Save meta box data.
	 * @param int $post_id
	 */
	public static function save( $post_id ) {
		// Add/Replace data to array
		$food_item_price = rp_clean( $_POST['food_item_price'] );
		$chef_item_badge = isset( $_POST['chef_badge_item'] ) ? 'yes' : 'no';

		// Save
		update_post_meta( $post_id, 'food_item_price', $food_item_price );
		update_post_meta( $post_id, 'chef_badge_item', $chef_item_badge );
	}
}
