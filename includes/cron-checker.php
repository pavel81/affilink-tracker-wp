<?php
add_action('affilink_check_links_cron', function () {
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
});

// Aktivace cron
if (!wp_next_scheduled('affilink_check_links_cron')) {
    wp_schedule_event(time(), 'hourly', 'affilink_check_links_cron');
}
