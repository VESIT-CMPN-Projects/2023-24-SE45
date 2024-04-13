

(function($) {

	var options = {
		canvas: true
	};

	var render, getTextInfo, addText;

	// Cache the prototype hasOwnProperty for faster access

	var hasOwnProperty = Object.prototype.hasOwnProperty;

	function init(plot, classes) {

		var Canvas = classes.Canvas;

	

		if (render == null) {
			getTextInfo = Canvas.prototype.getTextInfo,
			addText = Canvas.prototype.addText,
			render = Canvas.prototype.render;
		}

		// Finishes rendering the canvas, including overlaid text

		Canvas.prototype.render = function() {

			if (!plot.getOptions().canvas) {
				return render.call(this);
			}

			var context = this.context,
				cache = this._textCache;

			// For each text layer, render elements marked as active

			context.save();
			context.textBaseline = "middle";

			for (var layerKey in cache) {
				if (hasOwnProperty.call(cache, layerKey)) {
					var layerCache = cache[layerKey];
					for (var styleKey in layerCache) {
						if (hasOwnProperty.call(layerCache, styleKey)) {
							var styleCache = layerCache[styleKey],
								updateStyles = true;
							for (var key in styleCache) {
								if (hasOwnProperty.call(styleCache, key)) {

									var info = styleCache[key],
										positions = info.positions,
										lines = info.lines;

									

									if (updateStyles) {
										context.fillStyle = info.font.color;
										context.font = info.font.definition;
										updateStyles = false;
									}

									for (var i = 0, position; position = positions[i]; i++) {
										if (position.active) {
											for (var j = 0, line; line = position.lines[j]; j++) {
												context.fillText(lines[j].text, line[0], line[1]);
											}
										} else {
											positions.splice(i--, 1);
										}
									}

									if (positions.length == 0) {
										delete styleCache[key];
									}
								}
							}
						}
					}
				}
			}

			context.restore();
		};

		

		Canvas.prototype.getTextInfo = function(layer, text, font, angle, width) {

			if (!plot.getOptions().canvas) {
				return getTextInfo.call(this, layer, text, font, angle, width);
			}

			var textStyle, layerCache, styleCache, info;

			// Cast the value to a string, in case we were given a number

			text = "" + text;

			// If the font is a font-spec object, generate a CSS definition

			if (typeof font === "object") {
				textStyle = font.style + " " + font.variant + " " + font.weight + " " + font.size + "px " + font.family;
			} else {
				textStyle = font;
			}

			// Retrieve (or create) the cache for the text's layer and styles

			layerCache = this._textCache[layer];

			if (layerCache == null) {
				layerCache = this._textCache[layer] = {};
			}

			styleCache = layerCache[textStyle];

			if (styleCache == null) {
				styleCache = layerCache[textStyle] = {};
			}

			info = styleCache[text];

			if (info == null) {

				var context = this.context;

				// If the font was provided as CSS, create a div with those
				// classes and examine it to generate a canvas font spec.

				if (typeof font !== "object") {

					var element = $("<div>&nbsp;</div>")
						.css("position", "absolute")
						.addClass(typeof font === "string" ? font : null)
						.appendTo(this.getTextLayer(layer));

					font = {
						lineHeight: element.height(),
						style: element.css("font-style"),
						variant: element.css("font-variant"),
						weight: element.css("font-weight"),
						family: element.css("font-family"),
						color: element.css("color")
					};

				

					font.size = element.css("line-height", 1).height();

					element.remove();
				}

				textStyle = font.style + " " + font.variant + " " + font.weight + " " + font.size + "px " + font.family;


				info = styleCache[text] = {
					width: 0,
					height: 0,
					positions: [],
					lines: [],
					font: {
						definition: textStyle,
						color: font.color
					}
				};

				context.save();
				context.font = textStyle;


				var lines = (text + "").replace(/<br ?\/?>|\r\n|\r/g, "\n").split("\n");

				for (var i = 0; i < lines.length; ++i) {

					var lineText = lines[i],
						measured = context.measureText(lineText);

					info.width = Math.max(measured.width, info.width);
					info.height += font.lineHeight;

					info.lines.push({
						text: lineText,
						width: measured.width,
						height: font.lineHeight
					});
				}

				context.restore();
			}

			return info;
		};

		// Adds a text string to the canvas text overlay.

		Canvas.prototype.addText = function(layer, x, y, text, font, angle, width, halign, valign) {

			if (!plot.getOptions().canvas) {
				return addText.call(this, layer, x, y, text, font, angle, width, halign, valign);
			}

			var info = this.getTextInfo(layer, text, font, angle, width),
				positions = info.positions,
				lines = info.lines;

			// Text is drawn with baseline 'middle', which we need to account
			// for by adding half a line's height to the y position.

			y += info.height / lines.length / 2;

			// Tweak the initial y-position to match vertical alignment

			if (valign == "middle") {
				y = Math.round(y - info.height / 2);
			} else if (valign == "bottom") {
				y = Math.round(y - info.height);
			} else {
				y = Math.round(y);
			}

			

			if (!!(window.opera && window.opera.version().split(".")[0] < 12)) {
				y -= 2;
			}

			// Determine whether this text already exists at this position.
			// If so, mark it for inclusion in the next render pass.

			for (var i = 0, position; position = positions[i]; i++) {
				if (position.x == x && position.y == y) {
					position.active = true;
					return;
				}
			}

			// If the text doesn't exist at this position, create a new entry

			position = {
				active: true,
				lines: [],
				x: x,
				y: y
			};

			positions.push(position);

			

			for (var i = 0, line; line = lines[i]; i++) {
				if (halign == "center") {
					position.lines.push([Math.round(x - line.width / 2), y]);
				} else if (halign == "right") {
					position.lines.push([Math.round(x - line.width), y]);
				} else {
					position.lines.push([Math.round(x), y]);
				}
				y += line.height;
			}
		};
	}

	$.plot.plugins.push({
		init: init,
		options: options,
		name: "canvas",
		version: "1.0"
	});

})(jQuery);
