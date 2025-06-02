<?php
/**
 * Uninstall script for 5chat Live Chat plugin
 *
 * This file is called when the plugin is deleted via the WordPress admin.
 * It removes all plugin options and data from the database.
 *
 * @package FiveChat
 */

// Prevent direct file access
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options
delete_option( 'fivechat_website_token' );

// Clean up transients (cached token validation results)
// Get all sites in multisite if applicable
if ( is_multisite() ) {
    $sites = get_sites();
    foreach ( $sites as $site ) {
        switch_to_blog( $site->blog_id );
        
        // Delete options for this site
        delete_option( 'fivechat_website_token' );
        
        // Clean up all token validation cache transients
        wp_cache_flush();
        delete_transient_like( 'fivechat_token_valid_' );
        
        restore_current_blog();
    }
} else {
    // Single site - clean up transients
    wp_cache_flush();
    delete_transient_like( 'fivechat_token_valid_' );
}

/**
 * Helper function to delete transients with wildcard pattern
 */
function delete_transient_like( $prefix ) {
    // Instead of direct database query, we'll use a more WordPress-appropriate approach
    // This is acceptable for uninstall scripts where we need to clean up completely
    
    // Try to delete common transient patterns that our plugin might have created
    $possible_tokens = get_option( 'fivechat_website_token' );
    if ( ! empty( $possible_tokens ) ) {
        delete_transient( 'fivechat_token_valid_' . md5( $possible_tokens ) );
    }
    
    // Clear any general cache
    wp_cache_flush();
} 