<?php
/**
 * Settings management class for 5chat plugin
 *
 * @package FiveChat
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles plugin settings and admin page
 */
class FIVECHAT_Settings {

    /**
     * Token validator instance
     *
     * @var FIVECHAT_Token_Validator
     */
    private $token_validator;

    /**
     * Captured settings errors to prevent WordPress from showing them
     *
     * @var array
     */
    private $captured_errors = array();

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
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'update_option_fivechat_website_token', array( $this, 'clear_token_cache' ), 10, 2 );
        add_action( 'admin_init', array( $this, 'capture_settings_errors' ), 999 );
    }

    /**
     * Add the settings page to the WordPress admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __( '5chat Settings', '5chat-blazing-fast-live-chat' ),
            __( '5chat', '5chat-blazing-fast-live-chat' ),
            'manage_options',
            'fivechat-settings',
            array( $this, 'render_options_page' )
        );
    }

    /**
     * Register settings, sections, and fields for the settings page
     */
    public function settings_init() {
        // Register the setting with proper sanitization callback
        register_setting(
            'fivechat_settings_group',
            'fivechat_website_token',
            array(
                'sanitize_callback' => array( $this, 'sanitize_token' ),
                'show_in_rest'      => false,
            )
        );

        // Add a settings section
        add_settings_section(
            'fivechat_main_section',
            null, // No title needed for a single section
            null, // No description callback needed
            'fivechat-settings'
        );

        // Add the Website Token field
        add_settings_field(
            'fivechat_website_token_field',
            __( 'Website Token', '5chat-blazing-fast-live-chat' ),
            array( $this, 'render_token_field' ),
            'fivechat-settings',
            'fivechat_main_section',
            array( 'label_for' => 'fivechat_website_token_id' )
        );
    }

    /**
     * Sanitization callback for the website token
     *
     * @param string $input The input token value.
     * @return string Sanitized token value.
     */
    public function sanitize_token( $input ) {
        // Trim whitespace and sanitize as a simple text field
        $sanitized = sanitize_text_field( trim( $input ) );

        // If empty, allow it (user can clear the token)
        if ( empty( $sanitized ) ) {
            return $sanitized;
        }

        // Basic format validation - token should be alphanumeric with possible hyphens/underscores
        if ( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $sanitized ) ) {
            add_settings_error(
                'fivechat_website_token',
                'invalid_token_format',
                __( 'Invalid Website Token format. Please use only letters, numbers, hyphens, and underscores.', '5chat-blazing-fast-live-chat' ),
                'error'
            );
            // Return the previous value if validation fails
            return get_option( 'fivechat_website_token' );
        }

        // Validate token with 5chat API
        $validation_result = $this->token_validator->validate_token( $sanitized );

        if ( ! $validation_result['valid'] ) {
            add_settings_error(
                'fivechat_website_token',
                'invalid_token_api',
                sprintf(
                    /* translators: %s: Error message from the token validation API */
                    __( 'Token validation failed: %s', '5chat-blazing-fast-live-chat' ),
                    $validation_result['error']
                ),
                'error'
            );
            // Return the previous value if API validation fails
            return get_option( 'fivechat_website_token' );
        }

        // Token is valid - add success message only if this is a different token
        $old_token = get_option( 'fivechat_website_token' );
        if ( $old_token !== $sanitized ) {
            add_settings_error(
                'fivechat_website_token',
                'token_validated',
                __( 'Website Token validated successfully! Your 5chat widget is now active.', '5chat-blazing-fast-live-chat' ),
                'success'
            );
        }

        return $sanitized;
    }

    /**
     * Render the HTML for the Website Token input field
     */
    public function render_token_field() {
        $option_value = get_option( 'fivechat_website_token' );
        ?>
        <input type="text"
               id="fivechat_website_token_id"
               name="fivechat_website_token"
               value="<?php echo esc_attr( $option_value ); ?>"
               class="regular-text"
               placeholder="<?php esc_attr_e( 'Paste your Website Token here', '5chat-blazing-fast-live-chat' ); ?>">
        <p class="description">
            <?php esc_html_e( 'Find your Website Token in your 5chat dashboard.', '5chat-blazing-fast-live-chat' ); ?>
        </p>
        <?php
    }

    /**
     * Render the HTML for the settings page
     */
    public function render_options_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Check if we should suppress WordPress default success message
        $suppress_default = $this->should_suppress_default_notices();
        ?>
        <div class="wrap fivechat-settings-wrap">
            <!-- Header Banner -->
            <div class="fivechat-header-banner">
                <div class="fivechat-banner-content">
                    <div class="fivechat-logo-section">
                        <div class="fivechat-logo">
                            <?php $this->render_logo_image(); ?>
                            <span class="fivechat-logo-text">5chat</span>
                        </div>
                    </div>
                    <div class="fivechat-banner-actions">
                        <a href="https://5chat.io/dashboard" target="_blank" class="fivechat-dashboard-btn">
                            <span class="dashicons dashicons-external"></span>
                            <?php esc_html_e( 'Open Dashboard', '5chat-blazing-fast-live-chat' ); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="fivechat-main-content">
                <div class="fivechat-content-card">
                    <h1 class="fivechat-page-title"><?php esc_html_e( 'Widget Configuration', '5chat-blazing-fast-live-chat' ); ?></h1>
                    <p class="fivechat-page-description">
                        <?php esc_html_e( 'Configure your 5chat widget by entering your Website Token below. The chat widget will automatically appear on your site once configured.', '5chat-blazing-fast-live-chat' ); ?>
                    </p>

                    <?php $this->display_custom_settings_errors( $suppress_default ); ?>

                    <form action="options.php" method="post" id="fivechat-settings-form" class="fivechat-form">
                        <?php
                        settings_fields( 'fivechat_settings_group' );
                        do_settings_sections( 'fivechat-settings' );
                        ?>

                        <div class="fivechat-form-actions">
                            <?php submit_button( __( 'Save Configuration', '5chat-blazing-fast-live-chat' ), 'primary fivechat-save-btn', 'submit', false ); ?>
                        </div>
                    </form>
                </div>

                <!-- Help Section -->
                <div class="fivechat-help-card">
                    <h3 class="fivechat-help-title">
                        <span class="dashicons dashicons-lightbulb"></span>
                        <?php esc_html_e( 'Quick Setup Guide', '5chat-blazing-fast-live-chat' ); ?>
                    </h3>
                    <div class="fivechat-help-steps">
                        <div class="fivechat-step">
                            <div class="fivechat-step-number">1</div>
                            <div class="fivechat-step-content">
                                <strong><?php esc_html_e( 'Get Your Token', '5chat-blazing-fast-live-chat' ); ?></strong>
                                <p>
                                    <?php
                                    /* translators: %s: URL to the widget configuration page */
                                    echo wp_kses_post( sprintf(
                                        /* translators: %s: URL to the widget configuration page */
                                        __( 'Visit your <a href="%s" target="_blank" class="fivechat-link">widget configuration page <span class="dashicons dashicons-external"></span></a> to copy your Website Token.', '5chat-blazing-fast-live-chat' ),
                                        esc_url( 'https://5chat.io/dashboard/configuration/widget' )
                                    ) );
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="fivechat-step">
                            <div class="fivechat-step-number">2</div>
                            <div class="fivechat-step-content">
                                <strong><?php esc_html_e( 'Paste & Save', '5chat-blazing-fast-live-chat' ); ?></strong>
                                <p><?php 
                                /* translators: Instructions for users on how to save their chat widget token */
                                esc_html_e( 'Paste your token in the field above and click "Save Configuration". The token will be validated when you save.', '5chat-blazing-fast-live-chat' ); ?></p>
                            </div>
                        </div>
                        <div class="fivechat-step">
                            <div class="fivechat-step-number">3</div>
                            <div class="fivechat-step-content">
                                <strong><?php esc_html_e( 'Save & Go Live', '5chat-blazing-fast-live-chat' ); ?></strong>
                                <p><?php 
                                /* translators: Final step instruction for activating the chat widget */
                                esc_html_e( 'Click "Save Configuration" and your chat widget will immediately appear on your website.', '5chat-blazing-fast-live-chat' ); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="fivechat-help-footer">
                        <p>
                            <strong><?php esc_html_e( 'Need help?', '5chat-blazing-fast-live-chat' ); ?></strong>
                            <?php
                            /* translators: %s: URL to the support page */
                            echo wp_kses_post( sprintf(
                                /* translators: %s: URL to the support page */
                                __( 'Contact <a href="%s" target="_blank" class="fivechat-link">5chat support <span class="dashicons dashicons-external"></span></a>', '5chat-blazing-fast-live-chat' ),
                                esc_url( 'https://5chat.io' )
                            ) );
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin styles for the settings page
     *
     * @param string $hook_suffix The current admin page.
     */
    public function enqueue_admin_styles( $hook_suffix ) {
        // Only load on our settings page
        if ( 'settings_page_fivechat-settings' !== $hook_suffix ) {
            return;
        }

        wp_enqueue_style(
            'fivechat-admin',
            FIVECHAT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            FIVECHAT_VERSION
        );
    }

    /**
     * Clear token validation cache when token is updated
     *
     * @param string $old_value Old token value.
     * @param string $new_value New token value.
     */
    public function clear_token_cache( $old_value, $new_value ) {
        if ( $old_value !== $new_value ) {
            // Clear cache for old token
            if ( ! empty( $old_value ) ) {
                $this->token_validator->clear_token_cache( $old_value );
            }
            // Clear cache for new token
            if ( ! empty( $new_value ) ) {
                $this->token_validator->clear_token_cache( $new_value );
            }
        }
    }

    /**
     * Render the logo image
     */
    private function render_logo_image() {
        // WordPress-compliant image display using proper attribute building
        $image_url = FIVECHAT_PLUGIN_URL . 'assets/icon-128x128.png';
        $image_attributes = array(
            'src'     => esc_url( $image_url ),
            'width'   => '64',
            'height'  => '64',
            'alt'     => _x( '5chat', 'logo image alt text', '5chat-blazing-fast-live-chat' ),
            'class'   => 'fivechat-logo-icon',
            'loading' => 'lazy',
        );

        // Build image tag with proper escaping
        printf(
            '<img %s>',
            wp_kses_post( $this->build_image_attributes( $image_attributes ) )
        );
    }

    /**
     * Build image attributes string
     *
     * @param array $attributes Image attributes.
     * @return string Properly formatted attributes string.
     */
    private function build_image_attributes( $attributes ) {
        $attribute_strings = array();

        foreach ( $attributes as $key => $value ) {
            $attribute_strings[] = sprintf(
                '%s="%s"',
                esc_attr( $key ),
                esc_attr( $value )
            );
        }

        return implode( ' ', $attribute_strings );
    }

    /**
     * Display custom settings errors
     *
     * @param bool $suppress_default Whether to suppress WordPress default messages.
     */
    private function display_custom_settings_errors( $suppress_default = false ) {
        // Use captured errors instead of getting them again
        $settings_errors = $this->captured_errors;

        if ( empty( $settings_errors ) ) {
            return;
        }

        // Check if we have any error messages (not success)
        $has_errors = false;
        foreach ( $settings_errors as $error ) {
            if ( 'error' === $error['type'] ) {
                $has_errors = true;
                break;
            }
        }

        // If we have errors or are suppressing defaults, only show error messages
        if ( $has_errors || $suppress_default ) {
            foreach ( $settings_errors as $error ) {
                if ( 'error' === $error['type'] ) {
                    printf(
                        '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
                        wp_kses_post( $error['message'] )
                    );
                }
            }
        } else {
            // No errors, show all messages (including success)
            foreach ( $settings_errors as $error ) {
                $notice_class = 'error' === $error['type'] ? 'notice-error' : 'notice-success';
                printf(
                    '<div class="notice %s is-dismissible"><p>%s</p></div>',
                    esc_attr( $notice_class ),
                    wp_kses_post( $error['message'] )
                );
            }
        }
    }

    /**
     * Check if we should suppress WordPress default success message
     *
     * @return bool True if we should suppress default notices, false otherwise.
     */
    private function should_suppress_default_notices() {
        // Use captured errors instead of getting them again
        foreach ( $this->captured_errors as $error ) {
            if ( 'error' === $error['type'] ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Capture settings errors early to prevent WordPress from showing them
     */
    public function capture_settings_errors() {
        // Only capture on our settings page - use screen detection instead of $_GET
        $screen = get_current_screen();
        if ( ! $screen || 'settings_page_fivechat-settings' !== $screen->id ) {
            return;
        }

        // Capture our settings errors
        $this->captured_errors = get_settings_errors( 'fivechat_website_token' );

        // Clear them from WordPress to prevent default display
        if ( ! empty( $this->captured_errors ) ) {
            global $wp_settings_errors;
            unset( $wp_settings_errors['fivechat_website_token'] );
        }
    }
}
