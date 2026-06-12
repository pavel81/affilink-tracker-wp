<?php
add_action('admin_menu', function () {
    add_menu_page('Affilink Statistiky', 'Affilink Statistiky', 'manage_options', 'affilink_stats', 'affilink_render_stats');
});

function affilink_render_stats() {
    global $wpdb;
    $table = $wpdb->prefix . 'affilink_click_log';
    $rows = $wpdb->get_results("SELECT offer_id, COUNT(*) as clicks FROM $table GROUP BY offer_id ORDER BY clicks DESC LIMIT 50");

    echo '<div class="wrap"><h1>Statistiky Affilinků</h1><table class="widefat fixed"><thead><tr><th>Herna</th><th>Prokliků</th><th>HTTP stav</th><th>Poslední kontrola</th></tr></thead><tbody>';
    foreach ($rows as $row) {
        $title = get_the_title($row->offer_id);
        $status = get_post_meta($row->offer_id, '_affilink_last_status', true);
        $last_check = get_post_meta($row->offer_id, '_affilink_last_check', true);
        echo "<tr><td>{$title}</td><td>{$row->clicks}</td><td>{$status}</td><td>{$last_check}</td></tr>";
    }
    echo '</tbody></table></div>';
}
