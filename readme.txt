=== Stax ===
Contributors: StaxWP
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A lightweight hybrid starter theme: classic templates plus theme.json, a blank canvas for any page builder, ready for shops, communities and blogs.

== Description ==

Stax is a lightweight, hybrid starter theme for WordPress. It uses classic PHP templates (so every page builder and plugin finds the hooks it expects) together with full theme.json support (so the block editor looks like your site). No jQuery on the front end, fonts served from your own site, and a stylesheet that only loads the parts your plugins need.

Use it as a clean canvas for Elementor, Beaver Builder, Divi Builder, Spectra, Kadence Blocks or GenerateBlocks, or build with the block editor and the included patterns. It comes with good defaults for online shops (WooCommerce, Easy Digital Downloads), communities (BuddyPress, BuddyBoss Platform, bbPress), courses (LearnDash, LifterLMS, Tutor LMS) and blogs.

= Features =

* Header and footer builder in the Customizer (drag and drop rows, logo, menus, search, cart, button, custom HTML).
* Global colors and typography in the Customizer, mirrored into theme.json so the block editor palette, fonts and widths always match the front end.
* Block patterns (hero, features, call to action, latest posts, community) and block styles.
* Blog layouts: grid, list, big list and masonry, with sidebars on the left, right or both.
* Page templates for page builders: full width, no title, large container.
* Per page settings in the editor: hide the title section, layout, sidebars.
* Fast by default: no jQuery, deferred scripts, native lazy loading, Google Fonts hosted locally, plugin styles loaded only when the plugin is active.
* Accessibility: skip link, visible focus, keyboard friendly menus, labelled controls, WCAG AA default colors.
* Translation ready, RTL ready, WPML and Polylang ready (wpml-config.xml).

= Compatibility =

* Block editor: theme.json v3, editor styles, wide and full alignments, patterns, block styles.
* Elementor and Elementor Pro: Theme Builder locations (header, footer, single, archive), Elementor full width and canvas templates, per page "hide header" and "hide title" switches.
* Beaver Builder and Beaver Themer: header, footer and part layouts.
* Header Footer Elementor and other header/footer plugins (stax_header_callback / stax_footer_callback filters).
* WooCommerce: theme support, product gallery zoom, lightbox and slider, theme wrappers, cart and checkout blocks.
* BuddyPress, BuddyBoss Platform and bbPress, including BuddyBuilder.
* LearnDash, LifterLMS, Tutor LMS, Easy Digital Downloads.
* Yoast SEO, Rank Math and SEOPress breadcrumbs.
* AMP plugin, Jetpack.

== Frequently Asked Questions ==

= I updated from 1.x. Will my site look different? =

No. Sites updated from 1.x keep the full 1.x stylesheet and their colors until an administrator switches. Go to Appearance > Stax style (or Appearance > Customize > Stax style) to preview the new, much smaller stylesheet and switch with one click. Your colors, fonts, layout, header and footer are kept; a color changes only when it is too light to read (just enough to pass WCAG AA), and the styles of any Stax 1.x classes your content uses are kept. You can switch back for 30 days.

= Does Stax load anything from external servers? =

No. Google Fonts selected in the Customizer are downloaded once by your server and served from your site.

= Can I use it with my page builder? =

Yes. Choose the "Stax - Page builder. No title" template or the builder's own full width/canvas template. Elementor Pro and Beaver Themer headers and footers replace the theme's header and footer automatically.

== Changelog ==

= 2.0.0 =
Full refactor. Existing settings, menus, widgets, page templates, hooks and template files keep working.

* New: theme.json v3 with the Customizer palette and fonts, editor styles that match the front end inside the editor, five block patterns, five block styles, starter content.
* New: compatibility layer for WooCommerce (theme support, gallery features, proper product pages), Beaver Themer, Header Footer Elementor, LifterLMS, BuddyPress Nouveau, Rank Math and SEOPress breadcrumbs, WPML/Polylang (wpml-config.xml); small style files for BuddyPress, bbPress, LMS plugins and EDD, loaded only with those plugins.
* New: Google Fonts are served from your site (no requests from visitors to Google).
* Performance: no jQuery on the front end. Removed Modernizr, the CSS variables ponyfill, object-fit and IntersectionObserver polyfills, hc-sticky, Plyr, the jQuery lightbox and parallax plugins and the JavaScript lazy loader (replaced by CSS sticky, native media players, a 2 KB dialog lightbox and native lazy loading). Related posts and galleries scroll natively instead of loading Swiper. Main stylesheet from 539 KB to 176 KB, single post JavaScript from 483 KB to 41 KB (theme only).
* Accessibility: WCAG AA default link and meta colors, button text contrast, labelled search fields, submenu toggles with roles and states, accessible names for image and avatar links, visible focus, reduced motion support.
* Fixed: the theme no longer blocks its own updates from WordPress.org.
* Updated sites keep the 1.x look until an administrator switches in Appearance > Stax style or Customizer > Stax style: live before/after preview, one-click switch, switch back for 30 days. The switch keeps the site's own colors, fonts, layout, header and footer, darkens only the colors that fail WCAG AA contrast (just enough), and keeps the 1.x styles for the 1.x classes found in content, widgets, menus and Additional CSS.
* Fixed: RTL sites (the html tag carried a hard-coded dir="ltr").
* Fixed: the block editor showed default fonts and colors; the Stax page settings sidebar crashed in the editor.
* Fixed: breadcrumbs were doubled on some pages; Yoast breadcrumbs were echoed in the wrong place.
* Fixed: links to images with a class attribute got broken markup from the lightbox filter.
* Fixed: archive image captions, the blog page title and the stax_html_classes filter.
* Fixed: PHP 8.4/8.5 deprecations; "translations loaded too early" notice on WordPress 6.7+.
* Fixed: Customizer label typos ("Category & Breadcrumbs Style", "Center Horizontal & Vertical").
* Security: escaped output and sanitized input across templates and the admin panel; video thumbnails no longer unserialize remote data.
* Removed: usage tracker (Appsero) and its opt-in notice; its stored options and scheduled event are deleted on update. No usage data is collected.
* Requirements: WordPress 6.6+, PHP 7.4+ (tested with WordPress 7.1 and PHP 7.4 and 8.5).
* Developers: removed script handles stay registered (empty) so dependencies keep working; enqueue_plyr(), enqueue_hc_sticky() and enquque_jquery_parallax() are kept as no-ops; the 1.x stylesheet is available with the stax_use_legacy_css filter. Build tools: npm run build.

