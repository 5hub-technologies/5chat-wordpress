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
 * Text Domain:       fivechat-live-chat
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to:      6.8
 * Requires PHP:      7.4
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'FIVECHAT_VERSION', '1.0.0' );
define( 'FIVECHAT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FIVECHAT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'FIVECHAT_PLUGIN_FILE', __FILE__ );

/**
 * Main plugin class
 */
class FiveChat_Plugin {

    /**
     * Instance of this class
     *
     * @var FiveChat_Plugin
     */
    private static $instance = null;

    /**
     * Plugin components
     *
     * @var array
     */
    private $components = array();

    /**
     * Get the singleton instance
     *
     * @return FiveChat_Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
        $this->init_components();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once FIVECHAT_PLUGIN_PATH . 'includes/class-fivechat-token-validator.php';
        require_once FIVECHAT_PLUGIN_PATH . 'includes/class-fivechat-settings.php';
        require_once FIVECHAT_PLUGIN_PATH . 'includes/class-fivechat-admin.php';
        require_once FIVECHAT_PLUGIN_PATH . 'includes/class-fivechat-frontend.php';
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        register_activation_hook( FIVECHAT_PLUGIN_FILE, array( $this, 'activate' ) );
        register_deactivation_hook( FIVECHAT_PLUGIN_FILE, array( $this, 'deactivate' ) );
    }

    /**
     * Initialize plugin components
     */
    private function init_components() {
        $this->components['token_validator'] = new FiveChat_Token_Validator();
        $this->components['settings'] = new FiveChat_Settings( $this->components['token_validator'] );
        $this->components['admin'] = new FiveChat_Admin( $this->components['token_validator'] );
        $this->components['frontend'] = new FiveChat_Frontend();
    }

    /**
     * Plugin activation hook
     */
    public function activate() {
        // Add default options if they don't exist
        add_option( 'fivechat_website_token', '' );
    }

    /**
     * Plugin deactivation hook
     */
    public function deactivate() {
        // Clean up any temporary data if needed
        // Note: We don't delete the settings here as user might reactivate
    }

    /**
     * Get a component instance
     *
     * @param string $component Component name.
     * @return object|null Component instance or null if not found.
     */
    public function get_component( $component ) {
        return isset( $this->components[ $component ] ) ? $this->components[ $component ] : null;
    }
}

/**
 * Initialize the plugin
 */
function fivechat_init() {
    return FiveChat_Plugin::get_instance();
}

// Start the plugin
fivechat_init();