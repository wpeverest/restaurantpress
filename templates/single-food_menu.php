<?php
/**
 * The Template for displaying all single food item
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/single-food_menu.php.
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

get_header( 'food' ); ?>

	<?php
		/**
		 * restaurantpress_before_main_content hook.
		 *
		 * @hooked restaurantpress_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action( 'restaurantpress_before_main_content' );
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php rp_get_template_part( 'content', 'single-food' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * restaurantpress_after_main_content hook.
		 *
		 * @hooked restaurantpress_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'restaurantpress_after_main_content' );
	?>

	<?php
		/**
		 * restaurantpress_sidebar hook.
		 *
		 * @hooked restaurantpress_get_sidebar - 10
		 */
		do_action( 'restaurantpress_sidebar' );
	?>

<?php get_footer( 'food' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
