/* global StaxReactCustomize, Event */

import DynamicFieldInserter from "./dynamic-field-inserter.js";
import { render } from "@wordpress/element";

/**
 * Initialize the dynamic tag buttons.
 *
 * @return {boolean}|{void}
 */
export const init = () => {
	const controls = StaxReactCustomize?.dynamicTags?.controls || false;
	if (!controls) {
		return false;
	}
	StaxReactCustomize.fieldSelection = {};
	Object.keys(controls).forEach((controlId) => {
		const control = wp.customize.control(controlId);
		if (typeof control === "undefined") {
			return false;
		}
		const container = control.container[0];
		const dynamicControlWrap = document.createElement("div");

		dynamicControlWrap.setAttribute("id", `dynamic-${controlId}`);
		dynamicControlWrap.classList.add("stax-dynamic-tag-selector");
		container.classList.add("stax-has-dynamic-tag-selector");
		container.appendChild(dynamicControlWrap);

		const input = document.querySelector(
			`[data-customize-setting-link="${control.id}"]`
		);

		input.addEventListener("focusout", function (e) {
			StaxReactCustomize.fieldSelection[controlId] = {
				start: e.target.selectionStart,
				end: e.target.selectionEnd,
			};
		});
		render(
			<DynamicFieldInserter
				options={StaxReactCustomize?.dynamicTags?.options || []}
				allowedOptionsTypes={controls[controlId]}
				onSelect={(magicTag, group) =>
					addToField(magicTag, control, group)
				}
			/>,
			dynamicControlWrap
		);
	});
};

/**
 * Add dynamic tag to input field.
 *
 * @param {string} magicTag
 * @param {Object} control
 * @param {string} optionType
 */
const addToField = function (magicTag, control, optionType) {
	let tag;
	const input = document.querySelector(
		`[data-customize-setting-link="${control.id}"]`
	);

	if (optionType === "url" && control.params.type === "textarea") {
		tag = `<a href="{${magicTag}}">Link</a>`;
	} else {
		tag = `{${magicTag}}`;
	}

	if (optionType === "url" && input.value === "#") {
		input.value = tag;
	} else if (StaxReactCustomize.fieldSelection[control.id]) {
		const { start, end } = StaxReactCustomize.fieldSelection[control.id];
		const length = input.value.length;
		input.value =
			input.value.substring(0, start) +
			tag +
			input.value.substring(end, length);
	} else {
		input.value += tag;
	}
	StaxReactCustomize.fieldSelection[control.id] = false;
	input.focus();
	input.dispatchEvent(new Event("change"));
};
