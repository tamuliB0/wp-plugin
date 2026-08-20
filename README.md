# Reading List Plugin

### A WordPress plugin built from scratch that lets site owners manage and display a personal book collection.

Reading List Manager manages a personal book collection through its own database table rather than a built-in post type. It covers a full plugin lifecycle: creating its schema on activation via `dbDelta()`, an admin CRUD screen protected by nonces and capability checks, a Settings API panel for pagination, a cached `[reading_list]` shortcode for the frontend, and a full teardown in `uninstall.php`.

---

## 🚀 Features

- **Full Plugin Lifecycle:** `register_activation_hook()` creates the `wp_reading_list` table via `dbDelta()`. `register_deactivation_hook()` is kept as a deliberate no-op, since deactivating the plugin shouldn't touch the data — only `uninstall.php` (gated by the `WP_UNINSTALL_PLUGIN` constant) drops the table and deletes the `reading_list_per_page` option.
- **Custom Database Table:** Book data lives in its own table, queried through `$wpdb` using the `%i` identifier placeholder for the table name and `%s`/`%d` placeholders for values — no reliance on core post types.
- **Admin CRUD Screen:** `add_menu_page()` / `add_submenu_page()` add a Reading List screen for adding, editing, and deleting books, with every action guarded by `check_admin_referer()` nonces and `current_user_can( 'manage_options' )`.
- **Settings API:** A dedicated Settings page (`register_setting()`, `add_settings_section()`, `add_settings_field()`) lets admins configure books-per-page, validated through a custom `sanitize_callback` with `add_settings_error()` feedback.
- **Frontend Shortcode:** `[reading_list]` renders a cached table of books via `shortcode_atts()` and `wp_cache_get()` / `wp_cache_set()`, with an optional `status` attribute to filter by reading status.
- **Input Handling & Redirects:** Form input is sanitized with `sanitize_text_field()`, `sanitize_textarea_field()`, `absint()`, and `wp_unslash()`, with a redirect-after-post pattern via `wp_safe_redirect()` + `add_query_arg()` carrying a message flag back to the admin screen.

---

## 🛠️ Built With

- **CMS:** WordPress 7.0
- **Language:** PHP 8.3.6
- **Local Environment:** DDEV 1.25.2

---

## 📋 Prerequisites

To run this application locally, your system must have Docker and DDEV installed. Follow the links below for easy, step-by-step setup guides depending on your operating system:

1. **Docker CE / Container Engine**
   - Linux Users: [Official Docker Installation Guide for Linux](https://docs.docker.com/engine/install/)
   - Mac/Windows Users: [Docker Desktop Installation Guide](https://docs.docker.com/desktop/)
2. **DDEV CLI**
   - Follow the [Official DDEV Installation Script & Guide](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/) for all platforms.

---

## ⚙️ Setup Instructions
💡 Already have a local WordPress site running on DDEV? Skip to Step 2.

### 1. Initialize a Local WordPress Site
```bash
mkdir wp-plugin && cd wp-plugin
ddev config --project-type=wordpress
ddev start
ddev wp core download
ddev wp core install --url='$DDEV_PRIMARY_URL' --title="YOUR-WEBSITE-TITLE" --admin_user=admin --admin_password=admin --admin_email=admin@example.com
```
Your site will be at `https://<your-ddev-project-name>.ddev.site`. Admin login: `admin` / `admin`.

### 2. Clone the Plugin

```bash
cd wp-content/plugins
git clone https://github.com/tamuliB0/reading-list-plugin.git
```

### 3. Activate the Plugin

1. Run `ddev launch` to open your site in the browser.
2. Log in to the WordPress dashboard at `/wp-admin`.
3. Go to **Plugins**.
4. Find **Reading List Plugin** and click **Activate**.

> ✅ On activation, the plugin automatically creates its custom database table.

---

## 💻 Usage

### 🔖 Add the Shortcode

Paste this shortcode into any page or post to show your reading list on the frontend:

```
[reading_list]
```

### 📚 Manage Your Books

Go to **Reading List** in your WordPress admin menu to add, view, edit, or delete a book.

### 🌐 Live Demo

👉 [View Live Site](http://www.bhardwaj.lovestoblog.com/wp/)

---

### ⚙️ Plugin Settings

Go to **Reading List > Settings** to configure books-per-page using the built-in Settings panel.

### 🛠️ Useful Commands

- `ddev start` — Starts your local WordPress site and database
- `ddev stop` — Stops the project without losing data
- `ddev describe` — Shows your local URLs and database login details
