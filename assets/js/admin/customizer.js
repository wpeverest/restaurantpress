/* global tinycolor */
( function( $ ) {

	function isDark( color ) {
		var rgb = tinycolor( color ).toRgb(),
			brightness = ( ( rgb.r * 299 ) + ( rgb.g * 587 ) + ( rgb.b * 114 ) ) / 1000;

		return brightness < 155;
	}

	function changeColor( color, adjustment, saturation ) {
		if ( isDark( color ) ) {
			return tinycolor( color ).lighten( adjustment ).desaturate( saturation ).toString();
		} else {
			return tinycolor( color ).darken( adjustment ).desaturate( saturation ).toString();
		}
	}

	function colorZeroPad( number ) {
		var total = 6 - number.length;

		if ( 0 === total ) {
			return number;
		}

		for ( var i = 0; i < total; i++ ) {
			number = '0' + number;
		}

		return number;
	}

	function subtractColor( color, subtract ) {
		return '#' + colorZeroPad( Math.abs( parseInt( color.replace( '#', '' ), 16 ) - parseInt( subtract.replace( '#', '' ), 16 ) ).toString( 16 ) );
	}

	// Primary Color.
	wp.customize( 'restaurantpress_colors[primary]', function( value ) {
		value.bind( function( primary ) {
			var css         = '',
				primaryText = changeColor( primary, 50, 18 );

			// Buttons.
			css += '.restaurantpress #respond input#submit.alt:hover, .restaurantpress a.button.alt:hover, .restaurantpress button.button.alt:hover, .restaurantpress input.button.alt:hover { background-color: ' + subtractColor( primary, '#111111' ) + '; color: ' + primaryText + ' }';

			// Chef badge.
			css += '.restaurantpress span.chef { background-color: ' + primary + '; color: ' + primaryText + '; }';

			$( '#restaurantpress-colors-primary' ).remove();
			$( 'head' ).append( '<style id="restaurantpress-colors-primary">' + css + '</style>' );
		});
	});

})( jQuery );
