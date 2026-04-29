# M3: Navigation + Widget Areas

WordPress separates "what the theme supports" (registered in code) from
"what the admin configures" (set in wp-admin). In this milestone you
register navigation menus and a widget area in PHP, then configure them
through the WordPress admin.

## What to build

- Two navigation menu locations registered in `functions.php`:
  a primary menu (header) and a footer menu
- `wp_nav_menu()` calls in `header.php` and `footer.php` that render
  the assigned menus
- Fallback behavior when no menu is assigned to a location (pages list
  or nothing, no PHP errors)
- One widget area (sidebar) registered in `functions.php`
- `sidebar.php` template that calls `dynamic_sidebar()`
- Sidebar included on single post pages via `get_sidebar()`
- Sidebar hidden when no widgets are added (no empty container in HTML)

## Pointers

- [Navigation Menus](https://developer.wordpress.org/themes/functionality/navigation-menus/)
  -- `register_nav_menus()`, `wp_nav_menu()`, and the `fallback_cb` argument
- [Sidebars (Widget Areas)](https://developer.wordpress.org/themes/functionality/sidebars/)
  -- `register_sidebar()`, `dynamic_sidebar()`, `is_active_sidebar()`

## Growth edges

- **Register-in-code, configure-in-admin**: This is a pattern you'll
  see everywhere in WordPress. The theme declares support for something
  (menus, sidebars, customizer settings), and the admin UI lets users
  configure it. Your code never hardcodes the menu items.
- **Conditional template loading**: `get_sidebar()` on single posts
  but not on pages introduces template-level conditionals.

## Done when

- [ ] In wp-admin > Appearance > Menus, two locations appear: Primary and Footer
- [ ] Creating a menu and assigning it to Primary renders it in the header
- [ ] Creating a menu and assigning it to Footer renders it in the footer
- [ ] When no menu is assigned, the page loads without errors
- [ ] In wp-admin > Appearance > Widgets, a sidebar area appears
- [ ] Adding widgets to the sidebar area makes them appear on single post pages
- [ ] The sidebar does not appear on static pages
- [ ] When no widgets are added, the sidebar container is not in the HTML
