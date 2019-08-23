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
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
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
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

module.exports = _assertThisInitialized;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

module.exports = _classCallCheck;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/createClass.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/createClass.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

module.exports = _createClass;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

module.exports = _defineProperty;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _getPrototypeOf(o) {
  module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

module.exports = _getPrototypeOf;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/inherits.js":
/*!*********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/inherits.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf */ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js");

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) setPrototypeOf(subClass, superClass);
}

module.exports = _inherits;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/objectSpread.js":
/*!*************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/objectSpread.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var defineProperty = __webpack_require__(/*! ./defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      defineProperty(target, key, source[key]);
    });
  }

  return target;
}

module.exports = _objectSpread;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! ../helpers/typeof */ "./node_modules/@babel/runtime/helpers/typeof.js");

var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");

function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return assertThisInitialized(self);
}

module.exports = _possibleConstructorReturn;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _setPrototypeOf(o, p) {
  module.exports = _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}

module.exports = _setPrototypeOf;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/typeof.js":
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    module.exports = _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    module.exports = _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

module.exports = _typeof;

/***/ }),

/***/ "./node_modules/classnames/index.js":
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg) && arg.length) {
				var inner = classNames.apply(null, arg);
				if (inner) {
					classes.push(inner);
				}
			} else if (argType === 'object') {
				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if (typeof module !== 'undefined' && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ "./src/block.js":
/*!**********************!*\
  !*** ./src/block.js ***!
  \**********************/
/*! exports provided: RelatedPostsBlock, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "RelatedPostsBlock", function() { return RelatedPostsBlock; });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _data_data__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./data/data */ "./src/data/data.js");
/* harmony import */ var _components_posts_panel__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./components/posts-panel */ "./src/components/posts-panel.js");
/* harmony import */ var _components_image_panel__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./components/image-panel */ "./src/components/image-panel.js");
/* harmony import */ var _components_RestRequest__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./components/RestRequest */ "./src/components/RestRequest.js");









/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */

var InspectorControls = wp.editor.InspectorControls;
var _wp$components = wp.components,
    BaseControl = _wp$components.BaseControl,
    PanelBody = _wp$components.PanelBody,
    ToggleControl = _wp$components.ToggleControl,
    ServerSideRender = _wp$components.ServerSideRender,
    Disabled = _wp$components.Disabled;
var _wp$element = wp.element,
    Component = _wp$element.Component,
    Fragment = _wp$element.Fragment;
var __ = wp.i18n.__;
/**
 * Internal dependencies
 */





