# Developer Portfolio Theme

Build a WordPress classic theme for a developer portfolio site. You'll
work with the template hierarchy, The Loop, hooks, navigation menus,
widget areas, the Customizer API, and child themes.

This is your first CMS project. Instead of writing everything from
scratch, you're building inside WordPress's framework -- your PHP runs
when WordPress calls it, not the other way around.

**Scaffolding level:** Each milestone tells you what to build and points
you to the relevant WordPress Handbook pages. You find the implementation
approach yourself. The linter (PHPCS + WPCS) teaches WordPress coding
conventions by flagging what doesn't match.

## Setup

You already have ddev from P1/P2. This project uses `type: wordpress`
in ddev (P1/P2 used `type: php`). This gives you the `ddev wp` command
for running WordPress CLI operations inside the container.

### 1. Start the environment and install WordPress

```bash
cd developer-portfolio-theme
ddev start
ddev wp core download --skip-content
ddev wp core install --url='$DDEV_PRIMARY_URL' --title='Developer Portfolio' --admin_user=admin --admin_password=admin --admin_email=admin@example.com
```

Your site will be at `https://developer-portfolio-theme.ddev.site`.
Admin login: `admin` / `admin`.

Enable debug mode so PHP errors are visible (WordPress hides them by
default):

```bash
ddev wp config set WP_DEBUG true --raw
ddev wp config set WP_DEBUG_DISPLAY true --raw
```

### 2. Install PHPCS + WPCS

```bash
ddev composer install
```

The `composer.json` already includes PHPCS with the WordPress coding
standards ruleset. Run the linter on your theme:

```bash
ddev exec vendor/bin/phpcs
```

The `phpcs.xml.dist` file configures the standard and paths, so you
don't need to specify them every time.

You don't need to fix every warning in P3 -- just run it regularly and
notice what it flags. By P4, zero errors will be expected.

### 3. Activate the theme

The starter theme files are already at
`wp-content/themes/developer-portfolio/`. After running `ddev start`,
go to wp-admin > Appearance > Themes and activate "Developer Portfolio".

The starter files:
- `style.css` -- theme header comment (required by WordPress to
  recognize your theme)
- `index.php` -- empty, your main template
- `functions.php` -- empty, your theme setup

## Test content

WordPress starts with one "Hello World" post and one "Sample Page."
Before M2, create some test content so you have something to work with:
at least 5 posts across 2-3 categories, a few with featured images, and
2-3 pages. You can do this manually in wp-admin, or generate placeholder
posts:

```bash
ddev wp post generate --count=10
```

## How to work

- Create a branch for each milestone: `project/developer-portfolio-theme/milestone-1`,
  `project/developer-portfolio-theme/milestone-2`, etc.
- Commit whenever something works, even if it is small. A commit every
  30-60 minutes of work is normal.
- Write commit messages that start with a verb and describe what changed.
  Good: "Display posts using The Loop on home page".
  Bad: "Updated index.php".
- When a milestone is done, push your branch and open a PR on your fork.
  Then merge the PR into your own main right away -- do not wait for
  review. Start the next milestone from main:
  ```
  git checkout main
  git pull origin main
  git checkout -b project/developer-portfolio-theme/milestone-2
  ```
  Your reviewer will review the merged PR and leave comments. Apply any
  feedback in your next milestone.
- When new milestones are added, sync your fork with upstream:
  ```
  git fetch upstream
  git merge upstream/main
  ```
- This project has no automated tests. Check each item in the "Done
  when" list yourself by loading the page in your browser. If it works
  as described, the milestone is complete.

## Milestones

Milestone specs are in `milestones/`:

1. [Theme Skeleton + Home Page](milestones/m1-theme-skeleton.md)
2. [Template Hierarchy](milestones/m2-template-hierarchy.md)
3. [Navigation + Widget Areas](milestones/m3-navigation-widgets.md)
4. [Custom Page Templates + Portfolio](milestones/m4-custom-templates.md)
5. [Theme Polish + Customizer + Child Theme](milestones/m5-theme-polish.md)
