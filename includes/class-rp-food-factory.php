<?php
/**
 * Food Factory Class
 *
 * @class    RP_Food_Factory
 * @version  1.4.0
 * @package  RestaurantPress/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Food_Factory Class.
 */
class RP_Food_Factory {

	/**
	 * Get a food.
	 *
	 * @param mixed $food_id (default: false)
	 * @return RP_Food|bool Food object or null if the food cannot be loaded.
	 */
	public function get_food( $food_id = false ) {
		if ( ! $food_id = $this->get_food_id( $food_id ) ) {
			return false;
		}

		try {
			return new RP_Food( $food_id );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Get the food ID depending on what was passed.
	 *
	 * @param  mixed $food
	 * @return int|bool false on failure
	 */
	private function get_food_id( $food ) {
		if ( false === $food && isset( $GLOBALS['post'], $GLOBALS['post']->ID ) && 'food-menu' === get_post_type( $GLOBALS['post']->ID ) ) {
			return $GLOBALS['post']->ID;
		} elseif ( is_numeric( $food ) ) {
			return $food;
		} elseif ( $food instanceof RP_Food ) {
			return $food->get_id();
		} elseif ( ! empty( $food->ID ) ) {
			return $food->ID;
		} else {
			return false;
		}
	}
}
