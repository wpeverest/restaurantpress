jQuery( function( $ ) {

	/**
	 * Group actions
	 */
	var rp_meta_boxes_group_actions = {

		/**
		 * Initialize group actions.
		 */
		init: function() {
			$( 'select#layout_type' )
				.on( 'change', this.type_options )
				.change();
		},

		/**
		 * Show/hide fields by layout type options.
		 */
		type_options: function() {
			// Get value
			var select_val = $( this ).val();
			var is_feature = $( 'input#_featured_image:checked' ).length;

			// Shows rules
			if ( select_val === 'two_column' ) {
				$( '.show_if_two_column' ).show();
			} else {
				$( '.show_if_two_column' ).hide();
			}

			// Hide rules
			if ( select_val === 'grid_image' ) {
				$( '.hide_if_grid_image' ).hide();
			} else {
				$( '.hide_if_grid_image' ).show();
			}

			// Lightbox rules
			if ( is_feature && select_val === 'grid_image' ) {
				$( '.show_if_grid_image' ).show();
				$( 'input#_featured_image' ).prop( 'checked', false );
			}

			$( document.body ).trigger( 'restaurantpress-layout-type-change', select_val, $( this ) );
		}
	};

	rp_meta_boxes_group_actions.init();
});
