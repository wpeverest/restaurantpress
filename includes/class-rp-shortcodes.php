<?php
/**
 * RestaurantPress Shortcodes.
 *
 * @class    RP_Shortcodes
 * @version  1.0.0
 * @package  RestaurantPress/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Shortcodes Class.
 */
class RP_Shortcodes {

	/**
	 * Init Shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'restaurantpress_menu' => __CLASS__ . '::menu',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Loop over found food menu.
	 * @param  array  $query_args
	 * @param  array  $food_group
	 * @param  array  $atts
	 * @param  string $loop_name
	 * @return string
	 */
	private static function food_menu_loop( $query_args, $food_group, $atts, $loop_name ) {
		$food_data = array();
		$food_menu = new WP_Query( apply_filters( 'restaurantpress_shortcode_food_menu_query', $query_args, $atts ) );

		ob_start();

		if ( $food_menu->have_posts() ) : ?>

			<?php do_action( "restaurantpress_shortcode_before_{$loop_name}_loop" ); ?>

				<?php while ( $food_menu->have_posts() ) : $food_menu->the_post(); ?>

					<?php $food_data = self::food_menu_data( $food_group, $food_data ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php do_action( "restaurantpress_shortcode_after_{$loop_name}_loop" ); ?>

		<?php endif;

		wp_reset_postdata();

		$output = self::food_menu_output( $food_group, $food_data, $atts );

		$css_class = 'restaurantpress';

		if ( isset( $atts['class'] ) ) {
			$css_class .= ' ' . $atts['class'];
		}

		return '<div class="' . esc_attr( $css_class ) . '">' . $output . ob_get_clean() . '</div>';
	}

	/**
	 * Data Storage for food menu.
	 * @param  array $food_group
	 * @param  array $food_data
	 * @return array
	 * @access private
	 */
	private static function food_menu_data( $food_group, $food_data ) {
		global $post;

		$food_terms = get_the_terms( $post->ID, 'food_menu_cat' );

		if ( $food_terms && ! is_wp_error( $food_terms ) ) {
			$title      = get_the_title();
			$content    = get_the_content();
			$price      = get_post_meta( $post->ID, 'food_item_price', true );
			$chef_badge = get_post_meta( $post->ID, 'chef_badge_item', true );

			$image_id   = get_post_thumbnail_id( $post ->ID );
			$attach_url = wp_get_attachment_url( $image_id );

			if ( has_post_thumbnail() ) {
				$image      = get_the_post_thumbnail( $post->ID, 'food_thumbnail' );
				$image_grid = get_the_post_thumbnail( $post->ID, 'food_grid' );
				$popup      = 'yes';
			} else {
				$image      = rp_placeholder_img();
				$image_grid = rp_placeholder_img( 'food_grid' );
				$popup      = 'no';
			}

			foreach ( $food_terms as $term ) {
				if ( in_array( $term->term_id, $food_group ) ) {
					$food_data[ $term->term_id ][] = array(
						'title'      => $title,
						'content'    => $content,
						'price'      => $price,
						'image'      => $image,
						'image_grid' => $image_grid,
						'popup'      => $popup,
						'chef_badge' => $chef_badge,
						'attach_url' => $attach_url
					);
				}
			}
		}

		return $food_data;
	}

	/**
	 * Output for food menu.
	 * @param  array $food_group
	 * @param  array $food_data
	 * @param  array $atts
	 * @return array
	 * @access private
	 */
	private static function food_menu_output( $food_group, $food_data, $atts ) {
		global $post;

		$group_id = absint( $atts['id'] );

		// Check for layout type
		if ( $layout_type = get_post_meta( $group_id, 'layout_type', true ) ) {
			$include_file = 'html-view-page-' . str_replace( '_', '-', $layout_type ) . '.php';
			$include_path = untrailingslashit( plugin_dir_path( RP_PLUGIN_FILE ) ) . '/includes/views/' . $include_file;

			// Include a layout specific file
			if ( $include_path && is_readable( $include_path ) ) {
				include( $include_path );
			}
		}
	}

	/**
	 * Group menu shortcode.
	 * @param  array $atts
	 * @return string
	 */
	public static function menu( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		if ( ! isset( $atts['id'] ) ) {
			return '';
		}

		$atts = shortcode_atts( array(
			'id'       => '',
			'class'    => '',
			'orderby'  => 'date',
			'order'    => 'desc',
			'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
		), $atts, 'restaurantpress_menu' );

		$group_id   = absint( $atts['id'] );
		$query_args = array(
			'post_type'      => 'food_menu',
			'post_status'    => 'publish',
			'orderby'        => $atts['orderby'],
			'order'          => $atts['order'],
			'posts_per_page' => -1
		);

		// Check for Group ID
		if ( $group_id && 'food_group' == get_post_type( $group_id ) ) {
			$food_group = (array) get_post_meta( $group_id, 'food_grouping', true );
			$query_args = self::_maybe_add_category_args( $query_args, $food_group, $atts['operator'] );
		} else {
			return '';
		}

		return self::food_menu_loop( $query_args, $food_group, $atts, 'food_menu' );
	}

	/**
	 * Adds a tax_query index to the query to filter by category.
	 * @param  array  $args
	 * @param  array  $category
	 * @param  string $operator
	 * @return array
	 * @access private
	 */
	private static function _maybe_add_category_args( $args, $category, $operator ) {
		if ( ! empty( $category ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'food_menu_cat',
					'terms'    => array_map( 'absint', $category ),
					'operator' => $operator
				)
			);
		}

		return $args;
	}
}
