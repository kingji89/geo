<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('geo_clock_allowed_radius');

// Delete custom database table
global $wpdb;
$table_name = $wpdb->prefix . 'geo_clock_records';
$wpdb->query("DROP TABLE IF EXISTS $table_name");
