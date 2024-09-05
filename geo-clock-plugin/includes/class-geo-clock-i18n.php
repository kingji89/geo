<?php
class Geo_Clock_i18n {
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'geo-based-employee-clock',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
