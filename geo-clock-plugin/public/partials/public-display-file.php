<div class="geo-clock-wrapper">
    <h2><?php esc_html_e('Employee Clock In/Out', 'geo-based-employee-clock'); ?></h2>
    <?php if (is_user_logged_in()): ?>
        <button id="clock-button"><?php esc_html_e('Clock In', 'geo-based-employee-clock'); ?></button>
        <p id="clock-status"></p>
    <?php else: ?>
        <p><?php esc_html_e('Please log in to use the clock in/out system.', 'geo-based-employee-clock'); ?></p>
    <?php endif; ?>
</div>
