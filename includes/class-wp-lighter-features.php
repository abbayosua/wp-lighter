<?php
/**
 * WP Lighter Features Class
 *
 * Handles all core feature disabling, head bloat removal, resource control,
 * media optimization, and database enforcement.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WPLighter_Features {

    public function __construct() {
        add_action( 'init', array( $this, 'disable_core_features' ) );
        add_action( 'init', array( $this, 'remove_head_bloat' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'control_resources' ), 9999 );
        add_action( 'admin_enqueue_scripts', array( $this, 'control_resources' ), 9999 );
        add_action( 'init', array( $this, 'control_resources' ) );
        add_action( 'init', array( $this, 'optimize_media' ) );
        add_action( 'init', array( $this, 'apply_database_enforcements' ) );
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
            if ( ! defined( 'WP_POST_REVISIONS' ) ) {
                define( 'WP_POST_REVISIONS', (int) $options['limit_post_revisions'] );
            }
        }

        // Set autosave interval
        if ( isset( $options['set_autosave_interval'] ) && is_numeric( $options['set_autosave_interval'] ) ) {
            if ( ! defined( 'AUTOSAVE_INTERVAL' ) ) {
                define( 'AUTOSAVE_INTERVAL', (int) $options['set_autosave_interval'] );
            }
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