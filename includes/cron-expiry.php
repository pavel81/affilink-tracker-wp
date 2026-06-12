<?php
// Denní cron – kontrola expirovaných nabídek
add_action('affilink_daily_cron', function () {
    $today = date('Y-m-d');
    $args = [
        'post_type' => 'affiliate_offer',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_affil_valid_to',
                'value' => $today,
                'compare' => '=',
                'type' => 'DATE'
            ]
        ]
    ];
    $offers = get_posts($args);
    foreach ($offers as $offer) {
        // Můžeš přidat označení nebo akci (např. změna štítku nebo custom meta)
        update_post_meta($offer->ID, '_affil_expires_today', '1');
    }
});

// Registrace plánování cronu při aktivaci
if (!wp_next_scheduled('affilink_daily_cron')) {
    wp_schedule_event(time(), 'daily', 'affilink_daily_cron');
}

// Odstranit plán při deaktivaci
register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook('affilink_daily_cron');
});
