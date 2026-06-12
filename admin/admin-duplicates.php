<?php include plugin_dir_path(__FILE__) . '../templates/affilink-label-filters.php'; ?>
<?php
// admin/admin-duplicates.php

add_action('admin_menu', function () {
    add_submenu_page(
        null,
        'Duplicitní odkazy',
        'Duplicitní odkazy',
        'manage_options',
        'affilink-duplicates',
        'affilink_duplicates_page'
    );
});

function affilink_duplicates_enqueue_scripts($hook) {
    if ($hook !== 'affilink-tracker_page_affilink-duplicates') {
        return;
    }

    wp_enqueue_script(
        'affilink-duplicates-js',
        plugin_dir_url(__FILE__) . '../assets/js/affilink-duplicates.js',
        array('jquery'),
        null,
        true
    );

    wp_enqueue_style(
        'affilink-duplicates-css',
        plugin_dir_url(__FILE__) . '../assets/css/affilink-duplicates.css'
    );
}
add_action('admin_enqueue_scripts', 'affilink_duplicates_enqueue_scripts');

function affilink_extract_query_diff($url1, $url2) {
    $q1 = [];
    $q2 = [];
    parse_str(parse_url($url1, PHP_URL_QUERY), $q1);
    parse_str(parse_url($url2, PHP_URL_QUERY), $q2);
    $keys = array_unique(array_merge(array_keys($q1), array_keys($q2)));
    ob_start();
    echo "<table class='affilink-url-diff'><thead><tr><th>Parametr</th><th>URL 1</th><th>URL 2</th></tr></thead><tbody>";
    foreach ($keys as $key) {
        $v1 = isset($q1[$key]) ? esc_html($q1[$key]) : '';
        $v2 = isset($q2[$key]) ? esc_html($q2[$key]) : '';
        $class = ($v1 === $v2) ? '' : (preg_match('/(aff_id|subid|utm|campaign)/i', $key) ? 'affilink-diff-highlight' : 'affilink-diff-different');
        echo "<tr class='$class'><td>" . esc_html($key) . "</td><td>$v1</td><td>$v2</td></tr>";
    }
    echo "</tbody></table>";
    return ob_get_clean();
}

function affilink_duplicates_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'affilink_duplicates';

    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 100");

    echo '<div class="wrap">';
    echo '<h1>Duplicitní odkazy</h1>';

    if ($results) {
        foreach ($results as $row) {
            echo '<div class="affilink-compare-box">';
            echo '<p><strong>URL 1:</strong> ' . esc_html($row->url1) . '</p>';
            echo '<p><strong>URL 2:</strong> ' . esc_html($row->url2) . '</p>';
            echo '<button class="compare-btn button">Porovnat rozdíly</button>';
            echo '<div class="affilink-diff-output hidden">';
            echo affilink_extract_query_diff($row->url1, $row->url2);
            echo '</div>';
            echo '<hr>';
            echo '</div>';
        }
    } else {
        echo '<p>Žádné duplicitní odkazy nenalezeny.</p>';
    }

    echo '</div>';
}


function affilink_duplicates_page() {
    $view_format = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'grid';

    echo '<div class="wrap"><h1>Duplicitní odkazy</h1>';
    echo '<div style="margin-bottom: 1em;">';
    echo '<a href="?page=affilink-duplicates&view=grid" class="button">Zobrazit v mřížce</a> ';
    echo '<a href="?page=affilink-duplicates&view=list" class="button">Zobrazit jako text</a>';
    echo '</div>';

    $duplicate_links = get_option('affilink_duplicate_urls', []);
    if (empty($duplicate_links)) {
        echo '<p>Žádné duplicitní odkazy nebyly nalezeny.</p></div>';
        return;
    }

    if ($view_format === 'list') {
        echo '<ul style="font-family:monospace;">';
        foreach ($duplicate_links as $row) {
            echo '<li>' . esc_html($row['url']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>#</th><th>URL</th><th>Doména</th><th>Parametry</th></tr></thead><tbody>';
        $i = 1;
        foreach ($duplicate_links as $row) {
            $url = esc_url($row['url']);
            $domain = parse_url($url, PHP_URL_HOST);
            $query = parse_url($url, PHP_URL_QUERY);
            $params = [];
            parse_str($query, $params);

            echo '<tr>';
            echo '<td>' . $i++ . '</td>';
            echo '<td style="word-break: break-all;">' . $url . '</td>';
            echo '<td>' . esc_html($domain) . '</td>';
            echo '<td><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:4px;">';
            foreach ($params as $k => $v) {
                $key = esc_html($k);
                $val = esc_html($v);
                echo "<div style='background:#eef;padding:4px;border-radius:4px;'>$key=$val</div>";
            }
            echo '</div></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    echo '</div>';
}

<script src='<?php echo plugin_dir_url(__FILE__) . '../assets/js/filters.js'; ?>'></script>
