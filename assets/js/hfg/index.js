import { initNavigation, repositionDropdowns } from "./navigation.js";
import { HFG } from "./hgf.js";

function run() {
	window.HFG = new HFG();
	initNavigation();
}

function onResizeDebouncedRun() {
	repositionDropdowns();
}

/**
 * Run JS on load.
 */
window.addEventListener("load", () => {
	run();
});

/**
 * Do resize events debounced.
 */
let staxResizeTimeout;
window.addEventListener("resize", () => {
	clearTimeout(staxResizeTimeout);
	staxResizeTimeout = setTimeout(onResizeDebouncedRun, 500);
});
