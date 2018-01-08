<?php
/**
 * Customizer View: scss
 *
 * @package RestaurantPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

// Varibles.
$primary: <?php echo $colors['primary']; ?>;

.restaurantpress {

	span.chef {
		background-color: $primary;

		&.grid::before,
		&.grid::after {
			border-top-color: $primary;
		}
	}

	div.food_menu {
		span.price,
		p.price {
			color: $primary;
		}
	}

	div.foods {
		section.food_menu {
			.price {
				color: $primary;
			}

			.restaurantpress-loop-food__title a {
				color: $primary;
			}
		}
	}
}

.restaurantpress-group {
	#restaurant-press-section a {
		color: $primary;
	}

	.rp-grid-design-layout {
		ins .amount {
			color: #fff;
		}

		.rp-content-wrapper {
			border-bottom-color: $primary;

			span.price {
				background-color: $primary;

				&::before {
					border-right-color: $primary;
				}
			}
		}
	}

	.rp-list-design-layout {
		.rp-content-wrapper {
			p.price,
			span.price {
				color: $primary !important;
			}
		}
	}
}
