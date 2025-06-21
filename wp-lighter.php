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

class WPLighter {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_action( 'init', array( $this, 'disable_core_features' ) );
        add_action( 'init', array( $this, 'remove_head_bloat' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'control_resources' ), 9999 );
        add_action( 'admin_enqueue_scripts', array( $this, 'control_resources' ), 9999 );
        add_action( 'init', array( $this, 'control_resources' ) );
        add_action( 'init', array( $this, 'optimize_media' ) );
        add_action( 'init', array( $this, 'apply_database_enforcements' ) );
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
                do_settings_sections( 'wp-lighter' );
                submit_button( 'Save Changes' );
                ?>
            </form>
        </div>
        <?php
    }

    public function disable_core_features() {
        $options = get_option( 'wp_lighter_options' );

        // Disable Gutenberg block editor
        if ( isset( $options['disable_gutenberg'] ) && $options['disable_gutenberg'] ) {
            add_filter( 'use_block_editor_for_post', '__return_false', 10 );
            add_filter( 'use_block_editor_for_post_type', '__return_false', 10 );
        }

        // Disable Emoji scripts/styles
        if ( isset( $options['disable_emojis'] ) && $options['disable_emojis'] ) {
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' );
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
            remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
            add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce' ) );
            add_filter( 'wp_resource_hints', array( $this, 'disable_emojis_remove_dns_prefetch' ), 10, 2 );
        }

        // Disable Embeds (oEmbed)
        if ( isset( $options['disable_embeds'] ) && $options['disable_embeds'] ) {
            remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
            remove_action( 'wp_head', 'wp_oembed_add_host_js' );
            remove_action( 'rest_api_init', 'wp_oembed_register_endpoints', 5 );
            add_filter( 'embed_oembed_discover', '__return_false' );
            remove_filter( 'the_content', 'wp_autoembed' );
            remove_filter( 'pre_oembed_result', 'wp_filter_oembed_result', 10 );
        }

        // Disable XML-RPC
        if ( isset( $options['disable_xmlrpc'] ) && $options['disable_xmlrpc'] ) {
            add_filter( 'xmlrpc_enabled', '__return_false' );
            remove_action( 'wp_head', 'rsd_link' );
        }

        // Disable REST API for non-logged-in users
        if ( isset( $options['disable_rest_api_non_logged_in'] ) && $options['disable_rest_api_non_logged_in'] ) {
            add_filter( 'rest_authentication_errors', array( $this, 'disable_rest_api_for_non_logged_in_users' ) );
        }

        // Disable RSS/Atom feeds
        if ( isset( $options['disable_rss_feeds'] ) && $options['disable_rss_feeds'] ) {
            remove_action( 'wp_head', 'feed_links_extra', 3 );
            remove_action( 'wp_head', 'feed_links', 2 );
            remove_action( 'wp_head', 'rsd_link' );
            remove_action( 'wp_head', 'wlwmanifest_link' );
            remove_action( 'wp_head', 'wp_generator' );
            add_action( 'do_feed', array( $this, 'disable_all_feeds' ), 1 );
            add_action( 'do_feed_rdf', array( $this, 'disable_all_feeds' ), 1 );
            add_action( 'do_feed_rss', array( $this, 'disable_all_feeds' ), 1 );
            add_action( 'do_feed_rss2', array( $this, 'disable_all_feeds' ), 1 );
            add_action( 'do_feed_atom', array( $this, 'disable_all_feeds' ), 1 );
            add_action( 'do_feed_rss2_comments', array( $this, 'disable_all_feeds' ), 1 );
            add_action( 'do_feed_atom_comments', array( $this, 'disable_all_feeds' ), 1 );
        }
    }

    public function disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        }
        return $plugins;
    }

    public function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
        if ( 'dns-prefetch' == $relation_type ) {
            /**
             * WordPress 4.7+ has an array which contains the URLs to prefetch for emojis.
             *
             * @param array $urls URLs to print for resource hints.
             * @param string $relation_type The relation type the URLs are printed for.
             * @return array Of URLs to print for resource hints.
             */
            $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/13.1.0/svg/' );
            $urls = array_diff( $urls, array( $emoji_svg_url ) );
        }
        return $urls;
    }

    public function disable_rest_api_for_non_logged_in_users( $access ) {
        if ( ! is_user_logged_in() && ! is_null( $access ) ) {
            return new WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access the REST API.', 'wp-lighter' ), array( 'status' => rest_authorization_required_code() ) );
        }
        return $access;
    }

    public function disable_all_feeds() {
        wp_die( __( 'No feed available, please visit our <a href="' . esc_url( home_url( '/' ) ) . '">homepage</a>!', 'wp-lighter' ) );
    }

    public function remove_head_bloat() {
        $options = get_option( 'wp_lighter_options' );

        // Remove WLW Manifest link
        if ( isset( $options['remove_wlwmanifest_link'] ) && $options['remove_wlwmanifest_link'] ) {
            remove_action( 'wp_head', 'wlwmanifest_link' );
        }

        // Remove RSD (Really Simple Discovery) link
        if ( isset( $options['remove_rsd_link'] ) && $options['remove_rsd_link'] ) {
            remove_action( 'wp_head', 'rsd_link' );
        }

        // Remove Shortlink
        if ( isset( $options['remove_shortlink'] ) && $options['remove_shortlink'] ) {
            remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
            remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
        }

        // Remove WordPress generator meta
        if ( isset( $options['remove_wp_generator'] ) && $options['remove_wp_generator'] ) {
            remove_action( 'wp_head', 'wp_generator' );
        }
    }

    public function control_resources() {
        $options = get_option( 'wp_lighter_options' );

        // Disable Dashicons for non-admin users
        if ( isset( $options['disable_dashicons_non_admin'] ) && $options['disable_dashicons_non_admin'] ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_dequeue_style( 'dashicons' );
                wp_deregister_style( 'dashicons' );
            }
        }

        // Disable jQuery Migrate
        if ( isset( $options['disable_jquery_migrate'] ) && $options['disable_jquery_migrate'] ) {
            add_action( 'wp_default_scripts', function( $scripts ) {
                if ( ! empty( $scripts->registered['jquery'] ) ) {
                    $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, ['jquery-migrate'] );
                }
            } );
        }

        // Disable Heartbeat API
        if ( isset( $options['disable_heartbeat_api'] ) && $options['disable_heartbeat_api'] ) {
            wp_deregister_script( 'heartbeat' );
            add_filter( 'heartbeat_send_interval', '__return_zero' );
        }
    }

    public function optimize_media() {
        $options = get_option( 'wp_lighter_options' );

        // Force native lazy loading
        if ( isset( $options['force_native_lazy_loading'] ) && $options['force_native_lazy_loading'] ) {
            add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_lazy_loading_attribute' ) );
            add_filter( 'wp_img_tag_add_loading_attr', array( $this, 'add_lazy_loading_attribute_to_img_tag' ), 10, 2 );
        }

        // Add decoding="async" to all images
        if ( isset( $options['add_decoding_async'] ) && $options['add_decoding_async'] ) {
            add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_decoding_async_attribute' ) );
            add_filter( 'wp_img_tag_add_decoding_attr', array( $this, 'add_decoding_async_attribute_to_img_tag' ), 10, 2 );
        }
    }

    public function add_lazy_loading_attribute( $attr ) {
        if ( ! isset( $attr['loading'] ) ) {
            $attr['loading'] = 'lazy';
        }
        return $attr;
    }

    public function add_lazy_loading_attribute_to_img_tag( $loading, $image ) {
        return 'lazy';
    }

    public function add_decoding_async_attribute( $attr ) {
        if ( ! isset( $attr['decoding'] ) ) {
            $attr['decoding'] = 'async';
        }
        return $attr;
    }

    public function add_decoding_async_attribute_to_img_tag( $decoding, $image ) {
        return 'async';
    }

    public function apply_database_enforcements() {
        $options = get_option( 'wp_lighter_options' );

        // Limit post revisions
        if ( isset( $options['limit_post_revisions'] ) && is_numeric( $options['limit_post_revisions'] ) ) {
            define( 'WP_POST_REVISIONS', (int) $options['limit_post_revisions'] );
        }

        // Set autosave interval
        if ( isset( $options['set_autosave_interval'] ) && is_numeric( $options['set_autosave_interval'] ) ) {
            define( 'AUTOSAVE_INTERVAL', (int) $options['set_autosave_interval'] );
        }

        // Daily spam comment cleanup
        if ( isset( $options['daily_spam_comment_cleanup'] ) && $options['daily_spam_comment_cleanup'] ) {
            if ( ! wp_next_scheduled( 'wp_lighter_daily_spam_cleanup' ) ) {
                wp_schedule_event( time(), 'daily', 'wp_lighter_daily_spam_cleanup' );
            }
            add_action( 'wp_lighter_daily_spam_cleanup', array( $this, 'clean_spam_comments' ) );
        } else {
            // If the setting is disabled, ensure the cron job is unscheduled
            $timestamp = wp_next_scheduled( 'wp_lighter_daily_spam_cleanup' );
            if ( $timestamp ) {
                wp_unschedule_event( $timestamp, 'daily', 'wp_lighter_daily_spam_cleanup' );
            }
        }
    }

    public function clean_spam_comments() {
        global $wpdb;
        $wpdb->query( "DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'" );
    }
}

// Initialize the plugin
new WPLighter();