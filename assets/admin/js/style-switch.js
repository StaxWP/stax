/**
 * Stax style switch.
 *
 * Admin screens: dismissing the notice with its close button remembers "Not now".
 * Customizer: the notice as a notification, the preview toggle (a cookie read only by the preview)
 * and the switch buttons of the "Stax style" section.
 */
(function (settings) {
	'use strict';

	if (!settings) {
		return;
	}

	function post(action) {
		var body = new FormData();
		body.append('action', settings.action);
		body.append('do', action);
		body.append('_wpnonce', settings.nonce);

		return fetch(settings.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' }).then(function (response) {
			return response.json();
		});
	}

	// Admin screens: the close button of the notice means "Not now".
	if (!window.wp || !window.wp.customize || !settings.section) {
		document.addEventListener('click', function (event) {
			if (event.target.closest('.stax-style-notice .notice-dismiss')) {
				post('dismiss');
			}
		});

		return;
	}

	var api = window.wp.customize;
	var code = 'stax_style_notice';
	var removingNotice = false;

	// Empty mode: the saved style.
	function syncRadios(mode) {
		document.querySelectorAll('input[name="stax-style-preview"]').forEach(function (input) {
			input.checked = mode ? input.value === mode : '1' === input.dataset.current;
		});
	}

	function setPreview(mode) {
		document.cookie = settings.cookie + '=' + (mode || '') + '; path=' + settings.cookiePath + (mode ? '' : '; max-age=0') + '; SameSite=Lax' + ('https:' === window.location.protocol ? '; Secure' : '');
		api.previewer.refresh();
		syncRadios(mode);
	}

	function removeNotice() {
		removingNotice = true;
		api.notifications.remove(code);
		removingNotice = false;
	}

	function message(text, type) {
		var box = document.querySelector('.stax-style-message');

		if (!box) {
			return;
		}

		var notice = document.createElement('div');
		var paragraph = document.createElement('p');
		notice.className = 'notice inline notice-' + (type || 'info');
		paragraph.textContent = text;
		notice.appendChild(paragraph);
		box.innerHTML = '';
		box.appendChild(notice);
		box.focus();
	}

	// Reloads the Customizer with the result; the section opens and announces it.
	function reloadWith(result) {
		var url = new URL(window.location.href);
		url.searchParams.delete('changeset_uuid');
		url.searchParams.delete('autofocus[section]');
		url.searchParams.delete(settings.cookie);
		url.searchParams.set('stax-style', result);
		window.location.href = url.toString();
	}

	function switchStyle(action, button) {
		if (!api.state('saved')()) {
			api.section(settings.section).focus();
			message(settings.unsaved, 'warning');

			return;
		}

		button.disabled = true;
		button.setAttribute('aria-busy', 'true');
		button.dataset.label = button.textContent;
		button.textContent = settings.working;

		post(action).then(function (response) {
			if (response && response.success) {
				reloadWith(response.data.result);

				return;
			}

			throw new Error('failed');
		}).catch(function () {
			button.disabled = false;
			button.removeAttribute('aria-busy');
			button.textContent = button.dataset.label;
			message(settings.failed, 'error');
		});
	}

	api.bind('ready', function () {
		if (settings.showNotice) {
			api.notifications.add(new api.Notification(code, {
				type: 'info',
				dismissible: true,
				message: settings.noticeHtml,
				containerClasses: 'stax-style-notice-c'
			}));

			// The close button means "Not now".
			api.notifications.bind('remove', function (notification) {
				if (notification && notification.code === code && !removingNotice) {
					post('dismiss');
				}
			});
		}

		document.addEventListener('click', function (event) {
			var button = event.target.closest('[data-stax-style]');

			if (!button) {
				return;
			}

			event.preventDefault();

			var action = button.getAttribute('data-stax-style');

			if ('preview' === action) {
				removeNotice();
				setPreview('new');
				api.section(settings.section).focus();
			} else if ('dismiss' === action) {
				removeNotice();
				post('dismiss');
			} else {
				switchStyle(action, button);
			}
		});

		document.addEventListener('change', function (event) {
			if ('stax-style-preview' === event.target.name) {
				setPreview('1' === event.target.dataset.current ? '' : event.target.value);
			}
		});

		// Opened from "Preview" on the Dashboard, Themes or Stax style screen.
		syncRadios(settings.preview);

		// After a switch: open the section and move focus to the result, so screen readers announce it.
		if (settings.result) {
			api.section(settings.section, function (section) {
				section.focus({
					completeCallback: function () {
						var box = document.querySelector('.stax-style-message');

						if (box) {
							box.focus();
						}
					}
				});
			});
		}
	});
})(window.staxStyleSwitch);
