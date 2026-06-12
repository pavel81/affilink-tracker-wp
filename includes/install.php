<?php
function affilink_tracker_install() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   

    $table = $wpdb->prefix . 'affilink_click_log';
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        offer_id BIGINT UNSIGNED NOT NULL,
        clicked_at DATETIME NOT NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        referrer TEXT
    ) DEFAULT CHARSET=utf8mb4;";

    dbDelta($sql);
    $table_click_log = $wpdb->prefix . 'affilink_click_log';
    $sql_click_log = "CREATE TABLE IF NOT EXISTS $table_click_log (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        offer_id BIGINT(20) UNSIGNED NOT NULL,
        url TEXT NOT NULL,
        clicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_ip VARCHAR(100),
        user_agent TEXT,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql_click_log);

    $table_status_log = $wpdb->prefix . 'affilink_status_log';
    $sql_status_log = "CREATE TABLE IF NOT EXISTS $table_status_log (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        offer_id BIGINT(20) UNSIGNED NOT NULL,
        url TEXT NOT NULL,
        status_code INT,
        is_working TINYINT(1) DEFAULT 1,
        checked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql_status_log);

    $table_offers = $wpdb->prefix . 'affilink_offers';
    $sql_offers = "CREATE TABLE IF NOT EXISTS $table_offers (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        url TEXT NOT NULL,
        label VARCHAR(255),
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql_offers);

    $table_name_links = $wpdb->prefix . 'affilink_tracker_links';
    $sql2 = "CREATE TABLE IF NOT EXISTS $table_name_links (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        url TEXT NOT NULL,
        label VARCHAR(255) DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql2);

    $table_name = $wpdb->prefix . 'affilink_links';
    dbDelta("CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        url text NOT NULL,
        link_type varchar(20) DEFAULT NULL,
        created_at datetime DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;");

affilink_tracker_install();
 $charset_collate = $wpdb->get_charset_collate();
$table_duplicates = $wpdb->prefix . `affilink_duplicates`;
$sql_duplicates = "CREATE TABLE IF NOT EXISTS $table_duplicates(
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `url1` TEXT NOT NULL,
    `url2` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
)    $charset_collate;";
dbDelta($sql_duplicates);
} 
