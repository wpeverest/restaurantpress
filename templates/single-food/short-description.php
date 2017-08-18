<?php
/**
 * Single Food Short Description
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/single-food/short-description.php.
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
 * @version 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>
<div class="restaurantpress-food-details__short-description">
    <?php echo apply_filters( 'restaurantpress_short_description', $post->post_excerpt ); ?>
</div>
