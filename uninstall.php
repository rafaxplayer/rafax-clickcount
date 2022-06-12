<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
global $wpdb;
$table = $wpdb->prefix . 'rafax_clickcount';

$wpdb->query("DROP TABLE IF EXISTS $table");
    
