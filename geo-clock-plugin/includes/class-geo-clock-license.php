<?php
class Geo_Clock_License {
    public static function get_envato_token() {
        // Implement OAuth 2.0 flow to get an access token
        // For simplicity, you might store this token in the options table
        return get_option('geo_clock_envato_token');
    }

    public static function verify_purchase($purchase_code) {
        $token = self::get_envato_token();
        $response = wp_remote_get(
            "https://api.envato.com/v3/market/author/sale?code={$purchase_code}",
            array(
                'headers' => array(
                    'Authorization' => "Bearer {$token}",
                    'User-Agent' => 'Geo Clock Plugin Verification'
                )
            )
        );

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        // Verify the purchase details
        if (isset($data->item->id) && $data->item->id == 'YOUR_ITEM_ID') {
            return true;
        }

        return false;
    }

    public static function check_license() {
        if (get_transient('geo_clock_license_check')) {
            return true;
        }

        $purchase_code = get_option('geo_clock_purchase_code');
        if (!self::verify_purchase($purchase_code)) {
            // Disable plugin functionality or show a warning
            return false;
        }

        set_transient('geo_clock_license_check', true, WEEK_IN_SECONDS);
        return true;
    }
}