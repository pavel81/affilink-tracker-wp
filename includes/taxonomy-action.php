<?php
// Registrace taxonomy pro příležitosti
add_action('init', function () {
    register_taxonomy('affilink_action', 'affilink_offer', [
        'labels' => [
            'name' => 'Typy příležitostí',
            'singular_name' => 'Typ příležitosti',
            'search_items' => 'Hledat příležitosti',
            'all_items' => 'Všechny příležitosti',
            'edit_item' => 'Upravit příležitost',
            'update_item' => 'Aktualizovat příležitost',
            'add_new_item' => 'Přidat novou příležitost',
            'new_item_name' => 'Nový název příležitosti',
            'menu_name' => 'Typy příležitostí',
        ],
        'public' => true,
        'show_ui' => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'akce'],
    ]);
});

// Metadata: barva a styl
add_action('affilink_action_edit_form_fields', function ($term) {
    $color = get_term_meta($term->term_id, 'action_color', true);
    $style = get_term_meta($term->term_id, 'action_style', true);
    echo '
    <tr class="form-field">
        <th scope="row"><label for="action_color">Barva pozadí</label></th>
        <td><input type="text" name="action_color" id="action_color" value="' . esc_attr($color) . '" placeholder="#eeeeee" style="width:100%;"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="action_style">Styl (např. karta, panel)</label></th>
        <td><input type="text" name="action_style" id="action_style" value="' . esc_attr($style) . '" style="width:100%;"></td>
    </tr>';
});

add_action('edited_affilink_action', function ($term_id) {
    foreach (['action_color', 'action_style'] as $field) {
        if (isset($_POST[$field])) {
            update_term_meta($term_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
});


add_action('affilink_action_edit_form_fields', function ($term) {
    $icon = get_term_meta($term->term_id, 'action_icon', true);
    $start_date = get_term_meta($term->term_id, 'action_start', true);
    $end_date = get_term_meta($term->term_id, 'action_end', true);
    echo '
    
    <tr class="form-field">
        <th scope="row"><label for="action_icon">Ikona akce (Media)</label></th>
        <td>
            <input type="text" name="action_icon" id="action_icon" value="' . esc_attr($icon) . '" style="width:70%;">
            <button class="button" id="affilink_icon_button">Vybrat obrázek</button>
        </td>
    </tr>
URL ikony</label></th>
        <td><input type="text" name="action_icon" id="action_icon" value="' . esc_attr($icon) . '" placeholder="https://..." style="width:100%;"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="action_start">Datum začátku</label></th>
        <td><input type="date" name="action_start" value="' . esc_attr($start_date) . '"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="action_end">Datum konce</label></th>
        <td><input type="date" name="action_end" value="' . esc_attr($end_date) . '"></td>
    </tr>';
}, 10, 1);

add_action('edited_affilink_action', function ($term_id) {
    foreach (['action_icon', 'action_start', 'action_end'] as $field) {
        if (isset($_POST[$field])) {
            update_term_meta($term_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
});
