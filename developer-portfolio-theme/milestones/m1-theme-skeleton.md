# M1: Theme Skeleton + Home Page

Two phases: get WordPress running, then build your first theme page.

## Phase 1: Get WordPress running

Follow the setup instructions in the project README. When you're done:

- [ ] `ddev start` works and WordPress loads in your browser
- [ ] You can log into wp-admin (`admin` / `admin`)
- [ ] Your theme ("Developer Portfolio") appears in Appearance > Themes
- [ ] `ddev composer install` completes without errors

Stop here and verify everything works before continuing.

## Phase 2: Build the home page

Now build the minimum viable theme -- a home page that displays posts
using The Loop.

### What to build

- `index.php` that displays posts using The Loop
- `header.php` and `footer.php` template parts loaded via
  `get_header()` and `get_footer()`
- `header.php` must call `wp_head()` before `</head>`
- `footer.php` must call `wp_footer()` before `</body>`
- Your stylesheet enqueued via `wp_enqueue_style()` in `functions.php`
  (not a hardcoded `<link>` tag)
- A `screenshot.png` (1200x900 recommended) so the theme shows a
  preview in Appearance > Themes

### Pointers

- [Main Stylesheet (style.css)](https://developer.wordpress.org/themes/core-concepts/main-stylesheet/)
  -- the header comment format that makes WordPress recognize your theme
- [Template Files](https://developer.wordpress.org/themes/templates/)
  -- how WordPress decides which PHP file to load
- [The Loop](https://developer.wordpress.org/themes/basics/the-loop/)
  -- how posts are fetched and displayed
- [Including CSS & JavaScript](https://developer.wordpress.org/themes/basics/including-css-javascript/)
  -- `wp_enqueue_style()` and `wp_enqueue_scripts` action hook

### Growth edges

- **Framework dispatch**: WordPress decides which template file runs,
  not you. This is different from P1/P2 where your router decided.
- **Hook system**: `add_action()` and `wp_enqueue_scripts` are your
  first taste of event-driven programming.
- **External docs navigation**: The WordPress Handbook is your primary
  reference from here on.

### Done when

- [ ] Activating the theme shows posts on the home page
- [ ] Posts display title, date, and content (not just raw database output)
- [ ] When no posts exist, a "no posts found" message appears
- [ ] View source shows `wp_head()` output in `<head>` and `wp_footer()` output before `</body>`
- [ ] Stylesheet loads via the enqueue system (check the `<link>` tag source -- it should come from `wp_head()`, not a hardcoded tag)
- [ ] Your theme has a screenshot visible in Appearance > Themes
- [ ] You've run `ddev exec vendor/bin/phpcs` and reviewed its output (errors and warnings are expected -- just run it and notice what it flags)
