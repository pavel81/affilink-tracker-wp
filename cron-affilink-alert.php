<?php update_option('affilink_last_alert_cron', time()); ?><?php
// Kontrola nefunkčních odkazů (HTTP status != 200) a jejich logování
define('WP_USE_THEMES', false);
require_once(dirname(__FILE__) . '/../../../../wp-load.php');

global $wpdb;

$args = [
    'post_type' => 'affilink_offer',
    'posts_per_page' => -1,
    'post_status' => 'publish',
];

$offers = get_posts($args);

foreach ($offers as $offer) {
    $url = get_post_meta($offer->ID, 'affilink_url', true);
    if (!$url) continue;

    $response = wp_remote_get($url, ['timeout' => 10]);
    $status = is_wp_error($response) ? 0 : wp_remote_retrieve_response_code($response);
    $timestamp = current_time('mysql');

    update_post_meta($offer->ID, '_affilink_last_status', $status);
    update_post_meta($offer->ID, '_affilink_last_check', $timestamp);
    // Kontrola, kolikrát za 3 dny selhal
    $failures = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}affilink_status_log WHERE offer_id = %d AND checked_at >= NOW() - INTERVAL 3 DAY AND status_code != 200",
        $offer->ID
    ));

    if ($failures >= 3) {
        // Přepne příležitost do konceptu
        wp_update_post([
            'ID' => $offer->ID,
            'post_status' => 'draft',
        ]);
        // Odeslání upozornění
        if (is_email($notify_email)) {
            $subject = "Affilink deaktivován: " . get_the_title($offer->ID);
            $body = "Odkaz byl automaticky deaktivován kvůli více než 3 selháním během 3 dnů.\n\n".
                    "Herna: " . get_the_title($offer->ID) . "\nURL: {$url}\n\n".
                    "Status: {$status}\nČas: {$timestamp}";
            wp_mail($notify_email, $subject, $body);
        }
    }


    if ($status != 200) {
    $notify_mode = get_option('affilink_notify_mode', 'immediate');
    $notify_email = get_option('affilink_notify_email', get_option('admin_email'));

    if ($notify_mode === 'immediate' && is_email($notify_email)) {
        $subject = "Nefunkční affil odkaz: " . get_the_title($offer->ID);
        $body = "Byla detekována chyba při kontrole odkazu:\n\n".
                "Herna: " . get_the_title($offer->ID) . "\n".
                "URL: {$url}\n".
                "HTTP status: {$status}\n".
                "Čas: {$timestamp}";
        wp_mail($notify_email, $subject, $body);
    }

        $wpdb->insert($wpdb->prefix . 'affilink_status_log', [
            'offer_id' => $offer->ID,
            'status_code' => $status,
            'checked_at' => $timestamp,
        ]);
    }
}
