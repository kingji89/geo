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
                <?php 
                if (!empty($locations)) :
                    foreach ($locations as $index => $location) : 
                ?>
                    <tr>
                        <td>
                            <label for="location-name-<?php echo $index; ?>">Location Name:</label>
                            <input type="text" id="location-name-<?php echo $index; ?>" name="geo_clock_locations[<?php echo $index; ?>][name]" value="<?php echo esc_attr($location['name']); ?>" placeholder="Location Name" required>
                            
                            <label for="location-lat-<?php echo $index; ?>">Latitude:</label>
                            <input type="number" id="location-lat-<?php echo $index; ?>" step="any" name="geo_clock_locations[<?php echo $index; ?>][lat]" value="<?php echo esc_attr($location['lat']); ?>" placeholder="Latitude" required>
                            
                            <label for="location-lng-<?php echo $index; ?>">Longitude:</label>
                            <input type="number" id="location-lng-<?php echo $index; ?>" step="any" name="geo_clock_locations[<?php echo $index; ?>][lng]" value="<?php echo esc_attr($location['lng']); ?>" placeholder="Longitude" required>
                            
                            <label for="location-radius-<?php echo $index; ?>">Radius (meters):</label>
                            <input type="number" id="location-radius-<?php echo $index; ?>" name="geo_clock_locations[<?php echo $index; ?>][radius]" value="<?php echo esc_attr($location['radius']); ?>" placeholder="Radius (meters)" required>
                            
                            <button type="button" class="button remove-location">Remove</button>
                        </td>
                    </tr>
                <?php 
                    endforeach;
                else :
                ?>
                    <tr>
                        <td>No locations added yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="button" id="add-location" class="button">Add Location</button>
        <?php submit_button('Save Locations'); ?>
    </form>
</div>

<script type="text/template" id="location-row-template">
    <tr>
        <td>
            <label for="location-name-{index}">Location Name:</label>
            <input type="text" id="location-name-{index}" name="geo_clock_locations[{index}][name]" placeholder="Location Name" required>
            
            <label for="location-lat-{index}">Latitude:</label>
            <input type="number" id="location-lat-{index}" step="any" name="geo_clock_locations[{index}][lat]" placeholder="Latitude" required>
            
            <label for="location-lng-{index}">Longitude:</label>
            <input type="number" id="location-lng-{index}" step="any" name="geo_clock_locations[{index}][lng]" placeholder="Longitude" required>
            
            <label for="location-radius-{index}">Radius (meters):</label>
            <input type="number" id="location-radius-{index}" name="geo_clock_locations[{index}][radius]" placeholder="Radius (meters)" required>
            
            <button type="button" class="button remove-location">Remove</button>
        </td>
    </tr>
</script>