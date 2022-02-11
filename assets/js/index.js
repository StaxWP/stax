var SQ = SQ || {};

(function ($) {
	// USE STRICT
	"use strict";

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
			SQ.initialize.initHeaderCart();
			SQ.initialize.initLazy();
		},
		initSectionReveal: function () {
			var elements = document.querySelectorAll(".section-reveal");
			var config = {
				root: null,
				rootMargin: "0px",
			};

			var observer = new IntersectionObserver(function (entries, self) {
				$.each(entries, function (index, entry) {
					if (entry.isIntersecting) {
						$(entry.target).addClass("section-inview animated");
						self.unobserve(entry.target);
					}
				});
			}, config);

			$.each(elements, function (index, element) {
				observer.observe(element);
			});
		},

		initElementReveal: function () {
			var elements = document.querySelectorAll(".will-animate");
			var config = {
				root: null,
				rootMargin: "0px",
			};

			var observer = new IntersectionObserver(function (entries, self) {
				var x = 1;
				$.each(entries, function (index, entry) {
					if (entry.isIntersecting) {
						SQ.initialize.revealElement(entry.target, x);
						x++;
						self.unobserve(entry.target);
						entry.target.classList.remove("will-animate");
					}
				});
			}, config);

			$.each(elements, function (index, element) {
				observer.observe(element);
			});
		},

		revealElement: function (element, index) {
			var animation = $(element).data("cssanimate");

			$(element)
				.css({ "animation-delay": 0.05 * (1 + index) + "s" })
				.addClass(animation)
				.addClass("animated")
				.data("animation-state", "done");
		},

		initProgressBar: function () {
			var progressBar = $("#progressBar");
			if (progressBar.length) {
				var getMax = function () {
					return $document.height() - $window.height();
				};

				var getValue = function () {
					return $window.scrollTop();
				};

				if ("max" in document.createElement("progress")) {
					var progressEl = $(".svq-progress-bar progress");

					progressEl.prop({ max: getMax(), value: getValue() });

					$document.on("scroll", function () {
						progressEl.prop({ value: getValue() });
					});

					$window.resize(function () {
						progressEl.prop({ max: getMax(), value: getValue() });
					});
				}
			}
		},

		initPanelContentFade: function () {
			var panel = $(".svq-panel");
			var entryHeader = panel.find(".entry-header");

			if (panel.length && entryHeader.length) {
				var offsetTop = entryHeader.offset().top;
				var height = entryHeader.height();
				var fadeStartPoint = offsetTop - height + height / 2;

				if (
					panel.hasClass("svq-panel--media-below") ||
					panel.hasClass("svq-panel--half") ||
					offsetTop < height
				) {
					fadeStartPoint = offsetTop;
				}

				var lastScroll = 0;

				$window.on("scroll", function () {
					var scroll = $(this).scrollTop();

					var ratio = Math.abs(
						((scroll - fadeStartPoint) / 100) * 0.5
					);

					if (scroll > lastScroll) {
						if (scroll >= fadeStartPoint) {
							if (ratio <= 1)
								entryHeader.find(".fade-on-scroll").css({
									transform:
										"translateY(" + ratio * 40 + "px)",
									opacity: 1 - ratio,
								});
						}
					} else if (scroll < lastScroll) {
						if (scroll >= fadeStartPoint) {
							if (ratio <= 1)
								entryHeader.find(".fade-on-scroll").css({
									transform:
										"translateY(" + ratio * 40 + "px)",
									opacity: 1 - ratio,
								});
						} else {
							entryHeader.find(".fade-on-scroll").css({
								transform: "translateY(0px)",
								opacity: 1,
							});
						}
					}

					lastScroll = scroll;
				});
			}
		},

		initReadingTime: function () {
			var noTimePrefix = $(".no-reading-time");
			var readingTime = $(".reading-time");

			if (noTimePrefix.length && readingTime.length) {
				noTimePrefix.hide();

				var words = $(".entry-content")
					.text()
					.replace(/(\r\n|\n|\r)/gm, "")
					.split(" ");
				words = words.filter(function (v) {
					return v !== "";
				});

				$("span#words").html(words.length);

				var readingSpeed = 130;
				var readingTimeSec = (60 / readingSpeed) * words.length;
				var readingTimeMin = Math.round(readingTimeSec / 60);

				if (!readingTimeMin) {
					readingTimeMin = 1;
					noTimePrefix.show();
				}

				readingTime.html(Math.round(readingTimeMin));
			}
		},

		initStickySidebar: function () {
			if (!$.fn.hcSticky) {
				return false;
			}
			$(".svq-sticky-el").each(function (index, el) {
				var stickElement = null;
				var id = $(el).data("stick-to");
				if ($(el).parent().attr("id") === id) {
					stickElement = $(el).parent()[0];
				}

				if (stickElement) {
					$(el).hcSticky({
						stickTo: stickElement,
						followScroll: false,
						top: parseInt($(el).data("top")),
						responsive: {
							980: {
								disable: true,
							},
						},
					});
				}
			});
		},

		initButtonRippleEffect: function () {
			$body.on("click", ".button-ripple", function (e) {
				var x = e.pageX - $(this).offset().left;
				var y = e.pageY - $(this).offset().top;

				$(this).find(".button_ripple").remove();

				$('<span class="button_ripple"/>')
					.appendTo(this)
					.css({ left: x, top: y });
			});

			$body.on("keydown", function (e) {
				if (e.which == 9) {
					$body.addClass("keyboardfocus");
				}
			});

			$body.on("click", function (e) {
				$body.removeClass("keyboardfocus");
			});
		},

		initArticleNavigation: function () {
			var nav = $(".nav-article-section");

			if (nav.length) {
				var didScroll;
				var lastScrollTop = 0;
				var delta = 10;

				if ($window.height() >= $document.height()) {
					nav.removeClass("nav-down").addClass("nav-up");
				}

				$window.resize(function () {
					if ($window.height() >= $document.height()) {
						nav.removeClass("nav-down").addClass("nav-up");
					}
				});

				$window.on("scroll", function (event) {
					didScroll = true;
				});

				var showedBefore = false;

				setInterval(function () {
					if (didScroll) {
						var st = $(this).scrollTop();

						if (Math.abs(lastScrollTop - st) <= delta) return;

						if (st > lastScrollTop || st < 100) {
							nav.removeClass("nav-up")
								.addClass("nav-down")
								.removeClass("nav-hidden");
						} else if (st + $window.height() < $document.height()) {
							if (!showedBefore) {
								SQ.initialize.initLazy();
								showedBefore = true;
							}
							nav.removeClass("nav-down").addClass("nav-up");
						}

						lastScrollTop = st;
						didScroll = false;
					}
				}, 200);
			}
		},

		enableMultiLevelMenu: function () {
			$(".widget_nav_menu .menu li.menu-item-has-children a").on(
				"click",
				function (e) {
					if ($(this).next("ul.sub-menu").length) {
						e.preventDefault();

						var _this = $(this);

						$(
							".widget_nav_menu .menu > li.menu-item-has-children > a.active-menu"
						).each(function () {
							if (
								!$(this).is(_this) &&
								!$(this).next().find(_this).length
							) {
								$(this).removeClass("active-menu");
								$(this).next().slideUp();
							}
						});

						_this.toggleClass("active-menu");

						if (!_this.next().is(":visible")) {
							_this.next().slideDown();
						} else {
							_this.next().slideUp();
						}
					}
				}
			);

			/* Allow to click on parent items that have a link */
			$("#header a.dropdown-toggle").on("click", function (e) {
				if (
					!$("html").hasClass("touchevents") &&
					$(window).width() >= 991
				) {
					location.href = this.href;
				}
			});

			$(".dropdown-menu a.dropdown-toggle").on("click", function (e) {
				if (!$(this).next().hasClass("show")) {
					$(this)
						.parents(".dropdown-menu")
						.first()
						.find(".show")
						.removeClass("show");
				}

				var $subMenu = $(this).next(".dropdown-menu");
				$subMenu.toggleClass("show");
				$(this).closest(".dropdown").toggleClass("show");

				$(this)
					.parents("li.nav-item.dropdown.show")
					.on("hidden.bs.dropdown", function (e) {
						$(".dropdown-submenu .show").removeClass("show");
					});

				return false;
			});
		},

		initMasonry: function () {
			var masonryArticles = $(".svq-masonry-articles");
			if (masonryArticles.length) {
				masonryArticles.masonry({
					itemSelector: ".svq-article-col",
					columnWidth: ".svq-grid-sizer",
				});
			}
		},

		initLazy: function () {
			if (typeof LazyLoad === "function") {
				new LazyLoad({
					elements_selector: ".lazy",
					class_loading: "lazy-is-loading",
					class_loaded: "lazy-is-loaded",
				});
			}
		},

		initCommentsActions: function () {
			$(document).on(
				"click",
				'[data-action="btn-comments-trigger"]',
				function (e) {
					e.preventDefault();

					var postID = $(this).data("post-id");

					$body.addClass("comments-open");

					$(".for-post-id-" + postID)
						.show()
						.find(".comment-respond")
						.show();

					if (
						!$("#comments-post-" + postID).hasClass(
							"svq-comments--modal"
						)
					) {
						$(this).hide();
					}

					$window.trigger("svq-sticky-sidebar-update");
				}
			);

			$(document).on("submit", ".comment-form", function (e) {
				e.preventDefault();

				var _this = $(this);
				var formData = new FormData(this);

				$.ajax({
					method: "POST",
					url: $(this).attr("action"),
					data: formData,
					context: $(this),
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function () {
						_this
							.find("button[type=submit]")
							.prop("disabled", true)
							.addClass("is-loading");
					},
					complete: function () {
						_this
							.find("button[type=submit]")
							.prop("disabled", false)
							.removeClass("is-loading");
					},
					success: function (response) {
						if (response.status === "success") {
							_this.find(".request-response").html("");

							if (response.reply_to !== "0") {
								var comment = _this.closest(
									"#comment-" + response.reply_to
								);
								var commentChildren = comment
									.children("ul.children")
									.first();

								if (commentChildren.length) {
									commentChildren.append(response.data);
								} else {
									comment.append(
										'<ul class="children">' +
											response.data +
											"</ul>"
									);
								}

								var tempForm = _this.find("#wp-temp-form-div");

								if (tempForm.length) {
									_this
										.find(".cancel-comment-reply-link")
										.hide();
									tempForm.replaceWith(
										_this.find(".comment-respond")
									);
								}
							} else {
								var mainContainer = _this.closest(
									".svq-section-comments"
								);
								mainContainer
									.find(".svq-comments.comments-empty")
									.removeClass("comments-empty");
								mainContainer
									.find(".comments-list")
									.append(response.data);

								if (response.comment_status === "approved") {
									var commentStatus = mainContainer.find(
										".svq-status-comments"
									);
									if (
										!commentStatus.hasClass("has-comments")
									) {
										commentStatus.addClass("has-comments");
									}

									var commentCounter = mainContainer.find(
										".svq-svg-icon-wrapp"
									);
									if (
										!commentCounter.attr(
											"data-svg-notifier"
										)
									) {
										commentCounter.attr(
											"data-svg-notifier",
											1
										);
									} else {
										commentCounter.attr(
											"data-svg-notifier",
											parseInt(
												commentCounter.attr(
													"data-svg-notifier"
												)
											) + 1
										);
									}

									var introText = mainContainer.find(
										".comments-stat-info"
									);
									introText.html(introText.data("first"));
								}
							}

							window.addComment.init();

							_this
								.find(
									"input[type=text], input[type=email], textarea"
								)
								.val("");
							_this
								.find("input[type=checkbox]")
								.prop("checked", false);

							window.dispatchEvent(new Event("resize"));
							window.dispatchEvent(
								new Event("clear-comment-form-media")
							);
						} else if (response.status === "error") {
							_this.find(".request-response").html(response.data);
						}
					},
				});
			});
		},

		initHeaderCart: function () {
			const targetNode = $(".stx-nav-cart .hide_cart_widget_if_empty")[0];

			if (!targetNode) {
				return;
			}

			const config = { attributes: true, childList: true, subtree: true };

			const callback = function (mutationsList, observer) {
				for (const mutation of mutationsList) {
					if (mutation.type === "childList") {
						SQ.initialize.initLazy();
						document.dispatchEvent(new CustomEvent("scroll"));
					}
				}
			};

			const observer = new MutationObserver(callback);
			observer.observe(targetNode, config);

			$(".builder-item--header_cart_icon .cart-icon-wrapper").on(
				"hover",
				function () {
					SQ.initialize.initLazy();
				}
			);
		}
	};

	SQ.documentOnReady = {
		init: function () {
			SQ.initialize.init();

			objectFitPolyfill();
		},
	};

	SQ.documentOnLoad = {
		init: function () {
			SQ.documentOnLoad.initNestedCarousel();
			SQ.documentOnLoad.initArticlesCarousel();
			SQ.documentOnLoad.initGalleryCarousel();
			SQ.documentOnLoad.initArticlePanelCarousel();
			SQ.documentOnLoad.initPreloader();
		},

		initArticlesCarousel: function () {
			$(".svq-slider-articles").each(function () {
				var swiper = new Swiper($(this), {
					slidesPerView: "auto",
					nested: $(this).hasClass("svq-nested"),
				});

				$(this)
					.find(".slide-to--next")
					.on("click", function (e) {
						e.preventDefault();
						swiper.slideNext();
					});

				$(this)
					.find(".slide-to--back")
					.on("click", function (e) {
						e.preventDefault();
						swiper.slidePrev();
					});

				$window.resize(function () {
					swiper.update();
				});
			});
		},

		initNestedCarousel: function () {
			$(".svq-media-slider").each(function () {
				var masterCarousel = $(this).find(".svq-master-carousel");
				var childCarousel = $(this).find(".svq-child-carousel");

				var categories = [];

				if (masterCarousel.length) {
					categories = masterCarousel.data("slider-categories");
				}

				if (childCarousel.length) {
					var sliderId = childCarousel.data("slider-id");
					if (sliderId) {
						childCarousel = $(".svq-child-carousel-" + sliderId);
					}

					var carousel = new Swiper(childCarousel, {
						autoHeight: true,
						slidesPerView: 1,
						pagination: {
							el: ".svq-nav-pagination",
							clickable: true,
							type: "custom",
							renderCustom: function (swiper, current, total) {
								var navItems = "";

								if (categories.length) {
									for (var i = 0; i < total; i++) {
										var className =
											current - 1 === i
												? "svq-list-item-active"
												: "";

										navItems +=
											'<li class="svq-list-item swiper-slide ' +
											className +
											'">' +
											'<a href="#" class="svq-item-link svq-slider-nav-btn" role="tab" data-go-to-slide="' +
											i +
											'">' +
											categories[i] +
											"</a>" +
											"</li>";
									}
								}

								return navItems;
							},
						},
					});

					$window.resize(function () {
						carousel.update();
					});

					if (masterCarousel.length) {
						var sliderId = masterCarousel.data("slider-id");
						if (sliderId) {
							masterCarousel = $(
								".svq-master-carousel-" + sliderId
							);
						}

						var nav = new Swiper(masterCarousel, {
							slidesPerView: "auto",
						});

						var pagination = $(this).find(".svq-nav-pagination");

						pagination.on(
							"click",
							".svq-slider-nav-btn",
							function (e) {
								e.preventDefault();
								nav.slideTo(
									parseInt($(this).data("go-to-slide"))
								);
								carousel.slideTo(
									parseInt($(this).data("go-to-slide"))
								);
							}
						);

						$window.resize(function () {
							nav.update();
						});

						carousel.on("slideChange", function () {
							nav.slideTo(carousel.realIndex);
						});
					}

					var sliderBackBtn = $(this).find(".slide-to--back");
					var sliderNextBtn = $(this).find(".slide-to--next");

					sliderBackBtn.on("click", function (e) {
						e.preventDefault();
						var subCarousel = childCarousel.find(
							".svq-slider-articles"
						);
						var index = carousel.realIndex;

						if (subCarousel[index]) {
							var subCarouselSwiper = subCarousel[index].swiper;

							if (subCarouselSwiper.isBeginning) {
								carousel.slidePrev();
							} else {
								subCarouselSwiper.slidePrev();
							}

							if (
								subCarouselSwiper.isBeginning &&
								index - 1 === 0
							) {
								sliderBackBtn.trigger("is-not-visible");
							}
							sliderNextBtn.trigger("is-visible");
						}
					});

					sliderNextBtn.on("click", function (e) {
						e.preventDefault();
						var subCarousel = childCarousel.find(
							".svq-slider-articles"
						);
						var index = carousel.realIndex;

						if (subCarousel[index]) {
							var subCarouselSwiper = subCarousel[index].swiper;

							if (subCarouselSwiper.isEnd) {
								carousel.slideNext();
							} else {
								subCarouselSwiper.slideNext();
							}

							if (
								subCarouselSwiper.isEnd &&
								carousel.slides.length === index
							) {
								sliderNextBtn.trigger("is-not-visible");
							}
							sliderBackBtn.trigger("is-visible");
						}
					});

					sliderBackBtn
						.on("is-visible", function () {
							$(this).addClass("is-visible");
						})
						.on("is-not-visible", function () {
							$(this).removeClass("is-visible");
						});

					sliderNextBtn
						.on("is-visible", function () {
							$(this).addClass("is-visible");
						})
						.on("is-not-visible", function () {
							$(this).removeClass("is-visible");
						});
				}
			});
		},

		initGalleryCarousel: function () {
			$(".svq-gallery-slider").each(function () {
				var gallerySwiper = new Swiper($(this), {
					slidesPerView: "auto",
					freeMode: true,
					freeModeMomentumRatio: 0.5,
					freeModeMomentumVelocityRatio: 0.3,
					freeModeMinimumVelocity: 0.5,
					navigation: {
						nextEl: ".svq-gallery-control-next",
						prevEl: ".svq-gallery-control-prev",
					},
					pagination: {
						el: ".swiper-pagination",
						type: "bullets",
					},
					on: {
						init: function () {
							objectFitPolyfill();
						},
					},
				});

				$window.resize(function () {
					gallerySwiper.update();
				});
			});
		},

		initArticlePanelCarousel: function () {
			$(".svq-panel-slider").each(function () {
				var swiper = new Swiper($(this), {
					autoHeight: true,
					slidesPerView: "auto",
					effect: "fade",
					navigation: {
						nextEl: ".svq-panel-control-next",
						prevEl: ".svq-panel-control-prev",
					},
					pagination: {
						el: ".swiper-pagination",
						type: "fraction",
					},
				});

				$window.resize(function () {
					swiper.update();
				});
			});
		},

		initPreloader: function () {
			var pageLoader = $(".svq-page-loader");

			if (pageLoader.length) {
				pageLoader.addClass("not-visible");
			}
		},
	};

	var $window = $(window),
		$body = $("body"),
		$document = $(document);

	$document.ready(SQ.documentOnReady.init);
	$window.on("load", SQ.documentOnLoad.init);

	$window.on("reveal", function () {
		SQ.initialize.initElementReveal();
	});

	$window.on("svq-post-changed", function () {
		SQ.initialize.initLazy();
		window.dispatchEvent(new Event("loadLazy"));
		SQ.initialize.initElementReveal();
		SQ.initialize.initStickySidebar();
		objectFitPolyfill();
	});

	$window.on("masonry", function () {
		SQ.initialize.initMasonry();
	});
})(jQuery);
