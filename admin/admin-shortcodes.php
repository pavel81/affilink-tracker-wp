<?php
add_action('admin_menu', function () {
    add_menu_page('Affiliate Links', 'Affilink Tracker', 'manage_options', 'affilink_tracker', 'panda_affilink_tracker_page');
});

function panda_affilink_tracker_page() {
    echo '<div class="wrap"><h1>Affiliate Links SSL Kontrola</h1>';
    $links = get_option('affilink_links', []);
    foreach ($links as $link) {
        $ssl_status = panda_get_ssl_status($link['url']);
        $status_icon = $ssl_status === 'ok' ? '✅' : '❗';
        echo '<p><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_html($link['url']) . '</a> ';
        echo '<span class="ssl-status">' . $status_icon . '</span> ';
        echo '<a href="#" class="refresh-ssl" data-url="' . esc_attr($link['url']) . '">🔄</a></p>';
    }
    echo '<form method="post"><input type="submit" name="clear_ssl_cache" value="Vymazat SSL keš" class="button"></form>';
    echo '</div>';
}

add_action('admin_init', function () {
    if (isset($_POST['clear_ssl_cache'])) {
        delete_transient('panda_ssl_cache');
    }
    wp_enqueue_script('affilink-admin', plugin_dir_url(__FILE__) . '../assets/ssl-check.js', ['jquery'], null, true);
    wp_enqueue_style('affilink-style', plugin_dir_url(__FILE__) . '../assets/admin-style.css');
    wp_localize_script('affilink-admin', 'affilink_ajax', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ssl_check')]);
});

add_action('wp_ajax_panda_check_ssl', function () {
    check_ajax_referer('ssl_check', 'nonce');
    if (isset($_POST['url'])) {
        $url = esc_url_raw($_POST['url']);
        $status = panda_check_ssl_status($url);
        wp_send_json_success(['status' => $status]);
    }
    wp_send_json_error();
});

?>
<!-- Modal structure -->
<div id="ssl-modal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; border:1px solid #ccc; padding:20px; z-index:10000; box-shadow:0 0 10px rgba(0,0,0,0.3);">
    <h2>Výsledek SSL kontroly</h2>
    <div id="ssl-modal-content">Načítání...</div>
    <button id="ssl-modal-close" class="button">Zavřít</button>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('ssl-modal');
    const content = document.getElementById('ssl-modal-content');
    const closeBtn = document.getElementById('ssl-modal-close');

    document.querySelectorAll('.refresh-ssl').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            content.textContent = 'Probíhá kontrola...';
            modal.style.display = 'block';
            fetch(affilink_ajax.ajax_url, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'panda_check_ssl',
                    nonce: affilink_ajax.nonce,
                    url: url
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    content.textContent = 'Stav SSL: ' + data.data.status;
                } else {
                    content.textContent = 'Chyba při načítání SSL statusu.';
                }
            })
            .catch(() => {
                content.textContent = 'Chyba připojení.';
            });
        });
    });

    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });
});
</script>
