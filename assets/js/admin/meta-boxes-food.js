jQuery( function( $ ) {

	// Scroll to first checked category - https://github.com/scribu/wp-category-checklist-tree/blob/d1c3c1f449e1144542efa17dde84a9f52ade1739/category-checklist-tree.php
	$( function() {
		$( '[id$="-all"] > ul.categorychecklist' ).each( function() {
			var $list = $( this );
			var $firstChecked = $list.find( ':checked' ).first();

			if ( ! $firstChecked.length ) {
				return;
			}

			var pos_first   = $list.find( 'input' ).position().top;
			var pos_checked = $firstChecked.position().top;

			$list.closest( '.tabs-panel' ).scrollTop( pos_checked - pos_first + 5 );
		});
	});

	// Featured Visibility.
	$( '#featured-visibility' ).find( '.edit-featured-visibility' ).click( function() {
		if ( $( '#featured-visibility-select' ).is( ':hidden' ) ) {
			$( '#featured-visibility-select' ).slideDown( 'fast' );
			$( this ).hide();
		}
		return false;
	});
	$( '#featured-visibility' ).find( '.save-post-visibility' ).click( function() {
		$( '#featured-visibility-select' ).slideUp( 'fast' );
		$( '#featured-visibility' ).find( '.edit-featured-visibility' ).show();

		var label = $( 'input[name=_featured]' ).data( 'no' );

		if ( $( 'input[name=_featured]' ).is( ':checked' ) ) {
			label = $( 'input[name=_featured]' ).data( 'yes' );
			$( 'input[name=_featured]' ).attr( 'checked', 'checked' );
		}

		$( '#featured-visibility-display' ).text( label );
		return false;
	});
	$( '#featured-visibility' ).find( '.cancel-post-visibility' ).click( function() {
		$( '#featured-visibility-select' ).slideUp( 'fast' );
		$( '#featured-visibility' ).find( '.edit-featured-visibility' ).show();

		var label = $( 'input[name=_featured]' ).data( 'no' );

		if ( 'yes' === $( '#current_featured' ).val() ) {
			label = $( 'input[name=_featured]' ).data( 'yes' );
			$( 'input[name=_featured]' ).attr( 'checked', 'checked' );
		} else {
			$( 'input[name=_featured]' ).removeAttr( 'checked' );
		}

		$( '#featured-visibility-display' ).text( label );
		return false;
	});

	// Food gallery file uploads.
	var food_gallery_frame;
	var $image_gallery_ids = $( '#food_image_gallery' );
	var $food_images       = $( '#food_images_container' ).find( 'ul.food_images' );

	$( '.add_food_images' ).on( 'click', 'a', function( event ) {
		var $el = $( this );

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( food_gallery_frame ) {
			food_gallery_frame.open();
			return;
		}

		// Create the media frame.
		food_gallery_frame = wp.media.frames.food_gallery = wp.media({
			// Set the title of the modal.
			title: $el.data( 'choose' ),
			button: {
				text: $el.data( 'update' )
			},
			states: [
				new wp.media.controller.Library({
					title: $el.data( 'choose' ),
					filterable: 'all',
					multiple: true
				})
			]
		});

		// When an image is selected, run a callback.
		food_gallery_frame.on( 'select', function() {
			var selection = food_gallery_frame.state().get( 'selection' );
			var attachment_ids = $image_gallery_ids.val();

			selection.map( function( attachment ) {
				attachment = attachment.toJSON();

				if ( attachment.id ) {
					attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
					var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

					$food_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
				}
			});

			$image_gallery_ids.val( attachment_ids );
		});

		// Finally, open the modal.
		food_gallery_frame.open();
	});

	// Image ordering.
	$food_images.sortable({
		items: 'li.image',
		cursor: 'move',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'rp-metabox-sortable-placeholder',
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
		},
		update: function() {
			var attachment_ids = '';

			$( '#food_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
				var attachment_id = $( this ).attr( 'data-attachment_id' );
				attachment_ids = attachment_ids + attachment_id + ',';
			});

			$image_gallery_ids.val( attachment_ids );
		}
	});

	// Remove images.
	$( '#food_images_container' ).on( 'click', 'a.delete', function() {
		$( this ).closest( 'li.image' ).remove();

		var attachment_ids = '';

		$( '#food_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
			var attachment_id = $( this ).attr( 'data-attachment_id' );
			attachment_ids = attachment_ids + attachment_id + ',';
		});

		$image_gallery_ids.val( attachment_ids );

		// Remove any lingering tooltips.
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );

		return false;
	});
});
