<?php
/**
 * Single Food Chef Flash
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/single-product/chef-flash.php.
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

?>
<?php if ( 'yes' === get_post_meta( $post->ID, 'chef_badge_item', true ) ) : ?>

	<?php echo apply_filters( 'restaurantpress_chef_flash', '<span class="chef">' . esc_html__( 'Chef!', 'restaurantpress' ) . '</span>', $post ); ?>

<?php endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
