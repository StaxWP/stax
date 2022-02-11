(function ($) {
	"use strict";
	$.themePanel = function () {
		this.scope = $(document);
		this.init();
	};

	$.themePanel.prototype = {
		init: function () {
			var fw = this;

			fw.initTabs();

			// Init tooltips
			fw.initTooltips();

			// Init plugin ajax actions
			fw.initAddonsAjax();
		},

		initTooltips: function () {
			$(".tooltip-me").tooltip({
				position: { my: "center bottom", at: "center top-10" },
			});
		},

		initTabs: function () {
			var tabs = $(".cd-tabs");

			tabs.each(function () {
				var tab = $(this),
					tabItems = tab.find("ul.cd-tabs-navigation"),
					tabContentWrapper = tab.children("ul.cd-tabs-content"),
					tabNavigation = tab.find("nav");

				tabItems.on("click", "a", function (event) {
					event.preventDefault();
					var selectedItem = $(this);
					if (!selectedItem.hasClass("selected")) {
						var selectedTab = selectedItem.data("content"),
							selectedContent = tabContentWrapper.find(
								'li[data-content="' + selectedTab + '"]'
							);

						// slectedContentHeight = selectedContent.innerHeight();

						tabItems.find("a.selected").removeClass("selected");
						selectedItem.addClass("selected");
						selectedContent
							.addClass("selected")
							.siblings("li")
							.removeClass("selected");

						// change hash
						window.location.hash = selectedTab;
					}
				});
			});

			// Activate specific link on new page
			var hash = window.location.hash;
			var navLi = $(".cd-tabs-navigation > li");
			var string = hash.replace("-link", "");

			if (hash !== "" && navLi.find('a[href="' + string + '"]').length) {
				navLi.find('a[href="' + string + '"]').trigger("click");
			}
		},

		initAddonsAjax: function () {
			var fw = this;

			$(document).on("click", ".svq-extension-button", function (e) {
				e.preventDefault();

				// Perform the ajax call based on action
				var config = {};
				config.button = $(this);

				// config.button			= config.button.find('.spinner');
				config.status_classes =
					"svq-active svq-inactive svq-not-installed";
				config.el_container = config.button.closest(".svq-extension");
				config.status_holder = config.el_container.find(
					".svq-extension-status"
				);
				config.action = config.button.data("action");
				config.nonce = config.button.data("nonce");
				config.slug = config.button.data("slug");

				if (config.el_container.hasClass("svq-addons-disabled")) {
					return false;
				}

				var data = {
					security: config.nonce,
					action: "sq_do_plugin_action",
					plugin_action: config.button.data("action") || false,
					slug: config.button.data("slug") || false,
				};

				// Don't allow the user to click the button multiple times
				if (config.button.hasClass("is-active")) {
					return false;
				}

				// Add the loading class
				config.button.addClass("is-active");

				fw.performAjaxCall(data, config);

				return false;
			});
		},

		performAjaxCall: function (data, config, callback) {
			var fw = this;

			// Perform the ajax call
			$.ajax({
				type: "post",
				dataType: "json",
				url: ajaxurl,
				data: data,
				success: function (response) {
					// If we received an error, display it
					if (response.data.error) {
						alert(response.data.error);
					}

					if (response.data.redirect) {
						window.location = response.data.redirect;
					}

					// Update the plugin status
					fw.updatePluginStatus(config, response);

					if (typeof callback !== "undefined") {
						callback();
					}

					config.button.removeClass("is-active");
				},
				error: function (response) {
					var tryResponse = response.responseText.match(
						/{["{]+.*}$/i
					);
					if (tryResponse[0] && fw.isJsonString(tryResponse[0])) {
						// var messageModal = new SQModal();
						// messageModal.open({text: response.responseText, buttons: false, closeBtn: true});

						var goodResponse = JSON.parse(tryResponse[0]);

						// Update the plugin status
						fw.updatePluginStatus(config, goodResponse);
					} else {
						alert("There was a problem performing the action.");
					}

					if (typeof callback !== "undefined") {
						callback();
					}

					config.button.removeClass("is-active");
				},
			});
		},
		isJsonString: function (str) {
			try {
				JSON.parse(str);
			} catch (e) {
				return false;
			}
			return true;
		},
		updatePluginStatus: function (config, response) {
			// Update the plugin status
			config.el_container.removeClass(config.status_classes);
			config.el_container.addClass(response.data.status);
			config.status_holder.text(response.data.status_text);

			// Update the plugin
			config.button.data("action", response.data.action);
			config.button.text(response.data.action_text);
		},
	};

	$(document).ready(function () {
		// Call this on document ready
		$.themePanel = new $.themePanel();
	});
})(jQuery);
