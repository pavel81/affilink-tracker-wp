<?php
// Manuální import z CSV – pouze pro adminy
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=affiliate_offer',
        'Import z CSV',
        'Import z CSV',
        'manage_options',
        'affilink-import',
        'affilink_import_page'
    );
});

function affilink_import_page() {
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['import_csv']) && isset($_FILES['csv_file'])) {
        $file = $_FILES['csv_file']['tmp_name'];
        if (($handle = fopen($file, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row = array_combine($header, $data);
                $post_id = wp_insert_post([
                    'post_title' => $row['title'],
                    'post_name' => $row['slug'],
                    'post_content' => $row['content'],
                    'post_status' => 'publish',
                    'post_type' => 'affiliate_offer',
                ]);

                if ($post_id && !is_wp_error($post_id)) {
                    update_post_meta($post_id, '_affil_url', esc_url_raw($row['url']));
                    update_post_meta($post_id, '_affil_valid_from', sanitize_text_field($row['valid_from']));
                    update_post_meta($post_id, '_affil_valid_to', sanitize_text_field($row['valid_to']));
                    if (!empty($row['category'])) wp_set_object_terms($post_id, [$row['category']], 'affilink_category');
                    if (!empty($row['tag'])) wp_set_object_terms($post_id, [$row['tag']], 'affilink_tag');
                    if (!empty($row['bonus_type'])) wp_set_object_terms($post_id, [$row['bonus_type']], 'affilink_bonus_type');
                    if (!empty($row['image_url'])) {
                        // Můžeš přidat nahrání obrázku z URL pomocí media_sideload_image
                    }
                }
            }
            fclose($handle);
            echo '<div class="updated"><p>Import dokončen.</p></div>';
        }
    }

    echo '<div class="wrap"><h1>Import affiliate nabídek z CSV</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <p class="submit"><input type="submit" name="import_csv" class="button-primary" value="Importovat"></p>
    </form></div>';
}
