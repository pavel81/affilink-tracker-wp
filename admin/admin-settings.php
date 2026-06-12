<?php
// admin/admin-settings.php

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'affilink_settings_menu');

function affilink_settings_menu() {
    add_submenu_page(
        'affilink-tracker',
        'Nastavení Affilink',
        'Nastavení',
        'manage_options',
        'affilink-settings',
        'affilink_render_settings_page'
    );
}

function affilink_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Nastavení Affilink Tracker</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('affilink_settings_group');
            do_settings_sections('affilink-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'affilink_settings_init');

function affilink_settings_init() {
    register_setting('affilink_settings_group', 'affilink_use_ajax_compare');

    add_settings_section(
        'affilink_general_section',
        'Obecné nastavení',
        null,
        'affilink-settings'
    );

    add_settings_field(
        'affilink_use_ajax_compare',
        'Použít AJAX pro porovnávání URL',
        'affilink_use_ajax_compare_render',
        'affilink-settings',
        'affilink_general_section'
    );
}

function affilink_use_ajax_compare_render() {
    $value = get_option('affilink_use_ajax_compare');
    ?>
    <input type="checkbox" name="affilink_use_ajax_compare" value="1" <?php checked(1, $value, true); ?> />
    <label for="affilink_use_ajax_compare" title="AJAX porovnání umožní rychlejší a interaktivní kontrolu rozdílů v parametrech URL bez nutnosti reloadu stránky.">Povolit porovnávání parametrů přes AJAX</label>
    <?php
}
