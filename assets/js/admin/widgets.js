/**
 * Widgets JS
 */
jQuery( document ).ready( function( $ ) {

	// Regular color Picker
	$( document ).on( 'ready widget-updated widget-added', function() {
		$( ':input.color-picker-field, :input.color-picker' ).filter( ':not(.enhanced)' ).each( function() {
			if ( ! $( this ).data( 'wpWpColorPicker') ) {
				$( this ).wpColorPicker({
					change: _.throttle( function() {
						$( this ).trigger( 'change' );
					}, 3000 )
				}).addClass( 'enhanced' );
			}
		});
	});
});
