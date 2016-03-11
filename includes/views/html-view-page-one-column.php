<?php
/**
 * Layout View: One Column
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<section id="restaurant-press-section">
	<div class="resturant-press-wrapper">
		<div class="rp-single-column-layout rp-list-design-layout clearpress">
			<div class="rp-column-wrapper clearpress">
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

					?><div class="rp-column-1 rp-column-block clearpress rp-column-margin">
						<h3 class="rp-category-title">
							<?php if ( 'yes' == $category_icon && $image ) : ?>
								<span class="category-icon"><img src="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" width="24px" height="24px"></span> <?php echo esc_html( $food_term->name ); ?>
							<?php else : ?>
								<?php echo esc_html( $food_term->name ); ?>
							<?php endif; ?>
						</h3>
						<?php if ( ! empty( $food_data[ $food_id ] ) ) {
							foreach ( $food_data[ $food_id ] as $food_menu ) { ?>
								<div class="rp-column-single-block clearpress">
									<?php if ( 'no' == $featured_image ) : ?>
										<figure class ="rp-img">
											<?php if ( 'yes' === get_option( 'restaurantpress_enable_lightbox' ) && 'yes' == $food_menu['popup'] ) : ?>
												<a href="<?php echo $food_menu['attach_url']; ?>" itemprop="image" class="restaurentpress-main-image zoom" title="" data-rel="prettyPhoto"><?php echo $food_menu['image']; ?></a>
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
											<h4 class="rp-title"><?php echo $food_menu['title']; ?></h4>
											<div class="rp-price"><?php echo $food_menu['price']; ?></div>
										</div> <!--rp-title-price-wrap end -->
										<div class="rp-desc"><?php echo $food_menu['content']; ?></div>
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
