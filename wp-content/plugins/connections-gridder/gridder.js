;jQuery(document).ready( function($) {

	// Init the Chosen drop down.
	$('#cn-gridder .cn-category-select.cn-enhanced-select').chosen();

	// Init the cnGridder jQuery plugin.
	$('.cn-gridder-item').cnGridder();

	// Set the max width of each Gridder container.
	cnGridderWidth();

	// On a window resize recalculate and apply the max Gridder container width.
	$(window).resize( function() {

		cnGridderWidth();
	});

	/**
	 * Calculates and sets the max width of a Gridder container.
	 *
	 * @todo   This s/b part of the cnGridder jQuery plugin but it
	 *         would have to be refactored to work on each Gridder
	 *         container rather than each Gridder item.
	 *
	 * @access private
	 * @since  1.0
	 * @return {void}
	 */
	function cnGridderWidth() {

		$('.cn-gridder').each( function() {

			// Store a jQuery reference to the source element.
			var _instance = $(this);

			// If instance is a single entry, bail.
			if ( _instance.find('.cn-entry-single').length ) return;

			// Find a Gridder item.
			var _items = _instance.find('.cn-gridder-item');

			// Get the width of a Gridder item.
			var _itemWidth = _items.outerWidth(true);

			// Calculate the max number of Gridder items there can be based in its width.
			var _itemsMaxPerRow = Math.floor( _instance.parent().width() / _itemWidth );

			// Calculate the max width of the current instance of a selected Gridder container determined by tge whether which
			// is greater the max number of Gridder items per row or the number of Gridder items found in the current Gridder
			// container.
			var _instanceMaxWidth = _itemsMaxPerRow < _items.length ? _itemsMaxPerRow * _itemWidth : _items.length * _itemWidth;

			_instance.css( 'width', _instanceMaxWidth )
		});

	}

	// Render map on single entry page
	var cnSingleMap = $('#cn-gmap-single').length ? $('#cn-gmap-single') : false;

	if ( cnSingleMap != false ) {

		var fLatitude  = 0;
		var fLongitude = 0;
		var uuid       = cnSingleMap.attr('data-gmap-id');
		var strAddress = $( '#map-' + uuid ).attr('data-address');
		var strMapType = $( '#map-' + uuid ).attr('data-maptype');
		var intZoom    = parseInt( $( '#map-' + uuid ).attr('data-zoom') );

		if ( $('#map-' + uuid ).attr('data-latitude') )   fLatitude = parseFloat( $( '#map-' + uuid ).attr('data-latitude' ) );
		if ( $('#map-' + uuid ).attr('data-longitude') ) fLongitude = parseFloat( $( '#map-' + uuid ).attr('data-longitude') );

		if ( fLatitude == 0 && fLatitude == 0 ) {

			$( '#map-' + uuid ).goMap({
				markers: [ { address: '\'' + strAddress + '\'' } ] ,
				zoom: intZoom,
				maptype: strMapType
			});

		} else {

			$( '#map-' + uuid ).goMap({
				markers: [ { latitude: fLatitude , longitude: fLongitude } ] ,
				zoom: intZoom,
				maptype: strMapType
			});

		}

	}

});

