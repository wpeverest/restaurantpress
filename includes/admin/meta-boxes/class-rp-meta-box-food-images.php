<?php
/**
 * Food Images
 *
 * Display the food images meta box.
 *
 * @class    RP_Meta_Box_Food_Short_Description
 * @version  1.0.0
 * @package  RestaurantPress/Admin/Meta Boxes
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Meta_Box_Food_Images Class.
 */
class RP_Meta_Box_Food_Images {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		?>
		<div id="food_images_container">
			<ul class="food_images">
				<?php
					$food_image_gallery  = get_post_meta( $post->ID, '_food_image_gallery', true );
					$attachments         = array_filter( explode( ',', $food_image_gallery ) );
					$update_meta         = false;
					$updated_gallery_ids = array();

					if ( ! empty( $attachments ) ) {
						foreach ( $attachments as $attachment_id ) {
							$attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

							// if attachment is empty skip
							if ( empty( $attachment ) ) {
								$update_meta = true;
								continue;
							}

							echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
								' . $attachment . '
								<ul class="actions">
									<li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'restaurantpress' ) . '">' . __( 'Delete', 'restaurantpress' ) . '</a></li>
								</ul>
							</li>';

							// rebuild ids to be saved
							$updated_gallery_ids[] = $attachment_id;
						}

						// need to update food meta to set new gallery ids
						if ( $update_meta ) {
							update_post_meta( $post->ID, '_food_image_gallery', implode( ',', $updated_gallery_ids ) );
						}
					}
				?>
			</ul>

			<input type="hidden" id="food_image_gallery" name="food_image_gallery" value="<?php echo esc_attr( $food_image_gallery ); ?>" />

		</div>
		<p class="add_food_images hide-if-no-js">
			<a href="#" data-choose="<?php esc_attr_e( 'Add images to food gallery', 'restaurantpress' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'restaurantpress' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'restaurantpress' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'restaurantpress' ); ?>"><?php _e( 'Add food gallery images', 'restaurantpress' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		$attachment_ids = isset( $_POST['food_image_gallery'] ) ? array_filter( explode( ',', rp_clean( $_POST['food_image_gallery'] ) ) ) : array();

		update_post_meta( $post_id, '_food_image_gallery', implode( ',', $attachment_ids ) );
	}
}
