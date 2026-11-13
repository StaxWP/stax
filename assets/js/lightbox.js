/**
 * Stax lightbox: opens links marked with data-lightbox="group" in a native <dialog>.
 * Replaces the jQuery lightbox2 plugin used until 1.2.2. Keyboard: Esc closes, arrows move.
 */
(function () {
	'use strict';

	var doc = document;
	var i18n = window.staxLightbox || { close: 'Close', previous: 'Previous image', next: 'Next image', label: 'Image viewer' };
	var dialog;
	var image;
	var caption;
	var group = [];
	var index = 0;
	var opener = null;

	function button(className, label, text) {
		var el = doc.createElement('button');
		el.type = 'button';
		el.className = className;
		el.setAttribute('aria-label', label);
		el.textContent = text;
		return el;
	}

	function build() {
		dialog = doc.createElement('dialog');
		dialog.className = 'stax-lightbox';
		dialog.setAttribute('aria-label', i18n.label);

		var figure = doc.createElement('figure');
		image = doc.createElement('img');
		image.alt = '';
		caption = doc.createElement('figcaption');
		figure.appendChild(image);
		figure.appendChild(caption);

		var close = button('stax-lightbox__close', i18n.close, '×');
		var prev = button('stax-lightbox__prev', i18n.previous, '‹');
		var next = button('stax-lightbox__next', i18n.next, '›');

		close.addEventListener('click', function () {
			dialog.close();
		});
		prev.addEventListener('click', function () {
			show(index - 1);
		});
		next.addEventListener('click', function () {
			show(index + 1);
		});
		dialog.addEventListener('click', function (event) {
			if (event.target === dialog) {
				dialog.close();
			}
		});
		dialog.addEventListener('keydown', function (event) {
			var rtl = doc.documentElement.dir === 'rtl';
			if (event.key === 'ArrowRight') {
				show(index + (rtl ? -1 : 1));
			} else if (event.key === 'ArrowLeft') {
				show(index + (rtl ? 1 : -1));
			}
		});
		dialog.addEventListener('close', function () {
			image.removeAttribute('src');
			if (opener) {
				opener.focus();
			}
		});

		dialog.appendChild(close);
		dialog.appendChild(prev);
		dialog.appendChild(figure);
		dialog.appendChild(next);
		doc.body.appendChild(dialog);
	}

	function show(i) {
		if (!group.length) {
			return;
		}
		index = (i + group.length) % group.length;
		var link = group[index];
		var thumb = link.querySelector('img');
		image.src = link.href;
		image.alt = thumb ? thumb.alt : '';
		caption.textContent = link.getAttribute('data-caption') || link.getAttribute('data-title') || link.getAttribute('title') || '';
		dialog.classList.toggle('is-single', group.length < 2);
	}

	doc.addEventListener('click', function (event) {
		var link = event.target.closest ? event.target.closest('a[data-lightbox]') : null;
		if (!link || event.ctrlKey || event.metaKey || event.shiftKey || typeof HTMLDialogElement !== 'function') {
			return;
		}
		event.preventDefault();
		if (!dialog) {
			build();
		}
		var name = link.getAttribute('data-lightbox');
		group = Array.prototype.slice.call(doc.querySelectorAll('a[data-lightbox]')).filter(function (el) {
			return el.getAttribute('data-lightbox') === name;
		});
		opener = link;
		show(group.indexOf(link));
		dialog.showModal();
	});
})();
