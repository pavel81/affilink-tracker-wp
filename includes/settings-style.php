<?php
add_action('admin_menu', function () {
    add_options_page('Affilink Styly', 'Affilink Styly', 'manage_options', 'affilink-styles', function () {
        ?>
        <div class="wrap">
            <h1>Výchozí styl a CSS</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('affilink_style_settings');
                do_settings_sections('affilink_style_settings');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Globální výchozí styl boxu</th>
                        <td>
                            <select name="affilink_default_style">
                                <?php
                                $options = ['karta', 'panel', 'transparent', 'modern'];
                                $current = get_option('affilink_default_style', 'karta');
                                foreach ($options as $opt) {
                                    echo "<option value='{$opt}' " . selected($current, $opt, false) . ">{$opt}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Vlastní CSS</th>
                        <td><textarea name="affilink_custom_css" rows="10" cols="70"><?php echo esc_textarea(get_option('affilink_custom_css')); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    });
});

add_action('admin_init', function () {
    register_setting('affilink_style_settings', 'affilink_default_style');
    register_setting('affilink_style_settings', 'affilink_custom_css');
});

add_action('wp_head', function () {
    $custom_css = get_option('affilink_custom_css', '');
    if ($custom_css) {
        echo '<style>' . wp_kses_post($custom_css) . '</style>';
    }
});
