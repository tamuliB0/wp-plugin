<?php
/**
 * Plugin Name: Reading List Manager
 * Description: A personal reading list for your WordPress site.
 * Version:     1.0.0
 * Author:      tamuliB0
 * Text Domain: reading-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'reading_list_activate' );
function reading_list_activate() {
	// TODO: create the database table in M1
}

register_deactivation_hook( __FILE__, 'reading_list_deactivate' );
function reading_list_deactivate() {
	// TODO: clean up in M5
}
