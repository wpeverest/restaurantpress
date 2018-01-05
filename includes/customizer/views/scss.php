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

	span.price::before {
		border-right-color: $primary;
	}
}

.restaurantpress-group {
	span.price {
		background: $primary;
	}

	.rp-list-design-layout {
		p.price,
		span.price {
			color: $primary;
		}
	}

	.rp-grid-design-layout {
		ins .amount {
			color: #fff;
		}

		.rp-content-wrapper {
			border-bottom-color: $primary;
		}
	}
}

.restaurantpress-page p.price,
.restaurantpress-page span.price {
	color: $primary;
}

.restaurantpress-group #restaurant-press-section a,
.restaurantpress-page .restaurantpress-loop-food__title a {
	color: $primary;
}
