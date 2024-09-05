<?php
class Geo_Clock_Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, GEO_CLOCK_PLUGIN_URL . 'admin/css/geo-clock-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, GEO_CLOCK_PLUGIN_URL . 'admin/js/geo-clock-admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'geo_clock_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('geo-clock-admin-nonce'),
        ));
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'Geo Clock Settings', 
            'Geo Clock', 
            'manage_options', 
            $this->plugin_name, 
            array($this, 'display_plugin_setup_page'),
            'dashicons-clock',
            6
        );

        add_submenu_page(
            $this->plugin_name,
            'Geo Clock Locations',
            'Locations',
            'manage_options',
            $this->plugin_name . '-locations',
            array($this, 'display_location_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Employee Logs',
            'Employee Logs',
            'manage_options',
            $this->plugin_name . '-logs',
            array($this, 'display_logs_page')
        );
    }

    public function add_action_links($links) {
        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);
    }

    public function display_plugin_setup_page() {
        include_once GEO_CLOCK_PLUGIN_DIR . 'admin/partials/geo-clock-admin-display.php';
    }

    public function display_location_page() {
        $this->save_locations();
        include_once GEO_CLOCK_PLUGIN_DIR . 'admin/partials/geo-clock-admin-locations.php';
    }

    public function display_logs_page() {
        include_once GEO_CLOCK_PLUGIN_DIR . 'admin/partials/geo-clock-admin-logs.php';
    }

    public function save_locations() {
        if (isset($_POST['geo_clock_locations']) && check_admin_referer('geo_clock_locations_nonce', 'geo_clock_locations_nonce')) {
            $locations = array_map(function($location) {
                return array(
                    'name' => sanitize_text_field($location['name']),
                    'lat' => floatval($location['lat']),
                    'lng' => floatval($location['lng']),
                    'radius' => intval($location['radius'])
                );
            }, $_POST['geo_clock_locations']);
            update_option('geo_clock_locations', $locations);
            add_settings_error('geo_clock_messages', 'geo_clock_locations_updated', __('Locations updated successfully.', $this->plugin_name), 'updated');
        }
    }

    public function get_locations() {
        return get_option('geo_clock_locations', array());
    }

    public function get_employee_logs($per_page = 20, $page_number = 1) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'geo_clock_records';

        $sql = "SELECT r.*, u.display_name 
                FROM $table_name r
                JOIN {$wpdb->users} u ON r.user_id = u.ID
                ORDER BY r.clock_in DESC";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_employee_logs_count() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'geo_clock_records';
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }

    public function update_employee_log() {
        error_log('Update employee log function called');
        check_ajax_referer('geo-clock-admin-nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $log_id = intval($_POST['log_id']);
        $clock_in = sanitize_text_field($_POST['clock_in']);
        $clock_out = sanitize_text_field($_POST['clock_out']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'geo_clock_records';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'clock_in' => $clock_in,
                'clock_out' => $clock_out
            ),
            array('id' => $log_id),
            array('%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            error_log('Log updated successfully');
            wp_send_json_success('Log updated successfully');
        } else {
            error_log('Failed to update log: ' . $wpdb->last_error);
            wp_send_json_error('Failed to update log: ' . $wpdb->last_error);
        }
    }

    public function delete_employee_log() {
        error_log('Delete employee log function called');
        check_ajax_referer('geo-clock-admin-nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $log_id = intval($_POST['log_id']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'geo_clock_records';
        
        $result = $wpdb->delete($table_name, array('id' => $log_id), array('%d'));
        
        if ($result !== false) {
            error_log('Log deleted successfully');
            wp_send_json_success('Log deleted successfully');
        } else {
            error_log('Failed to delete log: ' . $wpdb->last_error);
            wp_send_json_error('Failed to delete log: ' . $wpdb->last_error);
        }
    }
}