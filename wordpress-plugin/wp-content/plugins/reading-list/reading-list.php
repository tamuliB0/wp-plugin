<?php
/**
 * Plugin Name: Reading List Manager
 * Description: A personal reading list for your WordPress site.
 * Version:     1.0.0
 * Author:      tamuliB0
 * Text Domain: reading-list
 *
 * @package Reading List
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'reading_list_activate' );

/**
 * Runs on plugin activation.
 */
function reading_list_activate() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'reading_list';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(255) NOT NULL,
	author varchar(255) NOT NULL,
	status varchar(20) NOT NULL DEFAULT 'to-read',
	notes text,
	created_at datetime NOT NULL,
	PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

register_deactivation_hook( __FILE__, 'reading_list_deactivate' );

/**
 * Runs on plugin deactivation.
 */
function reading_list_deactivate() {
}

/**
 * Display reading list shortcode output
 *
 * @param array $atts Shortcode attributes. Default empty.
 */
function reading_list_shortcode( $atts = array() ) {

	$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	$atts = shortcode_atts( array( 'status' => '' ), $atts, 'reading_list' );

	global $wpdb;
	$table_name = $wpdb->prefix . 'reading_list';

	if ( '' !== $atts['status'] ) {
		$results = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT title, author, status
				FROM %i WHERE status = %s
				ORDER BY created_at DESC',
				$table_name,
				$atts['status']
			)
		);
	} else {
		$results = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT title, author, status
				FROM %i ORDER BY created_at DESC',
				$table_name
			)
		);
	}
	ob_start();
	?>
	<table>
		<thead>
			<tr>
				<th><?php esc_html_e( 'Title', 'reading-list' ); ?></th>
				<th><?php esc_html_e( 'Author', 'reading-list' ); ?></th>
				<th><?php esc_html_e( 'Status', 'reading-list' ); ?></th>				
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $results as $result ) : ?>
			<tr>
				<td><?php echo esc_html( $result->title ); ?></td>
				<td><?php echo esc_html( $result->author ); ?></td>
				<td><?php echo esc_html( $result->status ); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php
	return ob_get_clean();
}
add_shortcode( 'reading_list', 'reading_list_shortcode' );