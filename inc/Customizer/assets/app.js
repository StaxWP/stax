import {
	initNavigation,
	repositionDropdowns,
} from "../../../assets/js/hfg/navigation";
import { removeClass, addClass } from "../../../assets/js/hfg/utils.js";
import { parseFontFamily } from "./common.js";
import { CSSVariablesHandler, addCSS, addTemplateCSS } from "./css-var-handler";

import {
	getStyle,
	buildStyle,
	addStyle as addVarStyle,
	hexToHsl,
} from "../core/controls/react/src/helpers";

function handleResponsiveRadioButtons(args, nextValue) {
	if (!args.additional) return false;

	const items = document.querySelectorAll(args.selector);

	const classesToAdd = [];

	Object.keys(nextValue).map((device) => {
		classesToAdd.push(`${device}-${nextValue[device]}`);
		return device;
	});

	_.each(items, function (item) {
		item.parentNode.classList.remove(...args.additional.remove_classes);
		item.parentNode.classList.add(...classesToAdd);
	});
}

/**
 * Run JS on load.
 */
window.addEventListener("load", function () {
	/**
	 * Add action when Header Panel rendered by customizer.
	 */
	document.addEventListener("header_builder_panel_changed", function (e) {
		if (e.detail.partial_id === "hfg_header_layout_partial") {
			window.HFG.init();
			window.HFG.initSearch();
			repositionDropdowns();
			return false;
		}
		if (e.detail.partial_id === "primary-menu_partial") {
			initNavigation();
			repositionDropdowns();
			return false;
		}
	});

	document.addEventListener(
		"customize_control_sidebar",
		function (e) {
			if (e.detail === "open") {
				window.HFG.toggleMenuSidebar(true);
			}
			if (e.detail === "close") {
				window.HFG.toggleMenuSidebar(false);
			}
		}.bind(this)
	);

	document.addEventListener(
		"customize_section_opened",
		function (e) {
			if (e.detail === "header_sidebar") {
				window.HFG.toggleMenuSidebar(true);
			}
		}.bind(this)
	);

	const deviceMap = {
		mobile: "max-width: 576px",
		tablet: "min-width: 576px",
		desktop: "min-width: 961px",
	};

	const varsHandler = new CSSVariablesHandler();

	_.each(staxCustomizePreview, function (settings, settingType) {
		_.each(settings, function (args, settingId) {
			wp.customize(settingId, function (setting) {
				setting.bind(function (newValue) {
					if (args.additional && args.additional.cssVar) {
						varsHandler.run(
							settingId,
							settingType,
							newValue,
							args.additional.cssVar
						);

						return false;
					}

					if (args.additional && args.additional.template) {
						addTemplateCSS(
							settingType,
							settingId,
							newValue,
							args.additional,
							args.responsive || false,
							args.directional || false
						);

						addVarStyle(
							settingType,
							settingId,
							newValue,
							args.additional
						);

						return false;
					}

					let style = "";

					switch (settingType) {
						case "stax_color_control":
							if (args.additional.partial) {
								wp.customize.selectiveRefresh
									.partial(args.additional.partial)
									.refresh();
								return false;
							}

							_.each(args.additional, (i) => {
								if (!i.selector) {
									return false;
								}
								newValue = newValue || i.fallback;
								style += `body ${i.selector} { ${i.prop}: ${newValue} !important; }`;
							});

							addCSS(settingId, style);
							break;
						case "stax_background_control":
							if (newValue.type === "color") {
								if (
									!newValue.colorValue &&
									args.additional.partial
								) {
									wp.customize.selectiveRefresh
										.partial(args.additional.partial)
										.refresh();
								}
								style += `body ${args.selector}{background-image: none !important;}`;
								const color =
									newValue.colorValue !== "undefined"
										? newValue.colorValue
										: "inherit";
								style += `${args.selector}:before{ content: none !important;}`;
								style += `body ${args.selector}, body ${args.selector} .primary-menu-ul .sub-menu {background-color: ${color}!important;}`;
								style += `${args.selector} .primary-menu-ul .sub-menu, ${args.selector} .primary-menu-ul .sub-menu li {border-color: ${color}!important;}`;
								addCSS(settingId, style);
								return false;
							}
							if (
								newValue.useFeatured &&
								staxCustomizePreview.currentFeaturedImage
							) {
								newValue.imageUrl =
									staxCustomizePreview.currentFeaturedImage;
							}
							style += args.selector + "{";
							style += newValue.imageUrl
								? `background-image: url("${newValue.imageUrl}") !important;`
								: "background-image: none !important;";
							style +=
								newValue.fixed === true
									? "background-attachment: fixed !important;"
									: "background-attachment: initial !important;";
							if (
								newValue.focusPoint &&
								newValue.focusPoint.x &&
								newValue.focusPoint.y
							) {
								style +=
									"background-position:" +
									(newValue.focusPoint.x * 100).toFixed(2) +
									"% " +
									(newValue.focusPoint.y * 100).toFixed(2) +
									"% !important;";
							}
							style += "background-size: cover !important;";
							style +=
								'top: 0; bottom: 0; width: 100%; content:"";';
							style += "}";
							const color = newValue.overlayColorValue || "unset";
							const overlay =
								newValue.overlayOpacity === 0
									? 0
									: newValue.overlayOpacity || 50;
							style += `body ${args.selector}, body ${args.selector} .primary-menu-ul .sub-menu {background-color: ${color}!important;}`;
							style += `${args.selector} .primary-menu-ul .sub-menu, ${args.selector} .primary-menu-ul .sub-menu li {border-color: ${color}!important;}`;
							style +=
								args.selector +
								":before { " +
								'content: "";' +
								"position: absolute; top: 0; bottom: 0; width: 100%;" +
								`background-color: ${color}!important;` +
								"opacity: " +
								overlay / 100 +
								"!important;}";
							style +=
								args.selector +
								"{ background-color: transparent !important; }";
							addCSS(settingId, style);
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Responsive_Radio_Buttons":
							handleResponsiveRadioButtons(args, newValue);
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Radio_Buttons":
							if (!args.additional) return false;

							const classes =
								"hfg-item-v-top hfg-item-v-middle hfg-item-v-bottom";
							const newClass = "hfg-item-v-" + newValue;

							const itemInner = document.querySelectorAll(
								args.selector
							);

							_.each(itemInner, function (item) {
								removeClass(
									item.parentNode.parentNode,
									classes
								);
								addClass(item.parentNode.parentNode, newClass);
							});
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\Radio_Image":
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\Range":
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Responsive_Range":
							const value = JSON.parse(newValue);
							if (value.mobile > 0) {
								style +=
									"@media (max-width: 576px) { body " +
									args.selector +
									"{ " +
									args.additional.prop +
									":" +
									value.mobile +
									args.additional.unit +
									";}}";
							} else {
								style +=
									"@media (max-width: 576px) { body " +
									args.selector +
									"{ " +
									args.additional.prop +
									":unset;}}";
							}
							if (value.tablet > 0) {
								style +=
									"@media (min-width: 576px) { body " +
									args.selector +
									"{ " +
									args.additional.prop +
									":" +
									value.tablet +
									args.additional.unit +
									";}}";
							} else {
								style +=
									"@media (min-width: 576px) { body " +
									args.selector +
									"{ " +
									args.additional.prop +
									":unset;}}";
							}
							if (value.desktop > 0) {
								style +=
									"@media (min-width: 961px) { body " +
									args.selector +
									"{ " +
									args.additional.prop +
									":" +
									value.desktop +
									args.additional.unit +
									";}}";
							} else {
								style +=
									"@media (min-width: 961px) { body " +
									args.selector +
									"{ " +
									args.additional.prop +
									":unset;}}";
							}
							addCSS(settingId, style);
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Spacing":
							for (const device in deviceMap) {
								style +=
									"@media (" +
									deviceMap[device] +
									") { body " +
									args.selector +
									"{";
								for (const optionType in newValue[device]) {
									if (newValue[device][optionType] !== "") {
										let cssProp =
											args.additional.prop +
											"-" +
											optionType;
										if (
											args.additional.prop ===
											"border-width"
										) {
											cssProp = `border-${optionType}-width`;
										}
										if (
											args.additional.prop ===
											"border-radius"
										) {
											const mapDirectionToCorners = {
												top: "top-left",
												right: "top-right",
												bottom: "bottom-right",
												left: "bottom-left",
											};
											cssProp = `border-${mapDirectionToCorners[optionType]}-radius`;
										}
										style +=
											cssProp +
											":" +
											newValue[device][optionType] +
											newValue[device + "-unit"] +
											";";
									}
								}
								style += "}}";
							}
							addCSS(settingId, style);
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Typography":
							if (args.selector !== true) {
								style += `html ${args.selector}{`;

								if (newValue.textTransform) {
									style += `text-transform: ${newValue.textTransform};`;
								}

								if (newValue.fontWeight !== "none") {
									style += `font-weight: ${newValue.fontWeight};`;
								}

								style += `}`;

								for (const device in deviceMap) {
									style += `@media (${deviceMap[device]}) {
											html ${args.selector} {`;
									if (
										args.live_refresh_default &&
										args.live_refresh_default.size
									) {
										style += `font-size:${args.live_refresh_default.size[device]}${args.live_refresh_default.size.suffix[device]};`;
									}

									if (
										newValue.fontSize &&
										newValue.fontSize[device]
									) {
										style += `font-size:${newValue.fontSize[device]}${newValue.fontSize.suffix[device]};`;
									}

									if (
										args.live_refresh_default &&
										args.live_refresh_default.letter_spacing
									) {
										style += `letter-spacing:${args.live_refresh_default.letter_spacing[device]}px;`;
									}

									if (
										newValue.letterSpacing &&
										newValue.letterSpacing[device]
									) {
										style += `letter-spacing:${newValue.letterSpacing[device]}px;`;
									}

									if (
										args.live_refresh_default &&
										args.live_refresh_default.line_height
									) {
										style += `line-height:${args.live_refresh_default
											.line_height[device]
											}${args.live_refresh_default
												.line_height.suffix &&
												args.live_refresh_default
													.line_height.suffix[device]
												? args.live_refresh_default
													.line_height.suffix[
												device
												]
												: ""
											};`;
									}

									if (
										newValue.lineHeight &&
										newValue.lineHeight[device]
									) {
										style += `line-height:${newValue.lineHeight[device]
											}${newValue.lineHeight.suffix[
											device
											] || ""
											};`;
									}

									style += `}}`;
								}

								addCSS(settingId, style);
							} else {
								let typoStylesObj = getStyle();

								if (
									args.live_refresh_default &&
									args.live_refresh_default.size.vars &&
									args.live_refresh_default.size.vars.length
								) {
									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.size.vars[0],
										args.live_refresh_default.size.mobile,
										args.live_refresh_default.size.suffix
											.mobile
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.size.vars[1],
										args.live_refresh_default.size.tablet,
										args.live_refresh_default.size.suffix
											.tablet
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.size.vars[2],
										args.live_refresh_default.size.desktop,
										args.live_refresh_default.size.suffix
											.desktop
									);
								}

								if (
									args.live_refresh_default &&
									args.live_refresh_default.letter_spacing
										.vars &&
									args.live_refresh_default.letter_spacing
										.vars.length
								) {
									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.letter_spacing
											.vars[0],
										args.live_refresh_default.letter_spacing
											.mobile,
										args.live_refresh_default.letter_spacing
											.suffix.mobile
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.letter_spacing
											.vars[1],
										args.live_refresh_default.letter_spacing
											.tablet,
										args.live_refresh_default.letter_spacing
											.suffix.tablet
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.letter_spacing
											.vars[2],
										args.live_refresh_default.letter_spacing
											.desktop,
										args.live_refresh_default.letter_spacing
											.suffix.desktop
									);
								}

								if (
									args.live_refresh_default &&
									args.live_refresh_default.line_height
										.vars &&
									args.live_refresh_default.line_height.vars
										.length
								) {
									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.line_height
											.vars[0],
										args.live_refresh_default.line_height
											.mobile,
										args.live_refresh_default.line_height
											.suffix.mobile
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.line_height
											.vars[1],
										args.live_refresh_default.line_height
											.tablet,
										args.live_refresh_default.line_height
											.suffix.tablet
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										args.live_refresh_default.line_height
											.vars[2],
										args.live_refresh_default.line_height
											.desktop,
										args.live_refresh_default.line_height
											.suffix.desktop
									);
								}

								if (
									newValue.fontSize &&
									newValue.fontSize.vars &&
									newValue.fontSize.vars.length
								) {
									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.fontSize.vars[0],
										newValue.fontSize.mobile,
										newValue.fontSize.suffix.mobile
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.fontSize.vars[1],
										newValue.fontSize.tablet,
										newValue.fontSize.suffix.tablet
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.fontSize.vars[2],
										newValue.fontSize.desktop,
										newValue.fontSize.suffix.desktop
									);
								}

								if (
									newValue.letterSpacing &&
									newValue.letterSpacing.vars &&
									newValue.letterSpacing.vars.length
								) {
									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.letterSpacing.vars[0],
										newValue.letterSpacing.mobile,
										newValue.letterSpacing.suffix.mobile
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.letterSpacing.vars[1],
										newValue.letterSpacing.tablet,
										newValue.letterSpacing.suffix.tablet
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.letterSpacing.vars[2],
										newValue.letterSpacing.desktop,
										newValue.letterSpacing.suffix.desktop
									);
								}

								if (
									newValue.lineHeight &&
									newValue.lineHeight.vars &&
									newValue.lineHeight.vars.length
								) {
									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.lineHeight.vars[0],
										newValue.lineHeight.mobile,
										newValue.lineHeight.suffix.mobile
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.lineHeight.vars[1],
										newValue.lineHeight.tablet,
										newValue.lineHeight.suffix.tablet
									);

									typoStylesObj = addVarStyle(
										typoStylesObj,
										newValue.lineHeight.vars[2],
										newValue.lineHeight.desktop,
										newValue.lineHeight.suffix.desktop
									);
								}

								buildStyle(typoStylesObj);
							}
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Button_Appearance":
							const bgColor = newValue.background || "unset";
							const txtColor = newValue.text || "currentColor";
							const borderColor = newValue.text || "";

							let mainSelector = `html ${args.selector}`,
								colorSelector = `html ${args.selector} .icon-bar`;

							if (
								args.additional &&
								args.additional.additional_buttons
							) {
								_.each(
									args.additional.additional_buttons,
									(button) => {
										mainSelector +=
											",html " + button.button;
										colorSelector +=
											",html " +
											button.button +
											" " +
											button.text;
									}
								);
							}
							style += `${mainSelector} {
										background-color: ${bgColor};
										color: ${txtColor};`;
							if (typeof newValue.borderRadius !== "object") {
								style += `border-radius: ${newValue.borderRadius}px;`;
							} else {
								style += `
                                        border-top-left-radius: ${newValue.borderRadius.top}px;
                                        border-top-right-radius: ${newValue.borderRadius.right}px;
                                        border-bottom-right-radius: ${newValue.borderRadius.bottom}px;
                                        border-bottom-left-radius: ${newValue.borderRadius.left}px;
                                        `;
							}

							if (newValue.type === "outline") {
								if (typeof newValue.borderWidth !== "object") {
									style += `border: ${newValue.borderWidth}px solid ${borderColor};`;
								} else {
									style += `
                                            border-style: solid;
                                            border-color: ${borderColor};
                                            border-top-width: ${newValue.borderWidth.top}px;
                                            border-right-width: ${newValue.borderWidth.right}px;
                                            border-bottom-width: ${newValue.borderWidth.bottom}px;
                                            border-left-width: ${newValue.borderWidth.left}px;
                                            `;
								}
							}

							if (newValue.type === "fill") {
								style += "border: none;";
							}

							style += "}";
							style += `${colorSelector} {
										background-color: ${txtColor};
										color: ${txtColor};
									}`;
							addCSS(settingId, style);
							break;
						case "text":
							const textContainer = document.querySelector(
								args.selector
							);

							if (newValue === "") {
								textContainer.parentNode.removeChild(
									textContainer
								);
								return false;
							}

							if (textContainer === null) {
								const wrap = document.createElement(
									args.additional.html_tag
								);
								wrap.classList.add(args.additional.wrap_class);
								document
									.querySelector(args.additional.parent)
									.prepend(wrap);
							}

							document.querySelector(args.selector).innerHTML =
								newValue;
							break;
						case "stax_range_control":
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Range":
							if (args.additional.type === "svg-icon-size") {
								style += `html ${args.selector} {
											width: ${newValue}px;
											height: ${newValue}px;
										}`;
								addCSS(settingId, style);
								return false;
							} else if (args.additional.type === "default") {
								addCSS(settingId, style);
								return false;
							}

							console.log(args, args.selector, newValue);
							style += `html ${args.selector} {
											${args.additional.type}: ${newValue}px;
										}`;
							addCSS(settingId, style);

							break;
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Color":
							const colorValue =
								newValue === "" ? "unset" : newValue;
							style += `html ${args.selector} {
										${args.additional.prop}: ${colorValue};
									}`;
							addCSS(settingId, style);
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Font_Family":
							break;
						case "\\Stax\\Customizer\\Core\\Controls\\React\\Global_Colors":
							let globalColorStylesObj = getStyle();

							const { palettes, activePalette } = newValue;
							const { colors } = palettes[activePalette];

							Object.keys(colors).map((slug) => {
								if (colors[slug].output === "hsl") {
									var color = hexToHsl(colors[slug].color);

									const mappedHsl = colors[slug].vars.map(
										(hsl_var, i) => {
											return {
												var: hsl_var,
												value: color[i],
											};
										}
									);

									mappedHsl.forEach((item) => {
										globalColorStylesObj = addVarStyle(
											globalColorStylesObj,
											item.var,
											item.value
										);
									});
								} else {
									globalColorStylesObj = addVarStyle(
										globalColorStylesObj,
										colors[slug].vars,
										colors[slug].color
									);
								}

								return false;
							});

							buildStyle(globalColorStylesObj);
							break;
					}
				});
			});
		});
	});

	wp.customize.preview.bind("font-selection", function (data) {
		const controlData = staxCustomizePreview[data.type][data.controlId];

		let selector = controlData.selector;

		const source = data.source;
		const id = data.controlId + "_font_family";

		if (source.toLowerCase() === "google") {
			const linkNode = document.querySelector("#" + id);
			const fontValue = data.value.replace(" ", "+");
			const url =
				"//fonts.googleapis.com/css?family=" +
				fontValue +
				'%3A100%2C200%2C300%2C400%2C500%2C600%2C700%2C800&display=swap"';
			if (linkNode !== null) {
				linkNode.setAttribute("href", url);
			} else {
				const newNode = document.createElement("link");
				newNode.setAttribute("rel", "stylesheet");
				newNode.setAttribute("id", id);
				newNode.setAttribute("href", url);
				newNode.setAttribute("type", "text/css");
				newNode.setAttribute("media", "all");
				document.querySelector("head").appendChild(newNode);
			}
		}

		const { additional = false } = controlData;

		if (additional !== false && additional.cssVar !== undefined) {
			return false;
		}

		const defaultFontface = data.inherit
			? "inherit"
			: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif';

		// Make selector more specific by adding `html` before.
		selector = selector.split(",");
		selector = selector
			.map(function (sel) {
				return "html " + sel;
			})
			.join(",");
		if (data.value === false) {
			addCSS(
				data.controlId,
				selector + "{font-family: " + defaultFontface + ";}"
			);
			return false;
		}
		const parsedFontFamily = parseFontFamily(data.value);
		addCSS(
			data.controlId,
			selector + "{font-family: " + parsedFontFamily + " ;}"
		);
	});

	wp.customize("background_image", function (value) {
		value.bind(function (newval) {
			if (!newval) {
				document
					.querySelector("body")
					.classList.remove("custom-background");
			}
		});
	});
});

