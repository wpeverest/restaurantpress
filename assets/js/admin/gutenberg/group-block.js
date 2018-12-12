/**
 * RestaurantPress Group Block
 *
 * A block for embedding a RestaurantPress into a post/page.
 */

'use strict';

/* global rp_group_block_data, wp */
const { createElement } = wp.element;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { SelectControl, ToggleControl, PanelBody, ServerSideRender, Placeholder } = wp.components;

const RestaurantPressIcon = createElement( 'svg', { width: 24, height: 24, viewBox: '0 0 24 24' },
	createElement( 'path', { fill: 'currentColor', d: 'M22 18.11v.45a.67.67 0 0 1-.67.67H2.67a.67.67 0 0 1-.67-.67v-.45a.68.68 0 0 1 .67-.68h18.66a.68.68 0 0 1 .67.68zM21.39 16a.67.67 0 0 1-.67.68H3.28a.67.67 0 0 1-.67-.68 9.4 9.4 0 0 1 8-9.28 1.54 1.54 0 0 1-.06-.43 1.48 1.48 0 0 1 3 0 1.54 1.54 0 0 1-.06.43 9.4 9.4 0 0 1 7.9 9.28zm-9.87-7.49A.67.67 0 0 0 10.7 8a9 9 0 0 0-4.28 2.61 7.38 7.38 0 0 0-1.57 3.78.68.68 0 0 0 .62.73.68.68 0 0 0 .68-.62 5.93 5.93 0 0 1 1.25-3A7.82 7.82 0 0 1 11 9.32a.67.67 0 0 0 .49-.81z' } )
);

registerBlockType( 'restaurantpress/group-selector', {
	title: rp_group_block_data.i18n.title,
	description: rp_group_block_data.i18n.description,
	icon: RestaurantPressIcon,
	category: 'widgets',
	attributes: {
		groupId: {
			type: 'string',
		},
		orderBy: {
			type: 'string',
		},
		displayOrder: {
			type: 'boolean',
		},
	},
	edit( props ) {
		const { attributes: { groupId = '', orderBy = 'date', displayOrder = false }, setAttributes } = props;
		const groupOptions = rp_group_block_data.groups.map( value => (
			{ value: value.ID, label: value.post_title }
		) );
		const orderByOptions = Object.keys( rp_group_block_data.orderby ).map( ( index ) => (
			{ value: index, label: rp_group_block_data.orderby[ index ] }
		) );

		let jsx;

		groupOptions.unshift( { value: '', label: rp_group_block_data.i18n.group_select } );
		orderByOptions.unshift( { value: '', label: rp_group_block_data.i18n.order_select } );

		function selectGroup( value ) {
			setAttributes( { groupId: value } );
		}

		function selectOrderBy( value ) {
			setAttributes( { orderBy: value } );
		}

		function toggleDisplayOrder( value ) {
			setAttributes( { displayOrder: value } );
		}

		jsx = [
			<InspectorControls key="rp-gutenberg-group-selector-inspector-controls">
				<PanelBody title={ rp_group_block_data.i18n.group_settings }>
					<SelectControl
						label={ rp_group_block_data.i18n.group_selected }
						value={ groupId }
						options={ groupOptions }
						onChange={ selectGroup }
					/>
					<SelectControl
						label={ rp_group_block_data.i18n.orderby_selected }
						value={ orderBy }
						options={ orderByOptions }
						onChange={ selectOrderBy }
					/>
					<ToggleControl
						label={ rp_group_block_data.i18n.order_toogle }
						help={ rp_group_block_data.i18n.order_toogleHelp }
						checked={ displayOrder }
						onChange={ toggleDisplayOrder }
					/>
				</PanelBody>
			</InspectorControls>
		];

		if ( groupId ) {
			jsx.push(
				<ServerSideRender
					key="rp-gutenberg-group-selector-server-side-renderer"
					block="restaurantpress/group-selector"
					attributes={ props.attributes }
				/>
			);
		} else {
			jsx.push(
				<Placeholder
					key="rp-gutenberg-group-selector-wrap"
					icon={ RestaurantPressIcon }
					instructions={ rp_group_block_data.i18n.title }
					className="restaurantpress-gutenberg-group-selector-wrap">
					<SelectControl
						key="rp-gutenberg-group-selector-select-control"
						value={ groupId }
						options={ groupOptions }
						onChange={ selectGroup }
					/>
				</Placeholder>
			);
		}

		return jsx;
	},
	save() {
		return null;
	},
} );
