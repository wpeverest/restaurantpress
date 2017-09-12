<?php
/**
 * Layout View: Grid Image
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/layouts/grid-image.php.
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
<section id="restaurant-press-section">
	<div class="resturant-press-wrapper">
		<?php foreach ( $food_group as $food_id ) {
			if ( ! is_numeric( $food_id ) ) {
				continue;
			}

			$count = 1;
			$food_term = get_term_by( 'id', $food_id, 'food_menu_cat' );
			$term_id   = intval( $food_term->term_id );

			// Get post meta data
			$category_icon  = get_post_meta( $group_id, '_category_icon', true );
			$featured_image = get_post_meta( $group_id, '_featured_image', true );

			// Get category image
			$image = '';
			if ( $image_id = get_restaurantpress_term_meta( $term_id, 'thumbnail_id' ) ) {
				$image = wp_get_attachment_url( $image_id );
			}

			?><div class="rp-grid-design-layout">
				<h3 class="rp-category-title">
					<?php if ( 'yes' == $category_icon && $image ) : ?>
						<span class="category-icon"><img src="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" width="24px" height="24px"></span> <?php echo esc_html( $food_term->name ); ?>
					<?php else : ?>
						<?php echo esc_html( $food_term->name ); ?>
					<?php endif; ?>
				</h3>
				<?php if ( ! empty( $food_term->description ) ) : ?>
					<p><?php echo esc_html( $food_term->description ); ?></p>
				<?php endif; ?>
				<div class="rp-column-wrapper">
					<?php if ( ! empty( $food_data[ $food_id ] ) ) {
						foreach ( $food_data[ $food_id ] as $food_menu ) {
							$food = rp_get_food( $food_menu['post_id'] );
							?>
							<div class="rp-column-3 rp-column-margin">
								<?php if ( 'no' == $featured_image ) : ?>
									<figure class ="rp-img">
										<?php if ( 'yes' == $food_menu['popup'] ) : ?>
											<a href="<?php echo $food_menu['attach_url']; ?>" itemprop="image"><?php echo $food_menu['image_grid']; ?><span class="image-magnify"> <span> + </span> </span></a>
										<?php else : ?>
											<?php echo $food_menu['image_grid']; ?>
										<?php endif; ?>
										<?php if ( $food->is_chef_enable() ) : ?>
											<mark class="rp-chef-badge"><i class="chef-icon"> </i></mark>
										<?php endif; ?>
									</figure>
								<?php endif; ?>
								<div class="rp-content-wrapper">
									<div class="rp-title-price-wrap">
										<h4 class="rp-title">
											<?php if ( 'yes' === get_option( 'restaurantpress_single_page_display' ) ) : ?>
												<a href="<?php echo $food_menu['permalink']; ?>" class="restaurantpress-foodItem-link restaurantpress-loop-foodItem__link"><?php echo $food_menu['title']; ?></a>
											<?php else : ?>
												<?php echo $food_menu['title']; ?>
											<?php endif; ?>
										</h4>
									</div> <!--rp-title-price-wrap end -->
									<p class="rp-desc"><?php
										if ( $food_menu['excerpt'] ) {
											echo $food_menu['excerpt'];
										} else {
											echo rp_trim_string( $food_menu['content'], 255 );
										}
									?></p>
									<?php do_action( 'restaurantpress_after_food_loop_item', $food, 'grid_image' ); ?>
									<?php if ( $food->get_price_html() ) : ?>
										<span class="price"><?php echo $food->get_price_html(); ?></span>
									<?php endif; ?>
								</div> <!--rp-content-wrapper end-->
							</div> <!--rp-column-single-block end -->
							<?php if ( $count%3 == 0 ) {
								echo '<div class="clear"></div>';
							}

							$count++;
							?>
						<?php }
					} ?>
				</div> <!-- rp column wrapper end -->
			</div>
		<?php } ?>
	</div>
</section>
