<?php
class Geo_Clock_Public {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
  
  public function employee_clock_shortcode() {
        ob_start();
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            $clock_status = $this->get_user_clock_status($user_id);
            $location = $this->get_user_location($user_id);
            $daily_total = $this->get_daily_total($user_id);
            $day_log = $this->get_day_log($user_id);
            ?>
            <div class="geo-clock-wrapper">
                <!-- Your clock interface HTML here -->
            </div>
            <?php
        } else {
            ?>
            <p><?php esc_html_e('Please log in to use the time clock system.', 'geo-based-employee-clock'); ?></p>
            <?php
        }
        return ob_get_clean();
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, GEO_CLOCK_PLUGIN_URL . 'public/css/geo-clock-public.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, GEO_CLOCK_PLUGIN_URL . 'public/js/geo-clock.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'geo_clock_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('geo-clock-nonce'),
        ));
    }

    // ... (other methods remain the same)

    public function handle_clock_in_out() {
        check_ajax_referer('geo-clock-nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }

        $user_id = get_current_user_id();
        $lat = floatval($_POST['lat']);
        $lng = floatval($_POST['lng']);

        error_log("Geo Clock: User $user_id attempting to clock in/out at lat: $lat, lng: $lng");

        $locations = get_option('geo_clock_locations', array());
        $within_radius = false;
        $closest_location = null;
        $closest_distance = PHP_FLOAT_MAX;

        error_log("Geo Clock: Checking against " . count($locations) . " locations");

        foreach ($locations as $location) {
            $distance = $this->calculate_distance($lat, $lng, $location['lat'], $location['lng']);
            error_log("Geo Clock: Distance to {$location['name']}: $distance meters (allowed radius: {$location['radius']} meters)");
            if ($distance <= $closest_distance) {
                $closest_distance = $distance;
                $closest_location = $location;
            }
            if ($distance <= $location['radius']) {
                $within_radius = true;
                break;
            }
        }

        if (!$within_radius) {
            $message = sprintf(
                'You are not within the allowed area. Closest location: %s (%.2f meters away, allowed radius: %d meters)',
                $closest_location['name'],
                $closest_distance,
                $closest_location['radius']
            );
            error_log("Geo Clock: $message");
            wp_send_json_error($message);
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'geo_clock_records';

        $last_record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY id DESC LIMIT 1",
            $user_id
        ));

        try {
            if ($last_record && $last_record->clock_out == '0000-00-00 00:00:00') {
                // Attempting to clock out
                if ($last_record->location_name !== $closest_location['name']) {
                    $message = sprintf('You must clock out from the same location you clocked in: %s', $last_record->location_name);
                    error_log("Geo Clock: $message");
                    wp_send_json_error($message);
                    return;
                }

                $result = $wpdb->update(
                    $table_name,
                    array('clock_out' => current_time('mysql')),
                    array('id' => $last_record->id)
                );
                if ($result === false) {
                    throw new Exception($wpdb->last_error);
                }
                $message = sprintf('Clocked out successfully from %s', $closest_location['name']);
                error_log("Geo Clock: $message");
                wp_send_json_success($message);
            } else {
                // Clocking in
                $result = $wpdb->insert(
                    $table_name,
                    array(
                        'user_id' => $user_id,
                        'clock_in' => current_time('mysql'),
                        'location_lat' => $lat,
                        'location_lng' => $lng,
                        'location_name' => $closest_location['name']
                    )
                );
                if ($result === false) {
                    throw new Exception($wpdb->last_error);
                }
                $message = sprintf('Clocked in successfully at %s', $closest_location['name']);
                error_log("Geo Clock: $message");
                wp_send_json_success($message);
            }
        } catch (Exception $e) {
            error_log("Geo Clock Error: " . $e->getMessage());
            wp_send_json_error('An error occurred while processing your request: ' . $e->getMessage());
        }
    }

    private function calculate_distance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // in meters
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        $delta_lat = $lat2 - $lat1;
        $delta_lon = $lon2 - $lon1;
        $a = sin($delta_lat/2) * sin($delta_lat/2) + cos($lat1) * cos($lat2) * sin($delta_lon/2) * sin($delta_lon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earth_radius * $c;
        return $distance;
    }
}