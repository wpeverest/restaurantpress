/* global restaurantpress_settings_params */
( function( $ ) {

	// Color picker
	$( '.colorpick' )

		.iris({
			change: function( event, ui ) {
				$( this ).parent().find( '.colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
			},
			hide: true,
			border: true
		})

		.on( 'click focus', function( event ) {
			event.stopPropagation();
			$( '.iris-picker' ).hide();
			$( this ).closest( 'td' ).find( '.iris-picker' ).show();
			$( this ).data( 'original-value', $( this ).val() );
		})

		.on( 'change', function() {
			if ( $( this ).is( '.iris-error' ) ) {
				var original_value = $( this ).data( 'original-value' );

				if ( original_value.match( /^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/ ) ) {
					$( this ).val( $( this ).data( 'original-value' ) ).change();
				} else {
					$( this ).val( '' ).change();
				}
			}
		});

	$( 'body' ).on( 'click', function() {
		$( '.iris-picker' ).hide();
	});

	// Edit prompt
	$( function() {
		var changed = false;

		$( 'input, textarea, select, checkbox' ).change( function() {
			changed = true;
		});

		$( '.rp-nav-tab-wrapper a' ).click( function() {
			if ( changed ) {
				window.onbeforeunload = function() {
				    return restaurantpress_settings_params.i18n_nav_warning;
				};
			} else {
				window.onbeforeunload = '';
			}
		});

		$( '.submit input' ).click( function() {
			window.onbeforeunload = '';
		});
	});

	// Select all/none
	$( '.restaurantpress' ).on( 'click', '.select_all', function() {
		$( this ).closest( 'td' ).find( 'select option' ).attr( 'selected', 'selected' );
		$( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});

	$( '.restaurantpress' ).on( 'click', '.select_none', function() {
		$( this ).closest( 'td' ).find( 'select option' ).removeAttr( 'selected' );
		$( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});

	// Thumbnail cropping option updates and preview.
	$( '.restaurantpress-thumbnail-cropping' )
		.on( 'change input', 'input', function() {
			var value = $( '.restaurantpress-thumbnail-cropping input:checked' ).val(),
				$preview_images = $( '.restaurantpress-thumbnail-preview-block__image' );

			if ( 'custom' === value ) {

				var width_ratio  = Math.max( parseInt( $( 'input[name="thumbnail_cropping_aspect_ratio_width"]' ).val(), 10 ), 1 ),
					height_ratio = Math.max( parseInt( $( 'input[name="thumbnail_cropping_aspect_ratio_height"]' ).val(), 10 ), 1 ),
					height = ( 90 / width_ratio ) * height_ratio;

				$preview_images.animate( { height: height + 'px' }, 200 );

				$( '.restaurantpress-thumbnail-cropping-aspect-ratio' ).slideDown( 200 );

			} else if ( 'uncropped' === value ) {

				var heights = [ '120', '60', '80' ];

				$preview_images.each( function( index, element ) {
					var height = heights[ index ];
					$( element ).animate( { height: height + 'px' }, 200 );
				} );

				$( '.restaurantpress-thumbnail-cropping-aspect-ratio' ).hide();

			} else {
				$preview_images.animate( { height: '90px' }, 200 );

				$( '.restaurantpress-thumbnail-cropping-aspect-ratio' ).hide();
			}

			return false;
		});

	$( '.restaurantpress-thumbnail-cropping' ).find( 'input' ).change();

})( jQuery );
