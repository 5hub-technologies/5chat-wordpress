<?php
/**
 * Plugin Name:       5chat - Blazing fast live chat
 * Plugin URI:        https://5chat.io/integrations/wordpress
 * Description:       Easily integrate customer support live chat for your website.
 * Version:           1.0.0
 * Author:            5chat
 * Author URI:        https://5chat.io
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       fivechat
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to:      6.5
 * Requires PHP:      7.4
 * Network:           false
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'FIVECHAT_VERSION', '1.0.0' );
define( 'FIVECHAT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FIVECHAT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin activation hook
 */
function fivechat_activate() {
    // Add default options if they don't exist
    add_option( 'fivechat_website_token', '' );
}
register_activation_hook( __FILE__, 'fivechat_activate' );

/**
 * Plugin deactivation hook
 */
function fivechat_deactivate() {
    // Clean up any temporary data if needed
    // Note: We don't delete the settings here as user might reactivate
}
register_deactivation_hook( __FILE__, 'fivechat_deactivate' );

/**
 * Add the settings page to the WordPress admin menu.
 */
function fivechat_add_admin_menu() {
    add_options_page(
        '5chat Settings',
        '5chat',
        'manage_options',
        'fivechat-settings',
        'fivechat_options_page_html'
    );
}
add_action( 'admin_menu', 'fivechat_add_admin_menu' );

/**
 * Register settings, sections, and fields for the settings page.
 */
function fivechat_settings_init() {
    // Register the setting
    register_setting(
        'fivechat_settings_group',
        'fivechat_website_token',
        'fivechat_sanitize_token'
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
        __( 'Website Token', 'fivechat' ),
        'fivechat_token_field_html',
        'fivechat-settings',
        'fivechat_main_section',
        [ 'label_for' => 'fivechat_website_token_id' ]
    );
}
add_action( 'admin_init', 'fivechat_settings_init' );

/**
 * Add AJAX actions for token validation
 */
add_action( 'wp_ajax_fivechat_validate_token', 'fivechat_validate_token_ajax' );

/**
 * AJAX handler for token validation
 */
function fivechat_validate_token_ajax() {
    // Verify nonce for security
    if ( ! wp_verify_nonce( $_POST['nonce'], 'fivechat_validate_token' ) ) {
        wp_die( 'Security check failed' );
    }
    
    // Check user permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Insufficient permissions' );
    }
    
    $token = sanitize_text_field( $_POST['token'] );
    
    // Validate token format first
    if ( empty( $token ) ) {
        wp_send_json_error( array( 'message' => __( 'Token cannot be empty.', 'fivechat' ) ) );
    }
    
    if ( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $token ) ) {
        wp_send_json_error( array( 'message' => __( 'Invalid token format. Use only letters, numbers, hyphens, and underscores.', 'fivechat' ) ) );
    }
    
    // Test the token with 5chat API
    $validation_result = fivechat_test_token( $token );
    
    if ( $validation_result['valid'] ) {
        wp_send_json_success( array( 
            'message' => __( 'Token is valid! Chat widget will load successfully.', 'fivechat' ),
            'token' => $token
        ) );
    } else {
        wp_send_json_error( array( 
            'message' => $validation_result['error']
        ) );
    }
}

/**
 * Test a token by making a request to 5chat widget endpoint
 */
