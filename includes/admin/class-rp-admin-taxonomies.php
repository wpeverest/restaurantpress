<?php
/**
 * Handles taxonomies in admin
 *
 * @class    RP_Admin_Taxonomies
 * @version  1.0.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Admin_Taxonomies Class
 */
class RP_Admin_Taxonomies {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'delete_term', array( $this, 'delete_term' ), 5 );

		// Add form
		add_action( 'food_menu_cat_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'food_menu_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

		// Add columns
		add_filter( 'manage_edit-food_menu_cat_columns', array( $this, 'food_menu_cat_columns' ) );
		add_filter( 'manage_food_menu_cat_custom_column', array( $this, 'food_menu_cat_column' ), 10, 3 );

		// Taxonomy page descriptions
		add_action( 'food_menu_cat_pre_add_form', array( $this, 'food_menu_cat_description' ) );

		// Maintain hierarchy of terms
		add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );
	}

	/**
	 * When a term is deleted, delete its meta.
	 * @param mixed $term_id
	 */
	public function delete_term( $term_id ) {
		global $wpdb;

		$term_id = absint( $term_id );

		if ( $term_id && get_option( 'db_version' ) < 34370 ) {
			$wpdb->delete( $wpdb->restaurantpress_termmeta, array( 'restaurantpress_term_id' => $term_id ), array( '%d' ) );
		}
	}

	/**
	 * Category thumbnail fields.
	 */
	public function add_category_fields() {
		?>
		<div class="form-field term-thumbnail-wrap">
			<label><?php _e( 'Upload Image Icon', 'restaurantpress' ); ?></label>
			<div id="food_menu_cat_thumbnail"><img src="<?php echo esc_url( rp_placeholder_img_src() ); ?>" width="48px" height="48px" /></div>
			<div style="line-height: 48px;">
				<input type="hidden" id="food_menu_cat_thumbnail_id" name="food_menu_cat_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'restaurantpress' ); ?></button>
				<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'restaurantpress' ); ?></button>
				<div class="clear"></div>
			</div>
			<p class="description"><?php _e( 'Recommended Size: 24x24px', 'restaurantpress' ); ?></p>
			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if ( ! jQuery( '#food_menu_cat_thumbnail_id' ).val() ) {
					jQuery( '.remove_image_button' ).hide();
				}

				// Uploading files
				var file_frame;

				jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( "Choose an image", "restaurantpress" ); ?>',
						button: {
							text: '<?php _e( "Use image", "restaurantpress" ); ?>'
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						var attachment = file_frame.state().get( 'selection' ).first().toJSON();

						jQuery( '#food_menu_cat_thumbnail_id' ).val( attachment.id );
						jQuery( '#food_menu_cat_thumbnail' ).find( 'img' ).attr( 'src', attachment.url );
						jQuery( '.remove_image_button' ).show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery( document ).on( 'click', '.remove_image_button', function() {
					jQuery( '#food_menu_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( rp_placeholder_img_src() ); ?>' );
					jQuery( '#food_menu_cat_thumbnail_id' ).val( '' );
					jQuery( '.remove_image_button' ).hide();
					return false;
				});

				jQuery( document ).ajaxComplete( function( event, request, options ) {
					if ( request && 4 === request.readyState && 200 === request.status && options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {
						/* global wpAjax */
						var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
						if ( ! res || res.errors ) {
							return;
						}

						// Clear Thumbnail fields on submit.
						jQuery( '#food_menu_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( rp_placeholder_img_src() ); ?>' );
						jQuery( '#food_menu_cat_thumbnail_id' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						return;
					}
				});
			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Edit category iconfont fields.
	 * @param mixed $term Term (category) being edited.
	 */
	public function edit_category_fields( $term ) {

		$thumbnail_id = absint( get_restaurantpress_term_meta( $term->term_id, 'thumbnail_id', true ) );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = rp_placeholder_img_src();
		}
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Upload Image Icon', 'restaurantpress' ); ?></label></th>
			<td>
				<div id="food_menu_cat_thumbnail"><img src="<?php echo esc_url( $image ); ?>" width="48px" height="48px" /></div>
				<div style="line-height: 48px;">
					<input type="hidden" id="food_menu_cat_thumbnail_id" name="food_menu_cat_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'restaurantpress' ); ?></button>
					<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'restaurantpress' ); ?></button>
					<div class="clear"></div>
				</div>
				<p class="description"><?php _e( 'Recommended Size: 24x24px', 'restaurantpress' ); ?></p>
				<script type="text/javascript">

					// Only show the "remove image" button when needed
					if ( '0' === jQuery( '#food_menu_cat_thumbnail_id' ).val() ) {
						jQuery( '.remove_image_button' ).hide();
					}

					// Uploading files
					var file_frame;

					jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php _e( "Choose an image", "restaurantpress" ); ?>',
							button: {
								text: '<?php _e( "Use image", "restaurantpress" ); ?>'
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							var attachment = file_frame.state().get( 'selection' ).first().toJSON();

							jQuery( '#food_menu_cat_thumbnail_id' ).val( attachment.id );
							jQuery( '#food_menu_cat_thumbnail' ).find( 'img' ).attr( 'src', attachment.url );
							jQuery( '.remove_image_button' ).show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery( document ).on( 'click', '.remove_image_button', function() {
						jQuery( '#food_menu_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( rp_placeholder_img_src() ); ?>' );
						jQuery( '#food_menu_cat_thumbnail_id' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save category icon fields.
	 * @param mixed  $term_id Term ID being saved.
	 * @param mixed  $tt_id
	 * @param string $taxonomy
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['food_menu_cat_thumbnail_id'] ) && 'food_menu_cat' === $taxonomy ) {
			update_restaurantpress_term_meta( $term_id, 'thumbnail_id', absint( $_POST['food_menu_cat_thumbnail_id'] ) );
		}
	}

	/**
	 * Thumbnail column added to category admin.
	 * @param  mixed $columns
	 * @return array
	 */
	public function food_menu_cat_columns( $columns ) {
		$new_columns = array();

		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		$new_columns['thumb'] = __( 'Image', 'restaurantpress' );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Thumbnail column value added to category admin.
	 * @param  string $columns
	 * @param  string $column
	 * @param  int    $id
	 * @return array
	 */
	public function food_menu_cat_column( $columns, $column, $id ) {
		if ( 'thumb' == $column ) {

			$thumbnail_id = get_restaurantpress_term_meta( $id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = rp_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: https://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'restaurantpress' ) . '" class="wp-post-image" height="48" width="48" />';

		}

		return $columns;
	}

	/**
	 * Description for food_menu_cat page to aid users.
	 */
	public function food_menu_cat_description() {
		echo wpautop( __( 'Menu Item categories for your food can be managed here. To see more categories listed click the "screen options" link at the top of the page.', 'restaurantpress' ) );
	}

	/**
	 * Maintain term hierarchy when editing a food.
	 * @param  array $args
	 * @return array
	 */
	public function disable_checked_ontop( $args ) {
		if ( ! empty( $args['taxonomy'] ) && 'food_menu_cat' === $args['taxonomy'] ) {
			$args['checked_ontop'] = false;
		}
		return $args;
	}
}

new RP_Admin_Taxonomies();
