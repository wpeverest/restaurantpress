<?php
/**
 * Loop Food Image
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/loop/food-image.php.
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

global $post, $food;
$thumbnail_size    = apply_filters( 'restaurantpress_food_thumbnails_large_size', 'full' );
$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
?>
<div class="restaurantpress-food-gallery">
	<figure class="restaurantpress-food-gallery__wrapper thumbnail">
		<?php
		$attributes = array(
			'title'                   => get_post_field( 'post_title', $post_thumbnail_id ),
			'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
			'data-src'                => $full_size_image[0],
			'data-large_image'        => $full_size_image[0],
			'data-large_image_width'  => $full_size_image[1],
			'data-large_image_height' => $full_size_image[2],
		);

		if ( has_post_thumbnail() ) {
			$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'food_thumbnail' ) . '" class="restaurantpress-food-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '">';
			$html .= restaurantpress_get_food_thumbnail( 'food_thumbnail', $attributes );
			$html .= '</a></div>';
		} else {
			$html  = '<div class="restaurantpress-food-gallery__image--placeholder">';
			$html .= restaurantpress_get_food_thumbnail();
			$html .= '</div>';
		}

		echo apply_filters( 'restaurantpress_loop_food_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );
		?>
	</figure>
</div>
