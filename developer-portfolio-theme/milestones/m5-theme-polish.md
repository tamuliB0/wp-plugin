# M5: Theme Polish + Customizer + Child Theme

Final milestone. You add Customizer settings so site owners can
configure the theme from wp-admin, replace raw PHP escaping with
WordPress escaping functions, and create a child theme.

## What to build

- At least two Customizer settings (e.g. footer text, accent color)
  registered in `functions.php` using the Customizer API, with live
  preview updating as the admin adjusts values
- Replace every `htmlspecialchars()` call (and any unescaped output)
  with the correct WordPress escaping function:
  - `esc_html()` for text content
  - `esc_attr()` for HTML attributes
  - `esc_url()` for URLs
  - `wp_kses_post()` for post content that may contain HTML
- `wp_body_open()` call right after `<body>` in `header.php`
- A child theme directory (`developer-portfolio-child/`) with:
  - `style.css` containing a `Template: developer-portfolio` field
  - At least one overridden template file
  - Custom CSS that visibly changes the parent theme

## Pointers

- [Customizer API](https://developer.wordpress.org/themes/customize-api/)
  -- `$wp_customize->add_section()`, `add_setting()`, `add_control()`
- [Data Validation / Escaping](https://developer.wordpress.org/apis/security/data-validation/)
  -- when to use which escaping function
- [Child Themes](https://developer.wordpress.org/themes/advanced-topics/child-themes/)
  -- the `Template` field in style.css, how template override works

## Growth edges

- **Security-by-convention**: WordPress has context-specific escaping
  functions. `esc_html()` is not the same as `esc_attr()`. This is
  more precise than the `htmlspecialchars()` you used in P1/P2.
- **API design exposure**: The Customizer API shows how WordPress
  exposes configuration to non-developers. You're building admin UI
  without writing admin UI.
- **Inheritance model**: Child themes inherit everything from the
  parent and selectively override. This is the same concept as class
  inheritance but applied to files and templates.

## Done when

- [ ] wp-admin > Appearance > Customize shows your custom settings
- [ ] Changing a Customizer value updates the preview in real time
- [ ] No `htmlspecialchars()` calls remain in theme files -- all output uses WordPress escaping
- [ ] `esc_html()`, `esc_attr()`, `esc_url()` are each used at least once in the correct context
- [ ] `wp_body_open()` is called right after `<body>` in header.php
- [ ] The child theme (`developer-portfolio-child/`) activates without errors
- [ ] The child theme overrides at least one parent template
- [ ] The child theme adds visible CSS changes

## Stretch goals

If you finish the core deliverables and want to go further:

- **Translation readiness**: Wrap all user-facing strings in `__()`,
  `_e()`, or `esc_html__()` with the text domain `developer-portfolio`.
  See [Internationalization](https://developer.wordpress.org/plugins/internationalization/).
- **Theme Check plugin**: Install the [Theme Check](https://wordpress.org/plugins/theme-check/)
  plugin and get it to pass with no required-level errors.
