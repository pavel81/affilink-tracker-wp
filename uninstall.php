<?php
// Ověření, že uninstall je volán z WordPress prostředí
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Odstranění vlastní tabulky s prokliky
$table_name = $wpdb->prefix . 'affilink_clicks';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Zrušení cron hooků
wp_clear_scheduled_hook('affilink_check_links_cron');
wp_clear_scheduled_hook('affilink_daily_cron');

// Odstranění meta klíčů
delete_post_meta_by_key('_affilink_deactivation_ignored');
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}affilink_tracker_links");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}affilink_click_log");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}affilink_status_log");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}affilink_offers");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}affilink_links");
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}affilink_duplicates`");
