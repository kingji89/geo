<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form method="post" action="">
        <?php
        settings_fields('geo_clock_locations');
        wp_nonce_field('geo_clock_locations_nonce', 'geo_clock_locations_nonce');
        $locations = $this->get_locations();
        ?>
        <table class="form-table" role="presentation">
            <tbody id="geo-clock-locations">
                <?php foreach ($locations as $index => $location) : ?>
                    <tr>
                        <td>
                            <input type="text" name="geo_clock_locations[<?php echo $index; ?>][name]" value="<?php echo esc_attr($location['name']); ?>" placeholder="Location Name" required>
                            <input type="number" step="any" name="geo_clock_locations[<?php echo $index; ?>][lat]" value="<?php echo esc_attr($location['lat']); ?>" placeholder="Latitude" required>
                            <input type="number" step="any" name="geo_clock_locations[<?php echo $index; ?>][lng]" value="<?php echo esc_attr($location['lng']); ?>" placeholder="Longitude" required>
                            <input type="number" name="geo_clock_locations[<?php echo $index; ?>][radius]" value="<?php echo esc_attr($location['radius']); ?>" placeholder="Radius (meters)" required>
                            <button type="button" class="button remove-location">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="button" id="add-location" class="button">Add Location</button>
        <?php submit_button('Save Locations'); ?>
    </form>
</div>

<script type="text/template" id="location-row-template">
    <tr>
        <td>
            <input type="text" name="geo_clock_locations[{index}][name]" placeholder="Location Name" required>
            <input type="number" step="any" name="geo_clock_locations[{index}][lat]" placeholder="Latitude" required>
            <input type="number" step="any" name="geo_clock_locations[{index}][lng]" placeholder="Longitude" required>
            <input type="number" name="geo_clock_locations[{index}][radius]" placeholder="Radius (meters)" required>
            <button type="button" class="button remove-location">Remove</button>
        </td>
    </tr>
</script>