<?php
// admin/admin-menu.php

if (!defined('ABSPATH')) {
    exit;
}

// Registrace admin menu pro plugin Affilink Tracker
add_action('admin_menu', 'affilink_register_admin_menu');

function affilink_register_admin_menu() {
    add_menu_page(
        'Affilink Tracker',
        'Affilink Tracker',
        'manage_options',
        'affilink-tracker',
        'affilink_render_main_page',
        'dashicons-admin-links',
        56
    );

    add_submenu_page(
        'affilink-tracker',
        'Duplicitní odkazy',
        'Duplicitní odkazy',
        'manage_options',
        'affilink-duplicates',
        'affilink_render_duplicates_page'
    );
}

function affilink_render_main_page() {
    echo '<div class="wrap"><h1>Affilink Tracker</h1><p>Zde bude hlavní přehled.</p></div>';
}

function affilink_render_duplicates_page() {
    include plugin_dir_path(__FILE__) . 'admin-duplicates.php';
}
