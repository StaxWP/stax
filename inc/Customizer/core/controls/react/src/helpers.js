export function getStyle() {
	let styles = document.querySelector("#stax-css-vars-inline-css");
	let stylesObj = {};

	styles = styles.innerHTML
		.replace(":root{", "")
		.replace("}", "")
		.trim()
		.split(";");

	_.each(styles, function (style) {
		style = style.split(":");

		if (style[0] && style[1]) {
			stylesObj[style[0]] = style[1];
		}
	});

	return stylesObj;
}

export function getStyleFrame() {
	let styles = document.querySelector("#stax-header-inline-css");
	let stylesObj = {};

	styles = styles.innerHTML
		.replace(":root{", "")
		.replace("}", "")
		.trim()
		.split(";");

	_.each(styles, function (style) {
		style = style.split(":");

		if (style[0] && style[1]) {
			stylesObj[style[0]] = style[1];
		}
	});

	return stylesObj;
}

export function buildStyle(vars) {
	let styles = document.querySelector("#stax-css-vars-inline-css");
	let style = "";

	_.each(vars, function (val, name) {
		style += name + ":" + val + ";";
	});

	if (style) {
		styles.innerHTML = ":root{" + style + "}";
	}
}

export function buildStyleFrame(vars) {
	let styles = document.querySelector("#stax-header-inline-css");
	let style = "";

	_.each(vars, function (val, name) {
		style += name + ":" + val + ";";
	});

	if (style) {
		styles.innerHTML = ":root{" + style + "}";
	}
}

export function addStyle(stylesObj, item, value, unit = "") {
	var finalValue = value;

	if (unit) {
		finalValue = finalValue + unit;
	}

	if (value) {
		stylesObj["--" + item] = finalValue;
	} else {
		delete stylesObj["--" + item];
	}

	return stylesObj;
}

export function rgbToHex(rgb) {
	if (rgb.indexOf("#") !== -1) {
		return rgb;
	}

	rgb = rgb.match(
		/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i
	);

	return rgb && rgb.length === 4
		? "#" +
				("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
				("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
				("0" + parseInt(rgb[3], 10).toString(16)).slice(-2)
		: "";
}

export function hexToHsl(hex) {
	if (hex == null || hex == "") {
		return null;
	}

	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

	var r = parseInt(result[1], 16) / 255;
	var g = parseInt(result[2], 16) / 255;
	var b = parseInt(result[3], 16) / 255;

	var max = Math.max(r, g, b),
		min = Math.min(r, g, b);
	var h,
		s,
		l = (max + min) / 2;

	if (max === min) {
		h = s = 0;
	} else {
		var d = max - min;
		s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

		switch (max) {
			case r:
				h = (g - b) / d + (g < b ? 6 : 0);
				break;

			case g:
				h = (b - r) / d + 2;
				break;

			case b:
				h = (r - g) / d + 4;
				break;
		}

		h /= 6;
	}

	return [
		Math.ceil(h * 360),
		Math.round(s * 100) + "%",
		Math.round(l * 100) + "%",
	];
}

export function createContrast(hexcolor, hsl) {
	hexcolor = rgbToHex(hexcolor);

	hexcolor = hexcolor.replace("#", "");

	if (hexcolor.length === 3) {
		hexcolor =
			hexcolor[0] +
			hexcolor[0] +
			hexcolor[1] +
			hexcolor[1] +
			hexcolor[2] +
			hexcolor[2];
	}

	const r = parseInt(hexcolor.substr(0, 2), 16);
	const g = parseInt(hexcolor.substr(2, 2), 16);
	const b = parseInt(hexcolor.substr(4, 2), 16);
	const yiq = (r * 299 + g * 587 + b * 114) / 1000;

	if (hsl) {
		return yiq >= 154 ? "0, 0%, 0%" : "0, 0%, 100%";
	} else {
		return yiq >= 154 ? "#000000" : "#ffffff";
	}
}
