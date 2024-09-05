<?php
/**
 * Fired during plugin activation.
 */
class Geo_Clock_Activator {

    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'geo_clock_records';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            clock_in datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            clock_out datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            location_lat decimal(10,8) NOT NULL,
            location_lng decimal(11,8) NOT NULL,
            location_name varchar(255) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('geo_clock_db_version', '1.0');
    }
}