<?php
// Přidání admin stránky do menu
add_action('admin_menu', function () {
    add_menu_page(
        'Affilink Tracker',
        'Affilink Tracker',
        'manage_options',
        'affilink-tracker-settings',
        'affilink_tracker_settings_page',
        'dashicons-admin-generic'
    );
});

// Registrování nastavení
add_action('admin_init', function () {
    register_setting('affilink_tracker_settings_group', 'affilink_tracker_settings');

    add_settings_section(
        'affilink_tracker_main',
        'Základní nastavení',
        null,
        'affilink-tracker-settings'
    );

    add_settings_field(
        'default_redirect',
        'Výchozí redirect URL (fallback)',
        function () {
            $options = get_option('affilink_tracker_settings');
            echo '<input type="url" name="affilink_tracker_settings[default_redirect]" value="' . esc_attr($options['default_redirect'] ?? '') . '" style="width: 100%;">';
        },
        'affilink-tracker-settings',
        'affilink_tracker_main'
    );

    add_settings_field(
        'log_clicks',
        'Logování kliknutí',
        function () {
            $options = get_option('affilink_tracker_settings');
            $checked = !empty($options['log_clicks']) ? 'checked' : '';
            echo '<input type="checkbox" name="affilink_tracker_settings[log_clicks]" value="1" ' . $checked . '> Povolit logování kliknutí';
        },
        'affilink-tracker-settings',
        'affilink_tracker_main'
    );
});

// Obsah nastavení
function affilink_tracker_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Affilink Tracker – Nastavení</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('affilink_tracker_settings_group');
            do_settings_sections('affilink-tracker-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
