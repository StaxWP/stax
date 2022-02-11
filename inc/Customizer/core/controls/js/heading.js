(function ($) {
	"use strict";
	wp.staxHeadingAccordion = {
		init: function () {
			this.handleToggle();
		},
		handleToggle: function () {
			$(
				".customize-control-customizer-heading.accordion .stax-customizer-heading"
			).on("click", function () {
				var accordion = $(this).closest(".accordion");
				$(accordion).toggleClass("expanded");
				return false;
			});
		},
	};

	$(document).ready(function () {
		wp.staxHeadingAccordion.init();
	});
})(jQuery);
