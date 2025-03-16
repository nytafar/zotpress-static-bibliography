<?php
/**
 * Plugin Name: Zotpress Static Bibliography
 * Plugin URI: https://example.com/zotpress-static-bibliography
 * Description: Enhances Zotpress to render bibliography citations statically through PHP.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: zotpress-static-bibliography
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Depends: Zotpress
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Check if Zotpress is active and required files exist
 */
function zotpress_static_bibliography_check_dependencies() {
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    $dependency_errors = array();

    // Check if Zotpress is installed and active
    if (!is_plugin_active('zotpress/zotpress.php')) {
        $dependency_errors[] = __('Zotpress plugin must be installed and activated.', 'zotpress-static-bibliography');
    }

    // Check if required Zotpress files exist
    $required_files = array(
        '/zotpress/lib/request/request.class.php' => 'Zotpress request class file',
        '/zotpress/lib/request/request.functions.php' => 'Zotpress request functions file'
    );

    foreach ($required_files as $file => $name) {
        if (!file_exists(WP_PLUGIN_DIR . $file)) {
            $dependency_errors[] = sprintf(
                __('Required file "%s" is missing from Zotpress plugin.', 'zotpress-static-bibliography'),
                $name
            );
        }
    }

    return $dependency_errors;
}

/**
 * Display admin notices for dependency errors
 */
function zotpress_static_bibliography_admin_notices() {
    $errors = zotpress_static_bibliography_check_dependencies();
    
    if (!empty($errors)) {
        echo '<div class="error"><p>';
        echo '<strong>' . __('Zotpress Static Bibliography Error:', 'zotpress-static-bibliography') . '</strong><br>';
        echo implode('<br>', $errors);
        echo '</p></div>';
    }
}
add_action('admin_notices', 'zotpress_static_bibliography_admin_notices');

/**
 * The core plugin class.
 */
class Zotpress_Static_Bibliography {

    /**
     * Plugin directory path.
     *
     * @var string
     */
    private $plugin_dir;

    /**
     * Initialize the plugin.
     */
    public function __construct() {
        $this->plugin_dir = plugin_dir_path(__FILE__);
        
        // Only initialize if dependencies are met
        if (empty(zotpress_static_bibliography_check_dependencies())) {
            $this->init();
        }
    }

    /**
     * Initialize plugin functionality
     */
    private function init() {
        // Load plugin files
        require_once($this->plugin_dir . 'lib/utils.php');
        require_once($this->plugin_dir . 'lib/shortcode/shortcode.intext.php');
        require_once($this->plugin_dir . 'lib/shortcode/shortcode.intextbib.php');

        // Hook into WordPress at priority 20 to run after Zotpress
        add_action('init', array($this, 'remove_original_shortcodes'), 20);
        add_action('init', array($this, 'register_shortcodes'), 20);
        add_action('wp_enqueue_scripts', array($this, 'deregister_scripts'), 20);
    }

    /**
     * Check if Zotpress is active.
     */
    public function check_zotpress_dependency() {
        if (!function_exists('Zotpress_shortcode_request')) {
            add_action('admin_notices', array($this, 'zotpress_missing_notice'));
        }
    }

    /**
     * Display admin notice if Zotpress is not active.
     */
    public function zotpress_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Zotpress Static Bibliography requires Zotpress to be installed and activated.', 'zotpress-static-bibliography'); ?></p>
        </div>
        <?php
    }

    /**
     * Load our custom files before Zotpress loads its files.
     */
    public function load_custom_files() {
        // Include our enhanced files
        require_once($this->plugin_dir . 'lib/shortcode/shortcode.intextbib.php');
        require_once($this->plugin_dir . 'lib/shortcode/shortcode.intext.php');
    }

    /**
     * Register our custom shortcode handlers.
     */
    public function register_shortcodes() {
        // Remove the original shortcodes
        remove_shortcode('zotpressInTextBib');
        remove_shortcode('zotpressInText');
        
        // Add our enhanced shortcodes
        add_shortcode('zotpressInTextBib', 'ZotpressStatic_zotpressInTextBib');
        add_shortcode('zotpressInText', 'ZotpressStatic_zotpressInText');
    }
    
    /**
     * Remove Zotpress JavaScript for bibliography to prevent the loading spinner.
     */
    public function remove_zotpress_js() {
        // Dequeue and deregister the Zotpress bibliography scripts
        wp_dequeue_script('zotpress-bibliography-js');
        wp_deregister_script('zotpress-bibliography-js');
        
        // Dequeue and deregister the Zotpress in-text bibliography scripts
        wp_dequeue_script('zotpress-intextbib-js');
        wp_deregister_script('zotpress-intextbib-js');
        
        // Dequeue and deregister the Zotpress in-text citation scripts
        wp_dequeue_script('zotpress-intext-js');
        wp_deregister_script('zotpress-intext-js');
        
        // Remove the action that conditionally loads the scripts
        remove_action('wp_footer', 'Zotpress_theme_conditional_scripts_footer');
    }

    /**
     * Remove original Zotpress shortcodes.
     */
    public function remove_original_shortcodes() {
        remove_shortcode('zotpressInTextBib');
        remove_shortcode('zotpressInText');
    }

    /**
     * Deregister Zotpress scripts.
     */
    public function deregister_scripts() {
        wp_dequeue_script('zotpress-bibliography-js');
        wp_deregister_script('zotpress-bibliography-js');
        
        wp_dequeue_script('zotpress-intextbib-js');
        wp_deregister_script('zotpress-intextbib-js');
        
        wp_dequeue_script('zotpress-intext-js');
        wp_deregister_script('zotpress-intext-js');
    }
}

// Initialize the plugin
new Zotpress_Static_Bibliography();