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

const RestaurantPressIcon = createElement( 'svg', { width: 20, height: 20, viewBox: '0 0 24 24' },
	createElement( 'path', { fill: 'currentColor', d: 'M1024 135.296v-23.011c0-19.067-15.45-34.517-34.517-34.517h-954.966c-19.067 0-34.517 15.45-34.517 34.517v23.011c0 19.067 15.45 34.517 34.517 34.517h954.966c19.067 0 34.517-15.454 34.517-34.517zM992.935 245.022c0-19.067-15.45-34.517-34.517-34.517h-892.836c-19.067 0-34.517 15.45-34.517 34.517 0 240.477 177.417 440.292 408.229 475.444-2.080 6.922-3.231 14.239-3.231 21.828 0 41.871 34.066 75.937 75.937 75.937s75.937-34.066 75.937-75.937c0-7.589-1.151-14.907-3.231-21.828 230.812-35.147 408.229-234.968 408.229-475.444zM476.636 621.565c-4.648 18.492-23.389 29.68-41.89 25.068-5.486-1.381-135.403-34.908-219.122-134.040-70.985-84.055-79.982-189.051-80.328-193.478-1.496-19.003 12.698-35.621 31.705-37.113 0.92-0.074 1.832-0.106 2.743-0.106 17.838 0 32.952 13.724 34.374 31.811 0.069 0.865 7.446 87.084 64.257 154.35 68.854 81.533 181.402 111.158 183.238 111.637 18.464 4.657 29.661 23.393 25.022 41.871z' } )
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
	},
	edit( props ) {
		const { attributes: { groupId = '' }, setAttributes } = props;
		const groupOptions = rp_group_block_data.groups.map( value => (
			{ value: value.ID, label: value.post_title }
		) );
		let jsx;

		groupOptions.unshift( { value: '', label: rp_group_block_data.i18n.group_select } );

		function selectForm( value ) {
			setAttributes( { groupId: value } );
		}

		jsx = [
			<InspectorControls key="rp-gutenberg-group-selector-inspector-controls">
				<PanelBody title={ rp_group_block_data.i18n.group_settings }>
					<SelectControl
						label={ rp_group_block_data.i18n.group_selected }
						value={ groupId }
						options={ groupOptions }
						onChange={ selectForm }
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
						onChange={ selectForm }
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
