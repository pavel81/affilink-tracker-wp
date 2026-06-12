<?php
// Registrace taxonomie značky (štítky)
add_action('init', function () {
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
