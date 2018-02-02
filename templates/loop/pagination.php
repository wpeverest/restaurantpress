<?php
/**
 * Pagination - Show numbered pagination for menu pages
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/loop/pagination.php.
 *
 * HOWEVER, on occasion RestaurantPress will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.wpeverest.com/docs/restaurantpress/template-structure/
 * @package RestaurantPress/Templates
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

$total   = isset( $total ) ? $total : rp_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : rp_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}
?>
<nav class="restaurantpress-pagination">
	<?php
		echo paginate_links( apply_filters( 'restaurantpress_pagination_args', array( // WPCS: XSS ok.
			'base'         => $base,
			'format'       => $format,
			'add_args'     => false,
			'current'      => max( 1, $current ),
			'total'        => $total,
			'prev_text'    => '&larr;',
			'next_text'    => '&rarr;',
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3,
		) ) );
	?>
</nav>
