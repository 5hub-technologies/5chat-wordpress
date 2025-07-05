<?php
/**
 * Admin functionality class for 5chat plugin
 *
 * @package FiveChat
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles admin-related functionality
 */
class FIVECHAT_Admin {

    /**
     * Token validator instance
     *
     * @var FIVECHAT_Token_Validator
     */
    private $token_validator;

    /**
     * Constructor
     *
     * @param FIVECHAT_Token_Validator $token_validator Token validator instance.
     */
    public function __construct( FIVECHAT_Token_Validator $token_validator ) {
        $this->token_validator = $token_validator;
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action( 'admin_notices', array( $this, 'display_admin_notice_missing_token' ) );
        add_filter( 'plugin_action_links_' . plugin_basename( FIVECHAT_PLUGIN_FILE ), array( $this, 'add_settings_link' ) );
    }

    /**
     * Display an admin notice if the 5chat token is not set or invalid
     */
    public function display_admin_notice_missing_token() {
        $website_token = get_option( 'fivechat_website_token' );
        global $pagenow;

        // Check capability and limit to relevant pages
        if ( ! current_user_can( 'manage_options' ) || 
             ! in_array( $pagenow, array( 'index.php', 'plugins.php' ), true ) ) {
            return;
        }
        
        // Never show on options-general.php pages to avoid conflicts with settings_errors()
        if ( 'options-general.php' === $pagenow ) {
            return;
        }

        // Show notice if token is empty or invalid
        $show_notice = false;
        $notice_message = '';
        
        if ( empty( $website_token ) ) {
            $show_notice = true;
            $notice_message = sprintf( 
                '<strong>%s</strong>',
                __( '5chat Live Chat is active, but no Website Token is configured.', '5chat-blazing-fast-live-chat' )
            );
        } else {
            // Check if the current token is valid (cached check to avoid too many API calls)
            $token_check_cache = $this->token_validator->get_cache_status( $website_token );
            
            if ( false === $token_check_cache ) {
                // Cache expired or doesn't exist, validate token
                $validation_result = $this->token_validator->validate_token( $website_token );
                
                if ( ! $validation_result['valid'] ) {
                    $show_notice = true;
                    $notice_message = sprintf( 
                        '<strong>%s</strong>',
                        __( '5chat Live Chat token appears to be invalid.', '5chat-blazing-fast-live-chat' )
                    );
                }
            } elseif ( 'invalid' === $token_check_cache ) {
                $show_notice = true;
                $notice_message = sprintf( 
                    '<strong>%s</strong>',
                    __( '5chat Live Chat token appears to be invalid.', '5chat-blazing-fast-live-chat' )
                );
            }
        }
        
        if ( $show_notice ) {
            $this->render_admin_notice( $notice_message );
        }
    }

    /**
     * Render admin notice HTML
     *
     * @param string $message The notice message.
     */
    private function render_admin_notice( $message ) {
        $settings_page_url = admin_url( 'options-general.php?page=fivechat-settings' );
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <?php
                echo wp_kses_post( $message );
                $link_text = sprintf(
                    /* translators: %s: URL to the settings page */
                    __( 'Please <a href="%s">update your settings</a> to enable the chat widget.', '5chat-blazing-fast-live-chat' ),
                    esc_url( $settings_page_url )
                );
                echo ' ' . wp_kses_post( $link_text );
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Add a link to the settings page directly from the plugins list page
     *
     * @param array $links Existing plugin action links.
     * @return array Modified plugin action links.
     */
    public function add_settings_link( $links ) {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            esc_url( admin_url( 'options-general.php?page=fivechat-settings' ) ),
_x( 'Settings', 'plugin settings page link text', '5chat-blazing-fast-live-chat' )
        );
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Get current token status for debugging
     *
     * @return array Token status information.
     */
    public function get_token_status() {
        $website_token = get_option( 'fivechat_website_token' );
        
        if ( empty( $website_token ) ) {
            return array(
                'has_token' => false,
                'is_valid' => false,
                'cache_status' => false,
                'message' => __( 'No token configured', '5chat-blazing-fast-live-chat' ),
            );
        }

        $cache_status = $this->token_validator->get_cache_status( $website_token );
        $is_valid = ( 'valid' === $cache_status );

        return array(
            'has_token' => true,
            'is_valid' => $is_valid,
            'cache_status' => $cache_status,
            'message' => $is_valid ? 
                __( 'Token is valid and cached', '5chat-blazing-fast-live-chat' ) : 
                __( 'Token validation required', '5chat-blazing-fast-live-chat' ),
        );
    }
} 