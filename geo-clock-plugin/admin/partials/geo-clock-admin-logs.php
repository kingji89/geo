<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
    $per_page = 20;
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $total_items = $this->get_employee_logs_count();
    $logs = $this->get_employee_logs($per_page, $page);
    ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Location</th>
                <th>Total Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr data-log-id="<?php echo esc_attr($log['id']); ?>">
                    <td><?php echo esc_html($log['display_name']); ?></td>
                    <td><input type="text" class="clock-in" value="<?php echo esc_attr($log['clock_in']); ?>"></td>
                    <td><input type="text" class="clock-out" value="<?php echo esc_attr($log['clock_out']); ?>"></td>
                    <td><?php echo esc_html($log['location_name']); ?></td>
                    <td>
                        <?php
                        $clock_in = new DateTime($log['clock_in']);
                        $clock_out = $log['clock_out'] != '0000-00-00 00:00:00' ? new DateTime($log['clock_out']) : new DateTime();
                        $interval = $clock_out->diff($clock_in);
                        echo $interval->format('%H:%I:%S');
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

    <?php
    $big = 999999999; // need an unlikely integer
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => $page,
        'total' => ceil($total_items / $per_page)
    ));
    ?>
</div>