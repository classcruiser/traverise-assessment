/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************!*\
  !*** ./resources/js/plugin.js ***!
  \********************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
(function () {
  this.Traverise = function () {
    var defaults = {};
    this.elements = [];
    this.settings = arguments[0] && _typeof(arguments[0]) === 'object' ? extendDefaults(defaults, arguments[0]) : defaults;
    this.init();
  };
  Traverise.prototype.init = function () {
    console.log('Init plugin.');
    build.call(this);
  };
  Traverise.prototype.build = function (element) {
    console.log('Update plugin.');
  };
  function build(element) {
    console.log('Build plugin.');
  }
  function extendDefaults(defaults, properties) {
    Object.keys(properties).forEach(function (property) {
      if (properties.hasOwnProperty(property)) {
        defaults[property] = properties[property];
      }
    });
    return defaults;
  }
})();
/******/ })()
;