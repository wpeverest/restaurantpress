<?php
/**
 * Layout View: Grid Image
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

			?><div class="rp-grid-design-layout clearpress">
				<h3 class="rp-category-title">
					<?php if ( 'yes' == $category_icon && $image ) : ?>
						<span class="category-icon"><img src="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" width="24px" height="24px"></span> <?php echo esc_html( $food_term->name ); ?>
					<?php else : ?>
						<?php echo esc_html( $food_term->name ); ?>
					<?php endif; ?>
				</h3>
				<div class="rp-column-wrapper clearpress">
					<?php if ( ! empty( $food_data[ $food_id ] ) ) {
						foreach ( $food_data[ $food_id ] as $food_menu ) { ?>
							<div class="rp-column-3 clearpress rp-column-margin">
								<?php if ( 'no' == $featured_image ) : ?>
									<figure class ="rp-img">
										<?php if ( 'yes' === get_option( 'restaurantpress_enable_lightbox' ) && 'yes' == $food_menu['popup'] ) : ?>
											<a href="<?php echo $food_menu['attach_url']; ?>" itemprop="image" class="restaurentpress-main-image zoom" title="" data-rel="prettyPhoto"><?php echo $food_menu['image_grid']; ?><span class="image-magnify"> <span> + </span> </span></a>
										<?php else : ?>
											<?php echo $food_menu['image_grid']; ?>
										<?php endif; ?>
										<?php if ( 'yes' == $food_menu['chef_badge'] ) : ?>
											<mark class="rp-chef-badge"><i class="chef-icon"> </i></mark>
										<?php endif; ?>
									</figure>
								<?php endif; ?>
								<div class="rp-content-wrapper">
									<div class="rp-title-price-wrap clearpress">
										<h4 class="rp-title"><?php echo $food_menu['title']; ?></h4>
									</div> <!--rp-title-price-wrap end -->
									<div class="rp-desc"><?php echo $food_menu['content']; ?></div>
									<?php if ( ! empty( $food_menu['price'] ) ) : ?>
										<div class="rp-price"><?php echo $food_menu['price']; ?></div>
									<?php endif; ?>
								</div> <!--rp-content-wrapper end-->
							</div> <!--rp-column-single-block end -->
							<?php if ( $count%3 == 0 ) {
								echo '<div class="clearpress"></div>';
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