var instances = 0;
var RelatedPostsBlock =
/*#__PURE__*/
function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default()(RelatedPostsBlock, _Component);

  function RelatedPostsBlock() {
    var _this;

    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default()(this, RelatedPostsBlock);

    _this = _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(RelatedPostsBlock).apply(this, arguments)); // Data provided by this plugin.

    _this.previewExpanded = Object(_data_data__WEBPACK_IMPORTED_MODULE_10__["getPluginData"])('preview');
    _this.html5Gallery = Object(_data_data__WEBPACK_IMPORTED_MODULE_10__["getPluginData"])('html5_gallery');
    _this.defaultCategory = Object(_data_data__WEBPACK_IMPORTED_MODULE_10__["getPluginData"])('default_category');
    _this.updatePostTypes = _this.updatePostTypes.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this)); // The title is updated 1 second after a change.
    // This allows the user more time to type.

    _this.onTitleChange = _this.onTitleChange.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this));
    _this.titleDebounced = Object(lodash__WEBPACK_IMPORTED_MODULE_8__["debounce"])(_this.updateTitle, 1000);
    _this.toggleLinkCaption = _this.createToggleAttribute('link_caption');
    _this.toggleShowDate = _this.createToggleAttribute('show_date');
    _this.instanceId = instances++;
    return _this;
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default()(RelatedPostsBlock, [{
    key: "createToggleAttribute",
    value: function createToggleAttribute(propName) {
      var _this2 = this;

      return function () {
        var value = _this2.props.attributes[propName];
        var setAttributes = _this2.props.setAttributes;
        setAttributes(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()({}, propName, !value));
      };
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.titleDebounced.cancel();
    }
  }, {
    key: "onTitleChange",
    value: function onTitleChange(e) {
      // React pools events, so we read the value before debounce.
      // Alternately we could call `event.persist()` and pass the entire event.
      // For more info see reactjs.org/docs/events.html#event-pooling
      this.titleDebounced(e.target.value);
    }
  }, {
    key: "updateTitle",
    value: function updateTitle(value) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        title: value
      });
    }
  }, {
    key: "updatePostTypes",
    value: function updatePostTypes(postTypes) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        post_types: postTypes
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes,
          editorData = _this$props.editorData,
          postType = _this$props.postType,
          postID = _this$props.postID;
      var title = attributes.title,
          taxonomies = attributes.taxonomies,
          post_types = attributes.post_types,
          posts_per_page = attributes.posts_per_page,
          format = attributes.format,
          image_size = attributes.image_size,
          columns = attributes.columns,
          link_caption = attributes.link_caption,
          show_date = attributes.show_date,
          order = attributes.order,
          fields = attributes.fields;
      var titleID = 'inspector-text-control-' + this.instanceId;
      var className = classnames__WEBPACK_IMPORTED_MODULE_9___default()(this.props.className, {
        'rpbt-html5-gallery': 'thumbnails' === format && this.html5Gallery
      });

      if (Object(lodash__WEBPACK_IMPORTED_MODULE_8__["isUndefined"])(editorData)) {
        return null;
      }

      var shortcodeAttr = Object.assign({}, attributes);
      shortcodeAttr['post_id'] = postID;
      shortcodeAttr['terms'] = editorData.termIDs.join(',');

      if (!shortcodeAttr['terms'].length && -1 !== editorData.taxonomyNames.indexOf('category')) {
        // Use default category if this post supports the 'category' taxonomy and no terms are selected.
        shortcodeAttr['terms'] = this.defaultCategory;
      }

      var checkedPostTypes = post_types;

      if (Object(lodash__WEBPACK_IMPORTED_MODULE_8__["isUndefined"])(post_types) || !post_types) {
        // Use the post type from the current post if not set.
        checkedPostTypes = postType;
      }

      var inspectorControls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(PanelBody, {
        title: __('Related Posts Settings')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", {
        className: this.props.className + '-inspector-controls'
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("p", null, __('Note: The preview style is not the actual style used in the front end of your site.'))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(BaseControl, {
        label: __('Title'),
        id: titleID
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("input", {
        className: "components-text-control__input",
        type: "text",
        onChange: this.onTitleChange,
        defaultValue: title,
        id: titleID
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_components_posts_panel__WEBPACK_IMPORTED_MODULE_11__["default"], {
        postsPerPage: posts_per_page,
        onPostsPerPageChange: function onPostsPerPageChange(value) {
          // Don't allow 0 as a value.
          var newValue = 0 === Number(value) ? 1 : value;
          setAttributes({
            posts_per_page: Number(newValue)
          });
        },
        taxonomies: taxonomies,
        onTaxonomiesChange: function onTaxonomiesChange(value) {
          return setAttributes({
            taxonomies: value
          });
        },
        format: format,
        onFormatChange: function onFormatChange(value) {
          return setAttributes({
            format: value
          });
        },
        order: order,
        onOrderChange: function onOrderChange(value) {
          return setAttributes({
            order: value
          });
        },
        showDate: show_date,
        onShowDateChange: this.toggleShowDate,
        postTypes: checkedPostTypes,
        onPostTypesChange: this.updatePostTypes
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(PanelBody, {
        title: __('Image Settings')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_components_image_panel__WEBPACK_IMPORTED_MODULE_12__["default"], {
        imageSize: image_size,
        onImageSizeChange: function onImageSizeChange(value) {
          return setAttributes({
            image_size: value
          });
        },
        columns: columns,
        onColumnsChange: function onColumnsChange(value) {
          return setAttributes({
            columns: Number(value)
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(ToggleControl, {
        label: __(' Link image captions to posts'),
        checked: link_caption,
        onChange: this.toggleLinkCaption
      })));
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(Fragment, null, inspectorControls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", {
        className: className
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_components_RestRequest__WEBPACK_IMPORTED_MODULE_13__["default"], {
        block: "related-posts-by-taxonomy/related-posts-block",
        postID: postID,
        attributes: shortcodeAttr
      })));
    }
  }]);

  return RelatedPostsBlock;
}(Component);
/* harmony default export */ __webpack_exports__["default"] = (RelatedPostsBlock);

/***/ }),

/***/ "./src/components/RestRequest.js":
/*!***************************************!*\
  !*** ./src/components/RestRequest.js ***!
  \***************************************/
/*! exports provided: rendererPath, RestRequest, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "rendererPath", function() { return rendererPath; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "RestRequest", function() { return RestRequest; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/objectSpread */ "./node_modules/@babel/runtime/helpers/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_7__);








/**
 * External dependencies.
 */

/**
 * WordPress dependencies.
 */

var _wp$element = wp.element,
    Component = _wp$element.Component,
    RawHTML = _wp$element.RawHTML;
var _wp$components = wp.components,
    Placeholder = _wp$components.Placeholder,
    Spinner = _wp$components.Spinner;
var _wp$i18n = wp.i18n,
    __ = _wp$i18n.__,
    sprintf = _wp$i18n.sprintf;
var apiFetch = wp.apiFetch;
var addQueryArgs = wp.url.addQueryArgs;
function rendererPath(postID) {
  var attributes = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var urlQueryArgs = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var queryArgs = null !== attributes ? attributes : {}; // Defaults

  queryArgs.gallery_format = 'editor_block';
  queryArgs.is_editor = true;
  queryArgs.link_caption = false;
  return addQueryArgs("/related-posts-by-taxonomy/v1/posts/".concat(postID), _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_5___default()({}, queryArgs, urlQueryArgs));
}
var RestRequest =
/*#__PURE__*/
function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default()(RestRequest, _Component);

  function RestRequest(props) {
    var _this;

    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, RestRequest);

    _this = _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default()(RestRequest).call(this, props));
    _this.state = {
      response: null
    };
    return _this;
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(RestRequest, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.isStillMounted = true;
      this.fetch(this.props); // Only debounce once the initial fetch occurs to ensure that the first
      // renders show data as soon as possible.

      this.fetch = Object(lodash__WEBPACK_IMPORTED_MODULE_7__["debounce"])(this.fetch, 500);
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.isStillMounted = false;
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (!Object(lodash__WEBPACK_IMPORTED_MODULE_7__["isEqual"])(prevProps, this.props)) {
        this.fetch(this.props);
      }
    }
  }, {
    key: "fetch",
    value: function fetch(props) {
      var _this2 = this;

      if (!this.isStillMounted) {
        return;
      }

      if (null !== this.state.response) {
        this.setState({
          response: null
        });
      }

      var postID = props.postID,
          _props$attributes = props.attributes,
          attributes = _props$attributes === void 0 ? null : _props$attributes,
          _props$urlQueryArgs = props.urlQueryArgs,
          urlQueryArgs = _props$urlQueryArgs === void 0 ? {} : _props$urlQueryArgs;
      var path = rendererPath(postID, attributes, urlQueryArgs); // Store the latest fetch request so that when we process it, we can
      // check if it is the current request, to avoid race conditions on slow networks.

      var fetchRequest = this.currentFetchRequest = apiFetch({
        path: path
      }).then(function (response) {
        if (_this2.isStillMounted && fetchRequest === _this2.currentFetchRequest && response && response.rendered) {
          _this2.setState({
            response: response.rendered
          });
        }
      }).catch(function (error) {
        if (_this2.isStillMounted && fetchRequest === _this2.currentFetchRequest) {
          _this2.setState({
            response: {
              error: true,
              errorMsg: error.message
            }
          });
        }
      });
      return fetchRequest;
    }
  }, {
    key: "render",
    value: function render() {
      var response = this.state.response;

      if (!response) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(Placeholder, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(Spinner, null));
      } else if (response.error) {
        // translators: %s: error message describing the problem
        var errorMessage = sprintf(__('Error loading post: %s'), response.errorMsg);
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(Placeholder, null, errorMessage);
      } else if (!response.length) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(Placeholder, null, __('No results found.'));
      }

      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(RawHTML, {
        key: "html"
      }, response);
    }
  }]);

  return RestRequest;
}(Component);
/* harmony default export */ __webpack_exports__["default"] = (RestRequest);