/*
 * @file jQuery plugin for the Connections Business Directory Gridder Template.
 * @version 1.0
 * @author Steven A. Zahm
 * @copyright 2014
 * @license Licensed under MIT license
 * @url http://www.opensource.org/licenses/mit-license.php
 *
 * CREDIT: This plugin was based on the jQuery-Plugin-Boilerplate
 * @author Antonio Santiago
 * @copyright 2013
 * @url https://github.com/acanimal/jQuery-Plugin-Boilerplate
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
;(function( $, window, document, undefined ) {

	/**
	 * Store the plugin name in a variable. It helps you if later decide to
	 * change the plugin's name
	 * @type {String}
	 */
	var pluginName = 'cnGridder';

	/**
	 * The plugin constructor
	 * @param {DOM Element} element The DOM element where plugin is applied
	 * @param {Object} options Options passed to the constructor
	 */
	function Plugin( element, options ) {

		// Store a reference to the source element
		this.el = element;

		// Store a jQuery reference to the source element
		this.$el = $(element);

		// Whether or not the element's panel is visible or not.
		this.isVisible = false;

		// Set the instance options extending the plugin defaults and
		// the options passed by the user
		this.options = $.extend({}, $.fn[pluginName].defaults, options);

		// Initialize the plugin instance
		this.init();
	}

	/**
	 * Set up your Plugin protptype with desired methods.
	 * It is a good practice to implement 'init' and 'destroy' methods.
	 */
	Plugin.prototype = {

		/**
		 * Initialize the plugin instance.
		 * Set any other attribtes, store any other element reference, register
		 * listeners, etc
		 *
		 * When bind listerners remember to name tag it with your plugin's name.
		 * Elements can have more than one listener attached to the same event
		 * so you need to tag it to unbind the appropriate listener on destroy:
		 *
		 * @example
		 * this.$someSubElement.on('click.' + pluginName, function() {
		 *      // Do something
		 * });
		 *
		 */
		init: function() {

			var gridder = $('<div class="cn-gridder-panel"></div>');
			var _self   = this;

			_self.$el.on( 'click.' + pluginName, function(e) {
				e.preventDefault();

				var _me = $(this);

				/* If this panel is already visible, close it. */
				if ( _self.visible() ) {

					_self._close(_me);
					_self.visible(false);

				/* If this panel is not visible, create and show it. */
				} else {

					/* Ensure all other panels are closed and inactive. */
					_self._closeAll();

					/* Add the active class to the item clicked. */
					/* NOTE: This must come after removing the active class from any previous active panel. */
					_me.addClass('cn-gridder-active-item');

					/* Add/move the content panel. */
					panel = $('.cn-gridder-panel').length ? $('.cn-gridder-panel').insertAfter(this) : gridder.insertAfter(this);

					/* The panel content. */
					var content = _me.find('.cn-gridder-content');

					/* Grab the color and background-color so it can be applied to the panel to keep things pretty. */
					var panelTextColor = content.css('color');
					var panelColor     = _self.shade( content.css('background-color'), .2 );

					/* Build the panel content. */
					html  = "<div class=\"cn-gridder-panel-content\">";
						html += "<span class=\"cn-gridder-toggle cn-gridder-toggle-top-right cn-gridder-close\"></span>";
						html += content.html();
					html += "</div>";

					/* Populate the panel's content. */
					panel.html( html );

					/* Apply the color and background-color to the panel. */
					panel.css( 'background-color', panelColor ).css( 'color', panelTextColor )/*.css( 'max-width', _self.getPanelWidth() )*/;

					/* Lighten the overlay panel. */
					_me.find('.cn-gridder-overlay').css( 'background-color', panelColor );

					/* Set the color all links to the color set via the shortcode option. */
					$( 'a', panel ).each( function() {

						$(this).css( 'color', panelTextColor ).css( 'text-decoration', 'none' ).css( 'border', 'none' );
					});

					/* Scrolls to the current item. */
					$( _self._scrollTo() ).animate( { scrollTop: _me.position().top }, 0 );

					/* Show the panel content using an animation. */
					if ( ! _self.visible() ) {

						panel.find('.cn-gridder-panel-content').slideDown(  _self.options.animationSpeed,  _self.options.animationEasing, function() {
							_self.visible(true);
						});

					} else {

						panel.find('.cn-gridder-panel-content').fadeIn(  _self.options.animationSpeed,  _self.options.animationEasing, function() {
							_self.visible(true);
						});

					}

				}

				/* Add the click event to the panel's close button. */
				panel.on( 'click.' + pluginName, '.cn-gridder-close', function() {

					_self._close(_me);
					_self.visible(false);
				});

			});

		},

		/**
		 * Return or set the panel's visibility status.
		 *
		 * @access public
		 * @since  1.0
		 * @param  {bool}   value Whether or not the panel is visible.
		 * @return {bool}
		 */
		visible: function( value ) {

			if ( value === undefined ) {

				return this.isVisible;

			} else {

				this.isVisible = value;
			}

		},

		/**
		 * Close and remove the panel.
		 *
		 * @acess  private
		 * @since  1.0
		 * @param  {element} element The jQuery reference to the source panel DOM element.
		 * @return {void}
		 */
		_close: function( element ) {

			var _self = this;

			/* Grab the panel color. */
			var panelColor = element.find('.cn-gridder-content').css( 'background-color' );

			/* Remove the active class of the element clicked. */
			element.removeClass('cn-gridder-active-item');

			element.next().find('.cn-gridder-panel-content').fadeTo( _self.options.animationSpeed, 0.00, function() {

				element.next().slideUp( _self.options.animationSpeed, _self.options.animationEasing, function() {

					/* Change the overlay color back to the original color. */
					element.find('.cn-gridder-overlay').css( 'background-color', panelColor );

					$(this).remove();
					$(this).toggle();

				});

			});

		},

		/**
		 * If any other item's panel is visiable, set its visible status to false,
		 * remove the active class and reset the overaly color.
		 *
		 * @acess  private
		 * @since  1.0
		 * @return {void}
		 */
		_closeAll: function() {

			var _self = this;

			/* If any other panel is visible, set its  */
			$('.cn-gridder-item').each( function() {

				var _item = $(this);

				if ( _item.cnGridder('visible') ) {

					/* The object should have the active class, remove it. */
					_item.removeClass('cn-gridder-active-item').cnGridder( 'visible', false );

					/* Change the overlay color back to the original color. */
					var panelColor = _item.find('.cn-gridder-content').css( 'background-color' );

					_item.find('.cn-gridder-overlay').css( 'background-color', panelColor );

					// Since a panel is already visible, set the status of `this` to visible since
					// the content of the open panel will be replaced with this object's content.
					_self.visible(true);
				}
			});

		},

		_scrollTo : function () {

			/**
			 * @link http://stackoverflow.com/a/21583714
			 */

			// if missing doctype (quirks mode) then will always use 'body'
			if ( document.compatMode !== 'CSS1Compat' ) return 'body';

			// if there's a doctype (and your page should)
			// most browsers will support the scrollTop property on EITHER html OR body
			// we'll have to do a quick test to detect which one...

			var html = document.documentElement;
			var body = document.body;

			// get our starting position.
			// pageYOffset works for all browsers except IE8 and below
			var startingY = window.pageYOffset || body.scrollTop || html.scrollTop;

			// scroll the window down by 1px (scrollTo works in all browsers)
			var newY = startingY + 1;
			window.scrollTo(0, newY);

			// And check which property changed
			// FF and IE use only html. Safari uses only body.
			// Chrome has values for both, but says
			// body.scrollTop is deprecated when in Strict mode.,
			// so let's check for html first.
			var element = ( html.scrollTop === newY ) ? 'html' : 'body';

			// now reset back to the starting position
			window.scrollTo(0, startingY);

			return element;
		},

		getPanelWidth: function() {

			return Math.floor( $('.cn-gridder').parent().width() / this.$el.width() ) * this.$el.outerWidth(true);
		},

		/*
		 * The color blending and shading functions are from:
		 * @url http://stackoverflow.com/a/13542669
		 */
		shadeColor1: function(color, percent) {
			var num = parseInt(color.slice(1),16), amt = Math.round(2.55 * percent), R = (num >> 16) + amt, G = (num >> 8 & 0x00FF) + amt, B = (num & 0x0000FF) + amt;
			return "#" + (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (G<255?G<1?0:G:255)*0x100 + (B<255?B<1?0:B:255)).toString(16).slice(1);
		},

		shadeColor2: function(color, percent) {
			var f=parseInt(color.slice(1),16),t=percent<0?0:255,p=percent<0?percent*-1:percent,R=f>>16,G=f>>8&0x00FF,B=f&0x0000FF;
			return "#"+(0x1000000+(Math.round((t-R)*p)+R)*0x10000+(Math.round((t-G)*p)+G)*0x100+(Math.round((t-B)*p)+B)).toString(16).slice(1);
		},

		blendColors: function(c0, c1, p) {
			var f=parseInt(c0.slice(1),16),t=parseInt(c1.slice(1),16),R1=f>>16,G1=f>>8&0x00FF,B1=f&0x0000FF,R2=t>>16,G2=t>>8&0x00FF,B2=t&0x0000FF;
			return "#"+(0x1000000+(Math.round((R2-R1)*p)+R1)*0x10000+(Math.round((G2-G1)*p)+G1)*0x100+(Math.round((B2-B1)*p)+B1)).toString(16).slice(1);
		},

		shadeRGBColor: function(color, percent) {
			var f=color.split(","),t=percent<0?0:255,p=percent<0?percent*-1:percent,R=parseInt(f[0].slice(4)),G=parseInt(f[1]),B=parseInt(f[2]);
			return "rgb("+(Math.round((t-R)*p)+R)+","+(Math.round((t-G)*p)+G)+","+(Math.round((t-B)*p)+B)+")";
		},

		blendRGBColors: function(c0, c1, p) {
			var f=c0.split(","),t=c1.split(","),R=parseInt(f[0].slice(4)),G=parseInt(f[1]),B=parseInt(f[2]);
			return "rgb("+(Math.round((parseInt(t[0].slice(4))-R)*p)+R)+","+(Math.round((parseInt(t[1])-G)*p)+G)+","+(Math.round((parseInt(t[2])-B)*p)+B)+")";
		},

		shade: function(color, percent){
			if (color.length > 7 ) return this.shadeRGBColor(color,percent);
			else return this.shadeColor2(color,percent);
		},

		blend: function(color1, color2, percent){
			if (color1.length > 7) return this.blendRGBColors(color1,color2,percent);
			else return this.blendColors(color1,color2,percent);
		},

		/**
		 * The 'destroy' method is were you free the resources used by your plugin:
		 * references, unregister listeners, etc.
		 *
		 * Remember to unbind for your event:
		 *
		 * @example
		 * this.$someSubElement.off('.' + pluginName);
		 *
		 * Above example will remove any listener from your plugin for on the given
		 * element.
		 */
		destroy: function() {

			// Remove any attached data from your plugin
			this.$el.removeData();
		},

		/**
		 * Write public methods within the plugin's prototype. They can
		 * be called with:
		 *
		 * @example
		 * $('#element').pluginName('somePublicMethod','Arguments', 'Here', 1001);
		 *
		 * @param  {[type]} foo [some parameter]
		 * @param  {[type]} bar [some other parameter]
		 * @return {[type]}
		 */
		// somePublicMethod: function(foo, bar) {

		// 	// This is a call to a pseudo private method
		// 	this._pseudoPrivateMethod();

		// 	// This is a call to a real private method. You need to use 'call' or 'apply'
		// 	privateMethod.call(this);
		// },

		/**
		 * Another public method which acts as a getter method. You can call as any usual
		 * public method:
		 *
		 * @example
		 * $('#element').pluginName('isVisible');
		 *
		 * to get some interesting info from your plugin.
		 *
		 * @return {[type]} Return something
		 */
		isVisible: function() {

			return this.visible;
		},

		/**
		 * You can use the name convention functions started with underscore are
		 * private. Really calls to functions starting with underscore are
		 * filtered, for example:
		 *
		 *  @example
		 *  $('#element').pluginName('_pseudoPrivateMethod');  // Will not work
		 */
		// _pseudoPrivateMethod: function() {

		// }
	};

	/**
	 * This is a real private method. A plugin instance has access to it
	 * @return {[type]}
	 */
	// var privateMethod = function() {
	// 	console.log("privateMethod");
	// 	console.log(this);
	// };

	/**
	 * This is were we register our plugin within jQuery plugins.
	 * It is a plugin wrapper around the constructor and prevents agains multiple
	 * plugin instantiation (storeing a plugin reference within the element's data)
	 * and avoid any function starting with an underscore to be called (emulating
	 * private functions).
	 *
	 * @example
	 * $('#element').pluginName({
	 *     defaultOption: 'this options overrides a default plugin option',
	 *     additionalOption: 'this is a new option'
	 * });
	 */
	$.fn[pluginName] = function(options) {
		var args = arguments;

		if (options === undefined || typeof options === 'object') {
			// Creates a new plugin instance, for each selected element, and
			// stores a reference withint the element's data
			return this.each(function() {
				if (!$.data(this, 'plugin_' + pluginName)) {
					$.data(this, 'plugin_' + pluginName, new Plugin(this, options));
				}
			});
		} else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
			// Call a public pluguin method (not starting with an underscore) for each
			// selected element.
			if (Array.prototype.slice.call(args, 1).length == 0 && $.inArray(options, $.fn[pluginName].getters) != -1) {
				// If the user does not pass any arguments and the method allows to
				// work as a getter then break the chainability so we can return a value
				// instead the element reference.
				var instance = $.data(this[0], 'plugin_' + pluginName);
				return instance[options].apply(instance, Array.prototype.slice.call(args, 1));
			} else {
				// Invoke the speficied method on each selected element
				return this.each(function() {
					var instance = $.data(this, 'plugin_' + pluginName);
					if (instance instanceof Plugin && typeof instance[options] === 'function') {
						instance[options].apply(instance, Array.prototype.slice.call(args, 1));
					}
				});
			}
		}
	};

	/**
	 * Names of the pluguin methods that can act as a getter method.
	 * @type {Array}
	 */
	$.fn[pluginName].getters = ['visible'];

	/**
	 * Default options
	 */
	$.fn[pluginName].defaults = {
		animationSpeed:  200,
		animationEasing: "linear",
		// panelTextColor:  "#000",
		// panelColor:      "#f3f3f3"
	};

})( jQuery, window, document );
