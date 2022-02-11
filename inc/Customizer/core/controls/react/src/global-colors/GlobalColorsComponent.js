import GlobalColors from "./GlobalColors";
import { getStyle, buildStyle, addStyle, hexToHsl, rgbToHex } from "../helpers";
import { useState } from "@wordpress/element";

const GlobalColorsComponent = ({ control }) => {
	const { label, defaultValues } = control.params;
	const [values, setValues] = useState({ ...control.setting.get() });

	const save = (nextValue) => {
		setValues(nextValue);

		if (nextValue.flag) {
			delete nextValue.flag;
		} else {
			nextValue.flag = true;
		}

		control.setting.set(nextValue);

		const { activePalette, palettes } = nextValue;
		const currentPalette = palettes[activePalette];
		const { colors } = currentPalette;

		let stylesObj = getStyle();

		Object.keys(colors).map((i) => {
			if (colors[i].output === "hsl") {
				let color = rgbToHex(colors[i].color);
				color = hexToHsl(color);

				const mappedHsl = colors[i].vars.map((hsl_var, i) => {
					return {
						var: hsl_var,
						value: color[i],
					};
				});

				mappedHsl.forEach((item) => {
					stylesObj = addStyle(stylesObj, item.var, item.value);
				});
			} else if (colors[i].output === "hex") {
				stylesObj = addStyle(
					stylesObj,
					colors[i].color,
					colors[i].color.vars
				);
			}
		});

		buildStyle(stylesObj);
	};

	return (
		<GlobalColors
			onChange={save}
			currentValue={values}
			label={label}
			defaultValues={defaultValues}
		/>
	);
};

export default GlobalColorsComponent;
