<?php
/**
 * Regenerate Images Functionality
 *
 * All functionality pertaining to regenerating food images in realtime.
 *
 * @package RestaurantPress\Classes
 * @version 1.7.0
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Regenerate Images Class.
 */
class RP_Regenerate_Images {

	/**
	 * Background process to regenerate all images
	 *
	 * @var RP_Regenerate_Images_Request
	 */
	protected static $background_process;

	/**
	 * Stores size being generated on the fly.
	 *
	 * @var string
	 */
	protected static $requested_size;

	/**
	 * Init function
	 */
	public static function init() {
		include_once RP_ABSPATH . 'includes/class-rp-regenerate-images-request.php';
		self::$background_process = new RP_Regenerate_Images_Request();

		add_filter( 'wp_generate_attachment_metadata', array( __CLASS__, 'add_uncropped_metadata' ) );

		if ( ! is_admin() ) {
			// Handle on-the-fly image resizing.
			add_filter( 'wp_get_attachment_image_src', array( __CLASS__, 'maybe_resize_image' ), 10, 4 );
		}

		if ( apply_filters( 'restaurantpress_background_image_regeneration', true ) ) {
			// Actions to handle image generation when settings change.
			add_action( 'update_option_restaurantpress_thumbnail_cropping', array( __CLASS__, 'maybe_regenerate_images_option_update' ), 10, 3 );
			add_action( 'update_option_restaurantpress_thumbnail_image_width', array( __CLASS__, 'maybe_regenerate_images_option_update' ), 10, 3 );
			add_action( 'update_option_restaurantpress_single_image_width', array( __CLASS__, 'maybe_regenerate_images_option_update' ), 10, 3 );
			add_action( 'after_switch_theme', array( __CLASS__, 'maybe_regenerate_image_theme_switch' ) );
		}
	}

	/**
	 * We need to track if uncropped was on or off when generating the images.
	 *
	 * @param array $metadata Array of meta data.
	 * @return array
	 */
	public static function add_uncropped_metadata( $metadata ) {
		$size_settings = rp_get_image_size( 'restaurantpress_thumbnail' );
		$metadata['restaurantpress_thumbnail_uncropped'] = empty( $size_settings['height'] );
		return $metadata;
	}

	/**
	 * Check if we should maybe generate a new image size if not already there.
	 *
	 * @param array        $image Properties of the image.
	 * @param int          $attachment_id Attachment ID.
	 * @param string|array $size Image size.
	 * @param bool         $icon If icon or not.
	 * @return array
	 */
	public static function maybe_resize_image( $image, $attachment_id, $size, $icon ) {
		if ( ! apply_filters( 'restaurantpress_resize_images', true ) ) {
			return $image;
		}

		// Use a whitelist of sizes we want to resize. Ignore others.
		if ( ! in_array( $size, apply_filters( 'restaurantpress_image_sizes_to_resize', array( 'restaurantpress_thumbnail', 'restaurantpress_single', 'food_grid', 'food_thumbnail', 'food_single' ), true ) ) ) {
			return $image;
		}

		// Get image metadata - we need it to proceed.
		$imagemeta = wp_get_attachment_metadata( $attachment_id );

		if ( empty( $imagemeta ) ) {
			return $image;
		}

		$size_settings = rp_get_image_size( $size );

		// If size differs from image meta, or height differs and we're cropping, regenerate the image.
		if ( ! isset( $imagemeta['sizes'], $imagemeta['sizes'][ $size ] ) || $imagemeta['sizes'][ $size ]['width'] !== $size_settings['width'] || ( $size_settings['crop'] && $imagemeta['sizes'][ $size ]['height'] !== $size_settings['height'] ) ) {
			$image = self::resize_and_return_image( $attachment_id, $image, $size, $icon );
		}

		// If cropping mode has changed, regenerate the image.
		if ( '' === $size_settings['height'] && empty( $imagemeta['restaurantpress_thumbnail'] ) ) {
			$image = self::resize_and_return_image( $attachment_id, $image, $size, $icon );
		}

		return $image;
	}

