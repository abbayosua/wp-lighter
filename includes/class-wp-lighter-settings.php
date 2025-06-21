<?php
/**
 * WP Lighter Settings Class
 *
 * Handles all admin menu, settings registration, sections, fields, and callbacks.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WPLighter_Settings {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_action( 'admin_post_wp_lighter_toggle_status', array( $this, 'handle_toggle_status' ) );
    }

    public function add_admin_menu() {
        add_menu_page(
            'WP Lighter Settings',
            'WP Lighter',
            'manage_options',
            'wp-lighter',
            array( $this, 'settings_page' ),
            'dashicons-lightbulb',
            99
        );
    }

    public function settings_init() {
        register_setting( 'wp_lighter_settings_group', 'wp_lighter_options' );
        
        add_settings_section(
            'wp_lighter_disable_core_features_section',
            'Disable Core Features',
            array( $this, 'disable_core_features_section_callback' ),
            'wp-lighter'
        );

        add_settings_section(
            'wp_lighter_remove_head_bloat_section',
            'Remove Head Bloat',
            array( $this, 'remove_head_bloat_section_callback' ),
            'wp-lighter'
        );

        add_settings_section(
            'wp_lighter_resource_control_section',
            'Resource Control',
            array( $this, 'resource_control_section_callback' ),
            'wp-lighter'
        );

        add_settings_section(
            'wp_lighter_media_optimization_section',
            'Media Optimization',
            array( $this, 'media_optimization_section_callback' ),
            'wp-lighter'
        );

        add_settings_section(
            'wp_lighter_database_enforcement_section',
            'Database Enforcement',
            array( $this, 'database_enforcement_section_callback' ),
            'wp-lighter'
        );

        // Disable Core Features fields
        add_settings_field(
            'disable_gutenberg',
            'Disable Gutenberg Block Editor',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_disable_core_features_section',
            array( 'label_for' => 'disable_gutenberg', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_gutenberg' )
        );
        add_settings_field(
            'disable_emojis',
            'Disable Emoji Scripts/Styles',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_disable_core_features_section',
            array( 'label_for' => 'disable_emojis', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_emojis' )
        );
        add_settings_field(
            'disable_embeds',
            'Disable Embeds (oEmbed)',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_disable_core_features_section',
            array( 'label_for' => 'disable_embeds', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_embeds' )
        );
        add_settings_field(
            'disable_xmlrpc',
            'Disable XML-RPC',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_disable_core_features_section',
            array( 'label_for' => 'disable_xmlrpc', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_xmlrpc' )
        );
        add_settings_field(
            'disable_rest_api_non_logged_in',
            'Disable REST API for non-logged-in users',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_disable_core_features_section',
            array( 'label_for' => 'disable_rest_api_non_logged_in', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_rest_api_non_logged_in' )
        );
        add_settings_field(
            'disable_rss_feeds',
            'Disable RSS/Atom Feeds',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_disable_core_features_section',
            array( 'label_for' => 'disable_rss_feeds', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_rss_feeds' )
        );
        add_settings_field(
            'remove_wlwmanifest_link',
            'Remove WLW Manifest link',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_remove_head_bloat_section',
            array( 'label_for' => 'remove_wlwmanifest_link', 'option_name' => 'wp_lighter_options', 'field_id' => 'remove_wlwmanifest_link' )
        );
        add_settings_field(
            'remove_rsd_link',
            'Remove RSD (Really Simple Discovery) link',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_remove_head_bloat_section',
            array( 'label_for' => 'remove_rsd_link', 'option_name' => 'wp_lighter_options', 'field_id' => 'remove_rsd_link' )
        );
        add_settings_field(
            'remove_shortlink',
            'Remove Shortlink',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_remove_head_bloat_section',
            array( 'label_for' => 'remove_shortlink', 'option_name' => 'wp_lighter_options', 'field_id' => 'remove_shortlink' )
        );
        add_settings_field(
            'remove_wp_generator',
            'Remove WordPress generator meta',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_remove_head_bloat_section',
            array( 'label_for' => 'remove_wp_generator', 'option_name' => 'wp_lighter_options', 'field_id' => 'remove_wp_generator' )
        );
        add_settings_field(
            'disable_dashicons_non_admin',
            'Disable Dashicons for non-admin users',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_resource_control_section',
            array( 'label_for' => 'disable_dashicons_non_admin', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_dashicons_non_admin' )
        );
        add_settings_field(
            'disable_jquery_migrate',
            'Disable jQuery Migrate',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_resource_control_section',
            array( 'label_for' => 'disable_jquery_migrate', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_jquery_migrate' )
        );
        add_settings_field(
            'disable_heartbeat_api',
            'Disable Heartbeat API',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_resource_control_section',
            array( 'label_for' => 'disable_heartbeat_api', 'option_name' => 'wp_lighter_options', 'field_id' => 'disable_heartbeat_api' )
        );
        add_settings_field(
            'force_native_lazy_loading',
            'Force native lazy loading',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_media_optimization_section',
            array( 'label_for' => 'force_native_lazy_loading', 'option_name' => 'wp_lighter_options', 'field_id' => 'force_native_lazy_loading' )
        );
        add_settings_field(
            'add_decoding_async',
            'Add decoding="async" to all images',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_media_optimization_section',
            array( 'label_for' => 'add_decoding_async', 'option_name' => 'wp_lighter_options', 'field_id' => 'add_decoding_async' )
        );
        add_settings_field(
            'limit_post_revisions',
            'Limit post revisions',
            array( $this, 'text_input_callback' ),
            'wp-lighter',
            'wp_lighter_database_enforcement_section',
            array( 'label_for' => 'limit_post_revisions', 'option_name' => 'wp_lighter_options', 'field_id' => 'limit_post_revisions', 'type' => 'number', 'placeholder' => 'e.g., 5' )
        );
        add_settings_field(
            'set_autosave_interval',
            'Set autosave interval (seconds)',
            array( $this, 'text_input_callback' ),
            'wp-lighter',
            'wp_lighter_database_enforcement_section',
            array( 'label_for' => 'set_autosave_interval', 'option_name' => 'wp_lighter_options', 'field_id' => 'set_autosave_interval', 'type' => 'number', 'placeholder' => 'e.g., 300' )
        );
        add_settings_field(
            'daily_spam_comment_cleanup',
            'Daily spam comment cleanup',
            array( $this, 'checkbox_callback' ),
            'wp-lighter',
            'wp_lighter_database_enforcement_section',
            array( 'label_for' => 'daily_spam_comment_cleanup', 'option_name' => 'wp_lighter_options', 'field_id' => 'daily_spam_comment_cleanup' )
        );
    }

    public function disable_core_features_section_callback() {
        echo '<p>Settings related to disabling core WordPress features.</p>';
    }

    public function remove_head_bloat_section_callback() {
        echo '<p>Settings related to removing unnecessary elements from the HTML head.</p>';
    }

    public function resource_control_section_callback() {
        echo '<p>Settings related to controlling resource loading (CSS, JS).</p>';
    }

    public function media_optimization_section_callback() {
        echo '<p>Settings related to media file optimization.</p>';
    }

    public function database_enforcement_section_callback() {
        echo '<p>Settings related to database cleanup and optimization.</p>';
    }

    public function checkbox_callback( $args ) {
        $options = get_option( $args['option_name'] );
        $checked = isset( $options[ $args['field_id'] ] ) ? checked( 1, $options[ $args['field_id'] ], false ) : '';
        echo '<input type="checkbox" id="' . esc_attr( $args['field_id'] ) . '" name="' . esc_attr( $args['option_name'] ) . '[' . esc_attr( $args['field_id'] ) . ']" value="1" ' . $checked . ' />';
    }

    public function text_input_callback( $args ) {
        $options = get_option( $args['option_name'] );
        $value = isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : '';
        
        // Set default values for specific fields
        if ( empty( $value ) ) {
            switch ( $args['field_id'] ) {
                case 'limit_post_revisions':
                    $value = 3;
                    break;
                case 'set_autosave_interval':
                    $value = 300;
                    break;
            }
        }
        echo '<input type="text" id="' . esc_attr( $args['field_id'] ) . '" name="' . esc_attr( $args['option_name'] ) . '[' . esc_attr( $args['field_id'] ) . ']" value="' . esc_attr( $value ) . '" />';
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>WP Lighter Settings</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'wp_lighter_settings_group' );
                echo '<input type="hidden" name="_wp_http_referer" value="' . esc_attr( admin_url( 'admin.php?page=wp-lighter' ) ) . '" />';
                ?>
                <h2>Advanced Settings</h2>
                <?php do_settings_sections( 'wp-lighter' ); ?>
                <?php submit_button( 'Save Changes' ); ?>
            </form>
            <?php $this->display_debug_table(); ?>
        </div>
        <?php
    }

    public function display_debug_table() {
        $options = get_option( 'wp_lighter_options' );
        if ( ! $options ) {
            $options = array(); // Ensure $options is an array even if no settings are saved yet
        }

        $settings_map = array(
            'disable_gutenberg' => 'Disable Gutenberg Block Editor',
            'disable_emojis' => 'Disable Emoji Scripts/Styles',
            'disable_embeds' => 'Disable Embeds (oEmbed)',
            'disable_xmlrpc' => 'Disable XML-RPC',
            'disable_rest_api_non_logged_in' => 'Disable REST API for non-logged-in users',
            'disable_rss_feeds' => 'Disable RSS/Atom Feeds',
            'remove_wlwmanifest_link' => 'Remove WLW Manifest link',
            'remove_rsd_link' => 'Remove RSD (Really Simple Discovery) link',
            'remove_shortlink' => 'Remove Shortlink',
            'remove_wp_generator' => 'Remove WordPress generator meta',
            'disable_dashicons_non_admin' => 'Disable Dashicons for non-admin users',
            'disable_jquery_migrate' => 'Disable jQuery Migrate',
            'disable_heartbeat_api' => 'Disable Heartbeat API',
            'force_native_lazy_loading' => 'Force native lazy loading',
            'add_decoding_async' => 'Add decoding="async" to all images',
            'limit_post_revisions' => 'Limit post revisions',
            'set_autosave_interval' => 'Set autosave interval (seconds)',
            'daily_spam_comment_cleanup' => 'Daily spam comment cleanup',
        );

        echo '<h2>Current WP Lighter Settings (Debug)</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Setting</th><th>Status/Value</th></tr></thead>';
        echo '<tbody>';

        foreach ( $settings_map as $field_id => $label ) {
            $value = isset( $options[ $field_id ] ) ? $options[ $field_id ] : 'Not Set';
            $display_value = '';

            if ( in_array( $field_id, array(
                'disable_gutenberg',
                'disable_emojis',
                'disable_embeds',
                'disable_xmlrpc',
                'disable_rest_api_non_logged_in',
                'disable_rss_feeds',
                'remove_wlwmanifest_link',
                'remove_rsd_link',
                'remove_shortlink',
                'remove_wp_generator',
                'disable_dashicons_non_admin',
                'disable_jquery_migrate',
                'disable_heartbeat_api',
                'force_native_lazy_loading',
                'add_decoding_async',
                'daily_spam_comment_cleanup'
            ) ) ) {
                $display_value = ( $value == 1 ) ? 'Enabled' : 'Disabled';
            } else {
                $display_value = empty( $value ) ? 'Not Set' : esc_html( $value );
            }

            echo '<tr>';
            echo '<td>' . esc_html( $label ) . '</td>';
            echo '<td>' . $display_value . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }
}