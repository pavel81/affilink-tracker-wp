<?php
// admin/ajax-handler.php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_affilink_compare_urls', 'affilink_compare_urls_ajax_handler');

function affilink_compare_urls_ajax_handler() {
    check_ajax_referer('affilink_ajax_nonce', '_ajax_nonce');

    $url1 = isset($_POST['url1']) ? sanitize_text_field(wp_unslash($_POST['url1'])) : '';
    $url2 = isset($_POST['url2']) ? sanitize_text_field(wp_unslash($_POST['url2'])) : '';

    if (!$url1 || !$url2) {
        wp_send_json_error('Neplatné URL.');
    }

    if (!function_exists('affilink_highlight_url_diff')) {
        require_once plugin_dir_path(__FILE__) . '../includes/functions.php';
    }

    echo affilink_highlight_url_diff($url1, $url2);
    wp_die();
}
