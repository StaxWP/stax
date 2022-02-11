/*
 * Plyr init for theme
 */

(function ($) {
	function initLazyVideo() {
		var lazyVideoObserver = new IntersectionObserver(function (entries, observer) {
			entries.forEach(function (video) {
				if (video.isIntersecting) {
					for (var source in video.target.children) {
						var videoSource = video.target.children[source];
						if (typeof videoSource.tagName === 'string' && videoSource.tagName === 'SOURCE') {
							videoSource.src = videoSource.dataset.src;
						}
					}

					video.target.load();

					var props = {
						controls: [
							'play-large',
							'play',
							'progress',
							'mute',
							'volume',
							'fullscreen'
						],
						ratio: '16:9'
					};

					if ($(video.target).hasClass('plyr-no-action')) {
						props.controls = [];
						props.clickToPlay = false;
					}

					new Plyr(video.target, props);

					video.target.classList.add('lazy-video-loaded');
					lazyVideoObserver.unobserve(video.target);
				}
			});
		});

		$('.lazy-video').each(function (index, lazyVideo) {
			lazyVideoObserver.observe(lazyVideo);
		});
	}

	function initVideoPlayer() {
		var videoPlayer = $('.video-plyr-alone');

		if (videoPlayer.length) {
			videoPlayer.each(function () {
				if ($(this).hasClass('lazy-video-loaded')) {
					return false;
				}

				var props = {
					controls: [
						'play-large',
						'play',
						'progress',
						'mute',
						'volume',
						'fullscreen',
					],
					ratio: '16:9',
				};

				if ($(this).hasClass('plyr-no-action')) {
					props.controls = [];
					props.clickToPlay = false;
				}
				var player = new Plyr(this, props);

				var __this = $(this);
				var hidePanelTitle = false;

				var panel = $(this).closest('.svq-panel');
				var grid = $(this).closest('.svq-article');

				if (panel.length && panel.hasClass('svq-panel--title-over'))
					hidePanelTitle = true;

				player.on('ready', function () {
					if (panel.length) {
						panel.find('.svq-progressive__placeholder-video').addClass('video-is-loaded');
					} else if (grid.length) {
						grid.find('.svq-progressive__placeholder-video').addClass('video-is-loaded');
					}

					player.on('play', function () {
						videoPlayer.each(function (i, vidPlayer) {
							if (!$(vidPlayer).is(__this)) {
								$(vidPlayer)[0].plyr.pause();
							}
						});

						$('.audio-plyr-alone').each(function (i, audioPlayer) {
							$(audioPlayer)[0].plyr.pause();
						});

						if (panel.length) {
							if (hidePanelTitle)
								panel.find('.entry-header').fadeOut();

							panel.find('.svq-progressive__placeholder-video').addClass('svq-overlay-off');
							$('body').addClass('video-is-playing');
						} else if (grid.length) {
							grid.find('.svq-progressive__placeholder-video').addClass('svq-overlay-off');
							$('body').addClass('video-is-playing');
						}
					});

					player.on('pause', function () {
						if (panel.length) {
							if (hidePanelTitle)
								panel.find('.entry-header').fadeIn();

							panel.find('.svq-progressive__placeholder-video').removeClass('svq-overlay-off');
							$('body').removeClass('video-is-playing');
						} else if (grid.length) {
							grid.find('.svq-progressive__placeholder-video').removeClass('svq-overlay-off');
							$('body').removeClass('video-is-playing');
						}
					});

					player.on('ended', function () {
						if (panel.length) {
							if (hidePanelTitle)
								panel.find('.entry-header').fadeIn();

							panel.find('.svq-progressive__placeholder-video').removeClass('svq-overlay-off');
							$('body').removeClass('video-is-playing');
							player.fullscreen.exit();
						} else if (grid.length) {
							grid.find('.svq-progressive__placeholder-video').removeClass('svq-overlay-off');
							$('body').removeClass('video-is-playing');
							player.fullscreen.exit();
						}
					});

					player.on('enterfullscreen', function () {
						$(this).addClass('enter-full-screen');
						player.play();
					});

					player.on('exitfullscreen', function () {
						$(this).removeClass('enter-full-screen');

					});
				});
			});
		}

		var elements = document.querySelectorAll('.video-plyr-alone');
		var config = {
			root: null,
			rootMargin: '0px'
		};

		var observer = new IntersectionObserver(function (entries, self) {
				$.each(entries, function (index, entry) {
					if (!entry.isIntersecting) {
						$(entry.target)[0].plyr.pause();
					}
				});
			}, config
		);

		$.each(elements, function (index, element) {
			observer.observe(element);
		});
	}

	function initAudioPlayer() {
		var audioPlayer = $('.audio-plyr-alone');

		if (audioPlayer.length) {
			audioPlayer.each(function () {
				var player = new Plyr(this, {
					controls: [
						'play',
						'progress',
						'current-time',
					],
				});

				var __this = $(this);

				player.on('ready', function () {
					player.on('play', function () {
						audioPlayer.each(function (i, audioPlayer) {
							if (!$(audioPlayer).is(__this)) {
								$(audioPlayer)[0].plyr.pause();
							}
						});

						$('.video-plyr-alone').each(function (i, videoPlayer) {
							$(videoPlayer)[0].plyr.pause();
						});
					});
				});
			});
		}
	}

	function initGutenbergBlocks() {
		var video_blocks = $('.video-plyr-block');

		if (video_blocks.length) {
			video_blocks.each(function () {
				var player = new Plyr(this, {
					controls: ['play-large', 'play', 'progress', 'mute', 'volume', 'fullscreen']
				});

				var __this = $(this);

				player.on('ready', function () {
					__this.parent().addClass('video-is-loaded');
					player.on('play', function () {
						$(this).parent('.svq-progressive__placeholder-video').addClass('svq-overlay-off');
						$('body').addClass('video-is-playing');

						video_blocks.each(function (i, vidPlayer) {
							if (!$(vidPlayer).is(__this)) {
								$(vidPlayer)[0].plyr.pause();
							}
						});

						$('.audio-plyr-block').each(function (i, audioPlayer) {
							$(audioPlayer)[0].plyr.pause();
						});
					});

					player.on('pause', function () {
						$(this).parent('.svq-progressive__placeholder-video').removeClass('svq-overlay-off');
						$('body').removeClass('video-is-playing');
					});

					player.on('ended', function () {
						$(this).parent('.svq-progressive__placeholder-video').removeClass('svq-overlay-off');
						$('body').removeClass('video-is-playing');
						player.fullscreen.exit();
					});

					player.on('enterfullscreen', function () {
						player.play();
						$(this).addClass('enter-full-screen');
					});

					player.on('exitfullscreen', function () {
						$(this).removeClass('enter-full-screen');
					});
				});
			});
		}

		var elements = document.querySelectorAll('.video-plyr-block');
		var config = {
			root: null,
			rootMargin: '0px'
		};

		var observer = new IntersectionObserver(function (entries, self) {
				$.each(entries, function (index, entry) {
					if (!entry.isIntersecting) {
						$(entry.target).trigger('pause');
					}
				});
			}, config
		);

		$.each(elements, function (index, element) {
			observer.observe(element);
		});

		var audio_blocks = $('.audio-plyr-block');
		if (audio_blocks.length) {
			audio_blocks.each(function () {
				var player = new Plyr(this, {
					controls: [
						'play',
						'progress',
						'current-time'
					]
				});

				var __this = $(this);

				player.on('ready', function () {
					player.on('play', function () {
						audio_blocks.each(function (i, audioPlayer) {
							if (!$(audioPlayer).is(__this)) {
								$(audioPlayer)[0].plyr.pause();
							}
						});

						$('.video-plyr-block').each(function (i, videoPlayer) {
							$(videoPlayer)[0].plyr.pause();
						});
					});
				});
			});
		}
	}

	$(document).ready(function () {
		initLazyVideo();
		initVideoPlayer();
		initAudioPlayer();
		initGutenbergBlocks();

		$(window).on('reveal', function () {
			initLazyVideo();
			initVideoPlayer();
			initAudioPlayer();
		});
	});
})(jQuery);
