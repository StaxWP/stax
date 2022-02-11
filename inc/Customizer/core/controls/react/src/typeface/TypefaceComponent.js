import PropTypes from "prop-types";
import { useState } from "@wordpress/element";

import Typeface from "./Typeface";

const TypefaceComponent = ({ control }) => {
	let setVal = control.setting.get();
	let defaultParams = {
		size_units: ["px", "rem", "em"],
		line_height_units: ["px", "rem", "em"],
		letter_spacing_units: ["px", "rem", "em"],
		weight_default: "none",
		text_transform: "none",
		size_default: {
			vars: [],
			suffix: {
				mobile: "rem",
				tablet: "rem",
				desktop: "rem",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		line_height_default: {
			vars: [],
			suffix: {
				mobile: "rem",
				tablet: "rem",
				desktop: "rem",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		letter_spacing_default: {
			vars: [],
			suffix: {
				mobile: "px",
				tablet: "px",
				desktop: "px",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		font_weight_default: {
			vars: "",
			value: "",
		},
	};

	const emptyValue = {
		textTransform: "none",
		fontSize: {
			vars: [],
			suffix: {
				mobile: "rem",
				tablet: "rem",
				desktop: "rem",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		lineHeight: {
			vars: [],
			suffix: {
				mobile: "rem",
				tablet: "rem",
				desktop: "rem",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		letterSpacing: {
			vars: [],
			suffix: {
				mobile: "px",
				tablet: "px",
				desktop: "px",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		font_weight_default: {
			vars: "",
			value: "none",
		},
	};

	const emptyDefault = {
		size_units: ["px", "rem", "em"],
		line_height_units: ["px", "rem", "em"],
		weight_default: "none",
		text_transform: "none",
		size_default: {
			vars: [],
			suffix: {
				mobile: "rem",
				tablet: "rem",
				desktop: "rem",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		line_height_default: {
			vars: [],
			suffix: {
				mobile: "rem",
				tablet: "rem",
				desktop: "rem",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		letter_spacing_default: {
			vars: [],
			suffix: {
				mobile: "px",
				tablet: "px",
				desktop: "px",
			},
			mobile: "",
			tablet: "",
			desktop: "",
		},
		font_weight_default: {
			vars: "",
			value: "none",
		},
	};

	if (!setVal) {
		setVal = emptyValue;
	}

	const controlParams = control.params.input_attrs
		? {
				...defaultParams,
				...JSON.parse(control.params.input_attrs),
		  }
		: defaultParams;

	if (control.params.input_attrs.length) {
		const inputAttrs = JSON.parse(control.params.input_attrs);
		if (inputAttrs.default_is_empty) {
			defaultParams = emptyDefault;
		}
	}

	// Added Later. Make sure we have a default value if none is selected.
	setVal.lineHeight = setVal.lineHeight || defaultParams.line_height_default;
	setVal.lineHeight.suffix =
		setVal.lineHeight.suffix || defaultParams.line_height_default.suffix;

	// Added Later. Make sure we have a suffix for line height.
	controlParams.line_height_default.suffix =
		controlParams.line_height_default.suffix ||
		defaultParams.line_height_default.suffix;

	// Added Later. Make sure we have a vars for line height.
	controlParams.line_height_default.vars =
		controlParams.line_height_default.vars ||
		defaultParams.line_height_default.vars;

	// Added Later. Make sure we have a default value if none is selected.
	setVal.letterSpacing =
		setVal.letterSpacing || defaultParams.letter_spacing_default;
	setVal.letterSpacing.suffix =
		setVal.letterSpacing.suffix ||
		defaultParams.letter_spacing_default.suffix;

	// Added Later. Make sure we have a suffix for letter spacing.
	controlParams.letter_spacing_default.suffix =
		controlParams.letter_spacing_default.suffix ||
		defaultParams.letter_spacing_default.suffix;

	// Added Later. Make sure we have a vars for letter spacing.
	controlParams.letter_spacing_default.vars =
		controlParams.letter_spacing_default.vars ||
		defaultParams.letter_spacing_default.vars;

	const [value, setValue] = useState({
		fontSize: setVal.fontSize,
		lineHeight: setVal.lineHeight,
		letterSpacing: setVal.letterSpacing,
		fontWeight: setVal.fontWeight,
		textTransform: setVal.textTransform,
		flag: false,
	});

	const updateValues = (nextValue) => {
		setValue({ ...value, ...nextValue });
		control.setting.set({
			...control.setting.get(),
			...nextValue,
			flag: !control.setting.get().flag,
		});
	};

	const { label, refresh_on_reset } = control.params;
	const {
		with_transform,
		with_weight,
		with_height,
		with_spacing,
		size_default,
		size_units,
		line_height_default,
		line_height_units,
		letter_spacing_default,
		letter_spacing_units,
	} = controlParams;

	return (
		<Typeface
			label={label}
			value={value}
			withTextTransform={with_transform}
			withTextWeight={with_weight}
			withLineHeight={with_height}
			withLetterSpacing={with_spacing}
			defaultFS={size_default}
			fSUnit={size_units}
			onChange={updateValues}
			refreshAfterReset={refresh_on_reset}
			defaultLH={line_height_default}
			lHunit={line_height_units}
			defaultLS={letter_spacing_default}
			lSunit={letter_spacing_units}
		/>
	);
};

TypefaceComponent.propTypes = {
	control: PropTypes.object.isRequired,
};

export default TypefaceComponent;
