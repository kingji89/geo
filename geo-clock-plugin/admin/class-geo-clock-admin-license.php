<?php
class Geo_Clock_Admin_License {
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_license_menu'));
        add_action('admin_init', array(__CLASS__, 'register_license_settings'));
    }

    public static function add_license_menu() {
        add_options_page('Geo Clock License', 'Geo Clock License', 'manage_options', 'geo-clock-license', array(__CLASS__, 'license_page'));
    }

    public static function register_license_settings() {
        register_setting('geo_clock_license', 'geo_clock_purchase_code');
    }

    public static function license_page() {
        ?>
        <div class="wrap">
            <h1>Geo Clock License</h1>
            <form method="post" action="options.php">
                <?php settings_fields('geo_clock_license'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Purchase Code</th>
                        <td><input type="text" name="geo_clock_purchase_code" value="<?php echo esc_attr(get_option('geo_clock_purchase_code')); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}