function fivechat_test_token( $token ) {
    $widget_url = 'https://5chat.io/widget/' . $token;
    
    // Make HTTP request to validate token
    $response = wp_remote_get( $widget_url, array(
        'timeout' => 10,
        'headers' => array(
            'User-Agent' => 'WordPress 5chat Plugin Token Validator'
        )
    ) );
    
    // Check for request errors
    if ( is_wp_error( $response ) ) {
        return array(
            'valid' => false,
            'error' => __( 'Unable to connect to 5chat. Please check your internet connection and try again.', 'fivechat' )
        );
    }
    
    $response_code = wp_remote_retrieve_response_code( $response );
    
    // Check response status
    if ( $response_code === 200 ) {
        return array(
            'valid' => true,
            'error' => ''
        );
    } elseif ( $response_code === 404 ) {
        return array(
            'valid' => false,
            'error' => __( 'Invalid token. Please check your Website Token in your 5chat dashboard.', 'fivechat' )
        );
    } else {
        return array(
            'valid' => false,
            'error' => sprintf( 
                __( 'Token validation failed (HTTP %d). Please try again or contact 5chat support.', 'fivechat' ),
                $response_code
            )
        );
    }
}

/**
 * Render the HTML for the settings page.
 */
function fivechat_options_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap fivechat-settings-wrap">
        <!-- Header Banner -->
        <div class="fivechat-header-banner">
            <div class="fivechat-banner-content">
                <div class="fivechat-logo-section">
                    <div class="fivechat-logo">
                        <img src="<?php echo FIVECHAT_PLUGIN_URL; ?>assets/icon-128x128.png" width="32" height="32" alt="5chat" class="fivechat-logo-icon">
                        <span class="fivechat-logo-text">5chat</span>
                    </div>
                    <span class="fivechat-tagline">Blazing Fast Live Chat</span>
                </div>
                <div class="fivechat-banner-actions">
                    <a href="https://5chat.io/dashboard" target="_blank" class="fivechat-dashboard-btn">
                        <span class="dashicons dashicons-external"></span>
                        <?php esc_html_e( 'Open Dashboard', 'fivechat' ); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="fivechat-main-content">
            <div class="fivechat-content-card">
                <h1 class="fivechat-page-title"><?php esc_html_e( 'Widget Configuration', 'fivechat' ); ?></h1>
                <p class="fivechat-page-description">
                    <?php esc_html_e( 'Configure your 5chat widget by entering your Website Token below. The chat widget will automatically appear on your site once configured.', 'fivechat' ); ?>
                </p>
                
                <?php settings_errors( 'fivechat_website_token' ); ?>
                
                <form action="options.php" method="post" id="fivechat-settings-form" class="fivechat-form">
                    <?php
                    settings_fields( 'fivechat_settings_group' );
                    do_settings_sections( 'fivechat-settings' );
                    ?>
                    
                    <div id="fivechat-validation-status" class="fivechat-validation-container">
                        <div id="fivechat-validation-loading" class="fivechat-status fivechat-loading" style="display: none;">
                            <div class="fivechat-status-icon">
                                <div class="fivechat-spinner"></div>
                            </div>
                            <span class="fivechat-status-text"><?php esc_html_e( 'Validating token...', 'fivechat' ); ?></span>
                        </div>
                        <div id="fivechat-validation-success" class="fivechat-status fivechat-success" style="display: none;">
                            <div class="fivechat-status-icon">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="10" fill="#10B981"/>
                                    <path d="M6 10L8.5 12.5L14 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <span class="fivechat-status-text" id="fivechat-success-message"></span>
                        </div>
                        <div id="fivechat-validation-error" class="fivechat-status fivechat-error" style="display: none;">
                            <div class="fivechat-status-icon">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="10" fill="#EF4444"/>
                                    <path d="M13 7L7 13M7 7L13 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <span class="fivechat-status-text" id="fivechat-error-message"></span>
                        </div>
                    </div>
                    
                    <div class="fivechat-form-actions">
                        <?php submit_button( __( 'Save Configuration', 'fivechat' ), 'primary fivechat-save-btn', 'submit', false ); ?>
                    </div>
                </form>
            </div>
            
            <!-- Help Section -->
            <div class="fivechat-help-card">
                <h3 class="fivechat-help-title">
                    <span class="dashicons dashicons-lightbulb"></span>
                    <?php esc_html_e( 'Quick Setup Guide', 'fivechat' ); ?>
                </h3>
                <div class="fivechat-help-steps">
                    <div class="fivechat-step">
                        <div class="fivechat-step-number">1</div>
                        <div class="fivechat-step-content">
                            <strong><?php esc_html_e( 'Get Your Token', 'fivechat' ); ?></strong>
                            <p>
                                <?php esc_html_e( 'Visit your', 'fivechat' ); ?> 
                                <a href="https://5chat.io/dashboard/configuration/widget" target="_blank" class="fivechat-link">
                                    <?php esc_html_e( 'widget configuration page', 'fivechat' ); ?>
                                    <span class="dashicons dashicons-external"></span>
                                </a> 
                                <?php esc_html_e( 'to copy your Website Token.', 'fivechat' ); ?>
                            </p>
                        </div>
                    </div>
                    <div class="fivechat-step">
                        <div class="fivechat-step-number">2</div>
                        <div class="fivechat-step-content">
                            <strong><?php esc_html_e( 'Paste & Validate', 'fivechat' ); ?></strong>
                            <p><?php esc_html_e( 'Paste your token in the field above. It will be validated automatically in real-time.', 'fivechat' ); ?></p>
                        </div>
                    </div>
                    <div class="fivechat-step">
                        <div class="fivechat-step-number">3</div>
                        <div class="fivechat-step-content">
                            <strong><?php esc_html_e( 'Save & Go Live', 'fivechat' ); ?></strong>
                            <p><?php esc_html_e( 'Click "Save Configuration" and your chat widget will immediately appear on your website.', 'fivechat' ); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="fivechat-help-footer">
                    <p>
                        <strong><?php esc_html_e( 'Need help?', 'fivechat' ); ?></strong> 
                        <?php esc_html_e( 'Contact', 'fivechat' ); ?> 
                        <a href="https://5chat.io" target="_blank" class="fivechat-link">
                            <?php esc_html_e( '5chat support', 'fivechat' ); ?>
                            <span class="dashicons dashicons-external"></span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Reset and Base Styles */
        .fivechat-settings-wrap {
            margin: 0 0 0 -20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
        }
        
        .fivechat-settings-wrap h1.wp-heading-inline {
            display: none;
        }

        /* Header Banner */
        .fivechat-header-banner {
            background: linear-gradient(135deg, #1a2231 0%, #0f1419 100%);
            color: white;
            padding: 32px;
            margin: 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .fivechat-banner-content {
            max-width: 1200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .fivechat-logo-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .fivechat-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .fivechat-logo-icon {
            border-radius: 6px;
            flex-shrink: 0;
        }

        .fivechat-logo-text {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .fivechat-tagline {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 500;
            margin-left: 44px;
        }

        .fivechat-dashboard-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .fivechat-dashboard-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateY(-1px);
        }

        .fivechat-dashboard-btn .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
        }

        /* Main Content */
        .fivechat-main-content {
            padding: 32px;
            max-width: 1200px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
        }

        .fivechat-content-card,
        .fivechat-help-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            border: 1px solid #E5E7EB;
        }

        .fivechat-content-card {
            padding: 32px;
        }

        .fivechat-page-title {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 8px 0;
        }

        .fivechat-page-description {
            color: #6B7280;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 32px 0;
        }

        /* Form Styles */
        .fivechat-form .form-table {
            margin: 0;
        }

        .fivechat-form .form-table th {
            padding: 0 0 8px 0;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .fivechat-form .form-table td {
            padding: 0 0 24px 0;
        }

        .fivechat-form input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.2s ease;
            background: #FAFAFA;
        }

        .fivechat-form input[type="text"]:focus {
            outline: none;
            border-color: #1a2231;
            background: white;
            box-shadow: 0 0 0 3px rgba(26, 34, 49, 0.1);
        }

        .fivechat-form .description {
            color: #6B7280;
            font-size: 14px;
            margin-top: 8px;
        }

        /* Validation Status */
        .fivechat-validation-container {
            margin: 24px 0;
        }

        .fivechat-status {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .fivechat-status.fivechat-loading {
            background: #F1F5F9;
            color: #1a2231;
            border: 1px solid #E2E8F0;
        }

        .fivechat-status.fivechat-success {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #D1FAE5;
        }

        .fivechat-status.fivechat-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .fivechat-status-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .fivechat-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #E2E8F0;
            border-top: 2px solid #1a2231;
            border-radius: 50%;
            animation: fivechat-spin 1s linear infinite;
        }

        @keyframes fivechat-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Form Actions */
        .fivechat-form-actions {
            padding-top: 24px;
            border-top: 1px solid #E5E7EB;
        }

        .fivechat-save-btn {
            background: #1a2231 !important;
            border-color: #1a2231 !important;
            color: white !important;
            padding: 12px 24px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s ease !important;
        }

        .fivechat-save-btn:hover {
            background: #0f1419 !important;
            border-color: #0f1419 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 8px rgba(26, 34, 49, 0.3) !important;
        }

        /* Help Card */
        .fivechat-help-card {
            padding: 24px;
            height: fit-content;
        }

        .fivechat-help-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 20px 0;
        }

        .fivechat-help-title .dashicons {
            color: #F59E0B;
            font-size: 20px;
            width: 20px;
            height: 20px;
        }

        .fivechat-help-steps {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 24px;
        }

        .fivechat-step {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .fivechat-step-number {
            width: 32px;
            height: 32px;
            background: #1a2231;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .fivechat-step-content strong {
            color: #111827;
            font-size: 14px;
            display: block;
            margin-bottom: 4px;
        }

        .fivechat-step-content p {
            color: #6B7280;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        .fivechat-help-footer {
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
        }

        .fivechat-help-footer p {
            color: #6B7280;
            font-size: 14px;
            margin: 0;
        }

        .fivechat-link {
            color: #1a2231;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .fivechat-link:hover {
            color: #0f1419;
            text-decoration: underline;
        }

        .fivechat-link .dashicons {
            font-size: 14px;
            width: 14px;
            height: 14px;
        }

        /* Settings Errors Override */
        .fivechat-settings-wrap .notice {
            margin: 0 0 24px 0;
            border-radius: 8px;
            border-left-width: 4px;
        }

        .fivechat-settings-wrap .notice-success {
            background: #ECFDF5;
            border-left-color: #10B981;
        }

        .fivechat-settings-wrap .notice-error {
            background: #FEF2F2;
            border-left-color: #EF4444;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .fivechat-main-content {
                grid-template-columns: 1fr;
                gap: 24px;
                padding: 24px;
            }
        }

        @media (max-width: 768px) {
            .fivechat-banner-content {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }

            .fivechat-logo-section {
                align-items: flex-start;
            }

            .fivechat-tagline {
                margin-left: 0;
            }

            .fivechat-content-card,
            .fivechat-help-card {
                padding: 20px;
            }

            .fivechat-header-banner {
                padding: 24px 20px;
            }

            .fivechat-main-content {
                padding: 20px;
            }
        }
    </style>
    
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var validationTimeout;
            var lastValidatedToken = '';
            var isValidToken = false;
            
            // Token input field
            var $tokenField = $('#fivechat_website_token_id');
            var $form = $('#fivechat-settings-form');
            var $submitButton = $('#submit');
            
            // Validation status elements
            var $statusContainer = $('#fivechat-validation-status');
            var $loadingStatus = $('#fivechat-validation-loading');
            var $successStatus = $('#fivechat-validation-success');
            var $errorStatus = $('#fivechat-validation-error');
            var $successMessage = $('#fivechat-success-message');
            var $errorMessage = $('#fivechat-error-message');
            
            // Real-time validation on input
            $tokenField.on('input', function() {
                var token = $(this).val().trim();
                
                // Clear previous timeout
                clearTimeout(validationTimeout);
                
                // Hide all status messages
                hideAllStatus();
                
                // If empty, don't validate
                if (token === '') {
                    isValidToken = false;
                    return;
                }
                
                // If same as last validated, don't re-validate
                if (token === lastValidatedToken) {
                    return;
                }
                
                // Debounce validation (wait 800ms after user stops typing)
                validationTimeout = setTimeout(function() {
                    validateToken(token);
                }, 800);
            });
            
            // Prevent form submission if token is invalid
            $form.on('submit', function(e) {
                var token = $tokenField.val().trim();
                
                if (token !== '' && !isValidToken) {
                    e.preventDefault();
                    
                    // Show error message
                    showError('<?php esc_js_e( 'Please enter a valid Website Token before saving.', 'fivechat' ); ?>');
                    
                    return false;
                }
            });
            
            function validateToken(token) {
                // Show loading status
                showLoading();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'fivechat_validate_token',
                        token: token,
                        nonce: '<?php echo wp_create_nonce( 'fivechat_validate_token' ); ?>'
                    },
                    success: function(response) {
                        lastValidatedToken = token;
                        
                        if (response.success) {
                            isValidToken = true;
                            showSuccess(response.data.message);
                        } else {
                            isValidToken = false;
                            showError(response.data.message);
                        }
                    },
                    error: function() {
                        isValidToken = false;
                        showError('<?php esc_js_e( 'Connection error. Please try again.', 'fivechat' ); ?>');
                    }
                });
            }
            
            function hideAllStatus() {
                $statusContainer.hide();
                $loadingStatus.hide();
                $successStatus.hide();
                $errorStatus.hide();
            }
            
            function showLoading() {
                $statusContainer.show();
                $loadingStatus.show();
                $successStatus.hide();
                $errorStatus.hide();
            }
            
            function showSuccess(message) {
                $successMessage.text(message);
                $statusContainer.show();
                $loadingStatus.hide();
                $successStatus.show();
                $errorStatus.hide();
            }
            
            function showError(message) {
                $errorMessage.text(message);
                $statusContainer.show();
                $loadingStatus.hide();
                $successStatus.hide();
                $errorStatus.show();
            }
            
            // Validate current token on page load if it exists
            var initialToken = $tokenField.val().trim();
            if (initialToken !== '') {
                validateToken(initialToken);
            }
        });
    </script>
    <?php
}

