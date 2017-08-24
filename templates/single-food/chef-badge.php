<?php
/**
 * Single Food Chef Badge
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/single-food/chef-badge.php.
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

global $post, $food;

?>
<?php if ( $food->is_chef_enable() ) : ?>

	<?php echo apply_filters( 'restaurantpress_chef_badge', '<span class="chef"><p class="screen-reader-text">' . esc_html__( 'Chef!', 'restaurantpress' ) . '</p></span>', $post, $food ); ?>

<?php endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
