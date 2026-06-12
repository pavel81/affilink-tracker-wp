<?php
// Získání všech unikátních affiliate sítí z databáze
global $wpdb;
$table_name = $wpdb->prefix . 'affilink_urls';
$results = $wpdb->get_col("SELECT DISTINCT affiliate_network FROM $table_name");

echo '<div class="affilink-network-filters">';
foreach ($results as $network) {
    $label = !empty($network) ? esc_html($network) : 'Neznámé';
    $data_attr = !empty($network) ? esc_attr($network) : 'unknown';
    echo '<label class="affilink-network-label" data-network="' . $data_attr . '">' . $label . '</label>';
}
echo '</div>';
?>
