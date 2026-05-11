# M4: Edit, Delete, and Nonces

Forms that modify data must be protected from cross-site request forgery
(CSRF). M3 introduced nonces for the add form. In this milestone you
extend that protection to edit and delete actions.

## What to build

- Each book row in the admin table gets Edit and Delete links
- Delete: a single-step action (nonce-protected GET link, no JS confirm required)
- Edit: clicking Edit loads a form pre-filled with the book's current data;
  submitting updates the row in the database
- The existing add form keeps its M3 nonce protection; the new edit form
  gets its own nonce field; deletion links include a nonce in the URL;
  all actions verify the nonce before touching the database
- After each action (edit, delete), redirect with an appropriate admin notice

## Pointers

- [Nonces](https://developer.wordpress.org/apis/security/nonces/)
  -- `wp_nonce_field()`, `wp_create_nonce()`, `wp_verify_nonce()`,
  `check_admin_referer()`
- [$wpdb Update / Delete](https://developer.wordpress.org/reference/classes/wpdb/)
  -- `$wpdb->update()`, `$wpdb->delete()`, format arrays
- [add_query_arg](https://developer.wordpress.org/reference/functions/add_query_arg/)
  -- building safe redirect URLs with parameters

## Growth edges

- **Nonces are not authentication**: They verify the request came from
  your page, not that the user is logged in. Always check capability
  first, nonce second.
- **GET vs POST for destructive actions**: DELETE is a destructive
  action. A nonce-protected GET link is acceptable for admin pages (no
  JS required), but the nonce is what prevents CSRF -- without it a
  malicious link in an email could delete data.
- **check_admin_referer() vs wp_verify_nonce()**: `check_admin_referer()`
  combines the nonce check with `die()` on failure -- simpler for admin
  pages where failure should always abort.

## Done when

- [ ] Every book row has Edit and Delete links
- [ ] Clicking Delete removes the book and redirects with a notice
- [ ] Clicking Edit shows a pre-filled form; submitting updates the row
- [ ] Add and edit forms include `wp_nonce_field()`; deletion links include `wp_create_nonce()` in the URL
- [ ] PR body names the capability check and nonce check order for add, edit, and delete
- [ ] Submitting a form with a tampered/missing nonce triggers `check_admin_referer()` and dies
- [ ] `ddev exec vendor/bin/phpcs wp-content/plugins/reading-list/` passes

**WPCS check:** Before opening your PR, run the linter. Fix all errors. Paste the PHPCS summary in your PR body.
