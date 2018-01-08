( function( $ ) {

	// Primary Color.
	wp.customize( 'restaurantpress_colors[primary]', function( value ) {
		value.bind( function( primary ) {
			var css = '';

			// Chef badge.
			css += '.restaurantpress span.chef { background-color: ' + primary + '; }';
			css += '.restaurantpress span.chef.grid::before, .restaurantpress span.chef.grid::after { border-top-color: ' + primary + '; }';

			// Page price.
			css += '.restaurantpress-page p.price, .restaurantpress-page span.price { color: ' + primary + '; }';

			// Page title.
			css += '.restaurantpress-page .restaurantpress-loop-food__title a, .restaurantpress-group #restaurant-press-section a { color: ' + primary + '; }';

			// List layout page.
			css += '.restaurantpress-group .rp-list-design-layout p.price, .restaurantpress-group .rp-list-design-layout span.price { color: ' + primary + '; }';

			// Grid layout page.
			css += '.restaurantpress-group .rp-grid-design-layout ins .amount { color: #fff; }';
			css += '.restaurantpress-group .rp-grid-design-layout .rp-content-wrapper { border-bottom-color: ' + primary + '; }';
			css += '.restaurantpress-group .rp-grid-design-layout .rp-content-wrapper span.price { background-color: ' + primary + '; }';
			css += '.restaurantpress-group .rp-grid-design-layout .rp-content-wrapper span.price::before { border-right-color: ' + primary + '; }';

			$( '#restaurantpress-colors-primary' ).remove();
			$( 'head' ).append( '<style id="restaurantpress-colors-primary">' + css + '</style>' );
		} );
	} );

})( jQuery );
