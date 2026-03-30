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
- Open a PR when a milestone is done. You can start the next milestone
  while waiting for review.
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

*Milestones 3 and 4 (tags and search) will be added here while you
work on milestones 1 and 2. The database schema already includes the
tables for tags -- you will use them later.*

## Stretch goals

If you finish all milestones before the next project is ready:
- Add pagination (show 10 bookmarks per page with next/previous links)
- Add sorting (by title or by date added)
- Add a "favorites" feature (mark bookmarks as favorites, show them first)
