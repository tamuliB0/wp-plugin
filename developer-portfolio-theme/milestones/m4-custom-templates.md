# M4: Custom Page Templates + Portfolio

WordPress lets you create page templates that appear as options in the
page editor. In this milestone you build a portfolio page that queries
posts from a specific category and displays them in a responsive grid.

## What to build

- A custom page template for the portfolio (Template Name comment in
  the file header makes it selectable in the page editor)
- The portfolio template uses a custom `WP_Query` instance (not the
  main query) to fetch posts from a "Portfolio" category
- Pagination using `paginate_links()` when there are more portfolio
  posts than fit on one page
- Responsive CSS grid layout: multi-column on desktop, single column
  on mobile
- A second custom page template (your choice -- "About Me", "Contact",
  "Full Width", or something else)
- Empty state: when no portfolio posts exist, display a message

## Pointers

- [Page Templates](https://developer.wordpress.org/themes/templates/page-templates/)
  -- the `Template Name` comment syntax that registers a template
- [WP_Query](https://developer.wordpress.org/reference/classes/wp_query/)
  -- custom queries, arguments like `category_name`, `posts_per_page`,
  `paged`
- [Pagination](https://developer.wordpress.org/themes/functionality/pagination/)
  -- `paginate_links()` for numbered page navigation

## Growth edges

- **Custom queries**: Until now, WordPress decided what to query. Now
  you write your own `WP_Query` to fetch exactly what you want. This
  is the pattern plugins and themes use for custom content displays.
- **Responsive CSS without a framework**: Media queries and CSS Grid
  or Flexbox, no Bootstrap or Tailwind.

## Done when

- [ ] Creating a page and selecting "Portfolio" template in the editor works
- [ ] The portfolio page displays posts from the "Portfolio" category in a grid
- [ ] The grid is multi-column on desktop (3 or 2 columns) and single column on mobile
- [ ] When more posts exist than `posts_per_page`, pagination links appear and work
- [ ] When no portfolio posts exist, a "no projects found" message appears
- [ ] A second custom page template is selectable and renders differently from the default
- [ ] The portfolio query does not break the main query (other pages still work normally)
