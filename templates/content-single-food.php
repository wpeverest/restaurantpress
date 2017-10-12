<?php
/**
 * The template for displaying food content in the single-food.php template
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/single-food.php.
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

?>

<?php
	/**
	 * restaurantpress_before_single_food hook.
	 *
	 * @hooked rp_print_notices - 10
	 */
	do_action( 'restaurantpress_before_single_food' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}
?>

<div id="food-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * restaurantpress_before_single_food_summary hook.
		 *
		 * @hooked restaurantpress_show_food_chef_badge - 10
		 * @hooked restaurantpress_show_food_images - 20
		 */
		do_action( 'restaurantpress_before_single_food_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * restaurantpress_single_food_summary hook.
			 *
			 * @hooked restaurantpress_template_single_title - 5
			 * @hooked restaurantpress_template_single_price - 10
			 * @hooked restaurantpress_template_single_excerpt - 20
			 * @hooked restaurantpress_template_single_contact - 20
			 * @hooked restaurantpress_template_single_meta - 40
			 * @hooked restaurantpress_template_single_sharing - 50
			 */
			do_action( 'restaurantpress_single_food_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * restaurantpress_after_single_food_summary hook.
		 *
		 * @hooked restaurantpress_output_food_data_tabs - 10
		 */
		do_action( 'restaurantpress_after_single_food_summary' );
	?>

</div><!-- #food-<?php the_ID(); ?> -->

<?php do_action( 'restaurantpress_after_single_food_menu' ); ?>
