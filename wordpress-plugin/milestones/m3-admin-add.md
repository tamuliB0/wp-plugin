# M3: Admin Page: Add a Book

WordPress plugins add pages to wp-admin via `add_menu_page()` and
`add_submenu_page()`. In this milestone you build an admin page with a
form to add new books to the reading list.

## What to build

- Register a top-level admin menu page called "Reading List"
- The page shows all current books in a simple table (reuse your query logic)
- A form below the table lets the admin add a new book (title, author, status, notes)
- On form submission: verify capability first, verify the form nonce second,
  validate required fields, sanitize input, then insert the row into
  `$wpdb->prefix . 'reading_list'` using `$wpdb->insert()`
- After a successful insert, redirect to the same page with a success notice

## Pointers

- [Administration Menus](https://developer.wordpress.org/plugins/administration-menus/)
  -- `add_menu_page()`, capability constants, menu slugs
- [Settings Security](https://developer.wordpress.org/plugins/security/)
  -- capability checks (`current_user_can()`) and nonces
- [Nonces](https://developer.wordpress.org/apis/security/nonces/)
  -- `wp_nonce_field()` and `check_admin_referer()` for the add form
- [$wpdb Insert](https://developer.wordpress.org/reference/classes/wpdb/insert/)
  -- `$wpdb->insert()`, format arrays
- [Data Sanitization](https://developer.wordpress.org/apis/security/sanitizing/)
  -- `sanitize_text_field()`, `sanitize_textarea_field()`
- [Admin Notices](https://developer.wordpress.org/reference/hooks/admin_notices/)
  -- display success/error messages after form submission

## Growth edges

- **Capability checks**: Admin pages should only be accessible to users
  with the right capability. `current_user_can( 'manage_options' )` is
  the standard gate for plugin settings pages.
- **Nonce before database writes**: Any admin form that changes data must
  include a nonce field and verify it before inserting. Do not build an
  insecure form now and plan to secure it later.
- **Post/Redirect/Get**: After a successful form submission, redirect
  instead of rendering. Without a redirect, refreshing the page
  re-submits the form.
- **$wpdb->insert() format arrays**: The second argument to insert() is
  the data, the third is the format array (`%s`, `%d`). Mismatch causes
  silent data corruption.

## Done when

- [ ] "Reading List" appears in the wp-admin left menu
- [ ] The page shows a table of existing books (seeded data from `data/reading-list.sql`)
- [ ] Submitting the add form with valid data inserts a row (verify in the table)
- [ ] Submitting with an empty title shows a validation error
- [ ] After a successful insert the page redirects and shows a success notice
- [ ] The add form includes `wp_nonce_field()` and the submission handler calls `check_admin_referer()` before inserting
- [ ] Submitting the form with a missing/tampered nonce does not insert a row
- [ ] The page is behind a capability check -- a Subscriber-role user cannot access it
- [ ] PR body includes the output self-audit from the project README
- [ ] `ddev exec vendor/bin/phpcs wp-content/plugins/reading-list/` passes

**WPCS check:** Before opening your PR, run the linter. Fix all errors. Paste the PHPCS summary in your PR body.
