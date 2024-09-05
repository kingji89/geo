<?php
/**
 * Plugin Name: Geo-Based Employee Clock In/Out
 * Plugin URI: http://example.com/geo-based-employee-clock
 * Description: A WordPress plugin for employee clock in/out with geolocation verification and admin dashboard.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: http://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: geo-based-employee-clock
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('GEO_CLOCK_VERSION', '1.0.0');
define('GEO_CLOCK_PLUGIN_NAME', 'geo-based-employee-clock');
define('GEO_CLOCK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GEO_CLOCK_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_geo_clock() {
    require_once GEO_CLOCK_PLUGIN_DIR . 'includes/class-geo-clock-activator.php';
    Geo_Clock_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_geo_clock() {
    require_once GEO_CLOCK_PLUGIN_DIR . 'includes/class-geo-clock-deactivator.php';
    Geo_Clock_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_geo_clock');
register_deactivation_hook(__FILE__, 'deactivate_geo_clock');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once GEO_CLOCK_PLUGIN_DIR . 'includes/class-geo-clock.php';

/**
 * Begins execution of the plugin.
 */
function run_geo_clock() {
    $plugin = new Geo_Clock();
    $plugin->run();
}
run_geo_clock();

/**
 * Register the shortcode
 */
function geo_clock_register_shortcode() {
    $plugin_public = new Geo_Clock_Public(GEO_CLOCK_PLUGIN_NAME, GEO_CLOCK_VERSION);
    add_shortcode('employee_clock', array($plugin_public, 'employee_clock_shortcode'));
}
add_action('init', 'geo_clock_register_shortcode');

/**
 * Add AJAX hooks for admin functions
 */
function geo_clock_ajax_hooks() {
    require_once GEO_CLOCK_PLUGIN_DIR . 'admin/class-geo-clock-admin.php';
    $plugin_admin = new Geo_Clock_Admin(GEO_CLOCK_PLUGIN_NAME, GEO_CLOCK_VERSION);
    add_action('wp_ajax_update_employee_log', array($plugin_admin, 'update_employee_log'));
    add_action('wp_ajax_delete_employee_log', array($plugin_admin, 'delete_employee_log'));
}
add_action('admin_init', 'geo_clock_ajax_hooks');