/**
 * Sanitization callback for the website token.
 */
function fivechat_sanitize_token( $input ) {
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
            __( 'Invalid Website Token format. Please use only letters, numbers, hyphens, and underscores.', 'fivechat' ),
            'error'
        );
        // Return the previous value if validation fails
        return get_option( 'fivechat_website_token' );
    }
    
    // Validate token with 5chat API
    $validation_result = fivechat_test_token( $sanitized );
    
    if ( ! $validation_result['valid'] ) {
        add_settings_error(
            'fivechat_website_token',
            'invalid_token_api',
            sprintf( 
                __( 'Token validation failed: %s', 'fivechat' ),
                $validation_result['error']
            ),
            'error'
        );
        // Return the previous value if API validation fails
        return get_option( 'fivechat_website_token' );
    }
    
    // Token is valid, show success message
    add_settings_error(
        'fivechat_website_token',
        'token_validated',
        __( 'Website Token validated successfully! Your 5chat widget is now active.', 'fivechat' ),
        'success'
    );
    
    return $sanitized;
}

/**
 * Callback function to render the HTML for the Website Token input field.
 */
function fivechat_token_field_html() {
    $option_value = get_option( 'fivechat_website_token' );
    ?>
    <input type="text"
           id="fivechat_website_token_id"
           name="fivechat_website_token"
           value="<?php echo esc_attr( $option_value ); ?>"
           class="regular-text"
           placeholder="<?php esc_attr_e( 'Paste your Website Token here', 'fivechat' ); ?>">
    <p class="description">
        <?php esc_html_e( 'Find your Website Token in your 5chat dashboard.', 'fivechat' ); ?>
    </p>
    <?php
}

