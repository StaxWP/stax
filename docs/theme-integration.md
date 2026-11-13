# Stax theme integration contract (version 1)

How a plugin (Stax Builder, or any site builder) takes over the Stax header or footer and sets the site's design tokens. Not part of the theme zip.

## Detect

```php
$support = get_theme_support( 'stax-builder' ); // false when the active theme is not Stax 2.x
// $support[0] === [
//     'version' => 1,
//     'header'  => 'stax_header',
//     'footer'  => 'stax_footer',
//     'tokens'  => [
//         'color'      => [ 'primary', 'text', 'heading', 'background', 'light', 'dark', 'link', 'border', 'meta', 'very-light-gray', 'very-dark-gray' ],
//         'fontFamily' => [ 'body', 'heading', 'system' ],
//         'layout'     => [ 'contentSize', 'wideSize' ],
//     ],
// ]
```

`version` changes only on breaking changes to this contract. New keys (like `tokens`) can be added without a bump; read them with `?? []`. `tokens` is described in "Design tokens: names" below.

## Take over an area

```php
add_filter( 'stax_theme_area_owner', function ( $owner, $area, $context ) {
	// $area: 'header' | 'footer'
	// $context: 'render'     front end, answer for the current request (placement rules apply here)
	//           'customizer' whether the plugin manages the area at all (replaces the theme controls)
	return my_plugin_has_part( $area, $context ) ? 'stax-builder' : $owner;
}, 10, 3 );

add_action( 'stax_header', 'my_plugin_render_header' ); // priority 1 to 10 or later, not 0
add_action( 'stax_footer', 'my_plugin_render_footer' );
```

- Return a non-empty owner id (string, sanitized with `sanitize_key`) to own the area; an empty string leaves it to the theme. `true` is accepted and becomes `plugin`.
- The filter can run several times per request (render: header action, footer action, body classes, CSS, assets). Cache the answer in the plugin.
- When the plugin answers for `render`, `stax_header` and `stax_footer` fire at the same place as before: inside `<body>`, inside `div.svq-page-wrapper`; the header before the page content (`main#primary`), the footer after it.

### What the theme does when it does not own an area

On the area action at priority 0 the theme removes its own callback (`stax_header_builder_action` / `stax_footer_builder_action`), so it prints no `<header class="header">` / `<footer id="site-footer">` wrappers and none of the builder markup (rows, menus, mobile sidebar, reading progress bar).

| | Header owned | Footer owned | Both owned |
|---|---|---|---|
| Theme markup for the area | none | none | none |
| Skip link `<a class="skip-link screen-reader-text" href="#primary">` | printed by the theme right before the plugin header | (in the theme header) | printed by the theme right before the plugin header |
| Inline builder CSS for the area | not generated | not generated | not generated |
| `menu_sidebar_*` body class | removed | kept | removed |
| `stax-header` stylesheet (builder CSS file, shared by both areas) | loaded | loaded | file not loaded; the handle stays for the Customizer CSS variables printed inline |
| `stax-hfg` script (menus, mobile sidebar) | loaded | loaded | file not loaded; the handle stays as a dependency |

The plugin header must not print its own skip link and should use landmark elements (`<header>` for the header part, `<footer>` for the footer part, `<nav aria-label>` for menus).

### Skip link with any header takeover

The theme header prints the skip link inside `<header class="header">`. Whenever something else renders the header, the theme prints the same link on `stax_header` at priority 0, before that header, so it stays the first focusable element and lands on `main#primary`:

- a plugin that owns the header through this contract (Stax Builder);
- an Elementor Pro Theme Builder header location (`elementor_location_exits( 'header', true )`; Elementor prints at priority 5 and only then removes the theme callback);
- Header Footer Elementor, Beaver Themer, or any `stax_header_callback` (they replace `stax_header_builder_action` on `wp`).

`stax()->skip_link()` prints at most once per request, so the link is never doubled. Without a takeover nothing changes.

Without a plugin (filter returns empty) the output is byte-identical to the theme without this contract.

## Customizer

When the filter returns an owner for the `customizer` context, the theme's Header / Footer builder panel (`hfg_header` / `hfg_footer`, same ids, so deep links keep working) shows only a note: "The header is managed by Stax Builder." with an "Edit in Stax Builder" button. The builder controls are not registered, so publishing the Customizer never touches the saved settings (`hfg_*` theme mods); turning the plugin off brings the theme header, footer and their controls back as they were.

```php
// Link and name in the note (defaults: admin.php?page=stax-builder, "Stax Builder").
add_filter( 'stax_theme_area_owner_url', fn( $url, $area, $owner ) => admin_url( 'admin.php?page=stax-builder&area=' . $area ), 10, 3 );
add_filter( 'stax_theme_area_owner_name', fn( $name, $area, $owner ) => 'Stax Builder', 10, 3 );
```

