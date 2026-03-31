# Bookmarks

## The Brief

Riya works at a small marketing agency. Throughout her day she finds
useful articles, tools, and references, but she loses them across
dozens of browser tabs and random notes. She needs a simple web app
where she can save bookmarks with a title and URL, organize them
with tags, and find them later.

Your job is to build this app for Riya. She does not need anything
fancy -- just something that works reliably in the browser.

This project uses procedural PHP. Each page is its own PHP file.
No frameworks, no MVC pattern, no base classes. Keep it simple.

## Setup

1. Fork this repository on GitHub
2. Clone your fork locally
3. Navigate to the `bookmarks/` directory
4. Run `ddev start` to start the environment
5. Run `ddev mysql < schema.sql` to create the database tables
6. Visit `https://bookmarks.ddev.site/` in your browser

If step 6 shows a blank page or directory listing, that is expected --
you have not created `public/index.php` yet. That is your first task.

## How to work

- Create a branch for each milestone: `project/bookmarks/milestone-1`,
  `project/bookmarks/milestone-2`, etc.
- Commit whenever something works, even if it is small. A commit every
  30-60 minutes of work is normal.
- Write commit messages that start with a verb and describe what changed.
  Good: "Show bookmarks from database on homepage".
  Bad: "Updated index.php".
- When a milestone is done, push your branch and open a PR on your fork.
  Then merge the PR into your own main right away -- do not wait for
  review. Start the next milestone from main:
  ```
  git checkout main
  git pull origin main
  git checkout -b project/bookmarks/milestone-3
  ```
  Your reviewer will review the merged PR and leave comments. Apply any
  feedback in your next milestone.
- When new milestones are added to this README, sync your fork with
  upstream to get the changes:
  ```
  git remote add upstream https://github.com/mbtamuli/php-projects.git
  git fetch upstream
  git merge upstream/main
  ```
  You only need the first command once. After that, `git fetch upstream`
  and `git merge upstream/main` is enough.
- This project has no automated tests. Check each item in the "Done
  when" list yourself by trying it in the browser. If it works as
  described, the milestone is complete.

## Milestone 1: It renders

Create `public/index.php`. Connect to the database and display the
sample bookmarks on the page as an HTML list. Each bookmark should
show its title and URL. Clicking the URL should open it.

**Done when:**
- Visiting the homepage shows a list of bookmarks with titles and URLs
- The bookmarks come from the database, not hardcoded HTML
- Clicking a URL opens it in a new tab

**For your first PR only**, also include this section in the description:

> **What I created from scratch:** (list every file you created that
> did not exist before)

## Milestone 2: I can add and manage

Build forms to add, edit, and delete bookmarks.

**Done when:**
- There is a form to add a new bookmark (title, URL, optional notes)
- Submitting the form saves the bookmark to the database
- Each bookmark has an edit link (e.g., `edit.php?id=1`) that shows a
  form pre-filled with its current data
- Each bookmark has a delete button that removes it
- Submitting an empty title or URL shows a validation error
- After adding, editing, or deleting, the page redirects back to the
  bookmark list

## Milestone 3: I can organize

Before starting this milestone, read the review comments on your
milestone 1 and milestone 2 PRs. Fix the HTML nesting bug in index.php
and add the `target` attribute to bookmark links. Apply the other
feedback patterns as you build this milestone.

Your add and edit forms have identical validation logic. Before adding
tag features, extract that shared logic into a function in a new file
(e.g., `functions.php`) and require it where needed. This way you only
write validation once.

Then: use the `tags` and `bookmark_tags` tables from schema.sql to let
Riya organize her bookmarks. Look at the schema to see how the three
tables relate to each other.

**Done when:**
- Each bookmark shows its tags (if any) on the homepage
- The add bookmark form lets you assign one or more existing tags
- The edit bookmark form lets you change a bookmark's tags
- There is a way to filter bookmarks by a specific tag (e.g., clicking
  a tag name shows only bookmarks with that tag)
- Adding a bookmark with a tag that does not exist yet creates the tag
- Removing all bookmarks from a tag does not delete the tag itself

## Milestone 4: I can find things

Build search and pagination so Riya can find bookmarks when her list
grows long.

**Done when:**
- There is a search box on the homepage. Typing a term and submitting
  shows only bookmarks whose title contains that term
- Search results still show tags and all the same controls (edit, delete)
- The homepage shows 10 bookmarks per page with previous/next links
  (or page numbers). Pagination works with and without an active search
- There is a way to sort bookmarks by title or by date added (e.g., a
  dropdown or clickable column headers). The current sort choice persists
  across pages

## Stretch goals

If you finish all milestones before the next project is ready:
- Add a "favorites" feature (mark bookmarks as favorites, show them first)
- Add bulk actions (select multiple bookmarks, delete or tag them at once)
- Add an import feature (paste a list of URLs, create bookmarks from page titles)