/***/ }),

/***/ "./src/components/image-panel.js":
/*!***************************************!*\
  !*** ./src/components/image-panel.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ImagePanel; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _data_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../data/data */ "./src/data/data.js");
/* harmony import */ var _data_options__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../data/options */ "./src/data/options.js");


/**
 * WordPress dependencies
 */
var __ = wp.i18n.__;
var _wp$components = wp.components,
    SelectControl = _wp$components.SelectControl,
    RangeControl = _wp$components.RangeControl;
/**
 * Internal dependencies
 */


 // Select input options

var imageOptions = Object(_data_options__WEBPACK_IMPORTED_MODULE_2__["getOptions"])('image_sizes');
function ImagePanel(_ref) {
  var imageSize = _ref.imageSize,
      onImageSizeChange = _ref.onImageSizeChange,
      columns = _ref.columns,
      onColumnsChange = _ref.onColumnsChange;
  return [onImageSizeChange && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
    key: "rpbt-select-image-size",
    label: __('Image Size'),
    value: "".concat(imageSize),
    options: imageOptions,
    onChange: function onChange(value) {
      onImageSizeChange(value);
    }
  }), onColumnsChange && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(RangeControl, {
    key: "rpbt-range-columns",
    label: __('Image Columns'),
    value: columns,
    onChange: onColumnsChange,
    min: 0,
    max: 20
  })];
}