(function ($) {
	$.staxCustomizeUtilities = {
		setLiveCss(settings, to) {
			"use strict";
			let result = "";
			if (typeof to !== "object") {
				$(settings.selectors).css(
					settings.cssProperty,
					to.toString() + settings.propertyUnit
				);
				return false;
			}
			$.each(to, function (key, value) {
				if (key === "suffix") {
					return true;
				}
				let unit = settings.propertyUnit;
				if (typeof settings.propertyUnit === "object") {
					unit = settings.propertyUnit[key];
				}
				const styleToAdd =
					settings.selectors +
					"{ " +
					settings.cssProperty +
					":" +
					value +
					unit +
					"}";
				switch (key) {
					default:
					case "mobile":
						result += styleToAdd;
						break;

					case "desktop":
						result +=
							"@media(min-width: 960px) {" + styleToAdd + "}";
						break;

					case "tablet":
						result +=
							"@media (min-width: 576px){" + styleToAdd + "}";
						break;
				}
			});

			const styleClass = $("." + settings.styleClass);
			if (styleClass.length > 0) {
				styleClass.text(result);
			} else {
				$("head").append(
					'<style type="text/css" class="' +
					settings.styleClass +
					'">' +
					result +
					"</style>"
				);
			}
		},
	};
})(jQuery);
