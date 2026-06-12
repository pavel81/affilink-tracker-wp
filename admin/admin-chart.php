<?php
add_action('admin_menu', function () {
    add_submenu_page('affilink_stats', 'Kliky v čase', 'Kliky v čase', 'manage_options', 'affilink_stats_chart', 'affilink_render_chart');
});

function affilink_render_chart() {
    global $wpdb;

    // Filtrovací formulář
    $herny = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'affilink_offer' AND post_status = 'publish' ORDER BY post_title ASC");

    $selected_offer = isset($_GET['offer_id']) ? intval($_GET['offer_id']) : 0;
    $range = $_GET['range'] ?? '30';

    echo '<div class="wrap"><h1>Prokliky v čase</h1>';
    echo '<form method="get" style="margin-bottom:20px;">
        <input type="hidden" name="page" value="affilink_stats_chart" />
        <label for="offer_id">Herna:</label>
        <select name="offer_id" id="offer_id">
            <option value="0">Všechny</option>';
    foreach ($herny as $herna) {
        $sel = $selected_offer == $herna->ID ? 'selected' : '';
        echo "<option value='{$herna->ID}' {$sel}>{$herna->post_title}</option>";
    }
    echo '</select> &nbsp; 
        <label for="range">Rozsah:</label>
        <select name="range" id="range">
            <option value="7" ' . ($range == "7" ? "selected" : "") . '>7 dní</option>
            <option value="30" ' . ($range == "30" ? "selected" : "") . '>30 dní</option>
            <option value="90" ' . ($range == "90" ? "selected" : "") . '>90 dní</option>
            <option value="all" ' . ($range == "all" ? "selected" : "") . '>Vše</option>
        </select>
        <button class="button">Zobrazit</button>
    </form>';

    // SQL dotaz podle filtru
    $conditions = [];
    if ($selected_offer) {
        $conditions[] = $wpdb->prepare("offer_id = %d", $selected_offer);
    }
    if ($range !== 'all') {
        $days = intval($range);
        $conditions[] = $wpdb->prepare("clicked_at >= NOW() - INTERVAL %d DAY", $days);
    }
    $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

    $table = $wpdb->prefix . 'affilink_click_log';
    $results = $wpdb->get_results("SELECT DATE(clicked_at) as click_date, COUNT(*) as total FROM $table $where GROUP BY click_date ORDER BY click_date ASC");

    $labels = [];
    $data = [];

    foreach ($results as $row) {
        $labels[] = $row->click_date;
        $data[] = $row->total;
    }

    echo '<canvas id="affilinkChart" height="100"></canvas></div>';
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<script>
        const ctx = document.getElementById("affilinkChart").getContext("2d");
        const chart = new Chart(ctx, {
            type: "line",
            data: {
                labels: ' . json_encode($labels) . ',
                datasets: [{
                    label: "Počet prokliků",
                    data: ' . json_encode($data) . ',
                    borderWidth: 2,
                    borderColor: "blue",
                    backgroundColor: "rgba(0, 119, 204, 0.1)",
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>';
}
