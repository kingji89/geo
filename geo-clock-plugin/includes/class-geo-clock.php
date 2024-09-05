<?php
class Geo_Clock {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        if (defined('GEO_CLOCK_VERSION')) {
            $this->version = GEO_CLOCK_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'geo-based-employee-clock';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once GEO_CLOCK_PLUGIN_DIR . 'includes/class-geo-clock-loader.php';
        require_once GEO_CLOCK_PLUGIN_DIR . 'includes/class-geo-clock-i18n.php';
        require_once GEO_CLOCK_PLUGIN_DIR . 'admin/class-geo-clock-admin.php';
        require_once GEO_CLOCK_PLUGIN_DIR . 'public/class-geo-clock-public.php';

        $this->loader = new Geo_Clock_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new Geo_Clock_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks() {
        $plugin_admin = new Geo_Clock_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_filter('plugin_action_links_' . plugin_basename(GEO_CLOCK_PLUGIN_DIR . $this->plugin_name . '.php'), $plugin_admin, 'add_action_links');
    }

    private function define_public_hooks() {
        $plugin_public = new Geo_Clock_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_shortcode('employee_clock', $plugin_public, 'employee_clock_shortcode');
        $this->loader->add_action('wp_ajax_clock_in_out', $plugin_public, 'handle_clock_in_out');
        $this->loader->add_action('wp_ajax_nopriv_clock_in_out', $plugin_public, 'handle_clock_in_out');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }
}