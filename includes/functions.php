<?php
// Register shortcode for affiliate links
function affilink_tracker_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => '',
    ), $atts, 'affilink');

    if (empty($atts['id'])) {
        return '';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'affilink_tracker_links';
    $link = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $atts['id']));

    if (!$link) {
        return '';
    }

    // Log view or click etc.

    // Return formatted affiliate link
    return '<a href="' . esc_url($link->url) . '" target="_blank" rel="nofollow">' . esc_html($link->label) . '</a>';
}
add_shortcode('affilink', 'affilink_tracker_shortcode');

// Pomocná funkce pro výpis logů podle období
function affilink_get_click_logs($start_date = null, $end_date = null) {
    global $wpdb;
    $table = $wpdb->prefix . 'affilink_click_log';
    $where = '1=1';

    if ($start_date) {
        $where .= $wpdb->prepare(" AND clicked_at >= %s", $start_date);
    }
    if ($end_date) {
        $where .= $wpdb->prepare(" AND clicked_at <= %s", $end_date);
    }

    return $wpdb->get_results("SELECT * FROM $table WHERE $where ORDER BY clicked_at DESC");
}

// Funkce pro detekci nefunkčních odkazů
function affilink_get_broken_links() {
    global $wpdb;
    $table = $wpdb->prefix . 'affilink_status_log';
    return $wpdb->get_results("SELECT * FROM $table WHERE is_working = 0 ORDER BY checked_at DESC");
}

// Funkce pro reaktivaci odkazu (např. AJAX volání)
function affilink_reactivate_offer($offer_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'affilink_offers';
    return $wpdb->update($table, ['is_active' => 1], ['id' => $offer_id]);
}

// Pomocná funkce pro formátování datumu
function affilink_format_date($datetime) {
    return date('d.m.Y H:i', strtotime($datetime));
}

// Pomocná funkce pro detekci neznámých nebo nových URL
function affilink_is_affil_url($url) {
    $patterns = [
        'affil',
        'utm',
        'clickid',
        'campaign'
    ];

    foreach ($patterns as $pattern) {
        if (stripos($url, $pattern) !== false) {
            return true;
        }
    }
    return false;
}
function affilink_add_manual_link($url) {
    global $wpdb;
    $table = $wpdb->prefix . 'affilink_links';
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return $wpdb->insert($table, [
            'url' => esc_url_raw($url),
            'link_type' => 'manual',
            'created_at' => current_time('mysql')
        ]);
    }
    return false;
}
/**
 * Porovná dvě URL a zvýrazní rozdíly ve vybraných parametrech.
 */
function affilink_highlight_url_diff($url1, $url2) {
    $parsed1 = parse_url($url1);
    $parsed2 = parse_url($url2);

    $query1 = [];
    $query2 = [];
    if (isset($parsed1['query'])) parse_str($parsed1['query'], $query1);
    if (isset($parsed2['query'])) parse_str($parsed2['query'], $query2);

    $important_keys = ['aff_id', 'subid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'campaign', 'cid', 'pid'];

    $diffs = [];

    foreach ($important_keys as $key) {
        $val1 = $query1[$key] ?? null;
        $val2 = $query2[$key] ?? null;

        if ($val1 !== $val2) {
            $diffs[$key] = [
                'value1' => $val1,
                'value2' => $val2,
            ];
        }
    }

    $output = "<table class='affilink-url-diff'><thead><tr><th>Parametr</th><th>URL 1</th><th>URL 2</th></tr></thead><tbody>";
    foreach ($diffs as $key => $pair) {
        $output .= "<tr>
            <td><strong>{$key}</strong></td>
            <td><span class='diff-changed'>" . esc_html($pair['value1']) . "</span></td>
            <td><span class='diff-changed'>" . esc_html($pair['value2']) . "</span></td>
        </tr>";
    }
    $output .= "</tbody></table>";

    if (empty($diffs)) {
        $output = "<p><em>Žádné rozdíly v klíčových parametrech</em></p>";
    }

    return $output;
}
