<?php
// Registrace taxonomií
add_action('init', function () {
    register_taxonomy('affilink_category', 'affiliate_offer', [
        'labels' => [
            'name' => 'Kategorie',
            'singular_name' => 'Kategorie',
        ],
        'hierarchical' => true,
        'public' => true,
        'rewrite' => ['slug' => 'nabidka-kategorie'],
    ]);

    register_taxonomy('affilink_tag', 'affiliate_offer', [
        'labels' => [
            'name' => 'Značky',
            'singular_name' => 'Značka',
        ],
        'hierarchical' => false,
        'public' => true,
        'rewrite' => ['slug' => 'nabidka-znacky'],
    ]);
});

// Shortcode s lazy loadingem a AMP podporou
add_shortcode('affilink_catalog', function ($atts) {
    $atts = shortcode_atts([
        'category' => '',
        'date' => '',
        'per_page' => 6,
        'offset' => 0
    ], $atts);

    $tax_query = [];
    if (!empty($atts['category'])) {
        $tax_query[] = [
            'taxonomy' => 'affilink_category',
            'field' => 'slug',
            'terms' => $atts['category'],
        ];
    }

    $meta_query = [];
    if (!empty($atts['date'])) {
        $target = match ($atts['date']) {
            'today' => date('Y-m-d'),
            'tomorrow' => date('Y-m-d', strtotime('+1 day')),
            default => $atts['date'],
        };
        $meta_query[] = [
            'key' => '_affil_valid_from',
            'value' => $target,
            'compare' => '<=',
            'type' => 'DATE'
        ];
        $meta_query[] = [
            'key' => '_affil_valid_to',
            'value' => $target,
            'compare' => '>=',
            'type' => 'DATE'
        ];
    }

    $query = new WP_Query([
        'post_type' => 'affiliate_offer',
        'posts_per_page' => $atts['per_page'],
        'offset' => $atts['offset'],
        'meta_query' => $meta_query,
        'tax_query' => $tax_query
    ]);

    $is_amp = function_exists('is_amp_endpoint') && is_amp_endpoint();

    ob_start();
    echo '<div id="affilink-container">';
    foreach ($query->posts as $offer) {
        echo render_affilink_offer($offer, $is_amp);
    }
    echo '</div>';

    if ($query->found_posts > $atts['per_page'] + $atts['offset']) {
        echo '<button id="load-more-affilink" 
                data-offset="' . ($atts['offset'] + $atts['per_page']) . '" 
                data-category="' . esc_attr($atts['category']) . '" 
                data-date="' . esc_attr($atts['date']) . '">
                Načíst další
              </button>';
    }

    return ob_get_clean();
});

// Funkce pro vykreslení jedné nabídky
function render_affilink_offer($offer, $is_amp = false) {
    $link = home_url('/go/' . $offer->post_name . '/');
    $thumb = get_the_post_thumbnail_url($offer->ID, 'medium');
    $style_class = 'affilink-style-karta';
    $box_style = '';

    $casino_terms = get_the_terms($offer->ID, 'affilink_casino');
    if (!empty($casino_terms) && !is_wp_error($casino_terms)) {
        $term = $casino_terms[0];
        $term_style = get_term_meta($term->term_id, 'term_style', true);
        $term_color = get_term_meta($term->term_id, 'term_color', true);
        $logo_url = get_term_meta($term->term_id, 'term_logo', true);

        if ($term_style) {
            $style_class = 'affilink-style-' . sanitize_html_class($term_style);
        }
        if ($term_color) {
            $box_style = 'background-color:' . esc_attr($term_color) . ';';
        }

        if ($logo_url) {
            $logo = $is_amp
                ? '<amp-img src="' . esc_url($logo_url) . '" width="120" height="60" layout="fixed" alt="logo" style="margin-bottom:8px;"></amp-img>'
                : '<img src="' . esc_url($logo_url) . '" alt="logo" style="max-width:120px;margin-bottom:8px;"><br>';
        } else {
            $logo = '';
        }
    } else {
        $logo = '';
    }

    $output = '<div class="affilink-offer ' . esc_attr($style_class) . '" style="border:1px solid #ccc;padding:1em;' . $box_style . '">';
    $output .= $logo;
    if ($thumb) {
        $output .= $is_amp
            ? '<amp-img src="' . esc_url($thumb) . '" width="400" height="250" layout="responsive" alt=""></amp-img><br>'
            : '<img src="' . esc_url($thumb) . '" alt="" loading="lazy" style="max-width:150px;"><br>';
    }
    $output .= '<h3>' . esc_html($offer->post_title) . '</h3>';
    $output .= '<div>' . wp_kses_post(wpautop($offer->post_content)) . '</div>';
    $output .= '<a href="' . esc_url($link) . '" class="button" target="_blank" rel="nofollow noopener">Navštívit nabídku</a>';
    $output .= '</div>';

    return $output;
}

// AJAX handler pro načítání dalších nabídek
add_action('wp_ajax_load_more_affilink', 'load_more_affilink');
add_action('wp_ajax_nopriv_load_more_affilink', 'load_more_affilink');

function load_more_affilink() {
    $offset = intval($_POST['offset']);
    $category = sanitize_text_field($_POST['category']);
    $date = sanitize_text_field($_POST['date']);

    echo do_shortcode('[affilink_catalog category="' . $category . '" date="' . $date . '" offset="' . $offset . '"]');
    wp_die();
}

// JavaScript pro lazy loading (vložený do patičky)
add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('affilink-container');
        const loadMore = document.getElementById('load-more-affilink');
        if (loadMore) {
            loadMore.addEventListener('click', function () {
                const offset = this.dataset.offset;
                const category = this.dataset.category;
                const date = this.dataset.date;

                const data = new FormData();
                data.append('action', 'load_more_affilink');
                data.append('offset', offset);
                data.append('category', category);
                data.append('date', date);

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.text())
                .then(html => {
                    container.insertAdjacentHTML('beforeend', html);
                    this.remove(); // nebo aktualizuj offset pro další načtení
                });
            });
        }
    });
    </script>
    <?php
});


