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
<?php

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
