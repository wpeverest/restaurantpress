<?php
/**
 * All functionality to regenerate images in the background when settings change.
 *
 * @package RestaurantPress/Classes
 * @version 1.7.0
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RP_Background_Process', false ) ) {
	include_once dirname( __FILE__ ) . '/abstracts/class-rp-background-process.php';
}

/**
 * Class that extends RP_Background_Process to process image regeneration in the background
 */
class RP_Regenerate_Images_Request extends RP_Background_Process {

	/**
	 * Stores the attachment ID being processed.
	 *
	 * @var integer
	 */
	protected $attachment_id = 0;

	/**
	 * Initiate new background process.
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'rp_regenerate_images';

		parent::__construct();
	}

	/**
	 * Code to execute for each item in the queue
	 *
	 * @param mixed $item Queue item to iterate over.
	 * @return bool
	 */
	protected function task( $item ) {
		if ( ! is_array( $item ) && ! isset( $item['attachment_id'] ) ) {
			return false;
		}

		if ( ! function_exists( 'wp_crop_image' ) ) {
			include( ABSPATH . 'wp-admin/includes/image.php' );
		}

		$this->attachment_id = absint( $item['attachment_id'] );
		$attachment          = get_post( $this->attachment_id );

		if ( ! $attachment || 'attachment' !== $attachment->post_type || 'image/' !== substr( $attachment->post_mime_type, 0, 6 ) ) {
			return false;
		}

		$fullsizepath = get_attached_file( $this->attachment_id );

		// Check if the file exists, if not just remove item from queue.
		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ) {
			return false;
		}

		// This function will generate the new image sizes.
		$metadata = wp_generate_attachment_metadata( $attachment->ID, $fullsizepath );

		// If something went wrong lets just remove the item from the queue.
		if ( is_wp_error( $metadata ) || empty( $metadata ) ) {
			return false;
		}

		// Update the meta data with the new size values.
		wp_update_attachment_metadata( $attachment->ID, $metadata );

		// We made it till the end, now lets remove the item from the queue.
		return false;
	}
}
