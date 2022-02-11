import { PluginSidebar, PluginSidebarMoreMenuItem } from "@wordpress/edit-post";
import { __ } from "@wordpress/i18n";

import { useShortcut } from "@wordpress/keyboard-shortcuts";
import { useCallback } from "@wordpress/element";
import { compose } from "@wordpress/compose";
import { withDispatch, withSelect, select, dispatch } from "@wordpress/data";

import MetaFieldsManager from "./MetaFieldsManager";

const Sidebar = compose(
	withDispatch((dispatchHandler) => {
		dispatchHandler("core/keyboard-shortcuts").registerShortcut({
			name: "stax/open-meta-sidebar",
			category: "block",
			description: __("Open Stax Sidebar", "stax"),
			keyCombination: {
				modifier: "access",
				character: "s",
			},
		});
	}),
	withSelect((selectHandler) => {
		return {
			template:
				selectHandler("core/editor").getEditedPostAttribute("template"),
		};
	})
)(function (templateData) {
	useShortcut(
		"stax/open-meta-sidebar",
		useCallback(() => {
			const currentActiveSidebar =
				select("core/edit-post").getActiveGeneralSidebarName();
			if (currentActiveSidebar) {
				dispatch("core/edit-post").closeGeneralSidebar(
					currentActiveSidebar
				);
				if ("meta-sidebar/stax-meta-sidebar" !== currentActiveSidebar) {
					dispatch("core/edit-post").openGeneralSidebar(
						"meta-sidebar/stax-meta-sidebar"
					);
				}
			} else {
				dispatch("core/edit-post").openGeneralSidebar(
					"meta-sidebar/stax-meta-sidebar"
				);
			}
		}, [])
	);

	if ("elementor_canvas" === templateData.template) {
		document.getElementById("stax-page-settings-notice").style.display =
			"none";
		return false;
	}

	document.getElementById("stax-page-settings-notice").style.display =
		"block";

	let sidebarLabel = __("Stax Options", "stax");

	return (
		<>
			<PluginSidebarMoreMenuItem target="stax-meta-sidebar">
				{sidebarLabel}
			</PluginSidebarMoreMenuItem>
			<PluginSidebar name="stax-meta-sidebar" title={sidebarLabel}>
				<MetaFieldsManager />
			</PluginSidebar>
		</>
	);
});

export default Sidebar;
