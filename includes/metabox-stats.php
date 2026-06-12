<?php
add_action('add_meta_boxes', function () {
    add_meta_box('affilink_offer_stats', 'Statistiky odkazu', 'render_affilink_offer_stats', 'affilink_offer', 'side', 'high');
});

function render_affilink_offer_stats($post) {
    global $wpdb;
    $clicks = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}affilink_click_log WHERE offer_id = %d",
        $post->ID
    ));
    $status = get_post_meta($post->ID, '_affilink_last_status', true);
    $last = get_post_meta($post->ID, '_affilink_last_check', true);

    $status_display = $status ? "<strong style='color:" . ($status == 200 ? "green" : "red") . "'>{$status}</strong>" : "<em>neznámý</em>";
    echo "<p><strong>Prokliků:</strong> {$clicks}</p>";
    echo "<p><strong>HTTP stav:</strong> {$status_display}</p>";
    echo "<p><strong>Kontrola:</strong> " . ($last ?: "<em>nikdy</em>") . "</p>";
}
