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

	// Tooltips
	$( document.body ).on( 'init_tooltips', function() {
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
	}).trigger( 'init_tooltips' );
});
