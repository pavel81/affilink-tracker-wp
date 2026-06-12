<?php
// Spuštění mimo WP cron: kontrola dostupnosti affil odkazů
define('WP_USE_THEMES', false);
require_once(dirname(__FILE__) . '/../../../../wp-load.php'); // upravit podle reálné cesty

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
