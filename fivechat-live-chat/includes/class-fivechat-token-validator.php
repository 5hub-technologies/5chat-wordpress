<?php
/**
 * Token validation class for 5chat plugin
 *
 * @package FiveChat
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles token validation with 5chat API
 */
class FiveChat_Token_Validator {

    /**
     * Cache group for transients
     *
     * @var string
     */
    private $cache_group = 'fivechat_token_validation';

    /**
     * Cache expiration time (1 hour)
     *
     * @var int
     */
    private $cache_expiration = HOUR_IN_SECONDS;

    /**
     * Test a token by making a request to 5chat widget endpoint
     *
     * @param string $token The token to validate.
     * @return array Validation result with 'valid' boolean and 'error' message.
     */
    public function validate_token( $token ) {
        if ( empty( $token ) ) {
            return array(
                'valid' => false,
                'error' => __( 'Token cannot be empty.', 'fivechat-live-chat' ),
            );
        }

        // Check cache first
        $cache_key = 'fivechat_token_valid_' . md5( $token );
        $cached_result = get_transient( $cache_key );

        if ( false !== $cached_result ) {
            return array(
                'valid' => ( 'valid' === $cached_result ),
                'error' => ( 'valid' === $cached_result ) ? '' : __( 'Invalid token. Please check your Website Token in your 5chat dashboard.', 'fivechat-live-chat' ),
            );
        }

        // Perform API validation
        $result = $this->perform_api_validation( $token );

        // Cache the result
        $cache_value = $result['valid'] ? 'valid' : 'invalid';
        set_transient( $cache_key, $cache_value, $this->cache_expiration );

        return $result;
    }

    /**
     * Perform the actual API validation
     *
     * @param string $token The token to validate.
     * @return array Validation result.
     */
    private function perform_api_validation( $token ) {
        $widget_url = 'https://5chat.io/widget/' . $token;

        // Make HTTP request to validate token
        $response = wp_remote_get( $widget_url, array(
            'timeout' => 10,
            'headers' => array(
                'User-Agent' => 'WordPress 5chat Plugin Token Validator/' . FIVECHAT_VERSION,
            ),
        ) );

        // Check for request errors
        if ( is_wp_error( $response ) ) {
            return array(
                'valid' => false,
                'error' => __( 'Unable to connect to 5chat. Please check your internet connection and try again.', 'fivechat-live-chat' ),
            );
        }

        $response_code = wp_remote_retrieve_response_code( $response );

        // Check response status
        if ( 200 === $response_code ) {
            return array(
                'valid' => true,
                'error' => '',
            );
        } elseif ( 404 === $response_code ) {
            return array(
                'valid' => false,
                'error' => __( 'Invalid token. Please check your Website Token in your 5chat dashboard.', 'fivechat-live-chat' ),
            );
        } else {
            return array(
                'valid' => false,
                'error' => sprintf(
                    /* translators: %d: HTTP response status code */
                    __( 'Token validation failed (HTTP %d). Please try again or contact 5chat support.', 'fivechat-live-chat' ),
                    $response_code
                ),
            );
        }
    }

    /**
     * Clear validation cache for a specific token
     *
     * @param string $token The token to clear cache for.
     */
    public function clear_token_cache( $token ) {
        if ( ! empty( $token ) ) {
            delete_transient( 'fivechat_token_valid_' . md5( $token ) );
        }
    }

    /**
     * Clear all validation caches
     */
    public function clear_all_caches() {
        // Get all known tokens to clear their specific caches
        // This is more WordPress-compliant than direct DB queries
        $current_token = get_option( 'fivechat_website_token' );
        
        if ( ! empty( $current_token ) ) {
            $this->clear_token_cache( $current_token );
        }
        
        // Clear any additional cached tokens if we have a way to track them
        // For now, we'll clear the current token cache only to avoid direct DB queries
        // In the future, consider maintaining a list of tokens that have been cached
    }

    /**
     * Get token cache status
     *
     * @param string $token The token to check.
     * @return string|false Cache status or false if not cached.
     */
    public function get_cache_status( $token ) {
        if ( empty( $token ) ) {
            return false;
        }

        return get_transient( 'fivechat_token_valid_' . md5( $token ) );
    }
} 