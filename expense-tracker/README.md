# Expense Tracker

Build a personal expense tracker. Users register, log spending by
category, attach receipt photos, and check monthly totals. You'll
design most of the database yourself -- the schema file gives you a
users table, the rest is up to you.

Procedural PHP. Each page is its own PHP file. No frameworks, no MVC,
no Composer packages.

## Setup

1. Fork this repository on GitHub
2. Clone your fork locally
3. Navigate to the `expense-tracker/` directory
4. Run `ddev start` to start the environment
5. Run `ddev mysql < schema.sql` to create the users table
6. Visit `https://expense-tracker.ddev.site/` in your browser

Step 6 will show a blank page or directory listing -- you haven't
created `public/index.php` yet.

The schema file only creates a users table. You design and create the
remaining tables as you work through each milestone. Add your CREATE
TABLE statements to schema.sql so your schema stays in one place.

## How to work

Same workflow as the bookmark manager, with one change: open your PR
early so you have a place to ask for help.

- Create a branch for each milestone: `project/expense-tracker/milestone-1`,
  `project/expense-tracker/milestone-2`, etc.
- At the start of each milestone, make an initial commit (even if
  empty), push the branch, and open a draft PR. This is your working
  space for that milestone.
- Commit whenever something works. A commit every 30-60 minutes is
  normal.
- Write commit messages that start with a verb. Good: "Add login form
  with email and password fields". Bad: "Updated files".
- When a milestone is done, fill in the PR description using the
  template below, mark the PR as ready, and merge into your own main.
  Do not wait for review. Start the next milestone from main:
  ```
  git checkout main
  git pull origin main
  git checkout -b project/expense-tracker/milestone-3
  ```
  Your reviewer will look at merged PRs and leave comments. Apply
  feedback going forward.
- **Stuck for more than an hour?** Post a comment on your draft PR.
  Say what you expected to happen, what actually happened, and what
  you tried. Your reviewer will reply with a hint.
- Sync with upstream when new content appears:
  ```
  git remote add upstream https://github.com/mbtamuli/php-projects.git
  git fetch upstream
  git merge upstream/main
  ```
  First command is one-time only.
- No automated tests. Check each "Done when" item yourself in the
  browser.

## Milestone 1: Users can register and log in

Build user registration, login, and session management. Every page
you build after this will check whether someone is logged in.

The schema file already has a users table. Modify it if your
implementation needs something different.

**Done when:**
- A new user can register with an email and password
- Passwords are securely hashed, not stored as plain text
- A registered user can log in
- Wrong credentials show an error without revealing which field was
  wrong
- A logged-in user stays logged in across page loads
- There is a way to log out
- Visiting any page while logged out redirects to login

## Milestone 2: Tracking expenses

Design the database tables you need for expenses and categories.
How do they relate to each other? How do they connect to the users
table?

**Done when:**
- A logged-in user can add an expense (amount, description, date,
  category)
- A user sees only their own expenses and can edit or delete them
- Categories are user-defined. A user can create, rename, and delete
  categories
- Deleting a category that has expenses handles the situation
  (your choice how)
- Each expense belongs to one category

## Milestone 3: Filtering and receipts

Filter expenses and attach receipt files.

**Done when:**
- Expenses can be filtered by category, by date range, or both
  at once
- Filtering handles any combination of the above without a separate
  hardcoded query for each one
- You can attach a receipt image or PDF when creating or editing an
  expense
- Uploaded files live on the filesystem with a path in the database,
  not as binary data in the table
- Uploads are validated for file type and size, with a clear error
  for invalid files
- Receipts can be viewed and removed from the expense detail page

## Milestone 4: Monthly summary

Before starting: read through your M1-M3 code and the review
comments on those PRs. What would you do differently now? Write
about it in this milestone's PR description.

Build a summary page that shows where the money goes.

**Done when:**
- A summary page shows total spending for the current month
- Spending is broken down by category
- You can navigate to previous and next months
- Each month shows how it compares to the month before (e.g.,
  "spent 12% more than last month")

## Stretch goals

If you finish all milestones before the next project is ready:

- **Export**: download expenses as CSV for a selected date range
- **Budgets**: set a monthly budget per category and show how much
  is used (e.g., "$80 of $150")
- **Recurring**: mark an expense as recurring monthly so it shows
  up each month without creating a separate entry
- **Refactoring**: compare the code for expense pages vs category
  pages. Is there duplication? Extract shared logic into common
  functions. Document what you changed and why in a PR
