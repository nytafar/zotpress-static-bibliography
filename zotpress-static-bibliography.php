<?php
/**
 * Plugin Name: Zotpress Static Bibliography
 * Plugin URI: https://jellum.net/zotpress-static-bibliography
 * Description: Enhances Zotpress to render bibliography citations statically through PHP.
 * Version: 1.0.1
 * Author: Lasse Jellum
 * Author URI: https://jellum.net
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: zotpress-static-bibliography
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define a debug function
if (!function_exists('zpstatic_debug_log')) {
    function zpstatic_debug_log($message) {
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            if (is_array($message) || is_object($message)) {
                error_log('ZPSTATIC DEBUG: ' . print_r($message, true));
            } else {
                error_log('ZPSTATIC DEBUG: ' . $message);
            }
        }
    }
}

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
        
        zpstatic_debug_log('Plugin initialized');
        
        // Load required files early
        $this->load_custom_files();
        
        // Check if Zotpress is active - use a lower priority to ensure Zotpress is fully loaded
        add_action('plugins_loaded', array($this, 'check_zotpress_dependency'), 20);
        
        // Hook into 'wp' action to ensure all WordPress data is loaded
        add_action('wp', array($this, 'setup_shortcodes'));
        
        // Add a hook to check shortcode globals
        add_action('wp_head', array($this, 'check_globals'), 1);
        
        // Remove Zotpress JavaScript for bibliography
        add_action('wp_enqueue_scripts', array($this, 'remove_zotpress_js'), 100);
        
        // Add Zotpress button to the Block Editor toolbar
        add_action('enqueue_block_editor_assets', array($this, 'register_editor_button'));
    }

    /**
     * Check if Zotpress is active.
     */
    public function check_zotpress_dependency() {
        zpstatic_debug_log('Checking Zotpress dependency');
        
        // Check for critical Zotpress functions
        $zotpress_functions = array(
            'Zotpress_shortcode_request' => function_exists('Zotpress_shortcode_request'),
            'Zotpress_prep_ajax_request_vars' => function_exists('Zotpress_prep_ajax_request_vars')
        );
        
        zpstatic_debug_log('Zotpress functions available: ' . json_encode($zotpress_functions));
        
        if (!function_exists('Zotpress_shortcode_request')) {
            add_action('admin_notices', array($this, 'zotpress_missing_notice'));
            return false;
        }
        return true;
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
        zpstatic_debug_log('Loading custom files');
        
        // Include our enhanced files
        require_once($this->plugin_dir . 'lib/utils.php');
        require_once($this->plugin_dir . 'lib/shortcode/shortcode.intextbib.php');
        require_once($this->plugin_dir . 'lib/shortcode/shortcode.intext.php');
    }

    /**
     * Setup shortcodes after WordPress is fully loaded.
     * This ensures that all required Zotpress functions are available.
     */
    public function setup_shortcodes() {
        zpstatic_debug_log('Setting up shortcodes');
        
        // Only proceed if Zotpress is active
        if (!function_exists('Zotpress_shortcode_request')) {
            zpstatic_debug_log('Zotpress_shortcode_request function not available');
            return;
        }
        
        // Remove original shortcodes
        $this->remove_original_shortcodes();
        
        // Register our custom shortcodes
        $this->register_shortcodes();
        
        zpstatic_debug_log('Shortcodes setup complete');
    }

    /**
     * Check if the global variables needed by the shortcode are available
     */
    public function check_globals() {
        global $post;
        
        zpstatic_debug_log('Checking globals');
        
        if (!isset($post) || empty($post)) {
            zpstatic_debug_log('$post global not available');
        } else {
            zpstatic_debug_log('$post global available with ID: ' . $post->ID);
        }
        
        if (!isset($GLOBALS['zp_shortcode_instances'])) {
            zpstatic_debug_log('$GLOBALS[\'zp_shortcode_instances\'] not available');
        } else {
            if (isset($post) && isset($GLOBALS['zp_shortcode_instances'][$post->ID])) {
                zpstatic_debug_log('$GLOBALS[\'zp_shortcode_instances\'][$post->ID] available with ' . count($GLOBALS['zp_shortcode_instances'][$post->ID]) . ' instances');
            } else {
                zpstatic_debug_log('$GLOBALS[\'zp_shortcode_instances\'][$post->ID] not available');
            }
        }
    }

    /**
     * Remove original Zotpress shortcodes.
     */
    public function remove_original_shortcodes() {
        zpstatic_debug_log('Removing original shortcodes');
        
        remove_shortcode('zotpressInTextBib');
        remove_shortcode('zotpressInText');
    }

    /**
     * Register our custom shortcode handlers.
     */
    public function register_shortcodes() {
        zpstatic_debug_log('Registering custom shortcodes');
        
        // Add our enhanced shortcodes
        add_shortcode('zotpressInTextBib', 'ZotpressStatic_zotpressInTextBib');
        add_shortcode('zotpressInText', 'ZotpressStatic_zotpressInText');
    }
    
    /**
     * Remove Zotpress JavaScript for bibliography to prevent the loading spinner.
     */
    public function remove_zotpress_js() {
        zpstatic_debug_log('Checking if shortcode is displayed');
        
        // Only dequeue if our shortcode is used
        if (isset($GLOBALS['zp_is_shortcode_displayed']) && $GLOBALS['zp_is_shortcode_displayed'] === true) {
            zpstatic_debug_log('Dequeuing Zotpress scripts');
            wp_dequeue_script('zotpress');
        }
    }
    
    /**
     * Register and enqueue the Zotpress editor button script.
     * This adds a dedicated Zotpress button to the Block Editor toolbar.
     */
    public function register_editor_button() {
        // Only proceed if Zotpress is active
        if (!function_exists('Zotpress_shortcode_request')) {
            return;
        }
        
        // Enqueue the script for the Block Editor
        wp_enqueue_script(
            'zotpress-editor-button',
            plugin_dir_url(__FILE__) . 'assets/js/zotpress-editor-button.js',
            array('wp-blocks', 'wp-rich-text', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            '1.0.0',
            true
        );
        
        // Add translations
        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('zotpress-editor-button', 'zotpress-static-bibliography');
        }
    }
}

// Initialize the plugin
new Zotpress_Static_Bibliography();