= 1.2.2 =
* Tested up to WordPress 7.0
* Raise minimum PHP requirement to 7.4
* Harden WooCommerce cart access and block parsing for PHP 8 compatibility

= 1.2.1 =
* Fix Classic editor styles and WordPress 6.0 testing

= 1.2.0 =
* Add all Google fonts instead of just popular
* Fix Customizer Slider controls
* Fix deprecated functions & fatal errors with newer Elementor versions
* Remove unused functions from elementor widgets

= 1.1.0 =
* Add new page template to be used for Page builders
* Add new option at page level to hide the whole title section. In case you want to use a page builder or add the title from Gutenberg.
* Other styling improvements

= 1.0.7 =
* Add starter-content theme support

= 1.0.6 =
* Fix Elementor section full width

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 2.0.0 =
Major update: no jQuery, much smaller CSS, theme.json and block patterns, better plugin compatibility. Updated sites keep the 1.x look until you switch in Appearance > Stax style (preview first, switch back for 30 days).

== Copyright ==

Stax WordPress Theme, Copyright 2019-2026 StaxWP https://staxwp.com
Stax is licensed and distributed under the GPLv3 or later.

== Privacy ==

Stax collects no usage data and sends nothing to StaxWP. Google Fonts chosen in the Customizer are downloaded once by your server and then served from your own site (uploads/fonts/stax), so visitors' browsers never contact Google. The usage tracker (Appsero) bundled until version 1.2.2 was removed, and what it stored is deleted on update.

== Resources ==

Stax uses some components from wp-rig https://github.com/wprig/wprig
License: GNU GPL v3

Stax uses header/footer builder code from Neve theme, Copyright ThemeIsle
License: GPLv2 or later, Source: https://wordpress.org/themes/neve/

Swiper 5.2, Copyright 2014-2019 Vladimir Kharlampidi (kept for child themes that call enqueue_swiper(); the theme itself no longer loads it)
License: MIT, Source: https://swiperjs.com/

Bootstrap CSS (parts of reboot, grid, utilities in the theme stylesheet), Copyright 2011-2018 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
License: MIT, Source: https://getbootstrap.com/

TGM-Plugin-Activation, © Thomas Griffin & Gary Jones & Juliette Reinders Folmer
License: GNU GPL v2 or later, Source: http://tgmpluginactivation.com/

SVG icons and the icon font (/assets/fonts) used in the theme are 100% created by us, Stax team (https://staxwp.com)
License: GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)

Image for theme screenshot, Credit Luca Bravo
License: CC0 1.0 Universal (CC0 1.0)
License URL: https://stocksnap.io/license
Source: https://stocksnap.io/photo/computer-keyboard-A28WZDTYEY

Image for theme screenshot, Credit Startup Stock Photos
License: CC0 1.0 Universal (CC0 1.0)
License URL: https://stocksnap.io/license
Source: https://stocksnap.io/photo/coding-programming-SCC00WCQ3I

Image for theme screenshot, Credit Helena Lopes
License: CC0 1.0 Universal (CC0 1.0)
License URL: https://stocksnap.io/license
Source: https://stocksnap.io/photo/woman-working-8LHTBAZW32

Image for theme screenshot, Credit Retro 56
License: CC0 1.0 Universal (CC0 1.0).
License URL: https://stocksnap.io/license
Source: https://stocksnap.io/photo/smartphone-camera-TBGNSSQV6B

Decorative shapes (/assets/img/parallax-bg), 404 image and Stax logo (/assets/img), created by the Stax team (https://staxwp.com)
License: GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)

Default fonts Mukta and Heebo are not bundled; they are downloaded from Google Fonts by the site on first use.
License: SIL Open Font License 1.1, Source: https://fonts.google.com/specimen/Mukta and https://fonts.google.com/specimen/Heebo

== Source code ==

Minified files are built from the readable sources with `npm run build` (bin/build.mjs). The unminified files ship next to the minified ones; the build tools and the CSS sources are in the public repository: https://github.com/StaxWP/stax

