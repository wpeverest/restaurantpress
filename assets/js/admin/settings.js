/* global restaurantpress_settings_params */
jQuery( window ).load( function() {

	// Color picker
	jQuery( '.colorpick' ).iris({
		change: function( event, ui ) {
			jQuery( this ).parent().find( '.colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
		},
		hide: true,
		border: true
	}).click( function() {
		jQuery( '.iris-picker' ).hide();
		jQuery( this ).closest( 'td' ).find( '.iris-picker' ).show();
	});

	jQuery( 'body' ).click( function() {
		jQuery( '.iris-picker' ).hide();
	});

	jQuery( '.colorpick' ).click( function( event ) {
		event.stopPropagation();
	});

	// Edit prompt
	jQuery( function() {
		var changed = false;

		jQuery( 'input, textarea, select, checkbox' ).change( function() {
			changed = true;
		});

		jQuery( '.rp-nav-tab-wrapper a' ).click( function() {
			if ( changed ) {
				window.onbeforeunload = function() {
				    return restaurantpress_settings_params.i18n_nav_warning;
				};
			} else {
				window.onbeforeunload = '';
			}
		});

		jQuery( '.submit input' ).click( function() {
			window.onbeforeunload = '';
		});
	});
});
