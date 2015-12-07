/* global rp_enhanced_select_params */
jQuery( function( $ ) {

	function getEnhancedSelectFormatString() {
		var formatString = {
			formatMatches: function( matches ) {
				if ( 1 === matches ) {
					return rp_enhanced_select_params.i18n_matches_1;
				}

				return rp_enhanced_select_params.i18n_matches_n.replace( '%qty%', matches );
			},
			formatNoMatches: function() {
				return rp_enhanced_select_params.i18n_no_matches;
			},
			formatAjaxError: function() {
				return rp_enhanced_select_params.i18n_ajax_error;
			},
			formatInputTooShort: function( input, min ) {
				var number = min - input.length;

				if ( 1 === number ) {
					return rp_enhanced_select_params.i18n_input_too_short_1;
				}

				return rp_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', number );
			},
			formatInputTooLong: function( input, max ) {
				var number = input.length - max;

				if ( 1 === number ) {
					return rp_enhanced_select_params.i18n_input_too_long_1;
				}

				return rp_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', number );
			},
			formatSelectionTooBig: function( limit ) {
				if ( 1 === limit ) {
					return rp_enhanced_select_params.i18n_selection_too_long_1;
				}

				return rp_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', limit );
			},
			formatLoadMore: function() {
				return rp_enhanced_select_params.i18n_load_more;
			},
			formatSearching: function() {
				return rp_enhanced_select_params.i18n_searching;
			}
		};

		return formatString;
	}

	$( document.body )

		.on( 'rp-enhanced-select-init', function() {

			// Regular select boxes
			$( ':input.rp-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
				var select2_args = $.extend({
					minimumResultsForSearch: 10,
					allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
					placeholder: $( this ).data( 'placeholder' )
				}, getEnhancedSelectFormatString() );

				$( this ).select2( select2_args ).addClass( 'enhanced' );
			});

			$( ':input.rp-enhanced-select-nostd' ).filter( ':not(.enhanced)' ).each( function() {
				var select2_args = $.extend({
					minimumResultsForSearch: 10,
					allowClear:  true,
					placeholder: $( this ).data( 'placeholder' )
				}, getEnhancedSelectFormatString() );

				$( this ).select2( select2_args ).addClass( 'enhanced' );
			});
		})

		.trigger( 'rp-enhanced-select-init' );

});
