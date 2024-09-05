<div class="wrap geo-clock-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div class="geo-clock-actions">
        <a href="<?php echo admin_url('admin-post.php?action=export_logs_csv'); ?>" class="button button-primary">Export to CSV</a>
    </div>
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'geo_clock_records';
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY clock_in DESC");
    
    if ($logs) :
    ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Total Hours</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log) : 
                    $user = get_userdata($log->user_id);
                    $username = $user ? $user->user_login : 'Unknown';
                    $clock_in = new DateTime($log->clock_in);
                    $clock_out = $log->clock_out !== '0000-00-00 00:00:00' ? new DateTime($log->clock_out) : new DateTime();
                    $total_hours = $clock_out->diff($clock_in)->format('%H:%I');
                ?>
                    <tr data-log-id="<?php echo esc_attr($log->id); ?>">
                        <td><?php echo esc_html($username); ?></td>
                        <td><input type="text" class="clock-in" value="<?php echo esc_attr($log->clock_in); ?>"></td>
                        <td><input type="text" class="clock-out" value="<?php echo esc_attr($log->clock_out); ?>"></td>
                        <td class="total-hours"><?php echo esc_html($total_hours); ?></td>
                        <td>
                            <?php 
                            if (isset($log->location_name)) {
                                echo esc_html($log->location_name);
                            } else {
                                echo 'Lat: ' . esc_html($log->location_lat) . ', Lng: ' . esc_html($log->location_lng);
                            }
                            ?>
                        </td>
                        <td>
                            <button class="button update-log">Update</button>
                            <button class="button delete-log">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No clock in/out records found.</p>
    <?php endif; ?>
</div>