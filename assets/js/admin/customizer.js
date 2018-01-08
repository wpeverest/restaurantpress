( function( $ ) {

	// Primary Color.
	wp.customize( 'restaurantpress_colors[primary]', function( value ) {
		value.bind( function( primary ) {
			var css = '';

			// Chef badge.
			css += '.restaurantpress span.chef { background-color: ' + primary + '; }';
			css += '.restaurantpress span.chef.grid::before, .restaurantpress span.chef.grid::after { border-top-color: ' + primary + '; }';

			// Food page.
			css += '.restaurantpress div.food_menu span.price, .restaurantpress div.food_menu p.price { color: ' + primary + '; }';

			// Food loop.
			css += '.restaurantpress div.foods section.food_menu .price { color: ' + primary + '; }';
			css += '.restaurantpress div.foods section.food_menu .restaurantpress-loop-food__title a { color: ' + primary + '; }';

			// Group layout page.
			css += '.restaurantpress-group .rp-grid-design-layout ins .amount { color: #fff; }';
			css += '.restaurantpress-group #restaurant-press-section a { color: ' + primary + '; }';
			css += '.restaurantpress-group .rp-grid-design-layout .rp-content-wrapper { border-bottom-color: ' + primary + '; }';
			css += '.restaurantpress-group .rp-grid-design-layout .rp-content-wrapper span.price { background-color: ' + primary + '; }';
			css += '.restaurantpress-group .rp-grid-design-layout .rp-content-wrapper span.price::before { border-right-color: ' + primary + '; }';
			css += '.restaurantpress-group .rp-list-design-layout .rp-content-wrapper p.price, .restaurantpress-group .rp-list-design-layout .rp-content-wrapper span.price { color: ' + primary + ' !important; }';

			$( '#restaurantpress-colors-primary' ).remove();
			$( 'head' ).append( '<style id="restaurantpress-colors-primary">' + css + '</style>' );
		} );
	} );

})( jQuery );
