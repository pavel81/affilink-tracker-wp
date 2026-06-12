<?php

// Kontrola dostupnosti odkazů
function affilink_cron_check_links() {
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

        update_post_meta($offer->ID, '_affilink_last_status', $status);
        update_post_meta($offer->ID, '_affilink_last_check', current_time('mysql'));
    }
}

// Souhrnné upozornění
function affilink_cron_summary() {
    global $wpdb;

    $notify_mode = get_option('affilink_notify_mode', 'immediate');
    $notify_email = get_option('affilink_notify_email', get_option('admin_email'));

    if ($notify_mode !== 'summary' || !is_email($notify_email)) {
        return;
    }

    $table = $wpdb->prefix . 'affilink_status_log';
    $results = $wpdb->get_results("SELECT offer_id, status_code, checked_at FROM $table WHERE checked_at >= NOW() - INTERVAL 1 DAY ORDER BY checked_at DESC");

    if (empty($results)) return;

    $body = "Souhrn nefunkčních affil odkazů za posledních 24 hodin:\n\n";
    foreach ($results as $row) {
        $title = get_the_title($row->offer_id);
        $url = get_post_meta($row->offer_id, 'affilink_url', true);
        $body .= "Herna: {$title}\nURL: {$url}\nStatus: {$row->status_code}\nČas: {$row->checked_at}\n\n";
    }

    $subject = "Souhrn nefunkčních odkazů – Affilink Tracker";
    wp_mail($notify_email, $subject, $body);
}

// Deaktivace po 3 chybách
function affilink_cron_alerts() {
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

        $failures = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}affilink_status_log WHERE offer_id = %d AND checked_at >= NOW() - INTERVAL 3 DAY AND status_code != 200",
            $offer->ID
        ));

        if ($failures >= (int) get_option('affilink_alert_failure_threshold', 3)) {
            wp_update_post([
                'ID' => $offer->ID,
                'post_status' => 'draft',
            ]);
        }
    }
}