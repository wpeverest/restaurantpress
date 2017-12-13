<?php
/**
 * The template for displaying food content within loops
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/content-food.php.
 *
 * HOWEVER, on occasion RestaurantPress will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.wpeverest.com/docs/restaurantpress/template-structure/
 * @author  WPEverest
 * @package RestaurantPress/Templates
 * @version 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $food;

// Ensure visibility
if ( empty( $food ) ) {
	return;
}
?>
<li <?php post_class(); ?>>

</li>
