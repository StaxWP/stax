import PropTypes from "prop-types";
import ButtonAppearance from "./ButtonAppearance";
import { useState, useEffect } from "@wordpress/element";

const ButtonAppearanceComponent = ({ control }) => {
	const controlValue = control.setting.get();

	const defaultsFromControl = {
		borderRadius: {
			top: "",
			right: "",
			bottom: "",
			left: "",
		},
		borderWidth: {
			top: "",
			right: "",
			bottom: "",
			left: "",
		},
	};

	const defaultVals = control.params.defaultVals
		? {
				...control.params.defaultVals,
				...defaultsFromControl,
		  }
		: defaultsFromControl;

	const [value, setValue] = useState({
		...defaultVals,
		...controlValue,
	});

	const updateValue = (prop, propVal) => {
		const nextValue = { ...value, [prop]: propVal };
		setValue(nextValue);
		control.setting.set(nextValue);
	};

	const { label, no_hover } = control.params;

	useEffect(() => {
		global.addEventListener("stax-changed-customizer-value", (e) => {
			if (!e.detail) return false;
			if (e.detail.id !== control.id) return false;
			// Migrate border-radius and border-width
			const r = e.detail.value.borderRadius;
			if (r && (typeof r === "string" || typeof r === "number")) {
				e.detail.value.borderRadius = {
					top: r,
					bottom: r,
					right: r,
					left: r,
				};
			}

			const w = e.detail.value.borderWidth;
			if (w && (typeof w === "string" || typeof w === "number")) {
				e.detail.value.borderWidth = {
					top: w,
					bottom: w,
					right: w,
					left: w,
				};
			}
			setValue({ ...value, ...e.detail.value });
			control.setting.set(e.detail.value);
		});
	}, []);

	return (
		<ButtonAppearance
			defaultVals={defaultVals}
			onChange={updateValue}
			value={value}
			label={label}
			noHover={no_hover}
		/>
	);
};

ButtonAppearanceComponent.propTypes = {
	control: PropTypes.object.isRequired,
};

export default ButtonAppearanceComponent;
