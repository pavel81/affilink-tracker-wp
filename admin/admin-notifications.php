<?php
add_action('admin_menu', function () {
    add_submenu_page('affilink_stats', 'Notifikace', 'Notifikace', 'manage_options', 'affilink_notifications', 'affilink_render_notifications_settings');
});



function affilink_render_notifications_settings() {
    ?>
    <div class="wrap">
        <h1>Upozornění Affilink Tracker</h1>
        <form method="post" action="options.php">
            <?php settings_fields('affilink_notifications_group'); ?>
            <?php do_settings_sections('affilink_notifications_group'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">E-mail pro upozornění</th>
                    <td>
                        <input type="email" name="affilink_notify_email" value="<?php echo esc_attr(get_option('affilink_notify_email', get_option('admin_email'))); ?>" class="regular-text">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Počet selhání pro deaktivaci odkazu</th>
                    <td>
                        <input type="number" name="affilink_alert_failure_threshold" value="<?php echo esc_attr(get_option('affilink_alert_failure_threshold', 3)); ?>" min="1" class="small-text">
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
