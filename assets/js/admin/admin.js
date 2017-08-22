/* global restaurantpress_admin */
jQuery( function ( $ ) {

	if ( 'undefined' === typeof restaurantpress_admin ) {
		return;
	}

	// Add buttons to food item screen.
	var $food_item_screen = $( '.edit-php.post-type-food_menu' ),
		$title_action     = $food_item_screen.find( '.page-title-action:first' ),
		$blankslate       = $food_item_screen.find( '.restaurantpress-BlankState' );

	if ( 0 === $blankslate.length ) {
		$title_action.show();
	} else {
		$title_action.hide();
	}

	// Field validation error tips
	$( document.body )

		.on( 'rp_add_error_tip', function( e, element, error_type ) {
			var offset = element.position();

			if ( element.parent().find( '.rp_error_tip' ).length === 0 ) {
				element.after( '<div class="rp_error_tip ' + error_type + '">' + restaurantpress_admin[error_type] + '</div>' );
				element.parent().find( '.rp_error_tip' )
					.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.rp_error_tip' ).width() / 2 ) )
					.css( 'top', offset.top + element.height() )
					.fadeIn( '100' );
			}
		})

		.on( 'rp_remove_error_tip', function( e, element, error_type ) {
			element.parent().find( '.rp_error_tip.' + error_type ).fadeOut( '100', function() { $( this ).remove(); } );
		})

		.on( 'click', function() {
			$( '.rp_error_tip' ).fadeOut( '100', function() { $( this ).remove(); } );
		})

		.on( 'blur', '.rp_input_price[type=text]', function() {
			$( '.rp_error_tip' ).fadeOut( '100', function() { $( this ).remove(); } );
		})

		.on( 'change', '.rp_input_price[type=text]', function() {
			var regex;

			if ( $( this ).is( '.rp_input_price' ) ) {
				regex = new RegExp( '[^\-0-9\%\\' + restaurantpress_admin.mon_decimal_point + ']+', 'gi' );
			} else {
				regex = new RegExp( '[^\-0-9\%\\' + restaurantpress_admin.decimal_point + ']+', 'gi' );
			}

			var value    = $( this ).val();
			var newvalue = value.replace( regex, '' );

			if ( value !== newvalue ) {
				$( this ).val( newvalue );
			}
		})

		.on( 'keyup', '.rp_input_price[type=text]', function() {
			var regex, error;

			if ( $( this ).is( '.rp_input_price' ) ) {
				regex = new RegExp( '[^\-0-9\%\\' + restaurantpress_admin.mon_decimal_point + ']+', 'gi' );
				error = 'i18n_mon_decimal_error';
			} else {
				regex = new RegExp( '[^\-0-9\%\\' + restaurantpress_admin.decimal_point + ']+', 'gi' );
				error = 'i18n_decimal_error';
			}

			var value    = $( this ).val();
			var newvalue = value.replace( regex, '' );

			if ( value !== newvalue ) {
				$( document.body ).triggerHandler( 'rp_add_error_tip', [ $( this ), error ] );
			} else {
				$( document.body ).triggerHandler( 'rp_remove_error_tip', [ $( this ), error ] );
			}
		})

		.on( 'change', '#_sale_price.rp_input_price[type=text]', function() {
			var sale_price_field = $( this ),
				regular_price_field = $( '#_regular_price' );

			var sale_price    = parseFloat( window.accounting.unformat( sale_price_field.val(), restaurantpress_admin.mon_decimal_point ) );
			var regular_price = parseFloat( window.accounting.unformat( regular_price_field.val(), restaurantpress_admin.mon_decimal_point ) );

			if ( sale_price >= regular_price ) {
				$( this ).val( '' );
			}
		})

		.on( 'keyup', '#_sale_price.rp_input_price[type=text]', function() {
			var sale_price_field = $( this ),
				regular_price_field = $( '#_regular_price' );

			var sale_price    = parseFloat( window.accounting.unformat( sale_price_field.val(), restaurantpress_admin.mon_decimal_point ) );
			var regular_price = parseFloat( window.accounting.unformat( regular_price_field.val(), restaurantpress_admin.mon_decimal_point ) );

			if ( sale_price >= regular_price ) {
				$( document.body ).triggerHandler( 'rp_add_error_tip', [ $(this), 'i18_sale_less_than_regular_error' ] );
			} else {
				$( document.body ).triggerHandler( 'rp_remove_error_tip', [ $(this), 'i18_sale_less_than_regular_error' ] );
			}
		})

		.on( 'init_tooltips', function() {
			var tiptip_args = {
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 200
			};

			$( '.tips, .help_tip, .restaurantpress-help-tip' ).tipTip( tiptip_args );

			// Add tiptip to parent element for widefat tables
			$( '.parent-tips' ).each( function() {
				$( this ).closest( 'a, th' ).attr( 'data-tip', $( this ).data( 'tip' ) ).tipTip( tiptip_args ).css( 'cursor', 'help' );
			});
		});

	// Tooltips
	$( document.body ).trigger( 'init_tooltips' );
});
