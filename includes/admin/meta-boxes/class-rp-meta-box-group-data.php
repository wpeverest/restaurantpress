<?php
/**
 * Food Group Data
 *
 * @class    RP_Meta_Box_Group_Data
 * @version  1.0.0
 * @package  RestaurantPress/Admin/Meta Boxes
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Meta_Box_Group_Data Class
 */
class RP_Meta_Box_Group_Data {

	/**
	 * Output the meta box.
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		global $post, $thepostid;

		wp_nonce_field( 'restaurantpress_save_data', 'restaurantpress_meta_nonce' );

		$thepostid = $post->ID;

		?>
		<style type="text/css">
			#edit-slug-box, #minor-publishing-actions { display:none }
		</style>
		<div id="group_options" class="panel-wrap group_data">
			<ul class="group_data_tabs rp-tabs">
				<?php
					$group_data_tabs = apply_filters( 'restaurantpress_group_data_tabs', array(
						'general' => array(
							'label'  => __( 'General', 'restaurantpress' ),
							'target' => 'general_group_data',
							'class'  => array( 'hide_if_grouped' ),
						),
						'grouping' => array(
							'label'  => __( 'Grouping', 'restaurantpress' ),
							'target' => 'grouping_group_data',
							'class'  => 'grouping_group_data',
						)
					) );

					foreach ( $group_data_tabs as $key => $tab ) {
						?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ', (array) $tab['class'] ); ?>">
							<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
						</li><?php
					}

					do_action( 'restaurantpress_food_group_write_panel_tabs' );
				?>
			</ul>
			<div id="general_group_data" class="panel restaurantpress_options_panel hidden"><?php

				echo '<div class="options_group">';

					// Layout Type
					restaurantpress_wp_select( array(
						'id'    => 'layout_type',
						'label' => __( 'Layout Type', 'restaurantpress' ),
						'options' => array(
							'one_column' => __( 'One Column', 'restaurantpress' ),
							'two_column' => __( 'Two Column', 'restaurantpress' ),
							'grid_image' => __( 'Grid Image', 'restaurantpress' ),
						),
						'desc_tip'    => 'true',
						'description' => __( 'Define whether or not the entire layout should be column based, or just with the grid image.', 'restaurantpress' )
					) );

				echo '</div>';

				echo '<div class="options_group">';

					// Category Icon
					restaurantpress_wp_checkbox( array( 'id' => '_category_icon', 'wrapper_class' => 'show_to_all_layout', 'label' => __( 'Category Icon', 'restaurantpress' ), 'description' => __( 'Show category image icon.', 'restaurantpress' ) ) );

					// Featured Image
					restaurantpress_wp_checkbox( array( 'id' => '_featured_image', 'wrapper_class' => 'hide_if_grid_image', 'label' => __( 'Featured Image', 'restaurantpress' ), 'description' => __( 'Disable the featured image.', 'restaurantpress' ) ) );

				echo '</div>';

				do_action( 'restaurantpress_group_options_general' );

			?></div>
			<div id="grouping_group_data" class="panel restaurantpress_options_panel hidden"><?php

				echo '<div class="options_group">';

				// Grouping Categories
				?>
				<p class="form-field"><label for="food_grouping"><?php _e( 'Grouping', 'restaurantpress' ); ?></label>
				<select id="food_grouping" name="food_grouping[]" class="rp-category-search" multiple="multiple" style="width: 50%;" data-placeholder="<?php esc_attr_e( 'Search for a food category&hellip;', 'restaurantpress' ); ?>" data-sortable="true">
					<?php
						$category_ids = (array) get_post_meta( $post->ID, 'food_grouping', true );

						foreach ( $category_ids as $term_id ) {
							$term = get_term_by( 'id', $term_id, 'food_menu_cat' );
							if ( is_object( $term ) ) {
								echo '<option value="' . esc_attr( $term_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $term->name . ' (' . $term->count . ')' ) . '</option>';
							}
						}
					?>
				</select> <?php echo rp_help_tip( __( 'A food must be in this category for the group to remain valid.', 'restaurantpress' ) ); ?></p>
				<?php

				echo '</div>';

			?></div>
			<?php do_action( 'restaurantpress_group_data_panels' ); ?>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Save meta box data.
	 * @param int $post_id
	 */
	public static function save( $post_id ) {
		// Add/Replace data to array
		$layout_type    = rp_clean( $_POST['layout_type'] );
		$category_icon  = isset( $_POST['_category_icon'] ) ? 'yes' : 'no';
		$featured_image = isset( $_POST['_featured_image'] ) ? 'yes' : 'no';
		$food_grouping  = isset( $_POST['food_grouping'] ) ? array_map( 'rp_clean', $_POST['food_grouping'] ) : array();

		// Save
		update_post_meta( $post_id, 'layout_type', $layout_type );
		update_post_meta( $post_id, '_category_icon', $category_icon );
		update_post_meta( $post_id, '_featured_image', $featured_image );
		update_post_meta( $post_id, 'food_grouping', $food_grouping );

		do_action( 'restaurantpress_group_options_save', $post_id );
	}
}
