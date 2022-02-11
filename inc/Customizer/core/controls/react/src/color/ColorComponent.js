import PropTypes from "prop-types";
import ColorControl from "../common/ColorControl.js";

import { useState, useEffect } from "@wordpress/element";

const ColorComponent = ({ control }) => {
	const [value, setValue] = useState(control.setting.get());

	const updateValues = (newVal) => {
		setValue(newVal);
		control.setting.set(newVal);
	};

	useEffect(() => {
		global.addEventListener("stax-changed-customizer-value", (e) => {
			if (!e.detail) return false;
			if (e.detail.id !== control.id) return false;
			updateValues(e.detail.value);
		});
	}, []);

	return (
		<div className="stax-white-background-control stax-color-control">
			<ColorControl
				label={control.params.label}
				selectedColor={value}
				defaultValue={control.params.default}
				alphaDisabled={control.params.disableAlpha}
				onChange={updateValues}
			/>
		</div>
	);
};

ColorComponent.propTypes = {
	control: PropTypes.object.isRequired,
};

export default ColorComponent;
