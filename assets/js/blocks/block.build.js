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
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__template_block__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__template_block___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__template_block__);

//import './popover/block';
//import './popup/block';

/***/ }),
/* 1 */
/***/ (function(module, exports) {


(function (wpI18n, wpBlocks, wpElement, wpEditor, wpComponents) {
  var __ = wpI18n.__;
  var registerBlockType = wpBlocks.registerBlockType;
  var Fragment = wpElement.Fragment;
  var InspectorControls = wpEditor.InspectorControls,
      InnerBlocks = wpEditor.InnerBlocks;
  var PanelBody = wpComponents.PanelBody,
      TextControl = wpComponents.TextControl,
      Disabled = wpComponents.Disabled;


  function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
  }

  registerBlockType('gtm/gtm-block', {
    // Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
    title: __('GTM Block'), // Block title.
    icon: 'screenoptions', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
    category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
    keywords: [__('gtm-block')],

    attributes: {
      blockId: {
        type: 'string',
        default: 'GTM000001'
      },
      blockHeading: {
        type: 'string',
        default: 'This Block is created for the template Plugin.'
      }
    },

    edit: function edit(props) {
      var attributes = props.attributes,
          setAttributes = props.setAttributes;

      // Creates a <p class='wp-block-cgb-block-inner-blocks'></p>.

      return [wp.element.createElement(
        InspectorControls,
        null,
        wp.element.createElement(
          PanelBody,
          { title: __('Block Settings') },
          wp.element.createElement(
            Disabled,
            null,
            wp.element.createElement(TextControl, {
              label: __('Block ID for your template:'),
              value: attributes.blockId
              //options={ [ { label: __( 'Has a blockId Membership' ), value: 'true' }, { label: __( 'Does not have a blockId Membership' ), value: 'false' } ] }
              , onChange: function onChange(blockId) {
                setAttributes({ blockId: blockId });
              }
            })
          )
        )
      ), wp.element.createElement(
        'div',
        { className: props.className, style: { borderWidth: 5,
            borderColor: getRandomColor(),
            borderStyle: "dashed" } },
        __(attributes.blockHeading),
        wp.element.createElement(InnerBlocks, null)
      )];
    },

    save: function save(props) {
      return wp.element.createElement(InnerBlocks.Content, null);
    }

  });
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.components);

/***/ })
/******/ ]);