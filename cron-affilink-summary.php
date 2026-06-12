<?php update_option('affilink_last_summary_cron', time()); ?><?php
// Souhrnné e-mailové upozornění na nefunkční odkazy
define('WP_USE_THEMES', false);
require_once(dirname(__FILE__) . '/../../../../wp-load.php');

global $wpdb;

$notify_mode = get_option('affilink_notify_mode', 'immediate');
$notify_email = get_option('affilink_notify_email', get_option('admin_email'));

if ($notify_mode !== 'summary' || !is_email($notify_email)) {
    exit;
}

$table = $wpdb->prefix . 'affilink_status_log';
$results = $wpdb->get_results("SELECT offer_id, status_code, checked_at FROM $table WHERE checked_at >= NOW() - INTERVAL 1 DAY ORDER BY checked_at DESC");

if (empty($results)) {
    exit;
}

$body = "Souhrn nefunkčních affil odkazů za posledních 24 hodin:\n\n";
foreach ($results as $row) {
    $title = get_the_title($row->offer_id);
    $url = get_post_meta($row->offer_id, 'affilink_url', true);
    $body .= "Herna: {$title}\nURL: {$url}\nStatus: {$row->status_code}\nČas: {$row->checked_at}\n\n";
}

$subject = "Souhrn nefunkčních odkazů – Affilink Tracker";
wp_mail($notify_email, $subject, $body);
