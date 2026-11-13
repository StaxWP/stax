/**
 * Stax front-end behaviour. Plain JavaScript, no jQuery.
 *
 * Every feature checks for its markup first, so a page without it costs nothing.
 * The global `SQ` object and the `reveal`, `masonry` and `svq-post-changed` window
 * events are kept from 1.x so child themes that call them keep working.
 */
(function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var settings = window.staxSettings || {};

	function $all(selector, context) {
		return Array.prototype.slice.call((context || doc).querySelectorAll(selector));
	}

	function on(target, type, handler, options) {
		if (target) {
			target.addEventListener(type, handler, options || false);
		}
	}

	/**
	 * Run `handler` for events on elements matching `selector` (event delegation).
	 */
	function delegate(type, selector, handler) {
		on(doc, type, function (event) {
			var match = event.target.closest ? event.target.closest(selector) : null;
			if (match) {
				handler.call(match, event, match);
			}
		});
	}

	function throttleFrame(callback) {
		var queued = false;
		return function () {
			if (queued) {
				return;
			}
			queued = true;
			window.requestAnimationFrame(function () {
				queued = false;
				callback();
			});
		};
	}

	function slide(element, open) {
		if (!element) {
			return;
		}
		element.hidden = !open;
		element.style.display = open ? 'block' : 'none';
	}

	function observeOnce(elements, callback) {
		if (!elements.length) {
			return;
		}
		if (!('IntersectionObserver' in window)) {
			elements.forEach(function (element, index) {
				callback(element, index);
			});
			return;
		}
		var observer = new IntersectionObserver(function (entries) {
			var count = 1;
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					callback(entry.target, count++);
					observer.unobserve(entry.target);
				}
			});
		});
		elements.forEach(function (element) {
			observer.observe(element);
		});
	}

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	var SQ = window.SQ || {};

	SQ.initialize = {
		init: function () {
			SQ.initialize.initProgressBar();
			SQ.initialize.initPanelContentFade();
			SQ.initialize.initReadingTime();
			SQ.initialize.initStickySidebar();
			SQ.initialize.initArticleNavigation();
			SQ.initialize.initButtonRippleEffect();
			SQ.initialize.initSectionReveal();
			SQ.initialize.initElementReveal();
			SQ.initialize.initMasonry();
			SQ.initialize.enableMultiLevelMenu();
			SQ.initialize.initCommentsActions();
			SQ.initialize.initParallax();
		},

		initSectionReveal: function () {
			observeOnce($all('.section-reveal'), function (element) {
				element.classList.add('section-inview', 'animated');
			});
		},

		initElementReveal: function () {
			observeOnce($all('.will-animate'), function (element, index) {
				var animation = element.getAttribute('data-cssanimate');
				element.style.animationDelay = 0.05 * (1 + index) + 's';
				if (animation && !reduceMotion) {
					element.classList.add(animation);
				}
				element.classList.add('animated');
				element.classList.remove('will-animate');
			});
		},

		initProgressBar: function () {
			var progress = doc.querySelector('.svq-progress-bar progress');
			if (!progress) {
				return;
			}
			var update = function () {
				progress.max = Math.max(1, root.scrollHeight - window.innerHeight);
				progress.value = window.pageYOffset;
			};
			update();
			on(window, 'scroll', throttleFrame(update), { passive: true });
			on(window, 'resize', throttleFrame(update));
		},

		initPanelContentFade: function () {
			var panel = doc.querySelector('.svq-panel');
			var header = panel ? panel.querySelector('.entry-header') : null;
			if (!header || reduceMotion) {
				return;
			}
			var targets = $all('.fade-on-scroll', header);
			if (!targets.length) {
				return;
			}
			var offsetTop = header.getBoundingClientRect().top + window.pageYOffset;
			var height = header.offsetHeight;
			var start = offsetTop - height / 2;
			if (panel.classList.contains('svq-panel--media-below') || panel.classList.contains('svq-panel--half') || offsetTop < height) {
				start = offsetTop;
			}
			var update = function () {
				var scroll = window.pageYOffset;
				var ratio = scroll >= start ? Math.min(1, Math.abs(((scroll - start) / 100) * 0.5)) : 0;
				targets.forEach(function (target) {
					target.style.transform = 'translateY(' + ratio * 40 + 'px)';
					target.style.opacity = String(1 - ratio);
				});
			};
			on(window, 'scroll', throttleFrame(update), { passive: true });
		},

		initReadingTime: function () {
			var readingTime = $all('.reading-time');
			var noTimePrefix = $all('.no-reading-time');
			var content = doc.querySelector('.entry-content');
			if (!readingTime.length || !noTimePrefix.length || !content) {
				return;
			}
			var words = content.textContent.split(/\s+/).filter(Boolean).length;
			var minutes = Math.round(((60 / 130) * words) / 60);
			var counter = doc.getElementById('words');
			if (counter) {
				counter.textContent = String(words);
			}
			noTimePrefix.forEach(function (element) {
				element.style.display = minutes ? 'none' : '';
			});
			readingTime.forEach(function (element) {
				element.textContent = String(minutes || 1);
			});
		},

		/**
		 * Sticky sidebars use CSS `position: sticky` (see .svq-sticky-el in the stylesheet).
		 * This only passes the configured top gap to CSS.
		 */
		initStickySidebar: function () {
			$all('.svq-sticky-el').forEach(function (element) {
				var top = parseInt(element.getAttribute('data-top'), 10);
				if (!isNaN(top)) {
					element.style.setProperty('--stax-sticky-top', top + 'px');
				}
			});
		},

		initButtonRippleEffect: function () {
			delegate('click', '.button-ripple', function (event, button) {
				if (reduceMotion) {
					return;
				}
				var rect = button.getBoundingClientRect();
				var old = button.querySelector('.button_ripple');
				if (old) {
					old.remove();
				}
				var ripple = doc.createElement('span');
				ripple.className = 'button_ripple';
				ripple.style.left = event.clientX - rect.left + 'px';
				ripple.style.top = event.clientY - rect.top + 'px';
				button.appendChild(ripple);
			});
			on(doc.body, 'keydown', function (event) {
				if (event.key === 'Tab') {
					doc.body.classList.add('keyboardfocus');
				}
			});
			on(doc.body, 'mousedown', function () {
				doc.body.classList.remove('keyboardfocus');
			});
		},

		initArticleNavigation: function () {
			var nav = doc.querySelector('.nav-article-section');
			if (!nav) {
				return;
			}
			var last = 0;
			var setUp = function () {
				nav.classList.remove('nav-down');
				nav.classList.add('nav-up');
			};
			if (window.innerHeight >= root.scrollHeight) {
				setUp();
			}
			on(window, 'scroll', throttleFrame(function () {
				var current = window.pageYOffset;
				if (Math.abs(last - current) <= 10) {
					return;
				}
				if (current > last || current < 100) {
					nav.classList.remove('nav-up', 'nav-hidden');
					nav.classList.add('nav-down');
				} else if (current + window.innerHeight < root.scrollHeight) {
					setUp();
				}
				last = current;
			}), { passive: true });
		},

		enableMultiLevelMenu: function () {
			delegate('click', '.widget_nav_menu .menu li.menu-item-has-children > a', function (event, link) {
				var submenu = link.nextElementSibling;
				if (!submenu || !submenu.classList.contains('sub-menu')) {
					return;
				}
				event.preventDefault();
				var open = !link.classList.contains('active-menu');
				$all('.widget_nav_menu .menu > li.menu-item-has-children > a.active-menu').forEach(function (other) {
					if (other !== link && !other.nextElementSibling.contains(link)) {
						other.classList.remove('active-menu');
						other.setAttribute('aria-expanded', 'false');
						slide(other.nextElementSibling, false);
					}
				});
				link.classList.toggle('active-menu', open);
				link.setAttribute('aria-expanded', open ? 'true' : 'false');
				slide(submenu, open);
			});
		},

		initMasonry: function () {
			if (typeof window.Masonry !== 'function') {
				return;
			}
			$all('.svq-masonry-articles').forEach(function (grid) {
				var masonry = new window.Masonry(grid, {
					itemSelector: '.svq-article-col',
					columnWidth: '.svq-grid-sizer',
					originLeft: !root.dir || root.dir !== 'rtl',
				});
				if (typeof window.imagesLoaded === 'function') {
					window.imagesLoaded(grid).on('progress', function () {
						masonry.layout();
					});
				}
			});
		},

		/**
		 * Comments: toggle button and AJAX submit (progressive enhancement over the core form).
		 */
		initCommentsActions: function () {
			delegate('click', '[data-action="btn-comments-trigger"]', function (event, button) {
				event.preventDefault();
				var postId = button.getAttribute('data-post-id');
				var expanded = button.getAttribute('aria-expanded') === 'true';
				doc.body.classList.toggle('comments-open', !expanded);
				button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
				$all('.for-post-id-' + postId).forEach(function (element) {
					element.style.display = expanded ? 'none' : '';
					var respond = element.querySelector('.comment-respond');
					if (respond) {
						respond.style.display = expanded ? 'none' : '';
					}
				});
				window.dispatchEvent(new Event('svq-sticky-sidebar-update'));
			});

			if (!settings.ajaxUrl || !window.fetch || !window.FormData) {
				return;
			}

			delegate('submit', '.comment-form.svq-form', function (event, form) {
				event.preventDefault();
				var submit = form.querySelector('[type=submit]');
				var response = form.querySelector('.request-response');
				var data = new FormData(form);
				data.append('action', 'post_comment');
				if (submit) {
					submit.disabled = true;
					submit.classList.add('is-loading');
				}
				window.fetch(settings.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
					.then(function (res) {
						return res.json();
					})
					.then(function (json) {
						if (json.status !== 'success') {
							if (response) {
								response.innerHTML = json.data || '';
							}
							return;
						}
						if (response) {
							response.innerHTML = '';
						}
						SQ.initialize.insertComment(form, json);
						$all('input[type=text], input[type=email], input[type=url], textarea', form).forEach(function (field) {
							field.value = '';
						});
						$all('input[type=checkbox]', form).forEach(function (field) {
							field.checked = false;
						});
						if (window.addComment && window.addComment.init) {
							window.addComment.init();
						}
						window.dispatchEvent(new Event('resize'));
						window.dispatchEvent(new Event('clear-comment-form-media'));
					})
					.catch(function () {
						// Network or parse error: fall back to the normal form post.
						form.submit();
					})
					.then(function () {
						if (submit) {
							submit.disabled = false;
							submit.classList.remove('is-loading');
						}
					});
			});
		},

		insertComment: function (form, json) {
			var wrapper = doc.createElement('ul');
			wrapper.innerHTML = json.data;

			if (json.reply_to && json.reply_to !== '0') {
				var parent = doc.getElementById('comment-' + json.reply_to);
				if (parent) {
					var children = parent.querySelector(':scope > ul.children');
					if (!children) {
						children = doc.createElement('ul');
						children.className = 'children';
						parent.appendChild(children);
					}
					while (wrapper.firstChild) {
						children.appendChild(wrapper.firstChild);
					}
				}
				var cancel = doc.getElementById('cancel-comment-reply-link');
				if (cancel) {
					cancel.click();
				}
				return;
			}

			var section = form.closest('.svq-section-comments');
			if (!section) {
				return;
			}
			var empty = section.querySelector('.svq-comments.comments-empty');
			if (empty) {
				empty.classList.remove('comments-empty');
			}
			var list = section.querySelector('.comments-list');
			if (list) {
				while (wrapper.firstChild) {
					list.appendChild(wrapper.firstChild);
				}
			}
			if (json.comment_status === 'approved') {
				var status = section.querySelector('.svq-status-comments');
				if (status) {
					status.classList.add('has-comments');
				}
				var counter = section.querySelector('.svq-svg-icon-wrapp');
				if (counter) {
					counter.setAttribute('data-svg-notifier', (parseInt(counter.getAttribute('data-svg-notifier'), 10) || 0) + 1);
				}
				var intro = section.querySelector('.comments-stat-info');
				if (intro && intro.getAttribute('data-first')) {
					intro.textContent = intro.getAttribute('data-first');
				}
			}
		},

		/**
		 * Decorative shapes on the 404 panel. Replaces the jQuery parallax plugin.
		 */
		initParallax: function () {
			var items = $all('[data-parallax]');
			if (!items.length || reduceMotion) {
				return;
			}
			items.forEach(function (item) {
				try {
					item._staxParallax = JSON.parse(item.getAttribute('data-parallax'));
				} catch (e) {
					item._staxParallax = {};
				}
			});
			var update = function () {
				var progress = Math.min(1, window.pageYOffset / Math.max(1, window.innerHeight));
				items.forEach(function (item) {
					var p = item._staxParallax;
					item.style.transform = 'translate(' + (p.x || 0) * progress + 'px,' + (p.y || 0) * progress + 'px)' +
						' rotateZ(' + (p.rotateZ || 0) * progress * 0.1 + 'deg) rotateY(' + (p.rotateY || 0) * progress * 0.1 + 'deg)';
				});
			};
			on(window, 'scroll', throttleFrame(update), { passive: true });
		},

		/** Kept for 1.x child themes: images use native lazy loading now. */
		initLazy: function () {},
	};

	/**
	 * Sliders (related posts, post format galleries) are native CSS scroll-snap rows
	 * (see "Scrollers" in the stylesheet). This only wires the arrow buttons and the
	 * category tabs. Replaces Swiper, which 1.x loaded on every single post.
	 */
	SQ.scroller = {
		track: function (element) {
			return element.querySelector(':scope > .swiper-wrapper') || element;
		},

		step: function (track, forward) {
			var slide = track.querySelector(':scope > .swiper-slide');
			var width = slide ? slide.getBoundingClientRect().width : track.clientWidth;
			var rtl = getComputedStyle(track).direction === 'rtl';
			var before = track.scrollLeft;
			track.scrollBy({ left: (forward !== rtl ? 1 : -1) * width, behavior: reduceMotion ? 'auto' : 'smooth' });
			return before;
		},

		atEnd: function (track, forward) {
			var max = track.scrollWidth - track.clientWidth - 2;
			var pos = Math.abs(track.scrollLeft);
			return forward ? pos >= max : pos <= 2;
		},

		bind: function (container, nextSelector, backSelector, onStep) {
			$all(nextSelector, container).forEach(function (button) {
				on(button, 'click', function (event) {
					event.preventDefault();
					onStep(true);
				});
			});
			$all(backSelector, container).forEach(function (button) {
				on(button, 'click', function (event) {
					event.preventDefault();
					onStep(false);
				});
			});
		},
	};

	SQ.documentOnLoad = {
		init: function () {
			SQ.documentOnLoad.initScrollers();
			SQ.documentOnLoad.initPreloader();
		},

		initScrollers: function () {
			$all('.svq-slider-articles:not(.svq-nested)').forEach(function (element) {
				if (element.closest('.svq-child-carousel')) {
					return;
				}
				var track = SQ.scroller.track(element);
				SQ.scroller.bind(element, '.slide-to--next', '.slide-to--back', function (forward) {
					SQ.scroller.step(track, forward);
				});
			});

			$all('.svq-gallery-slider').forEach(function (element) {
				var track = SQ.scroller.track(element);
				SQ.scroller.bind(element, '.svq-gallery-control-next', '.svq-gallery-control-prev', function (forward) {
					SQ.scroller.step(track, forward);
				});
			});

			$all('.svq-media-slider').forEach(SQ.documentOnLoad.initTabbedScroller);
		},

		/**
		 * "More from the author": one row per category, with category tabs.
		 */
		initTabbedScroller: function (element) {
			var child = element.querySelector('.svq-child-carousel');
			if (!child) {
				return;
			}
			var outer = SQ.scroller.track(child);
			var groups = $all(':scope > .swiper-slide', outer);
			var tabs = $all('.svq-slider-nav-btn', element);
			var current = 0;

			var show = function (index) {
				current = Math.max(0, Math.min(groups.length - 1, index));
				groups.forEach(function (group, i) {
					group.classList.toggle('swiper-slide-active', i === current);
				});
				tabs.forEach(function (tab, i) {
					tab.setAttribute('aria-selected', i === current ? 'true' : 'false');
					tab.parentNode.classList.toggle('svq-list-item-active', i === current);
				});
			};

			tabs.forEach(function (tab, i) {
				on(tab, 'click', function (event) {
					event.preventDefault();
					show(i);
				});
			});

			SQ.scroller.bind(element, '.slide-to--next', '.slide-to--back', function (forward) {
				var inner = groups[current] ? SQ.scroller.track(groups[current].querySelector('.svq-slider-articles') || groups[current]) : null;
				if (inner && !SQ.scroller.atEnd(inner, forward)) {
					SQ.scroller.step(inner, forward);
				} else {
					show(current + (forward ? 1 : -1));
				}
			});
		},

		initPreloader: function () {
			$all('.svq-page-loader').forEach(function (loader) {
				loader.classList.add('not-visible');
			});
		},
	};

	window.SQ = SQ;

	if (doc.readyState === 'loading') {
		on(doc, 'DOMContentLoaded', SQ.initialize.init);
	} else {
		SQ.initialize.init();
	}

	if (doc.readyState === 'complete') {
		SQ.documentOnLoad.init();
	} else {
		on(window, 'load', SQ.documentOnLoad.init);
	}

	on(window, 'reveal', SQ.initialize.initElementReveal);
	on(window, 'masonry', SQ.initialize.initMasonry);
	on(window, 'svq-post-changed', function () {
		SQ.initialize.initElementReveal();
		SQ.initialize.initStickySidebar();
	});
})();
