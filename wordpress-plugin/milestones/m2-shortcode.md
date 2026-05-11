# M2: Shortcode: Display the Reading List

WordPress shortcodes let editors embed dynamic content in posts and pages
using a tag like `[reading_list]`. In this milestone you register a
shortcode that queries the database and outputs an HTML table of books.

## What to build

- Register a `[reading_list]` shortcode
- Query all rows from `$wpdb->prefix . 'reading_list'` ordered by `created_at DESC`
- Output an HTML table with columns: Title, Author, Status
- The shortcode should support an optional `status` attribute to filter
  by reading status: `[reading_list status="reading"]`
- Output is properly escaped (no raw database values injected into HTML)

## Pointers

- [Shortcodes](https://developer.wordpress.org/plugins/shortcodes/)
  -- `add_shortcode()`, the callback signature, `$atts`, and `shortcode_atts()`
- [Shortcode Best Practices](https://developer.wordpress.org/plugins/shortcodes/shortcodes-with-parameters/)
  -- always return output, never echo; use `ob_start()`/`ob_get_clean()` for templates
- [$wpdb Queries](https://developer.wordpress.org/reference/classes/wpdb/)
  -- `$wpdb->get_results()`, `$wpdb->prepare()`
- [Data Sanitization/Escaping](https://developer.wordpress.org/apis/security/escaping/)
  -- `esc_html()`, `esc_attr()`

## Growth edges

- **Return, don't echo**: Shortcodes must return HTML, not echo it.
  Echoing breaks page structure. Use output buffering if your template
  logic is complex.
- **$wpdb->prepare() for anything dynamic**: Even a status filter from
  shortcode attributes must go through `prepare()`. Attributes come
  from post content -- they're user input.
- **Escaping at output, not storage**: Escape when you output, not when
  you save. Use the escaping function that matches the output context:
  `esc_html()` for table cell text, `esc_attr()` for attributes, and
  `esc_url()` for URLs.

## Done when

- [ ] Create a WordPress page, add `[reading_list]` to its content, and publish it -- the page shows an HTML table of books
- [ ] `[reading_list status="reading"]` filters to only books with that status
- [ ] View source: no raw PHP output, no unescaped values
- [ ] PR body includes the output self-audit from the project README
- [ ] The shortcode returns output (not echoes) -- test by placing it inside a paragraph: the paragraph renders correctly
- [ ] `ddev exec vendor/bin/phpcs wp-content/plugins/reading-list/` passes (or only warns, no errors)

**WPCS check:** Before opening your PR, run the linter. Fix all errors. Paste the PHPCS summary in your PR body.
