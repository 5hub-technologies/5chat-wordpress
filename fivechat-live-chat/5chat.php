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
 * Render the HTML for the settings page.
 */
function fivechat_options_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p><?php esc_html_e( 'Enter your 5chat Website Token below to enable the live chat widget.', 'fivechat' ); ?></p>
        
        <?php settings_errors( 'fivechat_website_token' ); ?>
        
        <form action="options.php" method="post">
            <?php
            settings_fields( 'fivechat_settings_group' );
            do_settings_sections( 'fivechat-settings' );
            submit_button( __( 'Save Settings', 'fivechat' ) );
            ?>
        </form>
        
        <div class="fivechat-help">
            <h3><?php esc_html_e( 'Need help?', 'fivechat' ); ?></h3>
            <p><?php esc_html_e( 'Visit your', 'fivechat' ); ?> <a href="https://5chat.io/dashboard" target="_blank"><?php esc_html_e( '5chat dashboard', 'fivechat' ); ?></a> <?php esc_html_e( 'to find your Website Token.', 'fivechat' ); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Sanitization callback for the website token.
 */
function fivechat_sanitize_token( $input ) {
    // Trim whitespace and sanitize as a simple text field
    $sanitized = sanitize_text_field( trim( $input ) );
    
    // Basic validation - token should be alphanumeric with possible hyphens/underscores
    if ( ! empty( $sanitized ) && ! preg_match( '/^[a-zA-Z0-9_-]+$/', $sanitized ) ) {
        add_settings_error(
            'fivechat_website_token',
            'invalid_token',
            __( 'Invalid Website Token format. Please check your token and try again.', 'fivechat' ),
            'error'
        );
        // Return the previous value if validation fails
        return get_option( 'fivechat_website_token' );
    }
    
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
 * Display an admin notice if the 5chat token is not set.
 */
function fivechat_admin_notice_missing_token() {
    $website_token = get_option( 'fivechat_website_token' );
    global $pagenow;

    // Check capability, if token is empty, and limit to relevant pages
    if ( current_user_can( 'manage_options' ) && empty( $website_token ) &&
        ( $pagenow == 'index.php' || $pagenow == 'plugins.php' || ( $pagenow == 'options-general.php' && isset($_GET['page']) && $_GET['page'] == 'fivechat-settings' ) )
    ) {

        $settings_page_url = admin_url( 'options-general.php?page=fivechat-settings' );
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <?php
                printf(
                /* translators: %s: Link to the 5chat settings page. */
                    wp_kses_post( __( '<strong>5chat Live Chat is active, but the Website Token is missing.</strong> Please <a href="%s">go to the settings page</a> to add your token and enable the chat widget.', 'fivechat' ) ),
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
 * Add a link to the settings page directly from the plugins list page.
 */
function fivechat_add_settings_link( $links ) {
    $settings_link = '<a href="' . admin_url( 'options-general.php?page=fivechat-settings' ) . '">' . __( 'Settings', 'fivechat' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
$plugin_basename = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_{$plugin_basename}", 'fivechat_add_settings_link' );