/***/ }),

/***/ "./src/components/post-type-control.js":
/*!*********************************************!*\
  !*** ./src/components/post-type-control.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _data_data__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../data/data */ "./src/data/data.js");







/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

var BaseControl = wp.components.BaseControl;
var withInstanceId = wp.compose.withInstanceId;
var Component = wp.element.Component;
/**
 * Internal dependencies
 */



function getPostTypeObjects() {
  var checkedPostTypes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var postTypeOjects = [];
  var postTypes = Object(_data_data__WEBPACK_IMPORTED_MODULE_7__["getPluginData"])('post_types');

  for (var key in postTypes) {
    if (!postTypes.hasOwnProperty(key)) {
      continue;
    }

    postTypeOjects.push({
      post_type: key,
      label: postTypes[key],
      checked: -1 !== checkedPostTypes.indexOf(key)
    });
  }

  return postTypeOjects;
}

var PostTypeControl =
/*#__PURE__*/
function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default()(PostTypeControl, _Component);

  function PostTypeControl() {
    var _this;

    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, PostTypeControl);

    _this = _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default()(PostTypeControl).apply(this, arguments));
    var postTypes = _this.props.postTypes; // Set the state with post type objects.

    _this.state = {
      items: getPostTypeObjects(postTypes.split(","))
    };
    return _this;
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(PostTypeControl, [{
    key: "onChange",
    value: function onChange(index) {
      // Update the state.
      var newItems = this.state.items.slice();
      newItems[index].checked = !newItems[index].checked;
      this.setState({
        items: newItems
      });
      var checked = this.state.items.filter(function (item) {
        return item.checked;
      });
      var postTypes = checked.map(function (obj) {
        return obj.post_type;
      });

      if (this.props.onChange) {
        this.props.onChange(postTypes.join(','));
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props = this.props,
          label = _this$props.label,
          help = _this$props.help,
          instanceId = _this$props.instanceId,
          postTypes = _this$props.postTypes;
      var id = 'inspector-multi-checkbox-control-' + instanceId;
      var describedBy;

      if (help) {
        describedBy = id + '__help';
      }

      var checked = postTypes.split(",");
      checked = checked.filter(function (item) {
        return Object(_data_data__WEBPACK_IMPORTED_MODULE_7__["inPluginData"])('post_types', item);
      });
      return !Object(lodash__WEBPACK_IMPORTED_MODULE_6__["isEmpty"])(this.state.items) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(BaseControl, {
        label: label,
        id: id,
        help: help,
        className: "blocks-checkbox-control"
      }, this.state.items.map(function (option, index) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("div", {
          key: id + '-' + index,
          className: "blocks-checkbox-control__option"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("input", {
          id: id + '-' + index,
          className: "blocks-checkbox-control__input",
          type: "checkbox",
          name: id + '-' + index,
          value: option.post_type,
          onChange: _this2.onChange.bind(_this2, index),
          checked: !(checked.indexOf(option.post_type) === -1),
          "aria-describedby": !!help ? id + '__help' : undefined
        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("label", {
          key: option.post_type,
          htmlFor: id + '-' + index
        }, option.label));
      }));
    }
  }]);

  return PostTypeControl;
}(Component);

/* harmony default export */ __webpack_exports__["default"] = (withInstanceId(PostTypeControl));

/***/ }),

