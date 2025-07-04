<?php
/**
 * Frontend functionality class for 5chat plugin
 *
 * @package FiveChat
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles frontend widget functionality
 */
class FIVECHAT_Frontend {

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_widget_script' ) );
        add_filter( 'script_loader_tag', array( $this, 'add_async_attribute' ), 10, 2 );
    }

    /**
     * Enqueue the 5chat widget script on the frontend
     */
    public function enqueue_widget_script() {
        $website_token = get_option( 'fivechat_website_token' );

        if ( empty( $website_token ) ) {
            return;
        }

        // Validate token format before using it
        if ( ! $this->is_valid_token_format( $website_token ) ) {
            return;
        }

        $widget_url = $this->build_widget_url( $website_token );
        
        wp_enqueue_script( 
            'fivechat-widget', 
            esc_url( $widget_url ), 
            array(), 
            FIVECHAT_VERSION, 
            false 
        );
    }

    /**
     * Add async attribute to the 5chat widget script
     *
     * @param string $tag    The script tag.
     * @param string $handle The script handle.
     * @return string Modified script tag.
     */
    public function add_async_attribute( $tag, $handle ) {
        if ( 'fivechat-widget' === $handle ) {
            return str_replace( ' src', ' async src', $tag );
        }
        return $tag;
    }

    /**
     * Validate token format
     *
     * @param string $token The token to validate.
     * @return bool True if token format is valid.
     */
    private function is_valid_token_format( $token ) {
        return preg_match( '/^[a-zA-Z0-9_-]+$/', $token );
    }

    /**
     * Build the widget URL
     *
     * @param string $token The validated token.
     * @return string The widget URL.
     */
    private function build_widget_url( $token ) {
        return 'https://5chat.io/widget/' . sanitize_text_field( $token );
    }

    /**
     * Check if widget should be displayed on current page
     *
     * @return bool True if widget should be displayed.
     */
    public function should_display_widget() {
        // Don't display on admin pages
        if ( is_admin() ) {
            return false;
        }

        // Don't display on login/register pages
        if ( function_exists( 'is_login' ) && is_login() ) {
            return false;
        }

        // Don't display if token is not configured
        $website_token = get_option( 'fivechat_website_token' );
        if ( empty( $website_token ) ) {
            return false;
        }

        // Allow filtering of widget display
        return apply_filters( 'fivechat_should_display_widget', true );
    }

    /**
     * Get widget configuration for debugging
     *
     * @return array Widget configuration information.
     */
    public function get_widget_config() {
        $website_token = get_option( 'fivechat_website_token' );
        
        return array(
            'has_token' => ! empty( $website_token ),
            'token_format_valid' => ! empty( $website_token ) ? $this->is_valid_token_format( $website_token ) : false,
            'should_display' => $this->should_display_widget(),
            'widget_url' => ! empty( $website_token ) ? $this->build_widget_url( $website_token ) : '',
            'script_enqueued' => wp_script_is( 'fivechat-widget', 'enqueued' ),
        );
    }
} 