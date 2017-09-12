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
		return get_post_meta( $this->get_id(), '_price', true );
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
	 * If the food is featured.
	 *
	 * @return boolean
	 */
	public function get_featured() {
		return get_post_meta( $this->get_id(), '_featured', true );
	}

	/**
	 * Returns whether or not the product is on sale.
	 *
	 * @return string
	 */
	public function get_chef_badge() {
		return get_post_meta( $this->get_id(), '_chef_badge', true );
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
	 * Get tag ids.
	 *
	 * @return array
	 */
	public function get_tag_ids() {
		return get_the_terms( $this->get_id(), 'food_menu_tag' );
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
	 * Returns whether or not the product is featured.
	 *
	 * @return bool
	 */
	public function is_featured() {
		return true === rp_string_to_bool( $this->get_featured() );
	}

	/**
	 * Returns whether or not the food has chef flash.
	 *
	 * @return bool
	 */
	public function is_chef_enable() {
		return apply_filters( 'restaurantpress_food_is_chef_enable', 'yes' === $this->get_chef_badge() );
	}

	/**
	 * Get the suffix to display after prices > 0.
	 *
	 * @param  string $price to calculate, left blank to just use get_price()
	 * @return string
	 */
	public function get_price_suffix( $price = '' ) {
		if ( '' === $price ) {
			$price = $this->get_price();
		}
		return apply_filters( 'restaurantpress_get_price_suffix', '', $this, $price );
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
			$price = rp_format_sale_price( $this->get_regular_price(), $this->get_price() ). $this->get_price_suffix();
		} else {
			$price = rp_price( $this->get_price() );
		}

		return apply_filters( 'restaurantpress_get_price_html', $price, $this );
	}

	/**
	 * Returns the main food image.
	 *
	 * @param string $size (default: 'food_thumbnail')
	 * @param array $attr
	 * @param bool $placeholder True to return $placeholder if no image is found, or false to return an empty string.
	 * @return string
	 */
	public function get_image( $size = 'food_thumbnail', $attr = array(), $placeholder = true ) {
		if ( has_post_thumbnail( $this->get_id() ) ) {
			$image = get_the_post_thumbnail( $this->get_id(), $size, $attr );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $this->get_id() ) ) && has_post_thumbnail( $parent_id ) ) {
			$image = get_the_post_thumbnail( $parent_id, $size, $attr );
		} elseif ( $placeholder ) {
			$image = rp_placeholder_img( $size );
		} else {
			$image = '';
		}
		return apply_filters( 'restaurantpress_food_get_image', rp_get_relative_url( $image ), $this, $size, $attr, $placeholder );
	}
}
