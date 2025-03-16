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
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
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
        
        // Check if Zotpress is active
        add_action('plugins_loaded', array($this, 'check_zotpress_dependency'));
        
        // Hook into Zotpress shortcodes
        add_action('init', array($this, 'register_shortcodes'), 20);
        
        // Remove Zotpress JavaScript for bibliography
        add_action('wp_enqueue_scripts', array($this, 'remove_zotpress_js'), 100);
        
        // Add our own files
        add_action('plugins_loaded', array($this, 'load_custom_files'), 30);
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
}

// Initialize the plugin
new Zotpress_Static_Bibliography();