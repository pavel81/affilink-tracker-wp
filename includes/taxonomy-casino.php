<?php
// Registrace taxonomie Herny
add_action('init', function () {
    register_taxonomy('affilink_casino', 'affiliate_offer', [
        'labels' => [
            'name' => 'Herny',
            'singular_name' => 'Herna',
            'search_items' => 'Hledat herny',
            'all_items' => 'Všechny herny',
            'edit_item' => 'Upravit hernu',
            'update_item' => 'Aktualizovat hernu',
            'add_new_item' => 'Přidat novou hernu',
            'new_item_name' => 'Nový název herny',
            'menu_name' => 'Herny',
        ],
        'hierarchical' => false,
        'public' => true,
        'rewrite' => ['slug' => 'herna'],
        'show_admin_column' => true,
    ]);
});

// Přidání pole pro logo (term meta)
add_action('affilink_casino_add_form_fields', function () {
    echo '<div class="form-field">
        <label for="term_logo">Logo URL</label>
        <input type="text" name="term_logo" id="term_logo" value="" placeholder="https://...">
        <p class="description">Zadej URL loga herny.</p>
    </div>';
});

add_action('affilink_casino_edit_form_fields', function ($term) {
    $logo = get_term_meta($term->term_id, 'term_logo', true);
    echo '<tr class="form-field">
        <th scope="row" valign="top"><label for="term_logo">Logo URL</label></th>
        <td>
            <input type="text" name="term_logo" id="term_logo" value="' . esc_attr($logo) . '" style="width:100%;">
            <p class="description">Zadej URL loga herny.</p>
        </td>
    </tr>';
});

add_action('created_affilink_casino', function ($term_id) {
    if (isset($_POST['term_logo'])) {
        update_term_meta($term_id, 'term_logo', esc_url_raw($_POST['term_logo']));
    }
});

add_action('edited_affilink_casino', function ($term_id) {
    if (isset($_POST['term_logo'])) {
        update_term_meta($term_id, 'term_logo', esc_url_raw($_POST['term_logo']));
    }
});


add_action('affilink_casino_edit_form_fields', function ($term) {
    $logo = get_term_meta($term->term_id, 'term_logo', true);
    $color = get_term_meta($term->term_id, 'term_color', true);
    $rating = get_term_meta($term->term_id, 'term_rating', true);
    $jurisdiction = get_term_meta($term->term_id, 'term_jurisdiction', true);
    $website = get_term_meta($term->term_id, 'term_website', true);

    echo '
    <tr class="form-field">
        <th scope="row"><label for="term_logo">Logo URL</label></th>
        <td><input type="text" name="term_logo" id="term_logo" value="' . esc_attr($logo) . '" style="width:100%;"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="term_color">Barva pozadí</label></th>
        <td><input type="text" name="term_color" id="term_color" value="' . esc_attr($color) . '" placeholder="#ffffff" style="width:100%;"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="term_rating">Hodnocení (1–5)</label></th>
        <td><input type="number" name="term_rating" id="term_rating" value="' . esc_attr($rating) . '" min="1" max="5" style="width:100%;"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="term_jurisdiction">Jurisdikce</label></th>
        <td><input type="text" name="term_jurisdiction" id="term_jurisdiction" value="' . esc_attr($jurisdiction) . '" style="width:100%;"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="term_website">Webová stránka</label></th>
        <td><input type="url" name="term_website" id="term_website" value="' . esc_attr($website) . '" style="width:100%;"></td>
    </tr>';
}, 10, 2);

add_action('edited_affilink_casino', function ($term_id) {
    foreach (['term_logo', 'term_color', 'term_rating', 'term_jurisdiction', 'term_website'] as $field) {
        if (isset($_POST[$field])) {
            update_term_meta($term_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
});


add_action('affilink_casino_edit_form_fields', function ($term) {
    $current_style = get_term_meta($term->term_id, 'term_style', true);
    $styles = ['karta', 'panel', 'transparent', 'modern'];
    echo '<tr class="form-field">
        <th scope="row"><label for="term_style">Styl boxu</label></th>
        <td><select name="term_style" id="term_style">';
    foreach ($styles as $style) {
        $selected = selected($current_style, $style, false);
        echo "<option value='{$style}' {$selected}>{$style}</option>";
    }
    echo '</select><p class="description">Zvol styl vykreslení boxu s nabídkou.</p></td>
    </tr>';
});

add_action('edited_affilink_casino', function ($term_id) {
    if (isset($_POST['term_style'])) {
        update_term_meta($term_id, 'term_style', sanitize_text_field($_POST['term_style']));
    }
});
