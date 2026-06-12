<?php
add_action('admin_menu', function () {
    add_submenu_page('affilink_stats', 'CRON úlohy', 'CRON úlohy', 'manage_options', 'affilink_cron_jobs', 'affilink_render_cron_page');
});

function affilink_render_cron_page() {
    $alert_url = site_url('/wp-content/plugins/affilink-tracker/cron-affilink-alert.php');
    $summary_url = site_url('/wp-content/plugins/affilink-tracker/cron-affilink-summary.php');

    $last_alert = get_option('affilink_last_alert_cron');
    $last_summary = get_option('affilink_last_summary_cron');

    echo '<div class="wrap"><h1>CRON úlohy pluginu Affilink Tracker</h1>';
    echo '<p>Zde najdeš přímé odkazy, vzory příkazů a stav posledního spuštění CRON skriptů.</p>';

    echo '<h2>Odkazy</h2>';
    echo '<table class="widefat"><thead><tr><th>Název</th><th>URL</th></tr></thead><tbody>';
    echo "<tr><td>Kontrola affil odkazů</td><td><code>{$alert_url}</code></td></tr>";
    echo "<tr><td>Souhrnné e-mailové oznámení</td><td><code>{$summary_url}</code></td></tr>";
    echo '</tbody></table>';

    echo '<h2>Vzory CRON příkazů</h2>';
    echo '<code>0 * * * * /usr/bin/curl -s ' . esc_url($alert_url) . ' > /dev/null 2>&1</code><br>';
    echo '<code>0 3 * * * /usr/bin/curl -s ' . esc_url($summary_url) . ' > /dev/null 2>&1</code>';

    echo '<h2>Stav posledního spuštění</h2>';
    echo '<ul>';
    echo '<li><strong>Kontrola odkazů:</strong> ' . ($last_alert ? date('d.m.Y H:i:s', $last_alert) : 'Nikdy') . '</li>';
    echo '<li><strong>Souhrnný e-mail:</strong> ' . ($last_summary ? date('d.m.Y H:i:s', $last_summary) : 'Nikdy') . '</li>';
    echo '</ul>';
    echo '</div>';
}
