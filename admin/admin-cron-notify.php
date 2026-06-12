<?php
add_action('admin_notices', function () {
    $threshold_hours = 24;
    $now = time();
    $alert_last = get_option('affilink_last_alert_cron');
    $summary_last = get_option('affilink_last_summary_cron');
    $notify = get_option('affilink_cron_email_warning_enabled');
    $email = get_option('affilink_cron_email_warning_address');

    $notices = [];

    if (!$alert_last || ($now - $alert_last) > $threshold_hours * 3600) {
        $notices[] = 'CRON skript pro kontrolu odkazů nebyl spuštěn déle než ' . $threshold_hours . ' hodin.';
        if ($notify && is_email($email)) {
            wp_mail($email, 'CRON upozornění – kontrola odkazů neaktivní', 'CRON skript affilink-alert nebyl aktivní více než 24 hodin.');
        }
    }

    if (!$summary_last || ($now - $summary_last) > $threshold_hours * 3600) {
        $notices[] = 'CRON skript pro souhrnné e-maily nebyl spuštěn déle než ' . $threshold_hours . ' hodin.';
        if ($notify && is_email($email)) {
            wp_mail($email, 'CRON upozornění – souhrn neaktivní', 'CRON skript affilink-summary nebyl aktivní více než 24 hodin.');
        }
    }

    if (!empty($notices)) {
        echo '<div class="notice notice-warning"><p>' . implode('<br>', $notices) . '</p></div>';
    }
});

// Nastavení v administraci
add_action('admin_menu', function () {
    add_submenu_page('affilink_stats', 'Nastavení CRON hlídání', 'CRON hlídání', 'manage_options', 'affilink_cron_settings', 'affilink_cron_settings_page');
});

function affilink_cron_settings_page() {
    if (isset($_POST['cron_save'])) {
        update_option('affilink_cron_email_warning_enabled', isset($_POST['enabled']) ? 1 : 0);
        update_option('affilink_cron_email_warning_address', sanitize_email($_POST['email']));
        echo '<div class="updated"><p>Nastavení uloženo.</p></div>';
    }

    $enabled = get_option('affilink_cron_email_warning_enabled', false);
    $email = get_option('affilink_cron_email_warning_address', get_bloginfo('admin_email'));

    echo '<div class="wrap"><h1>Nastavení hlídání CRON</h1>';
    echo '<form method="post">';
    echo '<label><input type="checkbox" name="enabled" ' . checked($enabled, 1, false) . '> Posílat upozornění na e-mail</label><br><br>';
    echo '<label>E-mail: <input type="email" name="email" value="' . esc_attr($email) . '" style="width:300px;"></label><br><br>';
    echo '<input type="submit" name="cron_save" class="button-primary" value="Uložit nastavení">';
    echo '</form></div>';
}
