<?php
function panda_check_ssl_status($url) {
    $parts = parse_url($url);
    if ($parts['scheme'] !== 'https') return 'fail';
    $host = $parts['host'];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_exec($ch);
    $error = curl_errno($ch);
    curl_close($ch);

    if ($error === 0) return 'ok';
    return 'fail';
}

function panda_get_ssl_status($url) {
    $cache = get_transient('panda_ssl_cache');
    if (!is_array($cache)) $cache = [];
    if (isset($cache[$url])) return $cache[$url];

    $status = panda_check_ssl_status($url);
    $cache[$url] = $status;
    set_transient('panda_ssl_cache', $cache, 12 * HOUR_IN_SECONDS);
    return $status;
}
