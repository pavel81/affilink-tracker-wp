<?php
add_action('init', function () {
    if (preg_match('#^/go/([^/]+)/?$#', $_SERVER['REQUEST_URI'], $matches)) {
        $slug = sanitize_title($matches[1]);
        $post = get_page_by_path($slug, OBJECT, 'affilink_offer');
        if (!$post) {
            status_header(404);
            echo 'Neplatný odkaz.';
            exit;
        }

        $url = get_post_meta($post->ID, 'affilink_url', true);
        if (!$url) {
            status_header(404);
            echo 'Odkaz není definován.';
            exit;
        }

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'affilink_click_log', [
            'offer_id' => $post->ID,
            'clicked_at' => current_time('mysql'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
        ]);

        wp_redirect(esc_url_raw($url), 301);
        exit;
    }
});
