(function ($, api) {
	"use strict";

	api.controlConstructor["select-multiple"] = api.Control.extend({
		ready: function () {
			var control = this;

			$("select", control.container).change(function () {
				var value = $(this).val();

				if (null === value) {
					control.setting.set("");
				} else {
					control.setting.set(value);
				}
			});
		},
	});
})(jQuery, wp.customize);
