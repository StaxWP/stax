import PropTypes from "prop-types";
import { __ } from "@wordpress/i18n";
import {
	Button,
	ButtonGroup,
	Dashicon,
	FocalPointPicker,
	Placeholder,
	RangeControl,
	ToggleControl,
} from "@wordpress/components";
import { MediaUpload } from "@wordpress/media-utils";

import ColorControl from "../common/ColorControl";

const Background = ({ onChange, value, label }) => {
	const getButtons = () => {
		const types = ["color", "image"];
		const labels = {
			color: __("Color", "stax"),
			image: __("Image", "stax"),
		};

		const buttons = [];
		types.map((type, index) => {
			buttons.push(
				<Button
					key={index}
					isPrimary={value.type === type}
					isSecondary={value.type !== type}
					onClick={() => {
						onChange({ type });
					}}
				>
					{labels[type]}
				</Button>
			);
			return type;
		});

		return buttons;
	};

	const {
		type,
		colorValue,
		useFeatured,
		imageUrl,
		focusPoint,
		fixed,
		overlayColorValue,
		overlayOpacity,
	} = value;

	return (
		<div className="stax-background-control">
			{label && <span className="customize-control-title">{label}</span>}
			<div className="control--top-toolbar">
				<ButtonGroup className="stax-background-type-control">
					{getButtons()}
				</ButtonGroup>
			</div>
			<div className="control--body">
				{type === "color" && (
					<>
						<ColorControl
							onChange={(val) => {
								onChange({ colorValue: val });
							}}
							selectedColor={colorValue}
							label={__("Color", "stax")}
						/>
						<div
							className="stax-color-preview"
							style={{
								backgroundColor: colorValue,
							}}
						/>
					</>
				)}
				{type === "image" && (
					<>
						<ToggleControl
							label={__("Use Featured Image", "stax")}
							checked={useFeatured}
							onChange={(val) => {
								onChange({ useFeatured: val });
							}}
						/>
						{!imageUrl ? (
							<Placeholder
								icon="format-image"
								label={
									useFeatured
										? __("Fallback Image", "stax")
										: __("Image", "stax")
								}
							>
								<p>
									{__(
										"Select from the Media Library or upload a new image",
										"stax"
									)}
								</p>
								<MediaUpload
									onSelect={(imageData) => {
										onChange({
											imageUrl: imageData.url,
										});
									}}
									allowedTypes={["image"]}
									render={({ open }) => (
										<Button isSecondary onClick={open}>
											{__("Add Image", "stax")}
										</Button>
									)}
								/>
							</Placeholder>
						) : (
							<>
								<Button
									disabled={!wp.media}
									className="remove-image"
									isDestructive
									isLink
									onClick={() => {
										onChange({
											imageUrl: "",
										});
									}}
								>
									<Dashicon icon="no" />
									{useFeatured
										? __("Remove Fallback Image", "stax")
										: __("Remove Image", "stax")}
								</Button>
								<FocalPointPicker
									url={imageUrl}
									value={focusPoint}
									onChange={(val) => {
										const newPoint = {
											x: parseFloat(val.x).toFixed(2),
											y: parseFloat(val.y).toFixed(2),
										};
										onChange({
											focusPoint: newPoint,
										});
									}}
								/>
							</>
						)}
						<ToggleControl
							label={__("Fixed Background", "stax")}
							checked={fixed}
							onChange={(val) => {
								onChange({ fixed: val });
							}}
						/>
						<ColorControl
							selectedColor={overlayColorValue}
							onChange={(val) => {
								onChange({
									overlayColorValue: val,
								});
							}}
							label={__("Overlay Color", "stax")}
						/>
						<div
							className="stax-color-preview"
							style={{
								backgroundColor: overlayColorValue,
							}}
						/>
						<RangeControl
							label={__("Overlay Opacity", "stax")}
							value={overlayOpacity}
							onChange={(val) => {
								onChange({
									overlayOpacity: val,
								});
							}}
							min="0"
							max="100"
						/>
					</>
				)}
			</div>
		</div>
	);
};

Background.propTypes = {
	value: PropTypes.shape({
		type: PropTypes.string,
		imageUrl: PropTypes.string,
		focusPoint: PropTypes.shape({
			x: PropTypes.number,
			y: PropTypes.number,
		}),
		colorValue: PropTypes.string,
		overlayColorValue: PropTypes.string,
		overlayOpacity: PropTypes.number,
		fixed: PropTypes.bool,
		useFeatured: PropTypes.bool,
	}).isRequired,
	onChange: PropTypes.func.isRequired,
	label: PropTypes.string,
};

export default Background;
