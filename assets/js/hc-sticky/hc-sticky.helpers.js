(function (window) {
	'use strict';

	var hcSticky = window.hcSticky;
	var document = window.document;

	/*
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign
	 */
	if (typeof Object.assign !== 'function') {
		Object.defineProperty(Object, 'assign', {
			value: function assign(target, varArgs) {
				'use strict';
				if (target == null) {
					throw new TypeError('Cannot convert undefined or null to object');
				}

				var to = Object(target);

				for (var index = 1; index < arguments.length; index++) {
					var nextSource = arguments[index];

					if (nextSource != null) {
						for (var nextKey in nextSource) {
							if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
								to[nextKey] = nextSource[nextKey];
							}
						}
					}
				}
				return to;
			},
			writable: true,
			configurable: true
		});
	}

	/*
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach
	 */
	if (!Array.prototype.forEach) {
		Array.prototype.forEach = function (callback) {
			var T, k;

			if (this == null) {
				throw new TypeError('this is null or not defined');
			}

			var O = Object(this);
			var len = O.length >>> 0;

			if (typeof callback !== 'function') {
				throw new TypeError(callback + ' is not a function');
			}

			if (arguments.length > 1) {
				T = arguments[1];
			}

			k = 0;

			while (k < len) {
				var kValue;

				if (k in O) {
					kValue = O[k];
					callback.call(T, kValue, k, O);
				}

				k++;
			}
		};
	}

	/*
	 * https://github.com/desandro/eventie
	 */
	var event = (function () {
		var docElem = document.documentElement;

		var bind = function () {
		};

		function getIEEvent(obj) {
			var event = window.event;
			// add event.target
			event.target = event.target || event.srcElement || obj;
			return event;
		}

		if (docElem.addEventListener) {
			bind = function (obj, type, fn) {
				obj.addEventListener(type, fn, false);
			};
		} else if (docElem.attachEvent) {
			bind = function (obj, type, fn) {
				obj[type + fn] = fn.handleEvent ?
					function () {
						var event = getIEEvent(obj);
						fn.handleEvent.call(fn, event);
					} :
					function () {
						var event = getIEEvent(obj);
						fn.call(obj, event);
					};
				obj.attachEvent("on" + type, obj[type + fn]);
			};
		}

		var unbind = function () {
		};

		if (docElem.removeEventListener) {
			unbind = function (obj, type, fn) {
				obj.removeEventListener(type, fn, false);
			};
		} else if (docElem.detachEvent) {
			unbind = function (obj, type, fn) {
				obj.detachEvent("on" + type, obj[type + fn]);
				try {
					delete obj[type + fn];
				} catch (err) {
					// can't delete window object properties
					obj[type + fn] = undefined;
				}
			};
		}

		return {
			bind: bind,
			unbind: unbind
		};
	})();

	// debounce taken from underscore
	var debounce = function (func, wait, immediate) {
		var timeout;

		return function () {
			var context = this;
			var args = arguments;
			var later = function () {
				timeout = null;
				if (!immediate) {
					func.apply(context, args);
				}
			};
			var callNow = immediate && !timeout;

			clearTimeout(timeout);
			timeout = setTimeout(later, wait);

			if (callNow) {
				func.apply(context, args);
			}
		};
	};

	// cross-browser get style
	var getStyle = function (el, style) {
		if (window.getComputedStyle) {
			return style ? document.defaultView.getComputedStyle(el, null).getPropertyValue(style) : document.defaultView.getComputedStyle(el, null);
		} else if (el.currentStyle) {
			return style ? el.currentStyle[style.replace(/-\w/g, function (s) {
				return s.toUpperCase().replace('-', '');
			})] : el.currentStyle;
		}
	};

	// check if object is empty
	var isEmptyObject = function (obj) {
		for (var name in obj) {
			return false;
		}

		return true;
	};

	// check if element has class
	var hasClass = function (el, className) {
		if (el.classList) {
			return el.classList.contains(className);
		} else {
			return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
		}
	};

	// like jQuery .offset()
	var offset = function (el) {
		var rect = el.getBoundingClientRect();
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

		return {
			top: rect.top + scrollTop,
			left: rect.left + scrollLeft
		};
	};

	// like jQuery .position()
	var position = function (el) {
		var offsetParent = el.offsetParent;
		var parentOffset = offset(offsetParent);
		var elemOffset = offset(el);
		var prentStyle = getStyle(offsetParent);
		var elemStyle = getStyle(el);

		parentOffset.top += parseInt(prentStyle.borderTopWidth) || 0;
		parentOffset.left += parseInt(prentStyle.borderLeftWidth) || 0;

		return {
			top: elemOffset.top - parentOffset.top - (parseInt(elemStyle.marginTop) || 0),
			left: elemOffset.left - parentOffset.left - (parseInt(elemStyle.marginLeft) || 0)
		};
	};

	// get cascaded instead of computed styles
	var getCascadedStyle = function (el) {
		// clone element
		var clone = el.cloneNode(true);

		clone.style.display = 'none';

		// remove name attr from cloned radio buttons to prevent their clearing
		Array.prototype.slice.call(clone.querySelectorAll('input[type="radio"]')).forEach(function (el) {
			el.removeAttribute('name');
		});

		// insert clone to DOM
		el.parentNode.insertBefore(clone, el.nextSibling);

		// get styles
		var currentStyle;

		if (clone.currentStyle) {
			currentStyle = clone.currentStyle;
		} else if (window.getComputedStyle) {
			currentStyle = document.defaultView.getComputedStyle(clone, null);
		}

		// new style oject
		var style = {};

		for (var prop in currentStyle) {
			if (isNaN(prop) && (typeof currentStyle[prop] === 'string' || typeof currentStyle[prop] === 'number')) {
				style[prop] = currentStyle[prop];
			}
		}

		// safari copy
		if (Object.keys(style).length < 3) {
			style = {}; // clear
			for (var prop in currentStyle) {
				if (!isNaN(prop)) {
					style[currentStyle[prop].replace(/-\w/g, function (s) {
						return s.toUpperCase().replace('-', '');
					})] = currentStyle.getPropertyValue(currentStyle[prop]);
				}
			}
		}

		// check for margin:auto
		if (!style.margin && style.marginLeft === 'auto') {
			style.margin = 'auto';
		} else if (!style.margin && style.marginLeft === style.marginRight && style.marginLeft === style.marginTop && style.marginLeft === style.marginBottom) {
			style.margin = style.marginLeft;
		}

		// safari margin:auto hack
		if (!style.margin && style.marginLeft === '0px' && style.marginRight === '0px') {
			var posLeft = el.offsetLeft - el.parentNode.offsetLeft;
			var marginLeft = posLeft - (parseInt(style.left) || 0) - (parseInt(style.right) || 0);
			var marginRight = el.parentNode.offsetWidth - el.offsetWidth - posLeft - (parseInt(style.right) || 0) + (parseInt(style.left) || 0);
			var diff = marginRight - marginLeft;

			if (diff === 0 || diff === 1) {
				style.margin = 'auto';
			}
		}

		// destroy clone
		clone.parentNode.removeChild(clone);
		clone = null;

		return style;
	};

	hcSticky.Helpers = {
		isEmptyObject: isEmptyObject,
		debounce: debounce,
		hasClass: hasClass,
		offset: offset,
		position: position,
		getStyle: getStyle,
		getCascadedStyle: getCascadedStyle,
		event: event
	};

})(window);
