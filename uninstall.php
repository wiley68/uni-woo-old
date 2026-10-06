<?php
// If uninstall not called from WordPress, then exit.
if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}
global $wpdb;
// remove plugin options
$options = array(
    'unipayment_status',
    'unipayment_unicid',
    'unipayment_reklama',
    'unipayment_cart',
    'unipayment_debug'
);
foreach ($options as $option) {
    delete_option( $option );
    delete_site_option( $option );
}
// Clear any cached data that has been removed.
wp_cache_flush();