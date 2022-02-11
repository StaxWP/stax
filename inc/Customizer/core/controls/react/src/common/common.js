import { __ } from "@wordpress/i18n";

export const maybeParseJson = (input) => {
	if (typeof input !== "string") {
		return input;
	}
	try {
		JSON.parse(input);
	} catch (error) {
		return input;
	}
	return JSON.parse(input);
};

/**
 * Simple object check.
 *
 * @param {Object} item
 * @return {boolean} Is object?
 */
export const isObject = (item) => {
	return item && typeof item === "object" && !Array.isArray(item);
};

/**
 * Deep merge objects.
 *
 * @param {...any} objects
 */
export const mergeDeep = (...objects) => {
	const isObjectValue = (obj) => obj && typeof obj === "object";
	return objects.reduce((prev, obj) => {
		Object.keys(obj).forEach((key) => {
			const pVal = prev[key];
			const oVal = obj[key];
			if (Array.isArray(pVal) && Array.isArray(oVal)) {
				prev[key] = pVal.concat(...oVal);
			} else if (isObjectValue(pVal) && isObjectValue(oVal)) {
				prev[key] = mergeDeep(pVal, oVal);
			} else {
				prev[key] = oVal;
			}
		});
		return prev;
	}, {});
};

export const getIntValAsResponsive = (value) => {
	value = maybeParseJson(value);
	if (
		typeof value === "object" &&
		Object.prototype.hasOwnProperty.call(value, "desktop") &&
		Object.prototype.hasOwnProperty.call(value, "tablet") &&
		Object.prototype.hasOwnProperty.call(value, "mobile")
	) {
		return value;
	}
	if (typeof value === "number") {
		value = {
			desktop: value,
			tablet: value,
			mobile: value,
		};
		return value;
	}

	return value;
};

export const globalPaletteColors = [
	{
		title: __("Body Background", "stax"),
		output: "hsl",
		vars: ["body-bg-h", "body-bg-s", "body-bg-l"],
	},
	{
		title: __("Text Color", "stax"),
		output: "hex",
		vars: "text-color",
	},
	{
		title: __("Heading Color", "stax"),
		output: "hex",
		vars: "heading-color",
	},
	{
		title: __("Primary Color", "stax"),
		output: "hsl",
		vars: ["primary-h", "primary-s", "primary-l"],
	},
	{
		title: __("Light Color", "stax"),
		output: "hsl",
		vars: ["light-h", "light-s", "light-l"],
	},
	{
		title: __("Dark Color", "stax"),
		output: "hsl",
		vars: ["dark-h", "dark-s", "dark-l"],
	},
	{
		title: __("Link Color", "stax"),
		output: "hsl",
		vars: ["link-h", "link-s", "link-l"],
	},
	{
		title: __("Border Color", "stax"),
		output: "hex",
		vars: "border-color",
	},
	{
		title: __("Meta Color", "stax"),
		output: "hex",
		vars: "text-meta-color",
	},
	{
		title: __("Archive Title Background", "stax"),
		output: "hsl",
		vars: ["archive-h", "archive-s", "archive-l"],
	},
	{
		title: __("Related Section Background", "stax"),
		output: "hsl",
		vars: ["related-h", "related-s", "related-l"],
	},
];

export const fontVariantMap = {
	100: "Thin 100",
	200: "Extra Light 200",
	300: "Light 300",
	400: "Regular 400",
	500: "Medium 500",
	600: "Semi-Bold 600",
	700: "Bold 700",
	800: "Extra Bold 800",
	900: "Black 900",
	"100italic": "Thin 100 Italic",
	"200italic": "Extra Light 200 Italic",
	"300italic": "Light 300 Italic",
	"400italic": "Regular 400 Italic",
	"500italic": "Medium 500 Italic",
	"600italic": "Semi-Bold 600 Italic",
	"700italic": "Bold 700 Italic",
	"800italic": "Extra Bold 800 Italic",
	"900italic": "Black 900 Italic",
};
