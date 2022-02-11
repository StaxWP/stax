import "./editor.scss";

import { registerPlugin } from "@wordpress/plugins";
import { Icon } from "@wordpress/components";

import Sidebar from "./components/Sidebar";
import { staxIcon } from "./helpers/icons.js";

registerPlugin("meta-sidebar", {
	icon: <Icon icon={staxIcon} />,
	render: Sidebar,
});
