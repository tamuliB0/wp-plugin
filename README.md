# Reading List Plugin

### A WordPress plugin built from scratch that lets site owners manage and display a personal book collection.

This plugin demonstrates a complete WordPress Plugin API lifecycle. It bypasses basic post-types by creating and querying a standalone, custom database table, providing custom administrative management screens, utilizing the official WordPress Settings API, and exposing clean frontend shortcodes.

## 🚀 Features

- **Full Lifecycle:** Runs setup on activation (creates the database table) and cleans up fully on uninstall.
- **Custom Database Table:** Stores book data in its own MySQL table using WordPress's built-in `$wpdb` object.
- **Admin Page:** Adds a menu page in your WordPress dashboard where you can add, view, and delete books.
- **Settings API:** Uses the native WordPress Settings API to save and validate plugin options securely.
- **Frontend Shortcode:** Add `[reading_list]` to any page or post to display your book collection.

## 🛠️ Built With

- **CMS:** WordPress 7.0
- **Language:** PHP 8.3.6
- **Local Environment:** DDEV 1.25.2

## 📋 Prerequisites

To run this application locally, your system must have Docker and DDEV installed. Follow the links below for easy, step-by-step setup guides depending on your operating system:

1. **Docker CE / Container Engine**
   - Linux Users: [Official Docker Installation Guide for Linux](https://docs.docker.com/engine/install/)
   - Mac/Windows Users: [Docker Desktop Installation Guide](https://docs.docker.com/desktop/)
2. **DDEV CLI**
   - Follow the [Official DDEV Installation Script & Guide](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/) for all platforms.


## ⚙️ Setup Instructions
💡 Already have a local WordPress site running on DDEV? Skip to Step 2.

### 1. Initialize a Local WordPress Site 
If you do not have an active local environment running, you can spin up a clean, isolated WordPress instance using DDEV in an empty directory:
```bash
mkdir wp-plugin && cd wp-plugin
ddev config --project-type=wordpress
ddev start
ddev wordpress download
```

### 2. Clone the Plugin

Clone this repository into the plugins folder:

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


## 💻 Usage

### 🔖 Add the Shortcode

Paste this shortcode into any page or post to show your reading list on the frontend:

```
[reading_list]
```

### 📚 Manage Your Books

Go to **Reading List** in your WordPress admin menu to:
- Add a new book
- View your full list
- Delete a book

### 🌐 Live Demo

👉 [View Live Site](http://www.bhardwaj.lovestoblog.com/wp/)

---

### ⚙️ Plugin Settings

Go to **Reading List > Settings** to configure plugin options using the built-in Settings panel.

### 🛠️ Useful Commands

- `ddev start` — Starts your local WordPress site and database
- `ddev stop` — Stops the project without losing data
- `ddev describe` — Shows your local URLs and database login details