/***/ "./src/components/posts-panel.js":
/*!***************************************!*\
  !*** ./src/components/posts-panel.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return PostsPanel; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _data_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../data/data */ "./src/data/data.js");
/* harmony import */ var _data_options__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../data/options */ "./src/data/options.js");
/* harmony import */ var _components_post_type_control__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../components/post-type-control */ "./src/components/post-type-control.js");


/**
 * WordPress dependencies
 */
var __ = wp.i18n.__;
var _wp$components = wp.components,
    SelectControl = _wp$components.SelectControl,
    RangeControl = _wp$components.RangeControl,
    ToggleControl = _wp$components.ToggleControl;
/**
 * Internal dependencies
 */



 // Select input options

var taxonomyOptions = getTaxonomyOptions();
var formatOptions = Object(_data_options__WEBPACK_IMPORTED_MODULE_2__["getOptions"])('formats');
var orderOptions = Object(_data_options__WEBPACK_IMPORTED_MODULE_2__["getOptions"])('order');
function PostsPanel(_ref) {
  var taxonomies = _ref.taxonomies,
      onTaxonomiesChange = _ref.onTaxonomiesChange,
      postsPerPage = _ref.postsPerPage,
      onPostsPerPageChange = _ref.onPostsPerPageChange,
      format = _ref.format,
      onFormatChange = _ref.onFormatChange,
      showDate = _ref.showDate,
      onShowDateChange = _ref.onShowDateChange,
      postTypes = _ref.postTypes,
      onPostTypesChange = _ref.onPostTypesChange,
      order = _ref.order,
      onOrderChange = _ref.onOrderChange;
  return [onPostsPerPageChange && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(RangeControl, {
    key: "rpbt-range-posts-per-page",
    label: __('Number of items'),
    value: postsPerPage,
    onChange: onPostsPerPageChange,
    min: -1,
    max: 100
  }), onTaxonomiesChange && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
    key: "rpbt-select-taxonomies",
    label: __('Taxonomies'),
    value: "".concat(taxonomies),
    options: taxonomyOptions,
    onChange: function onChange(value) {
      onTaxonomiesChange(value);
    }
  }), onPostTypesChange && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_post_type_control__WEBPACK_IMPORTED_MODULE_3__["default"], {
    label: __('Post Types'),
    onChange: onPostTypesChange,
    postTypes: postTypes
  }), onOrderChange && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
    key: "rpbt-select-order",
    label: __('Order posts'),
    value: "".concat(order),
    options: orderOptions,
    onChange: function onChange(value) {
      onOrderChange(value);
    }
  }), onFormatChange && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
    key: "rpbt-select-format",
    label: __('Format'),
    value: "".concat(format),
    options: formatOptions,
    onChange: function onChange(value) {
      onFormatChange(value);
    }
  }), onShowDateChange && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ToggleControl, {
    label: __('Display post date'),
    checked: showDate,
    onChange: onShowDateChange
  })];
}

