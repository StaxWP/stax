import PropTypes from "prop-types";
import classnames from "classnames";
import ResponsiveControl from "../common/Responsive.js";
import SizingControl from "../common/Sizing.js";
import { mergeDeep } from "../common/common";

import { useState, useEffect } from "@wordpress/element";
import { Button } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { mapValues } from "lodash";

const SpacingComponent = ({ control }) => {
	const defaultParams = {
		min: -300,
		max: 300,
		hideResponsiveButtons: false,
		units: ["px", "em", "%"],
	};

	const controlParams = control.params.input_attrs
		? {
				...defaultParams,
				...control.params.input_attrs,
		  }
		: defaultParams;

	const baseDefault = {
		mobile: { top: "", right: "", bottom: "", left: "" },
		tablet: { top: "", right: "", bottom: "", left: "" },
		desktop: { top: "", right: "", bottom: "", left: "" },
		"mobile-unit": "px",
		"tablet-unit": "px",
		"desktop-unit": "px",
	};

	const defaultValue = control.params.default
		? {
				...baseDefault,
				...control.params.default,
		  }
		: baseDefault;

	const dbVal = control.setting.get();
	const initialVal = mergeDeep(defaultValue, dbVal);

	const [value, setValue] = useState(initialVal);
	const [currentDevice, setCurrentDevice] = useState("desktop");

	const updateValueForCurrentDevice = (valueForDevice) => {
		const nextValue = { ...value };
		let complete = false;
		if (
			valueForDevice.top !== "" ||
			valueForDevice.right !== "" ||
			valueForDevice.bottom !== "" ||
			valueForDevice.left !== ""
		) {
			complete = true;
		}

		if (complete) {
			valueForDevice.top =
				valueForDevice.top === "" ? 0 : valueForDevice.top;
			valueForDevice.right =
				valueForDevice.right === "" ? 0 : valueForDevice.right;
			valueForDevice.bottom =
				valueForDevice.bottom === "" ? 0 : valueForDevice.bottom;
			valueForDevice.left =
				valueForDevice.left === "" ? 0 : valueForDevice.left;
		}

		nextValue[currentDevice] = valueForDevice;
		updateControlValue(nextValue);
	};

	const updateControlValue = (nextVal) => {
		setValue(nextVal);
		control.setting.set(nextVal);
	};

	useEffect(() => {
		global.addEventListener("stax-changed-customizer-value", (e) => {
			if (!e.detail) return false;
			if (e.detail.id !== control.id) return false;

			updateControlValue(e.detail.value || defaultValue);
		});
	}, []);

	const getButtons = () => {
		const { units } = controlParams;

		if (units.length === 1) {
			return (
				<Button isSmall disabled className="active alone">
					{units[0]}
				</Button>
			);
		}

		return units.map((unit, index) => {
			const buttonClass = classnames({
				active: value[currentDevice + "-unit"] === unit,
			});
			return (
				<Button
					isSmall
					key={index}
					className={buttonClass}
					onClick={() => {
						const nextValue = { ...value };
						nextValue[currentDevice + "-unit"] = unit;
						if (unit !== "em") {
							nextValue[currentDevice] = mapValues(
								nextValue[currentDevice],
								(v) => (v ? parseInt(v) : v)
							);
						}
						updateControlValue(nextValue);
					}}
				>
					{unit}
				</Button>
			);
		});
	};

	const options = [
		{
			type: "top",
			label: __("Top", "stax"),
			value: value[currentDevice].top,
		},
		{
			type: "right",
			label: __("Right", "stax"),
			value: value[currentDevice].right,
		},
		{
			type: "bottom",
			label: __("Bottom", "stax"),
			value: value[currentDevice].bottom,
		},
		{
			type: "left",
			label: __("Left", "stax"),
			value: value[currentDevice].left,
		},
	];
	const { hideResponsiveButtons } = controlParams;
	const { label } = control.params;
	const { min, max } = controlParams;
	const wrapClasses = classnames([
		"stax-white-background-control",
		"stax-sizing",
	]);
	return (
		<div className={wrapClasses}>
			<div className="stax-control-header">
				{label && (
					<span className="customize-control-title">{label}</span>
				)}
				<ResponsiveControl
					hideResponsive={hideResponsiveButtons}
					onChange={(nextDevice) => {
						setCurrentDevice(nextDevice);
					}}
				/>
				<div className="stax-units">{getButtons()}</div>
			</div>
			<SizingControl
				min={min}
				max={max}
				step={value[currentDevice + "-unit"] === "em" ? 0.1 : 1}
				options={options}
				defaults={defaultValue[currentDevice]}
				value={value[currentDevice]}
				onChange={updateValueForCurrentDevice}
				onReset={() => {
					updateControlValue(defaultValue);
				}}
			/>
		</div>
	);
};

SpacingComponent.propTypes = {
	control: PropTypes.object.isRequired,
};

export default SpacingComponent;
