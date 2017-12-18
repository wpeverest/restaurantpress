<?php
/**
 * The Template for displaying food archives, including the main food page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/archive-food.php.
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

get_header( 'food' );

/**
 * Hook: restaurantpress_before_main_content.
 *
 * @hooked restaurantpress_output_content_wrapper - 10 (outputs opening divs for the content)
 */
do_action( 'restaurantpress_before_main_content' );

?>
<header class="restaurantpress-foods-header">
	<?php if ( apply_filters( 'restaurantpress_show_page_title', true ) ) : ?>
		<h1 class="restaurantpress-foods-header__title page-title"><?php restaurantpress_page_title(); ?></h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: restaurantpress_archive_description.
	 *
	 * @hooked restaurantpress_taxonomy_archive_description - 10
	 */
	do_action( 'restaurantpress_archive_description' );
	?>
</header>

<!-- Testing -->
<div class="foods columns-1">
	<section id="post-65" class="post-65 food_menu type-food_menu status-publish has-post-thumbnail hentry food_menu_cat-breakfast food_menu_tag-test-tag">
		<span class="chef"><p class="screen-reader-text">Chef!</p></span>
		<figure class="restaurantpress-food-gallery__wrapper thumbnail">
			<img width="180" height="180" src="//themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-180x180.jpg" class="attachment-food_thumbnail size-food_thumbnail wp-post-image" alt="" srcset="//themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-180x180.jpg 180w, //themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-150x150.jpg 150w, //themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-600x600.jpg 600w, //themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-300x300.jpg 300w" sizes="(max-width: 180px) 100vw, 180px">
		</figure>
		<div class="summary entry-summary">
			<h2 class="food_title entry-title">
				<a href="http://themegrill.io/food/cappuccino/" class="restaurantpress-foodItem-link restaurantpress-loop-foodItem__link">Cappuccino</a>
			</h4>
			<p class="price"><del><span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">$</span>‎100.00</span></del> <ins><span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">$</span>‎50.00</span></ins></p>
			<div class="restaurantpress-food-details__short-description">
				<p>Jelly beans biscuit danish tart sweet donut candy canes. Dragée caramels tootsie roll sweet chocolate jelly-o carrot cake cotton candy marshmallow. Pie gummies sweet roll chocolate carrot cake brownie. Ice cream carrot cake chocolate cake topping lollipop pie chupa chups.</p>
			</div>
		</div>
	</section>
	<section id="post-65" class="post-65 food_menu type-food_menu status-publish has-post-thumbnail hentry food_menu_cat-breakfast food_menu_tag-test-tag">
		<span class="chef"><p class="screen-reader-text">Chef!</p></span>
		<figure class="restaurantpress-food-gallery__wrapper thumbnail">
			<img width="180" height="180" src="//themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-180x180.jpg" class="attachment-food_thumbnail size-food_thumbnail wp-post-image" alt="" srcset="//themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-180x180.jpg 180w, //themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-150x150.jpg 150w, //themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-600x600.jpg 600w, //themegrill.io/wp-content/uploads/2015/10/Bagel-Ham-300x300.jpg 300w" sizes="(max-width: 180px) 100vw, 180px">
		</figure>
		<div class="summary entry-summary">
			<h2 class="food_title entry-title">
				<a href="http://themegrill.io/food/cappuccino/" class="restaurantpress-foodItem-link restaurantpress-loop-foodItem__link">Cappuccino</a>
			</h4>
			<p class="price"><del><span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">$</span>‎100.00</span></del> <ins><span class="restaurantpress-Price-amount amount"><span class="restaurantpress-Price-currencySymbol">$</span>‎50.00</span></ins></p>
			<div class="restaurantpress-food-details__short-description">
				<p>Jelly beans biscuit danish tart sweet donut candy canes. Dragée caramels tootsie roll sweet chocolate jelly-o carrot cake cotton candy marshmallow. Pie gummies sweet roll chocolate carrot cake brownie. Ice cream carrot cake chocolate cake topping lollipop pie chupa chups.</p>
			</div>
		</div>
	</section>
</div>
<!-- End testing -->
<?php

if ( have_posts() ) {

	/**
	 * Hook: restaurantpress_before_menu_loop.
	 *
	 * @hooked rp_print_notices - 10
	 */
	do_action( 'restaurantpress_before_menu_loop' );

	restaurantpress_food_loop_start();

	if ( rp_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: restaurantpress_menu_loop.
			 */
			do_action( 'restaurantpress_menu_loop' );

			rp_get_template_part( 'content', 'food' );
		}
	}

	restaurantpress_food_loop_end();

	/**
	 * Hook: restaurantpress_after_menu_loop.
	 *
	 * @hooked restaurantpress_pagination - 10
	 */
	do_action( 'restaurantpress_after_menu_loop' );
} else {
	/**
	 * Hook: restaurantpress_no_foods_found.
	 *
	 * @hooked rp_no_foods_found - 10
	 */
	do_action( 'restaurantpress_no_foods_found' );
}

/**
 * Hook: restaurantpress_after_main_content.
 *
 * @hooked restaurantpress_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'restaurantpress_after_main_content' );

/**
 * Hook: restaurantpress_sidebar.
 *
 * @hooked restaurantpress_get_sidebar - 10
 */
do_action( 'restaurantpress_sidebar' );

get_footer( 'food' );