	/**
	 * Ensure we are dealing with the correct image attachment.
	 *
	 * @param WP_Post $attachment Attachment object.
	 * @return boolean
	 */
	public static function is_regeneratable( $attachment ) {
		if ( 'site-icon' === get_post_meta( $attachment->ID, '_wp_attachment_context', true ) ) {
			return false;
		}

		if ( wp_attachment_is_image( $attachment ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Only regenerate images for the requested size.
	 *
	 * @param array $sizes Array of image sizes.
	 * @return array
	 */
	public static function adjust_intermediate_image_sizes( $sizes ) {
		return array( self::$requested_size );
	}

	/**
	 * Generate the thumbnail filename and dimensions for a given file.
	 *
	 * @param string $fullsizepath Path to full size image.
	 * @param int    $thumbnail_width  The width of the thumbnail.
	 * @param int    $thumbnail_height The height of the thumbnail.
	 * @param bool   $crop             Whether to crop or not.
	 * @return array|false An array of the filename, thumbnail width, and thumbnail height, or false on failure to resize such as the thumbnail being larger than the fullsize image.
	 */
	private static function get_image( $fullsizepath, $thumbnail_width, $thumbnail_height, $crop ) {
		list( $fullsize_width, $fullsize_height ) = getimagesize( $fullsizepath );

		$dimensions = image_resize_dimensions( $fullsize_width, $fullsize_height, $thumbnail_width, $thumbnail_height, $crop );
		$editor     = wp_get_image_editor( $fullsizepath );

		if ( is_wp_error( $editor ) ) {
			return false;
		}

		if ( ! $dimensions || ! is_array( $dimensions ) ) {
			return false;
		}

		list( , , , , $dst_w, $dst_h ) = $dimensions;
		$suffix   = "{$dst_w}x{$dst_h}";
		$file_ext = strtolower( pathinfo( $fullsizepath, PATHINFO_EXTENSION ) );

		return array(
			'filename' => $editor->generate_filename( $suffix, null, $file_ext ),
			'width'    => $dst_w,
			'height'   => $dst_h,
		);
	}

	/**
	 * Regenerate the image according to the required size
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param array  $image Original Image.
	 * @param string $size Size to return for new URL.
	 * @param bool   $icon If icon or not.
	 * @return string
	 */
	private static function resize_and_return_image( $attachment_id, $image, $size, $icon ) {
		self::$requested_size = $size;
		$image_size           = rp_get_image_size( $size );
		$wp_uploads           = wp_upload_dir( null, false );
		$wp_uploads_dir       = $wp_uploads['basedir'];
		$wp_uploads_url       = $wp_uploads['baseurl'];
		$attachment           = get_post( $attachment_id );

		if ( ! $attachment || 'attachment' !== $attachment->post_type || ! self::is_regeneratable( $attachment ) ) {
			return $image;
		}

		$fullsizepath = get_attached_file( $attachment_id );

		if ( false === $fullsizepath || is_wp_error( $fullsizepath ) || ! file_exists( $fullsizepath ) ) {
			return $image;
		}

		if ( ! function_exists( 'wp_crop_image' ) ) {
			include ABSPATH . 'wp-admin/includes/image.php';
		}

		// Make sure registered image size matches the size we're requesting.
		add_image_size( $size, $image_size['width'], $image_size['height'], $image_size['crop'] );

		$thumbnail = self::get_image( $fullsizepath, $image_size['width'], $image_size['height'], $image_size['crop'] );

		// If the file is already there perhaps just load it.
		if ( $thumbnail && file_exists( $thumbnail['filename'] ) ) {
			$wp_uploads     = wp_upload_dir( null, false );
			$wp_uploads_dir = $wp_uploads['basedir'];
			$wp_uploads_url = $wp_uploads['baseurl'];

			return array(
				0 => str_replace( $wp_uploads_dir, $wp_uploads_url, $thumbnail['filename'] ),
				1 => $thumbnail['width'],
				2 => $thumbnail['height'],
			);
		}

		$old_metadata = wp_get_attachment_metadata( $attachment_id );

		// We only want to regen RP images.
		add_filter( 'intermediate_image_sizes', array( __CLASS__, 'adjust_intermediate_image_sizes' ) );

		// This function will generate the new image sizes.
		$new_metadata = wp_generate_attachment_metadata( $attachment_id, $fullsizepath );

		// Remove custom filter.
		remove_filter( 'intermediate_image_sizes', array( __CLASS__, 'adjust_intermediate_image_sizes' ) );

		// If something went wrong lets just return the original image.
		if ( is_wp_error( $new_metadata ) || empty( $new_metadata ) ) {
			return $image;
		}

		if ( ! empty( $old_metadata ) && ! empty( $old_metadata['sizes'] ) && is_array( $old_metadata['sizes'] ) ) {
			foreach ( $old_metadata['sizes'] as $old_size => $old_size_data ) {
				if ( empty( $new_metadata['sizes'][ $old_size ] ) ) {
					$new_metadata['sizes'][ $old_size ] = $old_metadata['sizes'][ $old_size ];
				}
			}

			// Handle legacy sizes.
			if ( isset( $new_metadata['sizes']['food_thumbnail'], $new_metadata['sizes']['restaurantpress_thumbnail'] ) ) {
				$new_metadata['sizes']['food_thumbnail'] = $new_metadata['sizes']['restaurantpress_thumbnail'];
			}
			if ( isset( $new_metadata['sizes']['food_grid'], $new_metadata['sizes']['restaurantpress_thumbnail'] ) ) {
				$new_metadata['sizes']['food_grid'] = $new_metadata['sizes']['restaurantpress_thumbnail'];
			}
			if ( isset( $new_metadata['sizes']['food_single'], $new_metadata['sizes']['restaurantpress_single'] ) ) {
				$new_metadata['sizes']['food_single'] = $new_metadata['sizes']['restaurantpress_single'];
			}
		}

		// Update the meta data with the new size values.
		wp_update_attachment_metadata( $attachment_id, $new_metadata );

		// Now we've done our regen, attempt to return the new size.
		$new_image = image_downsize( $attachment_id, $size );

		return $new_image ? $new_image : $image;
	}

	/**
	 * Check if we should regenerate the product images when options change.
	 *
	 * @param mixed  $old_value Old option value.
	 * @param mixed  $new_value New option value.
	 * @param string $option Option name.
	 */
	public static function maybe_regenerate_images_option_update( $old_value, $new_value, $option ) {
		if ( $new_value === $old_value ) {
			return;
		}

		self::queue_image_regeneration();
	}

	/**
	 * Check if we should generate images when new themes declares custom sizes.
	 */
	public static function maybe_regenerate_image_theme_switch() {
		if ( rp_get_theme_support( 'single_image_width' ) || rp_get_theme_support( 'thumbnail_image_width' ) ) {
			self::queue_image_regeneration();
		}
	}

	/**
	 * Get list of images and queue them for regeneration
	 */
	private static function queue_image_regeneration() {
		global $wpdb;
		// First lets cancel existing running queue to avoid running it more than once.
		self::$background_process->kill_process();

		// Now lets find all product image attachments IDs and pop them onto the queue.
		$images = $wpdb->get_results( // @codingStandardsIgnoreLine
			"SELECT ID
			FROM $wpdb->posts
			WHERE post_type = 'attachment'
			AND post_mime_type LIKE 'image/%'
			ORDER BY ID DESC"
		);
		foreach ( $images as $image ) {
			self::$background_process->push_to_queue( array(
				'attachment_id' => $image->ID,
			) );
		}

		// Lets dispatch the queue to start processing.
		self::$background_process->save()->dispatch();
	}
}

RP_Regenerate_Images::init();