Admin page slugs: Stax Builder registers its admin page as `stax-builder` (`admin.php?page=stax-builder`), which is the default link. The theme's own Stax Panel keeps `stax` (`themes.php?page=stax`). `$area` is `header`, `footer` or `tokens` (the Customizer token notes below).

## Design tokens: names

The theme publishes the token names it understands in `get_theme_support( 'stax-builder' )[0]['tokens']`, read from the theme's `theme.json` (single source; adding a palette color there adds it here):

| Key | Source in theme.json | Values today |
|---|---|---|
| `color` | `settings.color.palette[].slug` | `primary`, `text`, `heading`, `background`, `light`, `dark`, `link`, `border`, `meta`, `very-light-gray`, `very-dark-gray` |
| `fontFamily` | `settings.typography.fontFamilies[].slug` | `body`, `heading`, `system` |
| `layout` | keys of `settings.layout` | `contentSize`, `wideSize` |

A plugin that writes a brand to global styles uses these slugs when the active theme publishes them (instead of its own namespace), so the theme palette, the block color classes (`has-primary-color`, ...) and the theme's own CSS variables all follow the brand. On other themes it keeps its own names.

## Design tokens: precedence

**Global styles (user layer) > Customizer > theme defaults.**

- The theme feeds its Customizer palette and fonts into the theme layer of theme.json (`wp_theme_json_data_theme`). Core merges the user layer (the `wp_global_styles` post for the `stax` stylesheet, which the Site Editor or a plugin writes) on top, so global styles already win for the `--wp--preset--*` variables, block colors and layout sizes.
- The theme's own CSS reads its Customizer variables (`--primary-h/s/l`, `--link-*`, `--text-color`, `--font-family`, ...). Since this contract these variables also take the global styles values, so the whole theme (header/footer builder, buttons, links, meta, headings, backgrounds) follows a brand written to global styles. The Customizer settings are not changed; removing the global styles values brings the Customizer values back.

Slugs the theme maps (global styles `settings.color.palette.theme` or `.custom`, custom wins):

| Palette slug | Theme variable |
|---|---|
| `primary` | `--primary-h/s/l` (+ contrast) |
| `background` | `--body-bg-h/s/l` (+ contrast) |
| `light` | `--light-h/s/l` (+ contrast) |
| `dark` | `--dark-h/s/l` (+ contrast) |
| `link` | `--link-h/s/l` |
| `text` | `--text-color` |
| `heading` | `--heading-color` |
| `border` | `--border-color` |
| `meta` | `--text-meta-color` |

| Other setting | Effect |
|---|---|
| `settings.typography.fontFamilies` slug `body` | `--font-family` (body text) |
| `settings.typography.fontFamilies` slug `heading` | `--heading-font-family` |
| `settings.layout.contentSize` / `wideSize` | theme.json layout (core); `wideSize` also sets `.container` max-width from 1200 px |

Notes for the plugin:

- A user `palette.theme` replaces the whole theme palette in core (that is how the Site Editor saves it). Write all theme slugs (`primary`, `text`, `heading`, `background`, `light`, `dark`, `link`, `border`, `meta`, `very-light-gray`, `very-dark-gray`), or write the brand colors to `palette.custom` with the same slugs.
- The theme only maps families, it does not load font files for global styles fonts. Register them with `fontFace` (or the Font Library) so core prints the `@font-face` rules.
- Colors must be hex or `rgb()`; anything else is ignored by the mapping.

### Customizer notes for overridden values

When global styles override a value the Customizer also sets, the theme adds a short note above that control. The control stays and its saved value is kept; it applies again once the global styles value is removed.

| Global styles value | Note in |
|---|---|
| any mapped palette slug | Colors (lists the colors, for example "Primary, Link") |
| font family `body` | Typography > Body |
| font family `heading` | Typography > Headings |
| `layout.wideSize` | Layout > Site Container, above Width |

Text: "Set by your brand (Stax Builder)" with an "Edit in Stax Builder" link when a plugin claims the tokens, otherwise "Set in the site's global styles" (classic themes have no Styles screen in the Site Editor, so the note does not point there).

```php
// The plugin says it manages the brand in global styles (name and link come from the filters above, $area = 'tokens').
add_filter( 'stax_theme_tokens_owner', fn( $owner ) => my_plugin_brand_is_active() ? 'stax-builder' : $owner );
```

Code: `inc/Builder_Integration/Component.php` (contract, token names, ownership, assets, skip link, token notes), `inc/Builder_Integration/Managed_Control.php` (header/footer Customizer note), `inc/Builder_Integration/Token_Note_Control.php` (token note), `inc/Editor/Component.php::get_global_styles_tokens()` and `inc/Customizer/styles/Dynamic_Css.php` (tokens).