function getTaxonomyOptions() {
  var options = [{
    label: __('all taxonomies'),
    value: 'km_rpbt_all_tax'
  }];
  return Object(_data_options__WEBPACK_IMPORTED_MODULE_2__["getOptions"])('taxonomies', options);
}

/***/ }),

/***/ "./src/data/data.js":
/*!**************************!*\
  !*** ./src/data/data.js ***!
  \**************************/
/*! exports provided: _pluginData, hasData, inPluginData, getPluginData */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "_pluginData", function() { return _pluginData; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "hasData", function() { return hasData; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "inPluginData", function() { return inPluginData; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getPluginData", function() { return getPluginData; });
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/**
 * External dependencies
 */

/**
 * Don't use _pluginData directly, use getPluginData()
 */

var _pluginData = window.km_rpbt_plugin_data || {};
var _defaults = {
  post_types: {
    type: 'object'
  },
  taxonomies: {
    type: 'object'
  },
  formats: {
    type: 'object'
  },
  order: {
    type: 'object'
  },
  image_sizes: {
    type: 'object'
  },
  default_tax: {
    type: 'string'
  },
  all_tax: {
    type: 'string'
  },
  default_category: {
    type: 'string'
  },
  preview: {
    type: 'bool',
    default: true
    /* default required for booleans */

  },
  html5_gallery: {
    type: 'bool',
    default: false
  },
  show_date: {
    type: 'bool',
    default: false
  }
  /**
   * Check if a property key exists and has a value.
   * 
   * @param  {string} key Property to check.
   * @return {[type]}     Returns true if it exists
   */

};
function hasData(object, key) {
  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isObject"])(object) && object.hasOwnProperty(key)) {
    return !Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isUndefined"])(object[key]);
  }

  return false;
}
/**
 * Check if a value exists in a plugin data property.
 * 
 * @param  {string} key  Plugin data key.
 * @param  {string} value Value to test.
 * @return {bool}   True if value exists.
 */

function inPluginData(key, value) {
  return hasData(getPluginData(key), value);
}
/**
 * Get data provided by this plugin.
 *
 * Only returns data if it's the correct type.
 * Else returns empty value of the correct type.
 * 
 * @param  {string} key Property key in the plugin data.
 * @return {[type]}     Plugin data.
 */

function getPluginData(key) {
  var defaultValue = getDefault(key);

  if (!hasData(_pluginData, key) || Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isUndefined"])(defaultValue)) {
    return defaultValue;
  }

  var data = _pluginData[key];
  var dataType = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["get"])(_defaults, key + '.type');
  return isType(dataType, data) ? data : defaultValue;
}
/**
 * Get the default value for a setting.
 *
 * Booleans should always provide a default value.
 * If no default is provided an empty value with 
 * the correct type is returned.
 * 
 * @param  {string} key Plugin data property key.
 * @return {object|string|bool} Default value.
 */

function getDefault(key) {
  // Types to check. Booleans should have a default.
  var types = {
    object: {},
    string: ''
  };
  var keyValue = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["get"])(_defaults, key + '.default');
  var keyDefault = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["get"])(types, Object(lodash__WEBPACK_IMPORTED_MODULE_0__["get"])(_defaults, key + '.type'));
  return !Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isUndefined"])(keyValue) ? keyValue : keyDefault;
}
/**
 * Check if a value has the correct type.
 *
 * @param  {string}             type  Type of value. Accepts 'bool', 'object' and 'string'.
 * @param  {bool|object|string} value Value.
 * @return {Boolean} True if the value is of the correct type.
 */


