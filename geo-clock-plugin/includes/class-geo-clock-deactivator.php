<?php
/**
 * Fired during plugin deactivation.
 */
class Geo_Clock_Deactivator {

    public static function deactivate() {
        // Perform any necessary cleanup tasks here
        // For example, you might want to remove any options or user metadata added by your plugin
        
        // delete_option('geo_clock_settings');
        // global $wpdb;
        // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}geo_clock_records");
    }
}