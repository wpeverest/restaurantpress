<?php
/**
 * Food food Short Description
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/loop/short-description.php.
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

$short_description = apply_filters( 'restaurantpress_short_description', has_excerpt() ? get_the_excerpt() : get_the_content( __( 'Read more&hellip;', 'restaurantpress' ) ) );

if ( ! $short_description ) {
	return;
}

?>
<div class="restaurantpress-food-details__short-description">
	<?php echo $short_description; // WPCS: XSS ok. ?>
</div>