function isType(type, value) {
  var is_type = false;

  switch (type) {
    case 'bool':
      value = getBool(value);
      is_type = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isBoolean"])(value);
      break;

    case 'object':
      is_type = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isObject"])(value);
      break;

    case 'string':
      is_type = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isString"])(value);
      break;
  }

  return is_type;
}
/**
 * Get a boolean value from a string.
 *
 * wp_localize_script converts booleans to a string ('1' or '').
 *
 * @param  {string} value String with boolean value.
 * @return {bool} Boolean value if string is '1' or empty.
 */


function getBool(value) {
  if (!Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isString"])(value)) {
    return value;
  }

  var bool = Number(value.trim());
  return 1 === bool || 0 === bool ? 1 === bool : value;
}

/***/ }),

/***/ "./src/data/options.js":
/*!*****************************!*\
  !*** ./src/data/options.js ***!
  \*****************************/
/*! exports provided: getOptions */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getOptions", function() { return getOptions; });
/* harmony import */ var _data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./data */ "./src/data/data.js");
/**
 * Internal dependencies
 */

function getOptions(type) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  var typeOptions = Object(_data__WEBPACK_IMPORTED_MODULE_0__["getPluginData"])(type);

  for (var key in typeOptions) {
    if (typeOptions.hasOwnProperty(key)) {
      options.push({
        label: typeOptions[key],
        value: key
      });
    }
  }

  return options;
}

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _block_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block.js */ "./src/block.js");
/* harmony import */ var _data_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./data/data */ "./src/data/data.js");
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var withSelect = wp.data.withSelect;
var compose = wp.compose.compose;
/**
 * Internal dependencies
 */




if (!Object(lodash__WEBPACK_IMPORTED_MODULE_0__["isEmpty"])(_data_data__WEBPACK_IMPORTED_MODULE_2__["_pluginData"])) {
  registerRelatedPostsBlock();
}

function registerRelatedPostsBlock() {
  registerBlockType('related-posts-by-taxonomy/related-posts-block', {
    title: __('Related Posts by Taxonomy'),
    icon: 'megaphone',
    category: 'widgets',
    description: __('This block displays related posts by taxonomy.'),
    supports: {
      html: false,
      customClassName: false
    },
    edit: compose(withSelect(function (select, props) {
      return {
        postID: select('core/editor').getCurrentPostId(),
        postType: select('core/editor').getCurrentPostType(),
        registeredTaxonomies: select('core').getTaxonomies()
      };
    }), withSelect(function (select, props) {
      if (!props.registeredTaxonomies || !props.postType || !props.postID) {
        return null;
      }

      var taxonomyTerms = {};
      var taxonomyNames = [];
      var taxonomies = props.registeredTaxonomies;
      var postTaxonomies = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["filter"])(taxonomies, function (taxonomy) {
        return Object(lodash__WEBPACK_IMPORTED_MODULE_0__["includes"])(taxonomy.types, props.postType);
      });
      postTaxonomies.map(function (taxonomy) {
        taxonomyTerms[taxonomy.slug] = select('core/editor').getEditedPostAttribute(taxonomy.rest_base);
        taxonomyNames.push(taxonomy.slug);
      });
      var ids = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["pickBy"])(taxonomyTerms, function (value) {
        return value.length;
      });
      var terms = Object.keys(ids).map(function (tax) {
        return ids[tax];
      });
      return {
        editorData: {
          taxonomyTerms: taxonomyTerms,
          taxonomyNames: taxonomyNames,
          termIDs: Object(lodash__WEBPACK_IMPORTED_MODULE_0__["flatten"])(terms)
        }
      };
    }))(_block_js__WEBPACK_IMPORTED_MODULE_1__["default"]),
    save: function save() {
      // Rendering in PHP
      return null;
    }
  });
}

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = lodash;

/***/ })

/******/ });
//# sourceMappingURL=editor-block.js.map