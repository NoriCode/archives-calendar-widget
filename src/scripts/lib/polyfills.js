'use strict';

/**
 * classList Polyfill for IE < 10
 * https://github.com/remy/polyfills/blob/master/classList.js
 */
(function () {

	if (typeof window.Element === "undefined" || "classList" in document.documentElement) return;

	var prototype = Array.prototype,
		push = prototype.push,
		splice = prototype.splice,
		join = prototype.join;

	function DOMTokenList(el) {
		this.el = el;
		// The className needs to be trimmed and split on whitespace
		// to retrieve a list of classes.
		var classes = el.className.replace(/^\s+|\s+$/g, '').split(/\s+/);
		for (var i = 0; i < classes.length; i++) {
			push.call(this, classes[i]);
		}
	}

	DOMTokenList.prototype = {
		add: function (token) {
			if (this.contains(token)) return;
			push.call(this, token);
			this.el.className = this.toString();
		},
		contains: function (token) {
			return this.el.className.indexOf(token) !== -1;
		},
		item: function (index) {
			return this[index] || null;
		},
		remove: function (token) {
			if (!this.contains(token)) return;
			for (var i = 0; i < this.length; i++) {
				if (this[i] === token) break;
			}
			splice.call(this, i, 1);
			this.el.className = this.toString();
		},
		toString: function () {
			return join.call(this, ' ');
		},
		toggle: function (token) {
			if (!this.contains(token)) {
				this.add(token);
			} else {
				this.remove(token);
			}

			return this.contains(token);
		}
	};

	window.DOMTokenList = DOMTokenList;

	function defineElementGetter(obj, prop, getter) {
		if (Object.defineProperty) {
			Object.defineProperty(obj, prop, {
				get: getter
			});
		} else {
			obj.__defineGetter__(prop, getter);
		}
	}

	defineElementGetter(Element.prototype, 'classList', function () {
		return new DOMTokenList(this);
	});
})();

/**
 * Aobject.assign Polyfill
 * https://developer.mozilla.org/fr/docs/Web/JavaScript/Reference/Objets_globaux/Object/assign
 */
(function () {
	if (typeof Object.assign !== 'function') {
		// Must be writable: true, enumerable: false, configurable: true
		Object.defineProperty(Object, 'assign', {
			value: function assign(target, varArgs) { // .length of function is 2
				'use strict';
				if (target === null) { // TypeError if undefined or null
					throw new TypeError('Cannot convert undefined or null to object');
				}

				var to = Object(target);

				for (var index = 1; index < arguments.length; index++) {
					var nextSource = arguments[index];

					if (nextSource !== null) { // Skip over if undefined or null
						for (var nextKey in nextSource) {
							// Avoid bugs when hasOwnProperty is shadowed
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
})();
