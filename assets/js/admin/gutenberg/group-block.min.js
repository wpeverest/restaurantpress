/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * RestaurantPress Group Block
 *
 * A block for embedding a RestaurantPress into a post/page.
 */



/* global rp_group_block_data, wp */

var createElement = wp.element.createElement;
var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var _wp$components = wp.components,
    SelectControl = _wp$components.SelectControl,
    ToggleControl = _wp$components.ToggleControl,
    PanelBody = _wp$components.PanelBody,
    ServerSideRender = _wp$components.ServerSideRender,
    Placeholder = _wp$components.Placeholder;


var RestaurantPressIcon = createElement('svg', { width: 24, height: 24, viewBox: '0 0 24 24' }, createElement('path', { fill: 'currentColor', d: 'M22 18.11v.45a.67.67 0 0 1-.67.67H2.67a.67.67 0 0 1-.67-.67v-.45a.68.68 0 0 1 .67-.68h18.66a.68.68 0 0 1 .67.68zM21.39 16a.67.67 0 0 1-.67.68H3.28a.67.67 0 0 1-.67-.68 9.4 9.4 0 0 1 8-9.28 1.54 1.54 0 0 1-.06-.43 1.48 1.48 0 0 1 3 0 1.54 1.54 0 0 1-.06.43 9.4 9.4 0 0 1 7.9 9.28zm-9.87-7.49A.67.67 0 0 0 10.7 8a9 9 0 0 0-4.28 2.61 7.38 7.38 0 0 0-1.57 3.78.68.68 0 0 0 .62.73.68.68 0 0 0 .68-.62 5.93 5.93 0 0 1 1.25-3A7.82 7.82 0 0 1 11 9.32a.67.67 0 0 0 .49-.81z' }));

registerBlockType('restaurantpress/group-selector', {
	title: rp_group_block_data.i18n.title,
	description: rp_group_block_data.i18n.description,
	icon: RestaurantPressIcon,
	category: 'widgets',
	attributes: {
		groupId: {
			type: 'string'
		},
		orderBy: {
			type: 'string'
		},
		displayOrder: {
			type: 'boolean'
		}
	},
	edit: function edit(props) {
		var _props$attributes = props.attributes,
		    _props$attributes$gro = _props$attributes.groupId,
		    groupId = _props$attributes$gro === undefined ? '' : _props$attributes$gro,
		    _props$attributes$ord = _props$attributes.orderBy,
		    orderBy = _props$attributes$ord === undefined ? 'date' : _props$attributes$ord,
		    _props$attributes$dis = _props$attributes.displayOrder,
		    displayOrder = _props$attributes$dis === undefined ? false : _props$attributes$dis,
		    setAttributes = props.setAttributes;

		var groupOptions = rp_group_block_data.groups.map(function (value) {
			return { value: value.ID, label: value.post_title };
		});
		var orderByOptions = Object.keys(rp_group_block_data.orderby).map(function (index) {
			return { value: index, label: rp_group_block_data.orderby[index] };
		});

		var jsx = void 0;

		groupOptions.unshift({ value: '', label: rp_group_block_data.i18n.group_select });
		orderByOptions.unshift({ value: '', label: rp_group_block_data.i18n.order_select });

		function selectGroup(value) {
			setAttributes({ groupId: value });
		}

		function selectOrderBy(value) {
			setAttributes({ orderBy: value });
		}

		function toggleDisplayOrder(value) {
			setAttributes({ displayOrder: value });
		}

		jsx = [wp.element.createElement(
			InspectorControls,
			{ key: 'rp-gutenberg-group-selector-inspector-controls' },
			wp.element.createElement(
				PanelBody,
				{ title: rp_group_block_data.i18n.group_settings },
				wp.element.createElement(SelectControl, {
					label: rp_group_block_data.i18n.group_selected,
					value: groupId,
					options: groupOptions,
					onChange: selectGroup
				}),
				wp.element.createElement(SelectControl, {
					label: rp_group_block_data.i18n.orderby_selected,
					value: orderBy,
					options: orderByOptions,
					onChange: selectOrderBy
				}),
				wp.element.createElement(ToggleControl, {
					label: rp_group_block_data.i18n.order_toogle,
					help: rp_group_block_data.i18n.order_toogleHelp,
					checked: displayOrder,
					onChange: toggleDisplayOrder
				})
			)
		)];

		if (groupId) {
			jsx.push(wp.element.createElement(ServerSideRender, {
				key: 'rp-gutenberg-group-selector-server-side-renderer',
				block: 'restaurantpress/group-selector',
				attributes: props.attributes
			}));
		} else {
			jsx.push(wp.element.createElement(
				Placeholder,
				{
					key: 'rp-gutenberg-group-selector-wrap',
					icon: RestaurantPressIcon,
					instructions: rp_group_block_data.i18n.title,
					className: 'restaurantpress-gutenberg-group-selector-wrap' },
				wp.element.createElement(SelectControl, {
					key: 'rp-gutenberg-group-selector-select-control',
					value: groupId,
					options: groupOptions,
					onChange: selectGroup
				})
			));
		}

		return jsx;
	},
	save: function save() {
		return null;
	}
});

/***/ })
/******/ ]);