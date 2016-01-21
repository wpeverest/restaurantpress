/**
 * Widgets JS
 */
jQuery( function ( $ ) {

	function initColorPicker( widget ) {
		widget.find( '.rp-color-picker' ).wpColorPicker({
			change: _.throttle( function () {
				$( this ).trigger( 'change' );
			}, 3000 ),
			palettes: [ '#000000', '#ffffff', '#B02B2C', '#edae44', '#eeee22', '#83a846', '#7bb0e7', '#745f7e', '#5f8789', '#d65799', '#4ecac2' ]
		});
	}

	$( document ).on( 'widget-added widget-updated', function( e, widget ) {
		initColorPicker( widget );
	});

	$( document ).ready( function() {
		$( '#widgets-right .widget:has(.rp-color-picker)' ).each( function() {
			initColorPicker( $( this ) );
		});
	});
});
