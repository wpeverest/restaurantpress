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

// Ensure visibility.
if ( empty( $food ) ) {
	return;
}
?>
<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * Hook: restaurantpress_menu_loop_item_summary.
		 *
		 * @hooked restaurantpress_show_food_loop_chef_badge - 10
		 * @hooked restaurantpress_template_loop_food_thumbnail - 10
		 */
		do_action( 'restaurantpress_before_menu_loop_item_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * Hook: restaurantpress_menu_loop_item_summary.
			 *
			 * @hooked restaurantpress_template_loop_food_title - 10
			 * @hooked restaurantpress_template_loop_price - 20
			 * @hooked restaurantpress_template_loop_excerpt - 30
			 */
			do_action( 'restaurantpress_menu_loop_item_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * Hook: restaurantpress_after_menu_loop_item_summary.
		 */
		do_action( 'restaurantpress_after_menu_loop_item_summary' );
	?>
</section>
