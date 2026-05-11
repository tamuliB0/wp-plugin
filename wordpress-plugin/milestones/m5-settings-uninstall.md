# M5: Settings API + Uninstall

The Settings API is WordPress's standard way to store and render plugin
configuration. In this milestone you add a settings page where admins
can configure the plugin, and a proper uninstall routine that cleans up
when the plugin is deleted.

## What to build

- Add a submenu page under "Reading List" called "Settings"
- Register one setting: `reading_list_per_page` (integer, default 10) --
  how many books the `[reading_list]` shortcode shows per page
- Use `register_setting()`, `add_settings_section()`, and
  `add_settings_field()` to render the settings form (do not build the
  form manually)
- Apply the setting in your shortcode: paginate the results using the
  stored value
- Add `uninstall.php` at the plugin root: when the plugin is deleted
  (not just deactivated), drop `$wpdb->prefix . 'reading_list'` and
  delete the `reading_list_per_page` option

## Pointers

- [Settings API](https://developer.wordpress.org/plugins/settings/settings-api/)
  -- `register_setting()`, `add_settings_section()`, `add_settings_field()`,
  `settings_fields()`, `do_settings_sections()`
- [Options API](https://developer.wordpress.org/plugins/settings/options-api/)
  -- `get_option()`, `update_option()`, `delete_option()`
- [Uninstall Methods](https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/)
  -- `uninstall.php` vs. `register_uninstall_hook()`, and why `uninstall.php`
  is preferred

## Growth edges

- **Settings API vs. manual forms**: The Settings API is verbose but it
  handles nonces, permission checks, and option sanitization for you.
  Most WordPress plugins use it for settings pages.
- **Settings API call order**: Hook registration to `admin_init`. In that
  callback call `register_setting()`, `add_settings_section()`, and
  `add_settings_field()`. In the page callback render `settings_fields()`,
  `do_settings_sections()`, and `submit_button()`.
- **Deactivation vs. deletion**: Deactivation is reversible -- don't
  drop tables or delete options on deactivation. Deletion is permanent --
  `uninstall.php` is the right place for cleanup.
- **uninstall.php safety check**: Always check `WP_UNINSTALL_PLUGIN`
  at the top of `uninstall.php`. WordPress sets this constant before
  calling the file -- without the check, direct access to the file could
  delete your data.

## Done when

- [ ] A "Settings" submenu appears under "Reading List" in wp-admin
- [ ] The settings form renders using the Settings API call order above (not a hand-rolled form)
- [ ] Saving a value of 2 and visiting a page with `[reading_list]` shows only 2 books
- [ ] `uninstall.php` exists and begins with `if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }`
- [ ] Deleting the plugin from wp-admin drops the prefixed reading-list table and removes the option (verify in `ddev mysql`)
- [ ] `ddev exec vendor/bin/phpcs wp-content/plugins/reading-list/` passes

**WPCS check:** Before opening your PR, run the linter. Fix all errors. Paste the PHPCS summary in your PR body.
