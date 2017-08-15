<?php
/**
 * The template for displaying food_menu content in the single-food_menu.php template
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

?>

<?php
	/**
	 * restaurantpress_before_single_food_menu hook.
	 */
	do_action( 'restaurantpress_before_single_food_menu' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}
?>

<div id="food-<?php the_ID(); ?>" <?php post_class(); ?>>

</div><!-- #food-<?php the_ID(); ?> -->

<?php do_action( 'restaurantpress_after_single_food_menu' ); ?>
