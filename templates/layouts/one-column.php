<?php
/**
 * Layout View: One Column
 *
 * This template can be overridden by copying it to yourtheme/restaurantpress/layouts/one-column.php.
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
		<div class="rp-single-column-layout rp-list-design-layout">
			<div class="rp-column-wrapper">
				<?php foreach ( $food_group as $food_id ) {
					if ( ! is_numeric( $food_id ) ) {
						continue;
					}

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

					?><div class="rp-column-1 rp-column-block rp-column-margin">
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
						<?php if ( ! empty( $food_data[ $food_id ] ) ) {
							foreach ( $food_data[ $food_id ] as $food_menu ) { ?>
								<div class="rp-column-single-block">
									<?php if ( 'no' == $featured_image ) : ?>
										<figure class ="rp-img">
											<?php if ( 'yes' === get_option( 'restaurantpress_enable_lightbox' ) && 'yes' == $food_menu['popup'] ) : ?>
												<a href="<?php echo $food_menu['attach_url']; ?>" itemprop="image" class="restaurentpress-main-image zoom"><?php echo $food_menu['image']; ?></a>
											<?php else : ?>
												<?php echo $food_menu['image']; ?>
											<?php endif; ?>
											<?php if ( 'yes' == $food_menu['chef_badge'] ) : ?>
												<mark class="rp-chef-badge"><i class="chef-icon"> </i></mark>
											<?php endif; ?>
										</figure>
									<?php endif; ?>
									<div class="rp-content-wrapper">
										<div class="rp-title-price-wrap">
											<h4 class="rp-title">
												<a href="<?php echo $food_menu['permalink']; ?>" class="restaurantpress-foodItem-link restaurantpress-loop-foodItem__link"><?php echo $food_menu['title']; ?></a>
											</h4>
											<span class="rp-price"><?php echo $food_menu['price']; ?></span>
										</div> <!--rp-title-price-wrap end -->
										<p class="rp-desc"><?php echo $food_menu['content']; ?></p>
									</div> <!--rp-content-wrapper end-->
								</div> <!--rp-column-single-block end -->
							<?php }
						} ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
