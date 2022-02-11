import Accordion from "../common/Accordion";
import { Button } from "@wordpress/components";
import { Fragment } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { rotateLeft } from "@wordpress/icons";
import { debounce } from "lodash";
import ColorControl from "../common/ColorControl";

const PaletteColors = ({ values, defaults, save }) => {
	const { palettes, activePalette } = values;
	const palette = palettes[activePalette];
	const { colors, allowDeletion } = palette;

	const defaultColors = defaults.palettes[activePalette]
		? {
				...defaults.palettes[activePalette].colors,
		  }
		: {};

	palette.colors = { ...defaultColors, ...colors };

	const updateColorInPalette = (i, val) => {
		const nextValues = { ...values };

		if (nextValues.palettes[activePalette].colors[i].color === val) {
			return false;
		}

		nextValues.palettes[activePalette].colors[i].color = val;

		save(nextValues);
	};

	const resetPalette = () => {
		const nextValues = { ...values };
		nextValues.palettes[activePalette].colors = defaultColors;

		save(nextValues);
	};

	const paletteHasDefaultColors =
		Object.keys(defaultColors).filter((colorKey) => {
			return defaultColors[colorKey] !== colors[colorKey];
		}).length < 1;

	return (
		<Accordion label={__("Palette Colors", "stax")}>
			<div className="color-array-wrap">
				<Fragment key={activePalette}>
					{Object.keys(defaultColors).map((i) => {
						return (
							<ColorControl
								disableGlobal
								key={i}
								label={defaultColors[i].label}
								selectedColor={
									colors[i]
										? colors[i].color
										: defaults.palettes[activePalette]
												.colors[i].color
								}
								defaultValue={
									defaults.palettes[activePalette]
										? defaults.palettes[activePalette]
												.colors[i].color
										: "#FFFFFF"
								}
								onChange={debounce((value) => {
									updateColorInPalette(i, value);
								}, 100)}
							/>
						);
					})}
				</Fragment>
				{!allowDeletion && (
					<>
						<hr />
						<Button
							isLink
							className="reset-palette"
							onClick={resetPalette}
							disabled={paletteHasDefaultColors}
							icon={rotateLeft}
						>
							{__("Reset all to default", "stax")}
						</Button>
					</>
				)}
			</div>
		</Accordion>
	);
};
export default PaletteColors;
