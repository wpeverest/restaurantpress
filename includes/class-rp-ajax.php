<?php
/**
 * RestaurantPress RP_AJAX
 *
 * AJAX Event Handler
 *
 * @class    RP_AJAX
 * @version  1.0.0
 * @package  RestaurantPress/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_AJAX Class.
 */
class RP_AJAX {

	/**
	 * Hooks in ajax handlers
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax)
	 */
	public static function add_ajax_events() {
		// restaurantpress_EVENT => nopriv
		$ajax_events = array(
			'feature_food'           => false,
			'json_search_categories' => false,
			'rated'                  => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_restaurantpress_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_restaurantpress_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Toggle Featured status of a food from admin.
	 */
	public static function feature_food() {
		if ( current_user_can( 'edit_food_menus' ) && check_admin_referer( 'restaurantpress-feature-food' ) ) {
			$food = rp_get_food( absint( $_GET['food_id'] ) );

			if ( $food ) {
				update_post_meta( $food->get_id(), '_featured', rp_string_to_bool( ! $food->get_featured() ) );
			}
		}

		wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=food_menu' ) );
		exit;
	}

	/**
	 * Search for categories and return json.
	 */
	public static function json_search_categories() {
		ob_start();

		check_ajax_referer( 'search-categories', 'security' );

		if ( ! current_user_can( 'edit_food_menus' ) ) {
			wp_die( -1 );
		}

		if ( ! $search_text = rp_clean( stripslashes( $_GET['term'] ) ) ) {
			wp_die();
		}

		$found_categories = array();
		$args             = array(
			'taxonomy'   => array( 'food_menu_cat' ),
			'orderby'    => 'id',
			'order'      => 'ASC',
			'hide_empty' => true,
			'fields'     => 'all',
			'name__like' => $search_text,
		);

		if ( $terms = get_terms( $args ) ) {
			foreach ( $terms as $term ) {
				$term->formatted_name = '';

				if ( $term->parent ) {
					$ancestors = array_reverse( get_ancestors( $term->term_id, 'food_menu_cat' ) );
					foreach ( $ancestors as $ancestor ) {
						if ( $ancestor_term = get_term( $ancestor, 'food_menu_cat' ) ) {
							$term->formatted_name .= $ancestor_term->name . ' > ';
						}
					}
				}

				$term->formatted_name .= $term->name . ' (' . $term->count . ')';
				$found_categories[ $term->term_id ] = $term;
			}
		}

		wp_send_json( apply_filters( 'restaurantpress_json_search_found_categories', $found_categories ) );
	}

	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function rated() {
		if ( ! current_user_can( 'manage_restaurantpress' ) ) {
			die(-1);
		}

		update_option( 'restaurantpress_admin_footer_text_rated', 1 );
		die();
	}
}

RP_AJAX::init();
