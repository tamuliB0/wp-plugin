# M1: Plugin Skeleton + Activation

WordPress must recognise a file as a plugin before any of its code runs.
In this milestone you wire up the plugin header, create the database
table on activation, and verify the full lifecycle works.

## What to build

- The plugin header comment (already in the starter file -- just read it)
- An activation hook that creates `$wpdb->prefix . 'reading_list'` using `dbDelta()`
- A deactivation hook (can be empty for now)
- Verify the table is created on activation and survives deactivation

## Pointers

- [Plugin Header Requirements](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/)
  -- the exact comment format WordPress reads to identify a plugin
- [Activation / Deactivation Hooks](https://developer.wordpress.org/plugins/plugin-basics/activation-deactivation-hooks/)
  -- `register_activation_hook()`, `register_deactivation_hook()`
- [Creating Tables with Plugins](https://developer.wordpress.org/plugins/creating-tables-with-plugins/)
  -- `dbDelta()`, the `$wpdb->prefix` pattern, and why `dbDelta` is preferred
  over raw `CREATE TABLE`

## Growth edges

- **Plugin vs. theme lifecycle**: A plugin activates once and runs on
  every request until deactivated. A theme runs on every request while
  active. The activation hook fires only once -- this is where one-time
  setup (like table creation) belongs.
- **$wpdb and prefixes**: WordPress installations can share a database
  with different table prefixes. Always use `$wpdb->prefix` -- never
  hardcode `wp_`.
- **dbDelta limitations**: `dbDelta()` parses SQL literally. Two spaces
  after column definitions, `KEY` not `INDEX`. Deviations silently fail.

## Done when

- [ ] The plugin appears in wp-admin > Plugins with the correct name and description
- [ ] Activating the plugin creates the reading-list table (`wp_reading_list` on the default DDEV prefix; verify with `ddev mysql` or phpMyAdmin)
- [ ] Deactivating and reactivating does not error (dbDelta is idempotent)
- [ ] The table name is built with `$wpdb->prefix . 'reading_list'` (visible in SQL as `wp_reading_list` only on a default install)
- [ ] `ddev exec vendor/bin/phpcs wp-content/plugins/reading-list/` runs without crashing (warnings OK)

**WPCS check:** Before opening your PR, run the linter. Fix all errors. Paste the PHPCS summary in your PR body.
