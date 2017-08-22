<?php
/**
 * Abstract Food Class
 *
 * The RestaurantPress food class handles individual food data.
 *
 * @class    RP_Food
 * @version  1.4.0
 * @package  RestaurantPress/Abstracts
 * @category Abstract Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Food Class.
 */
class RP_Food {

	/**
	 * ID for this object.
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Set ID.
	 *
	 * @since 1.4.0
	 * @param int $id
	 */
	public function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Returns the unique ID for this object.
	 *
	 * @since  1.4.0
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the food if ID is passed, otherwise the product is new and empty.
	 * This class should NOT be instantiated, but the rp_get_product() function
	 * should be used. It is possible, but the rp_get_product() is preferred.
	 *
	 * @param int|RP_Food|object $food Food to init.
	 */
	public function __construct( $food = 0 ) {
		if ( is_numeric( $food ) && $food > 0 ) {
			$this->set_id( $food );
		} elseif ( $food instanceof self ) {
			$this->set_id( absint( $food->get_id() ) );
		} elseif ( ! empty( $food->ID ) ) {
			$this->set_id( absint( $food->ID ) );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the food object.
	*/

	/**
	 * Returns the food's active price.
	 *
	 * @return string price
	 */
	public function get_price() {
		return $this->get_sale_price() ? $this->get_sale_price() : $this->get_regular_price();
	}

	/**
	 * Returns the food's regular price.
	 *
	 * @return string price
	 */
	public function get_regular_price() {
		return get_post_meta( $this->get_id(), '_regular_price', true );
	}

	/**
	 * Returns the food's sale price.
	 *
	 * @return string price
	 */
	public function get_sale_price() {
		return get_post_meta( $this->get_id(), '_sale_price', true );
	}

	/**
	 * Returns whether or not the product is on sale.
	 *
	 * @return string
	 */
	public function get_chef_badge() {
		return get_post_meta( $this->get_id(), 'chef_badge_item', true );
	}

	/**
	 * Get category ids.
	 *
	 * @return array
	 */
	public function get_category_ids() {
		return get_the_terms( $this->get_id(), 'food_menu_cat' );
	}

	/**
	 * Returns the gallery attachment ids.
	 *
	 * @return array
	 */
	public function get_gallery_image_ids() {
		return wp_parse_id_list( get_post_meta( $this->get_id(), '_food_image_gallery', true ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

	/**
	 * Returns whether or not the food has chef flash.
	 *
	 * @return bool
	 */
	public function is_chef_enable() {
		return apply_filters( 'restaurantpress_food_is_chef_enable', 'yes' === $this->get_chef_badge() );
	}

	/**
	 * Returns the price in html format.
	 *
	 * @return string
	 */
	public function get_price_html() {
		if ( '' === $this->get_price() ) {
			$price = apply_filters( 'restaurantpress_empty_price_html', '', $this );
		} elseif ( $this->get_sale_price() ) {
			$price = rp_format_sale_price( $this->get_regular_price(), $this->get_price() );
		} else {
			$price = rp_price( $this->get_price() );
		}

		return apply_filters( 'restaurantpress_get_price_html', $price, $this );
	}
}
