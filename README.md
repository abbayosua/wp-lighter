# WP Lighter

**Plugin Name:** WP Lighter
**Plugin URI:** https://abbayosua.web.id/wp-lighter
**Description:** A WordPress plugin designed to optimize your WordPress installation by disabling non-essential core features, removing head bloat, controlling resource loading, optimizing media, and enforcing database hygiene. This plugin aims to improve site performance, reduce unnecessary requests, and maintain a cleaner, more efficient WordPress environment.
**Version:** 1.0.0
**Author:** Abba Yosua
**Author URI:** https://abbayosua.web.id

## Overview

WP Lighter is a powerful WordPress plugin that helps you streamline your website by selectively disabling features and optimizing various aspects of your WordPress installation. It provides a user-friendly interface within the WordPress admin area to control these optimizations, ensuring your site remains fast, secure, and efficient.

## Features

WP Lighter offers a comprehensive set of features categorized into five main areas:

### 1. Disable Core Features

This section allows you to disable WordPress core functionalities that might not be necessary for your specific website, thereby reducing overhead and potential security risks.

*   **Disable Gutenberg Block Editor:** Turn off the modern block editor for posts and post types, reverting to the classic editor experience.
*   **Disable Emoji Scripts/Styles:** Prevent WordPress from loading emoji-related scripts and styles, which can reduce HTTP requests.
*   **Disable Embeds (oEmbed):** Stop WordPress from automatically embedding content from external sources (like YouTube, Twitter) and remove related scripts.
*   **Disable XML-RPC:** Enhance security by disabling the XML-RPC interface, often targeted by brute-force attacks.
*   **Disable REST API for non-logged-in users:** Restrict access to the WordPress REST API for visitors who are not logged in, improving security.
*   **Disable RSS/Atom Feeds:** Completely disable all RSS and Atom feeds, redirecting users to your homepage if they try to access a feed URL.

### 2. Remove Head Bloat

Unnecessary links and meta tags in the `<head>` section of your HTML can add bloat and reveal information about your WordPress installation. This section helps you clean up your head.

*   **Remove WLW Manifest link:** Remove the link to the Windows Live Writer manifest file.
*   **Remove RSD (Really Simple Discovery) link:** Remove the RSD link, which is used for blog clients.
*   **Remove Shortlink:** Remove the `<link rel='shortlink'>` tag from your head.
*   **Remove WordPress generator meta:** Hide the WordPress version number from the meta tag, a minor security enhancement.

### 3. Resource Control

Take control over how scripts and styles are loaded on your website to improve performance.

*   **Disable Dashicons for non-admin users:** Prevent Dashicons (WordPress admin icons) from loading on the frontend for users who are not logged in as administrators.
*   **Disable jQuery Migrate:** Stop loading the jQuery Migrate script, which is often not needed on modern WordPress sites and can cause conflicts.
*   **Disable Heartbeat API:** Control or disable the WordPress Heartbeat API, which can consume server resources by sending frequent AJAX requests.

### 4. Media Optimization

Optimize how images are loaded to enhance page speed and user experience.

*   **Force native lazy loading:** Automatically add the `loading="lazy"` attribute to all images, enabling browser-level lazy loading.
*   **Add decoding="async" to all images:** Add the `decoding="async"` attribute to all images, suggesting to the browser that images can be decoded asynchronously, improving rendering performance.

### 5. Database Enforcement

Maintain a clean and efficient WordPress database by enforcing limits and scheduling cleanups.

*   **Limit post revisions:** Set a maximum number of revisions for posts and pages, preventing the database from growing excessively with old revisions.
*   **Set autosave interval (seconds):** Adjust the interval at which WordPress automatically saves post drafts, reducing frequent database writes.
*   **Daily spam comment cleanup:** Schedule a daily task to automatically delete all comments marked as spam from your database.

## Installation

1.  **Download the Plugin:** Download the `wp-lighter` plugin folder.
2.  **Upload via WordPress Admin:**
    *   Go to your WordPress dashboard.
    *   Navigate to `Plugins` > `Add New`.
    *   Click on the `Upload Plugin` button.
    *   Choose the downloaded plugin zip file and click `Install Now`.
    *   After installation, click `Activate Plugin`.
3.  **Manual Installation (FTP/SFTP):**
    *   Unzip the `wp-lighter` plugin folder.
    *   Connect to your website via FTP/SFTP client.
    *   Navigate to the `wp-content/plugins/` directory.
    *   Upload the entire `wp-lighter` folder to this directory.
    *   Go to your WordPress dashboard, navigate to `Plugins`, and activate `WP Lighter`.

## Usage

Once activated, you can access the WP Lighter settings page:

1.  Go to `Settings` > `WP Lighter` in your WordPress admin menu.
2.  On the settings page, you will find various sections corresponding to the features listed above.
3.  Check the checkboxes or enter values for the features you wish to enable or configure.
4.  Click the `Save Changes` button to apply your settings.

## Development

### Contributing

Feel free to fork the repository, make improvements, and submit pull requests.

### Reporting Issues

If you encounter any bugs or have feature requests, please open an issue on the GitHub repository.
.