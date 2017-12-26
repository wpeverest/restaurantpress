jQuery( function ( $ ) {

	// Allow Tabbing
	$( '#titlediv' ).find( '#title' ).keyup( function( event ) {
		var code = event.keyCode || event.which;

		// Tab key
		if ( code === '9' && $( '#restaurantpress-group-description' ).length > 0 ) {
			event.stopPropagation();
			$( '#restaurantpress-group-description' ).focus();
			return false;
		}
	});

	// Tabbed Panels
	$( document.body ).on( 'rp-init-tabbed-panels', function() {
		$( 'ul.rp-tabs' ).show();
		$( 'ul.rp-tabs a' ).click( function() {
			var panel_wrap = $( this ).closest( 'div.panel-wrap' );
			$( 'ul.rp-tabs li', panel_wrap ).removeClass( 'active' );
			$( this ).parent().addClass( 'active' );
			$( 'div.panel', panel_wrap ).hide();
			$( $( this ).attr( 'href' ) ).show();
			return false;
		});
		$( 'div.panel-wrap' ).each( function() {
			$( this ).find( 'ul.rp-tabs li' ).eq( 0 ).find( 'a' ).click();
		});
	}).trigger( 'rp-init-tabbed-panels' );
});
