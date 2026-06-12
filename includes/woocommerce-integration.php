<?php
// WooCommerce integrace pro sledování a správu affil URL u produktů

if (!defined('ABSPATH')) {
    exit;
}

// Získání produktů s affiliate URL
function affilink_get_wc_products_with_affil_url($limit = 100) {
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => $limit,
        'meta_query'     => [
            [
                'key'     => 'affilink_url',
                'compare' => 'EXISTS',
            ],
        ],
    ];

    $query = new WP_Query($args);
    $products = [];

    foreach ($query->posts as $product) {
        $url = get_post_meta($product->ID, 'affilink_url', true);
        if (!empty($url)) {
            $products[] = [
                'ID'    => $product->ID,
                'title' => get_the_title($product->ID),
                'url'   => $url,
                'link'  => get_edit_post_link($product->ID),
            ];
        }
    }

    return $products;
}
