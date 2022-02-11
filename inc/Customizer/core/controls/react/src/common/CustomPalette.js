import { __ } from "@wordpress/i18n";
import { Button } from "@wordpress/components";
import classnames from "classnames";
import { globalPaletteColors } from "../common/common";

const CustomPalette = ({ title, onChange, activeColor }) => {
	const focusGlobalColors = () => {
		wp.customize.control("stax_global_colors").focus();
	};

	return (
		<div className="stx-custom-palette-wrap">
			<div className="header">
				{title && (
					<span className="customize-control-title">{title}</span>
				)}
				<Button isLink onClick={focusGlobalColors}>
					{__("Edit", "stax")}
				</Button>
			</div>
			<div className="stx-custom-palette-inner">
				<ul>
					{globalPaletteColors.map((group, index) => {
						let style = {
							backgroundColor: "",
						};

						if (group.output === "hex") {
							style.backgroundColor = `var(--${group.vars})`;
						} else {
							style.backgroundColor = `hsl(var(--${group.vars[0]}),var(--${group.vars[1]}),var(--${group.vars[2]}))`;
						}

						const buttonClasses = classnames([
							"stx-custom-palette-color",
							{
								active: activeColor === style.backgroundColor,
							},
						]);

						return (
							<li title={group.title} key={index}>
								<a
									href="#global-select"
									onClick={(e) => {
										e.preventDefault();
										onChange(style.backgroundColor);
									}}
								>
									<span
										style={style}
										className={buttonClasses}
									/>
									<span>{group.title}</span>
								</a>
							</li>
						);
					})}
				</ul>
			</div>
		</div>
	);
};

export default CustomPalette;
