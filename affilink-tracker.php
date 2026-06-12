<?php
error_log('affilink-tracker-plugin, main file start');
/**
 * Plugin Name: Affilink Tracker
 */

register_activation_hook(__FILE__, function () {
    require_once plugin_dir_path(__FILE__) . 'includes/install.php';
});



require_once plugin_dir_path(__FILE__) . 'includes/redirect-handler.php';

require_once plugin_dir_path(__FILE__) . 'includes/cron-checker.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-stats.php';
require_once plugin_dir_path(__FILE__) . '/includes/install.php';
require_once plugin_dir_path(__FILE__) . '/admin/admin-shortcodes.php';
require_once plugin_dir_path(__FILE__) . '/admin/settings.php';
require_once plugin_dir_path(__FILE__) . '/includes/taxonomy-tags.php';
require_once plugin_dir_path(__FILE__) . '/includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/metabox-stats.php';
require_once plugin_dir_path(__FILE__) . 'includes/woocommerce-integration.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-chart.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-broken.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-notifications.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-reactivation.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-cron.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-cron-notify.php';
//error_log('affilink-tracker-plugin, main file stop');

 //register_activation_hook(__FILE__, 'affilink_tracker_activate');
// Registrace post typu
add_action('init', function () {
    register_post_type('affiliate_offer', [
        'labels' => [
            'name' => 'Affiliate nabdky',
            'singular_name' => 'Nabdka',
            'add_new' => 'Pidat novou',
            'add_new_item' => 'Pidat novou nabdku',
            'edit_item' => 'Upravit nabdku',
            'new_item' => 'Nov nabdka',
            'view_item' => 'Zobrazit nabdku',
            'search_items' => 'Hledat nabdky',
            'not_found' => 'Nenalezeno',
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'nabidka'],
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-megaphone',
    ]);
});

// Redirect logika /go/{slug}
add_action('init', function () {
    add_rewrite_rule('^go/([^/]+)/?$', 'index.php?affil_redirect=$matches[1]', 'top');
});
add_filter('query_vars', function ($vars) {
    $vars[] = 'affil_redirect';
    return $vars;
});
add_action('template_redirect', function () {
    $slug = get_query_var('affil_redirect');
    if ($slug) {
        $q = new WP_Query([
            'post_type' => 'affiliate_offer',
            'name' => $slug,
            'posts_per_page' => 1
        ]);
        if ($q->have_posts()) {
            $q->the_post();
            $url = get_post_meta(get_the_ID(), '_affil_url', true);
            $settings = get_option('affilink_tracker_settings');
            $log_clicks = !empty($settings['log_clicks']);
            if ($url) {
                if ($log_clicks) {
                    global $wpdb;
                    $wpdb->insert($wpdb->prefix . 'affilink_clicks', [
                        'offer_id' => get_the_ID(),
                        'click_time' => current_time('mysql'),
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'referer' => $_SERVER['HTTP_REFERER'] ?? '',
                    ]);
                }
                wp_redirect($url);
                exit;
            }
        }
        $fallback = get_option('affilink_tracker_settings')['default_redirect'] ?? home_url();
        wp_redirect($fallback);
        exit;
    }
});

// Meta box pro affiliate URL
add_action('add_meta_boxes', function () {
    add_meta_box('affilink_url_box', 'Affiliate odkaz', function ($post) {
        $value = get_post_meta($post->ID, '_affil_url', true);
        echo '<input type="url" name="affil_url" value="' . esc_attr($value) . '" style="width:100%">';
    }, 'affiliate_offer', 'normal', 'high');
});
add_action('save_post', function ($post_id) {
    if (isset($_POST['affil_url'])) {
        update_post_meta($post_id, '_affil_url', esc_url_raw($_POST['affil_url']));
    }
});
require_once __DIR__ . '/admin/import-csv.php';
require_once __DIR__ . '/includes/cron-expiry.php';
require_once __DIR__ . '/includes/taxonomy-casino.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('affilink-tracker-style', plugin_dir_url(__FILE__) . 'asset/css/public-style.css');
});

require_once plugin_dir_path(__FILE__) . 'includes/settings-style.php';
require_once plugin_dir_path(__FILE__) . 'includes/taxonomy-action.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes-action.php';

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_media();
    wp_enqueue_script('affilink-media-uploader', plugin_dir_url(__FILE__) . 'assets/js/media-uploader.js', ['jquery'], null, true);
});

// Registrace JS a CSS
add_action('admin_enqueue_scripts', function () {
    $base = plugin_dir_url(__FILE__);
    wp_enqueue_script('affilink-admin-js', $base . 'assets/js/admin-actions.js', ['jquery'], '1.0', true);
    wp_enqueue_script('affilink-media-uploader', $base . 'assets/js/media-uploader.js', ['jquery'], '1.0', true);
    wp_enqueue_style('affilink-admin-style', $base . 'assets/css/admin-style.css', [], '1.0');
});
add_action('admin_enqueue_scripts', function () {
    $base = plugin_dir_url(__FILE__);
    wp_enqueue_script('affilink-admin-js', $base . 'assets/js/ssl-check.js', ['jquery'], '1.0', true);
    wp_enqueue_style('affilink-admin-css', $base . 'assets/css/ssl-check.css', [], '1.0');
});
add_action('wp_enqueue_scripts', function () {
    $base = plugin_dir_url(__FILE__);
    wp_enqueue_style('affilink-public-style', $base . 'assets/css/public-style.css', [], '1.0');
});

require_once plugin_dir_path(__FILE__) . 'includes/functions.php';

require_once plugin_dir_path(__FILE__) . 'includes/cron-functions.php';

// Registrace plánovaných WP-Cron událostí
register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('affilink_check_links_cron')) {
        wp_schedule_event(time(), 'hourly', 'affilink_check_links_cron');
    }
    if (!wp_next_scheduled('affilink_alerts_cron')) {
        wp_schedule_event(time(), 'twicedaily', 'affilink_alerts_cron');
    }
    if (!wp_next_scheduled('affilink_summary_cron')) {
        wp_schedule_event(time(), 'daily', 'affilink_summary_cron');
    }
});

add_action('affilink_check_links_cron', 'affilink_cron_check_links');
add_action('affilink_alerts_cron', 'affilink_cron_alerts');
add_action('affilink_summary_cron', 'affilink_cron_summary');
