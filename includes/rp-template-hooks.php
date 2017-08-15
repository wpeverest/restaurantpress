<?php
/**
 * RestaurantPress Template Hooks
 *
 * Action/filter hooks used for RestaurantPress functions/templates.
 *
 * @author   WPEverest
 * @category Core
 * @package  RestaurantPress/Templates
 * @version  1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'body_class', 'rp_body_class' );

