/* global tinymce */
( function() {

	/**
	 * Check is empty.
	 *
	 * @param  {string} value
	 * @return {bool}
	 */
	function rpShortcodesIsEmpty( value ) {
		value = value.toString();

		if ( 0 !== value.length ) {
			return false;
		}

		return true;
	}

	/**
	 * Add the shortcodes downdown.
	 */
	tinymce.PluginManager.add( 'restaurantpress_shortcodes', function( editor ) {
		var ed = tinymce.activeEditor;

		editor.addButton( 'restaurantpress_shortcodes', {
			title: ed.getLang( 'restaurantpress_shortcodes.shortcode_title' ),
			icon: 'restaurantpress-shortcodes',
			onclick: function() {
				editor.windowManager.open({
					title: ed.getLang( 'restaurantpress_shortcodes.shortcode_title' ),
					body: [
						{
							type:  'textbox',
							name:  'id',
							label: ed.getLang( 'restaurantpress_shortcodes.id' )
						},
						{
							type:   'listbox',
							name:   'orderby',
							label:  ed.getLang( 'restaurantpress_shortcodes.orderby' ),
							values: [
								{
									text: ed.getLang( 'restaurantpress_shortcodes.date' ),
									value: 'date'
								},
								{
									text: ed.getLang( 'restaurantpress_shortcodes.title' ),
									value: 'title'
								},
								{
									text: ed.getLang( 'restaurantpress_shortcodes.rand' ),
									value: 'rand'
								},
								{
									text: ed.getLang( 'restaurantpress_shortcodes.menu_order' ),
									value: 'menu_order'
								},
								{
									text: ed.getLang( 'restaurantpress_shortcodes.none' ),
									value: 'none'
								}
							]
						},
						{
							type:   'listbox',
							name:   'order',
							label:  ed.getLang( 'restaurantpress_shortcodes.order' ),
							values: [
								{
									text: ed.getLang( 'restaurantpress_shortcodes.desc' ),
									value: 'DESC'
								},
								{
									text: ed.getLang( 'restaurantpress_shortcodes.asc' ),
									value: 'ASC'
								}
							]
						}
					],
					onsubmit: function ( e ) {
						var id = rpShortcodesIsEmpty( e.data.id ) ? '' : ' id="' + e.data.id + '"';

						if ( ! rpShortcodesIsEmpty( e.data.id ) ) {
							editor.insertContent( '[restaurantpress_menu' + id + ' orderby="' + e.data.orderby + '" order="' + e.data.order + '"]' );
						} else {
							editor.windowManager.alert( ed.getLang( 'restaurantpress_shortcodes.need_group_id' ) );
						}
					}
				});
			}
		});
	});
})();
