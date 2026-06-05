# WordPress Plugin: Reading List Manager

Build a WordPress plugin that lets site owners manage a personal reading
list. You will write all the plugin lifecycle code from scratch, register
a custom database table, build admin pages, expose a shortcode for the
frontend, and implement the Settings API. This is your first plugin --
you own the full plugin lifecycle from activation to uninstall.

**Scaffolding level:** Each milestone tells you what to build and points
you to the relevant WordPress Handbook pages. You find the implementation
approach yourself. The linter (PHPCS + WPCS) teaches WordPress coding
conventions by flagging what doesn't match.

## Setup

### 1. Start the environment and install WordPress

```
cd wordpress-plugin
ddev start
```

When finished:

- [ ] `ddev start` works and WordPress loads in your browser
- [ ] You can log into wp-admin (`admin` / `admin`)
- [ ] Your plugin appears in wp-admin > Plugins (deactivated, ready to activate)
- [ ] `ddev composer install` completes without errors

### 2. Install PHPCS + WPCS

```
ddev composer install
```

This installs PHP CodeSniffer and the WordPress Coding Standards ruleset.
The Composer installer plugin wires the standards together automatically.

To run the linter:

```
ddev exec vendor/bin/phpcs wp-content/plugins/reading-list/
```

**WPCS workflow rule (applies to every milestone):**
- Run the linter before you open a PR. Fix all errors. Warnings are OK if you can explain them.
- Paste the PHPCS summary (error count + top 3 violations) in your PR body.
- If you don't know why something is flagged, read the WPCS rule link in the output, then ask.

### 3. Activate the plugin

The starter plugin file is already at
`wp-content/plugins/reading-list/reading-list.php`. After `ddev start`,
go to wp-admin > Plugins and activate "Reading List Manager".

The starter file:
- `reading-list.php` -- plugin header comment (required by WordPress to
  recognise your plugin), empty activation/deactivation hooks

## Test content

A dummy SQL fixture is at `data/reading-list.sql`. Import it after
activation:

```
ddev import-db --file=data/reading-list.sql --no-drop
```

This fixture assumes DDEV's default `wp_` table prefix and creates
`wp_reading_list` with five seed rows. In plugin code, always build the
table name with `$wpdb->prefix . 'reading_list'` so the plugin works on
non-default WordPress installs.

## How to work

Each milestone is a pull request. Branch from `main`, do the work, open
a PR. Name your branch `milestone/N-short-name`, e.g.
`milestone/1-plugin-skeleton`.

Your PR description should answer:

- What did you build?
- What was the hardest part?
- What does the linter flag, and why?
- Output self-audit:
  - HTML text escaped with:
  - Attribute values escaped with:
  - URLs escaped with:
  - WordPress helpers that echo directly:
  - WordPress helpers that return values:

The reviewer will read your code and leave feedback. Address the feedback
in the same branch, push again. When the reviewer approves, the milestone
is done.

**For your first PR only**, also include:

> **What I created from scratch:** (list every file you created that did
> not exist before)

## Milestones

Milestone specs are in `milestones/`:

1. [Plugin Skeleton + Activation](milestones/m1-plugin-skeleton.md)
2. [Shortcode: Display the Reading List](milestones/m2-shortcode.md)
3. [Admin Page: Add a Book](milestones/m3-admin-add.md)
4. [Edit, Delete, and Nonces](milestones/m4-edit-delete-nonces.md)
5. [Settings API + Uninstall](milestones/m5-settings-uninstall.md)
