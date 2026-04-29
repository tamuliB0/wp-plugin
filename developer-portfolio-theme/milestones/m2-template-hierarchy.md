# M2: Template Hierarchy

WordPress uses a specific lookup order to decide which template file
handles a URL. In this milestone you build the template files that
cover the main content types: single posts, pages, archives, search,
and 404.

## What to build

- `single.php` -- displays a single blog post
- `page.php` -- displays a static page
- `archive.php` -- displays post listings for categories, tags, dates
- `search.php` -- displays search results
- `404.php` -- displays a "not found" page
- Template parts via `get_template_part()` so `single.php` and
  `archive.php` share rendering logic instead of duplicating it
- Post thumbnail (featured image) support: register it in
  `functions.php`, display it in templates, handle the case where
  no image is set

## Pointers

- [Template Hierarchy](https://developer.wordpress.org/themes/templates/template-hierarchy/)
  -- the diagram showing which file WordPress loads for each URL type
- [Template Parts](https://developer.wordpress.org/themes/templates/template-parts/)
  -- `get_template_part()` for reusable template fragments
- [Post Thumbnails](https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/)
  -- `add_theme_support('post-thumbnails')` and `the_post_thumbnail()`

## Growth edges

- **Convention over configuration**: WordPress's template naming is
  rigid -- `single.php` means "single post", not whatever you want.
  The hierarchy diagram is the map.
- **DRY via template parts**: Instead of copy-pasting HTML between
  templates, you extract shared pieces into `template-parts/` and
  load them with `get_template_part()`.

## Done when

- [ ] Clicking a post title goes to `single.php` (not `index.php`)
- [ ] Single post view shows title, date, content, and featured image (if set)
- [ ] A static page (create one in wp-admin) uses `page.php`
- [ ] Category/tag/date archive pages use `archive.php` and list posts
- [ ] The search form returns results rendered by `search.php`
- [ ] A nonsense URL (e.g. `/xyz123`) shows your `404.php`
- [ ] At least one template part is shared between `single.php` and `archive.php`
- [ ] Posts without a featured image render gracefully (no broken image, no error)
