( function( $ ) {

	// Primary Color.
	wp.customize( 'restaurantpress_colors[primary]', function( value ) {
		value.bind( function( primary ) {
			var css = '';

			// Chef badge.
			css += '.restaurantpress span.chef { background-color: ' + primary + '; }';
			css += '.restaurantpress span.chef.grid::before, .restaurantpress span.chef.grid::after { border-top-color: ' + primary + '; }';

			// Page title.
			css += '.restaurantpress-page .restaurantpress-loop-food__title a, .restaurantpress-group #restaurant-press-section a { color: ' + primary + '; }';

			// Group/page price.
			css += '.restaurantpress span.price::before { border-right-color: ' + primary + '; }';
			css += '.restaurantpress-group span.price { color: ' + primary + '; }';
			css += '.restaurantpress-group .rp-list-design-layout p.price, .restaurantpress-group .rp-list-design-layout span.price { color: ' + primary + '; }';

			// Group layout page.
			css += '.restaurantpress-group .rp-grid-design-layout ins .amount { color: #fff; }';
			css += '.restaurantpress-group .rp-grid-design-layout .rp-content-wrapper { border-bottom-color: ' + primary + '; }';

			$( '#restaurantpress-colors-primary' ).remove();
			$( 'head' ).append( '<style id="restaurantpress-colors-primary">' + css + '</style>' );
		} );
	} );

})( jQuery );
