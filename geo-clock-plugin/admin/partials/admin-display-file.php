<div class="wrap geo-clock-admin-wrapper">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('geo_clock_options');
        do_settings_sections('geo_clock_options');
        ?>
        <div class="geo-clock-form-group">
            <label for="geo_clock_allowed_radius">Allowed Radius (meters)</label>
            <input type="number" id="geo_clock_allowed_radius" name="geo_clock_allowed_radius" value="<?php echo esc_attr(get_option('geo_clock_allowed_radius', 100)); ?>" min="1" required>
        </div>
        <?php submit_button(); ?>
    </form>
</div>
