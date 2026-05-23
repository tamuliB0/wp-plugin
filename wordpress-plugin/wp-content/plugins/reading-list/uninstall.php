<?php
/**
 * Uninstall Reading List plugin.
 *
 * @package ReadingList
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'reading_list_per_page' );

global $wpdb;
$table_name = $wpdb->prefix . 'reading_list';
$wpdb->query(
	$wpdb->prepare(
		'DROP TABLE IF EXISTS %i',
		$table_name
	)
);
