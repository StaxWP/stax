import "./public-path.js";
import { render } from "@wordpress/element";

import { init as initDynamicFields } from "./dynamic-fields/index";
import { ToggleControl } from "./toggle/Control";
import { ResponsiveToggleControl } from "./responsive-toggle/Control";
import { BackgroundControl } from "./background/Control";
import { SpacingControl } from "./spacing/Control";
import { TypefaceControl } from "./typeface/Control";
import { FontFamilyControl } from "./font-family/Control";
import { RadioButtonsControl } from "./radio-buttons/Control";
import { ButtonAppearanceControl } from "./button-appearance/Control";
import { RangeControl } from "./range/Control";
import { ResponsiveRangeControl } from "./responsive-range/Control";
import { ColorControl } from "./color/Control";
import { PresetsSelectorControl } from "./presets-selector/Control";
import { MultiSelectControl } from "./multiselect/Control";
import { ResponsiveRadioButtonsControl } from "./responsive-radio-buttons/Control";
import { RadioImageControl } from "./radio-image/Control";
import { OrderingControl } from "./ordering/Control";
import { GlobalColorsControl } from "./global-colors/Control";
import { NRSpacingControl } from "./non-responsive-spacing/Control";
import { InlineSelectControl } from "./inline-select/Control";
import { BuilderControl } from "./builder/Control";
import { BuilderColumns } from "./builder-columns/Control";
import { InstructionsControl } from "./builder-instructions/Control";
import { HeadingControl } from "./heading/Control";
import { RepeaterControl } from "./repeater/Control";
import { RichTextControl } from "./rich-text/Control";

import "./style.scss";
import Instructions from "./builder-instructions/Instructions.tsx";

const { controlConstructor } = wp.customize;

controlConstructor.stax_toggle_control = ToggleControl;
controlConstructor.stax_responsive_toggle_control = ResponsiveToggleControl;
controlConstructor.stax_background_control = BackgroundControl;
controlConstructor.stax_spacing = SpacingControl;
controlConstructor.stax_typeface_control = TypefaceControl;
controlConstructor.stax_font_family_control = FontFamilyControl;
controlConstructor.stax_radio_buttons_control = RadioButtonsControl;
controlConstructor.stax_button_appearance = ButtonAppearanceControl;
controlConstructor.stax_range_control = RangeControl;
controlConstructor.stax_responsive_range_control = ResponsiveRangeControl;
controlConstructor.stax_color_control = ColorControl;
controlConstructor.stax_presets_selector = PresetsSelectorControl;
controlConstructor.stax_multiselect = MultiSelectControl;
controlConstructor.stax_responsive_radio_buttons_control =
	ResponsiveRadioButtonsControl;
controlConstructor.stax_radio_image_control = RadioImageControl;
controlConstructor.stax_ordering_control = OrderingControl;
controlConstructor.stax_global_colors = GlobalColorsControl;
controlConstructor.stax_non_responsive_spacing = NRSpacingControl;
controlConstructor.stax_inline_select = InlineSelectControl;
controlConstructor.stax_builder_control = BuilderControl;
controlConstructor.stax_builder_columns = BuilderColumns;
controlConstructor.hfg_instructions = InstructionsControl;
controlConstructor.stax_customizer_heading = HeadingControl;
controlConstructor.stax_repeater_control = RepeaterControl;
controlConstructor.stax_rich_text = RichTextControl;

const initDeviceSwitchers = () => {
	const deviceButtons = document.querySelector(
		"#customize-footer-actions .devices, .hfg--cb-devices-switcher a.switch-to"
	);
	deviceButtons.addEventListener("click", function (e) {
		const event = new CustomEvent("staxChangedRepsonsivePreview", {
			detail: e.target.dataset.device,
		});
		document.dispatchEvent(event);
	});
};

const initBlogPageFocus = () => {
	wp.customize.section("stax_blog_archive_layout", (section) => {
		section.expanded.bind((isExpanded) => {
			const front = wp.customize.control("show_on_front").setting();
			let pageId = "";
			if (front === "page") {
				pageId = wp.customize.control("page_for_posts").setting();
			}

			if (isExpanded) {
				wp.customize.previewer.previewUrl.set(
					pageId ? `/?page_id=${pageId}` : "/"
				);
			}
		});
	});
};

const initQuickLinksSections = () => {
	const quickLinks = document.querySelectorAll(
		".control-section.stax-quick-links"
	);

	quickLinks.forEach((node) => {
		const slug = node.getAttribute("data-slug");
		const section = wp.customize.section(slug);

		if (!section) {
			return;
		}

		render(<Instructions control={section} />, section.container[0]);
	});
};
const bindDataAttrQuickLinks = () => {
	const dataControlFocusElements = document.querySelectorAll(
		"[data-control-focus]"
	);

	if (!dataControlFocusElements) {
		return;
	}

	dataControlFocusElements.forEach((el) => {
		el.addEventListener("click", () => {
			const attribute = el.getAttribute("data-control-focus");

			if (!attribute) {
				return;
			}

			const control = window.wp.customize.control(attribute);

			if (!control) {
				return;
			}

			control.focus();
		});
	});
};

window.wp.customize.bind("ready", () => {
	initDynamicFields();
	initQuickLinksSections();
	bindDataAttrQuickLinks();
	initBlogPageFocus();
	initDeviceSwitchers();
	initBlogPageFocus();
});

window.HFG = {
	getSettings: () => {
		const usedSettings = {};
		const { headerControls } = window.StaxReactCustomize;
		Object.keys(headerControls).forEach((modKey) => {
			const control = window.wp.customize.control(modKey);
			// If the control isn't there don't do anything.
			if (!control) {
				return;
			}

			// If the value is default don't do anything.
			const value = control.setting();

			// Compare stringified versions as this can contain arrays and objects.
			if (
				JSON.stringify(value) === JSON.stringify(headerControls[modKey])
			) {
				return;
			}

			// Save key/value pair.
			usedSettings[modKey] = value;
		});
		return JSON.stringify(usedSettings);
	},
};
