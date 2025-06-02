<?php
/**
 * Uninstall script for 5chat Live Chat plugin
 *
 * This file is called when the plugin is deleted via the WordPress admin.
 * It removes all plugin options and data from the database.
 *
 * @package FiveChat
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options
delete_option( 'fivechat_website_token' );

// For multisite installations, delete options for all sites
if ( is_multisite() ) {
    global $wpdb;
    
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        delete_option( 'fivechat_website_token' );
        restore_current_blog();
    }
} 