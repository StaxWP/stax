/* global staxMetabox,jQuery */

(function ($) {
	$.staxMetabox = {
		data: staxMetabox,

		init() {
			this.syncRangeToNumber();
			this.handleDependentUi();
		},

		syncRangeToNumber() {
			$("#stax-page-settings .stax-range-input").each(function (
				index,
				element
			) {
				const range = $(element).find("input.stx-range");
				const number = $(element).find("input.stx-number");
				$(range).on("input change", function (e) {
					$(number).val(e.target.value);
				});
				$(number).on("input change", function (e) {
					$(range).val(e.target.value);
				});
			});
		},
		handleDependentUi() {
			$("#stax-page-settings .stax-dependent").each(function (
				index,
				element
			) {
				const influencer = $("input#" + $(element).data("depends"));
				$(influencer).on("change", function () {
					$(element).toggleClass("stax-hidden");
				});
			});
		},
	};
})(jQuery);

jQuery(window).on("load", function () {
	jQuery.staxMetabox.init();
});
