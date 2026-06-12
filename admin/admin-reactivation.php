<?php
add_action('admin_menu', function () {
    add_submenu_page('affilink_stats', 'Správa deaktivací', 'Deaktivované odkazy', 'manage_options', 'affilink_deactivated_links', 'affilink_render_deactivated_links');
});

function affilink_render_deactivated_links() {
    global $wpdb;

    if (isset($_POST['bulk_action']) && isset($_POST['selected_offers'])) {
        foreach ($_POST['selected_offers'] as $offer_id) {
            $offer_id = intval($offer_id);
            if ($_POST['bulk_action'] === 'reactivate') {
                wp_update_post(['ID' => $offer_id, 'post_status' => 'publish']);
                delete_post_meta($offer_id, '_affilink_deactivation_ignored');
            } elseif ($_POST['bulk_action'] === 'ignore') {
                update_post_meta($offer_id, '_affilink_deactivation_ignored', 1);
            }
        }
        echo '<div class="updated"><p>Hromadná akce provedena.</p></div>';
    }

    $args = [
        'post_type' => 'affilink_offer',
        'post_status' => 'draft',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'OR',
            ['key' => '_affilink_last_status', 'value' => 200, 'compare' => '!='],
            ['key' => '_affilink_last_status', 'compare' => 'NOT EXISTS']
        ]
    ];
    $offers = get_posts($args);

    echo '<div class="wrap"><h1>Deaktivované affil odkazy</h1>';
    if (empty($offers)) {
        echo '<p>Žádné deaktivované odkazy nebyly nalezeny.</p></div>';
        return;
    }

    echo '<form method="post">';
    echo '<table class="widefat fixed"><thead><tr><th><input type="checkbox" id="check-all"></th><th>Herna</th><th>Stav</th><th>Výjimka</th></tr></thead><tbody>';
    foreach ($offers as $offer) {
        $status = get_post_meta($offer->ID, '_affilink_last_status', true);
        $ignored = get_post_meta($offer->ID, '_affilink_deactivation_ignored', true);
        $title = get_the_title($offer->ID);

        echo "<tr>
            <td><input type='checkbox' name='selected_offers[]' value='{$offer->ID}'></td>
            <td>{$title}</td>
            <td>{$status}</td>
            <td>" . ($ignored ? "<em>Ignorováno</em>" : "-") . "</td>
        </tr>";
    }
    echo '</tbody></table><br>';
    echo '<select name="bulk_action">
        <option value="reactivate">Znovu aktivovat</option>
        <option value="ignore">Označit jako výjimku</option>
    </select> ';
    echo '<input type="submit" class="button-primary" value="Provést u vybraných">';
    echo '</form></div>';
?>
     <script>
        document.getElementById("check-all").addEventListener("change", function() {
            let checkboxes = document.querySelectorAll('input[name="selected_offers[]"]');
            for (let checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
    <?Php
}
