<?php
add_shortcode('affilink_action', function ($atts) {
    $atts = shortcode_atts(['action' => ''], $atts, 'affilink_action');
    $term = get_term_by('slug', $atts['action'], 'affilink_action');
    if (!$term) return '';

    $args = [
        'post_type' => 'affilink_offer',
        'posts_per_page' => -1,
        'tax_query' => [[
            'taxonomy' => 'affilink_action',
            'field' => 'slug',
            'terms' => $atts['action']
        ]]
    ];
    $offers = get_posts($args);
    if (empty($offers)) return '';

    $color = get_term_meta($term->term_id, 'action_color', true);
    $icon = get_term_meta($term->term_id, 'action_icon', true);
    $start = get_term_meta($term->term_id, 'action_start', true);
    $end = get_term_meta($term->term_id, 'action_end', true);
    $today = date('Y-m-d');
    if (($start && $start > $today) || ($end && $end < $today)) return '';
    
    $style = get_term_meta($term->term_id, 'action_style', true);
    $style_class = 'affilink-style-' . ($style ? sanitize_html_class($style) : 'karta');
    $output = ($icon ? '<img src="' . esc_url($icon) . '" alt="ikona" style="max-height:40px;margin-bottom:5px;"><br>' : '') . '<div class="affilink-action-group ' . esc_attr($style_class) . '" style="background:' . esc_attr($color) . ';padding:1em;margin:1em 0;">';
    $output .= '<h3>' . esc_html($term->name) . '</h3>';
    foreach ($offers as $offer) {
        $link = home_url('/go/' . $offer->post_name . '/');
        $output .= '<div class="affilink-offer"><a href="' . esc_url($link) . '">' . esc_html($offer->post_title) . '</a></div>';
    }
    $output .= '</div>';
    return $output;
});