/**
 * Add the 5chat widget script to the website frontend head.
 */
function fivechat_add_widget_script() {
    $website_token = get_option( 'fivechat_website_token' );

    if ( ! empty( $website_token ) ) {
        $sanitized_token = esc_attr( $website_token );
        ?>
        <script src="https://5chat.io/widget/<?php echo $sanitized_token; ?>" async></script>
        <?php
    }
}
add_action( 'wp_head', 'fivechat_add_widget_script' );

/**
 * Display an admin notice if the 5chat token is not set or invalid.
 */
function fivechat_admin_notice_missing_token() {
    $website_token = get_option( 'fivechat_website_token' );
    global $pagenow;

    // Check capability and limit to relevant pages
    if ( ! current_user_can( 'manage_options' ) || 
         ! in_array( $pagenow, [ 'index.php', 'plugins.php', 'options-general.php' ] ) ) {
        return;
    }
    
    // Don't show on the settings page itself to avoid redundancy
    if ( $pagenow === 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] === 'fivechat-settings' ) {
        return;
    }

    // Show notice if token is empty or invalid
    $show_notice = false;
    $notice_message = '';
    
    if ( empty( $website_token ) ) {
        $show_notice = true;
        $notice_message = __( '<strong>5chat Live Chat is active, but no Website Token is configured.</strong>', 'fivechat' );
    } else {
        // Check if the current token is valid (cached check to avoid too many API calls)
        $token_check_cache = get_transient( 'fivechat_token_valid_' . md5( $website_token ) );
        
        if ( $token_check_cache === false ) {
            // Cache expired or doesn't exist, validate token
            $validation_result = fivechat_test_token( $website_token );
            
            // Cache result for 1 hour
            set_transient( 'fivechat_token_valid_' . md5( $website_token ), $validation_result['valid'] ? 'valid' : 'invalid', HOUR_IN_SECONDS );
            
            if ( ! $validation_result['valid'] ) {
                $show_notice = true;
                $notice_message = __( '<strong>5chat Live Chat token appears to be invalid.</strong>', 'fivechat' );
            }
        } elseif ( $token_check_cache === 'invalid' ) {
            $show_notice = true;
            $notice_message = __( '<strong>5chat Live Chat token appears to be invalid.</strong>', 'fivechat' );
        }
    }
    
    if ( $show_notice ) {
        $settings_page_url = admin_url( 'options-general.php?page=fivechat-settings' );
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <?php
                echo wp_kses_post( $notice_message );
                printf(
                    ' ' . __( 'Please <a href="%s">update your settings</a> to enable the chat widget.', 'fivechat' ),
                    esc_url( $settings_page_url )
                );
                ?>
            </p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'fivechat_admin_notice_missing_token' );

/**
 * Clear token validation cache when token is updated
 */
function fivechat_clear_token_cache( $old_value, $new_value ) {
    if ( $old_value !== $new_value ) {
        // Clear cache for old token
        if ( ! empty( $old_value ) ) {
            delete_transient( 'fivechat_token_valid_' . md5( $old_value ) );
        }
        // Clear cache for new token
        if ( ! empty( $new_value ) ) {
            delete_transient( 'fivechat_token_valid_' . md5( $new_value ) );
        }
    }
}
add_action( 'update_option_fivechat_website_token', 'fivechat_clear_token_cache', 10, 2 );

/**
 * Add a link to the settings page directly from the plugins list page.
 */
function fivechat_add_settings_link( $links ) {
    $settings_link = '<a href="' . admin_url( 'options-general.php?page=fivechat-settings' ) . '">' . __( 'Settings', 'fivechat' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
$plugin_basename = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_{$plugin_basename}", 'fivechat_add_settings_link' );