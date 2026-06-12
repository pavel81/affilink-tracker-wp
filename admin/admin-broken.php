<?php
add_action('admin_menu', function () {
    add_submenu_page('affilink_stats', 'Nefunkční odkazy', 'Nefunkční odkazy', 'manage_options', 'affilink_broken_links', 'affilink_render_broken_links');
});

function affilink_render_broken_links() {
    global $wpdb;
    $table = $wpdb->prefix . 'affilink_status_log';
    $results = $wpdb->get_results("SELECT offer_id, status_code, checked_at FROM $table ORDER BY checked_at DESC LIMIT 100");

    echo '<div class="wrap"><h1>Nefunkční affil odkazy</h1>';
    echo '<table class="widefat fixed"><thead><tr><th>Herna</th><th>Status</th><th>Čas kontroly</th></tr></thead><tbody>';
    foreach ($results as $row) {
        $title = get_the_title($row->offer_id);
        echo "<tr><td>{$title}</td><td style='color:red;'><strong>{$row->status_code}</strong></td><td>{$row->checked_at}</td></tr>";
    }
    echo '</tbody></table></div>';
}
