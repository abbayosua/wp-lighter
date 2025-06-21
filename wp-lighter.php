<?php
/**
 * Plugin Name: WP Lighter
 * Plugin URI: https://abbayosua.web.id/wp-lighter
 * Description: A WordPress plugin to lighten your WordPress installation by disabling core features, removing head bloat, controlling resources, optimizing media, and enforcing database hygiene.
 * Version: 1.0.0
 * Author: Abba Yosua
 * Author URI: https://abbayosua.web.id/
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-lighter-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-lighter-features.php';

class WPLighter {

    public function __construct() {
        new WPLighter_Settings();
        new WPLighter_Features();
    }
}

// Initialize the plugin
new WPLighter();