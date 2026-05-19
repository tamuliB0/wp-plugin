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

	$table_name      = $wpdb->prefix . 'reading_list';
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
	$status     = sanitize_text_field( $atts['status'] );
	$cache_key  = 'reading_list_status_' . ( '' === $status ? 'all' : $status );

	$results = wp_cache_get( $cache_key, 'reading_list' );

	if ( false === $results ) {
		if ( '' !== $atts['status'] ) {
			$sql = $wpdb->prepare(
				'SELECT title, author, status
				FROM %i WHERE status = %s
				ORDER BY created_at DESC',
				$table_name,
				$status
			);
		} else {
			$sql = $wpdb->prepare(
				'SELECT title, author, status
				FROM %i ORDER BY created_at DESC',
				$table_name
			);
		}
		$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared
		wp_cache_set( $cache_key, $results, 'reading_list' );
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

/**
 * Register admin menu page
 */
function reading_list_options_page() {
	add_menu_page(
		__( 'Reading List', 'reading-list' ),
		'Reading List',
		'manage_options',
		'reading_list',
		'reading_list_options_page_html'
	);
}
add_action( 'admin_menu', 'reading_list_options_page' );

/**
 * Handles add/update form submission logic.
 */
function reading_list_form_submission_logic() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! isset( $_POST['title'], $_POST['author'], $_POST['status'], $_POST['notes'] ) ) {
		return;
	}
	global $wpdb;
	$table_name = $wpdb->prefix . 'reading_list';
	$title      = sanitize_text_field( wp_unslash( $_POST['title'] ) );
	$author     = sanitize_text_field( wp_unslash( $_POST['author'] ) );
	$status     = sanitize_text_field( wp_unslash( $_POST['status'] ) );
	$notes      = sanitize_textarea_field( wp_unslash( $_POST['notes'] ) );

	if ( empty( $title ) ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'message' => 'error',
				),
				menu_page_url( 'reading_list', false )
			)
		);
		exit;
	}
	$data   = array(
		'title'  => $title,
		'author' => $author,
		'status' => $status,
		'notes'  => $notes,
	);
	$format = array(
		'%s',
		'%s',
		'%s',
		'%s',
	);
	if ( ! empty( $_POST['book_id'] ) ) {
		check_admin_referer( 'edit_book_action', 'edit_book_nonce' );

		$book_id = absint( $_POST['book_id'] );

		$updated = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$table_name,
			$data,
			array(
				'id' => $book_id,
			),
			$format,
			array(
				'%d',
			)
		);
		if ( false !== $updated ) {
			wp_cache_delete( 'reading_list_admin_all', 'reading_list' );

			wp_safe_redirect(
				add_query_arg(
					array(
						'message' => 'updated',
					),
					menu_page_url( 'reading_list', false )
				)
			);
			exit;
		}
	} else {
			check_admin_referer( 'add_book_action', 'add_book_nonce' );
			$inserted = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$table_name,
				array(
					'title'  => $title,
					'author' => $author,
					'status' => $status,
					'notes'  => $notes,
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
				)
			);
		if ( $inserted ) {
			wp_cache_delete( 'reading_list_admin_all', 'reading_list' );
			wp_safe_redirect(
				add_query_arg(
					array(
						'message' => 'success',
					),
					menu_page_url( 'reading_list', false )
				)
			);
			exit;
		}
	}
}
add_action( 'admin_init', 'reading_list_form_submission_logic' );

/**
 * Handles deleting a book from the reading list
 */
function reading_list_delete_book() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
	if ( ! isset( $_GET['nonce'], $_GET['id'] )
		|| 'delete_book' !== ( $action ) ) {
			return;
	}
	check_admin_referer( 'delete_book', 'nonce' );
	global $wpdb;
	$table_name = $wpdb->prefix . 'reading_list';
	$book_id    = absint( $_GET['id'] );

	$deleted = $wpdb->delete(
		$table_name,
		array(
			'id' => $book_id,
		),
		array(
			'%d',
		)
	);
	wp_cache_delete( 'reading_list_admin_all', 'reading_list' );
	wp_safe_redirect(
		add_query_arg(
			array(
				'message' => $deleted ? 'deleted' : 'delete_error',
			),
			menu_page_url( 'reading_list', false )
		)
	);
	exit;
}
add_action( 'admin_init', 'reading_list_delete_book' );
/**
 * Display the admin page for reading list
 */
function reading_list_options_page_html() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'reading_list';

	$edit_book = null;
	if ( isset( $_GET['id'] ) ) {
		$book_id = absint( $_GET['id'] );

		$sql       = $wpdb->prepare(
			'SELECT * FROM %i WHERE id = %d',
			$table_name,
			$book_id,
		);
		$edit_book = $wpdb->get_row( $sql );
	}

	$message = isset( $_GET['message'] ) ? sanitize_text_field( wp_unslash( $_GET['message'] ) ) : '';
	if ( 'success' === $message ) { // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Book added successfully.', 'reading-list' ); ?></p>
		</div>
		<?php
	}
	if ( 'updated' === $message ) { // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Book updated successfully.', 'reading-list' ); ?></p>
		</div>
		<?php
	}
	if ( 'error' === $message ) { // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Title is required.', 'reading-list' ); ?></p>
		</div>
		<?php
	}
	if ( 'deleted' === $message ) { // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Book deleted successfully.', 'reading-list' ); ?></p>
		</div>
		<?php
	}
	if ( 'delete_error' === $message ) { // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Failed to delete book.', 'reading-list' ); ?></p>
		</div>
		<?php
	}

	$cache_key = 'reading_list_admin_all';
	$results   = wp_cache_get( $cache_key, 'reading_list' );

	if ( false === $results ) {
		$sql     = $wpdb->prepare(
			'SELECT * 
			FROM %i 
			ORDER BY created_at DESC',
			$table_name
		);
		$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared
		wp_cache_set( $cache_key, $results, 'reading_list' );
	}
	?>
	<?php if ( $edit_book ) : ?>
		<h3><?php esc_html_e( 'Edit book', 'reading_list' ); ?></h3>
		<form method="post">
			<?php wp_nonce_field( 'edit_book_action', 'edit_book_nonce' ); ?>
			<input type="hidden" name="book_id" value="<?php echo esc_attr( $edit_book->id ); ?>">
			<label>Title:</label>
			<input type="text" name="title" value="<?php echo esc_attr( $edit_book->title ); ?>">
			<label>Author:</label>
			<input type="text" name="author" value="<?php echo esc_attr( $edit_book->author ); ?>">
			<label>Status:</label>
			<input type="text" name="status" value="<?php echo esc_attr( $edit_book->status ); ?>">
			<label>Notes:</label>
			<input type="text" name="notes" value="<?php echo esc_attr( $edit_book->notes ); ?>">
			
			<input type="submit" value="<?php esc_attr_e( 'Update', 'reading-list' ); ?>">
			<a href="<?php echo esc_url( menu_page_url( 'reading_list', false ) )?>" >Go back</a>
		</form>

	<?php else : ?>
			<h1><?php echo esc_html( 'My ' . get_admin_page_title() ); ?></h1>
	<table>
		<thead>
			<tr>
				<th><?php esc_html_e( 'Title', 'reading-list' ); ?></th>
				<th><?php esc_html_e( 'Author', 'reading-list' ); ?></th>
				<th><?php esc_html_e( 'Status', 'reading-list' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'reading-list' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $results as $result ) : ?>
				<?php
				$delete_url = add_query_arg(
					array(
						'action' => 'delete_book',
						'id'     => $result->id,
						'nonce'  => wp_create_nonce( 'delete_book' ),
					),
					menu_page_url( 'reading_list', false ),
				);
				?>
				<?php
				$edit_url = add_query_arg(
					array(
						'action' => 'edit_book',
						'id'     => $result->id,
					),
					menu_page_url( 'reading_list', false )
				);
				?>
			<tr>
				<td><?php echo esc_html( $result->title ); ?></td>
				<td><?php echo esc_html( $result->author ); ?></td>
				<td><?php echo esc_html( $result->status ); ?></td>
				<td>
					<a href="<?php echo esc_url( $edit_url ); ?>">Edit</a> |
					<a href="<?php echo esc_url( $delete_url ); ?>">Delete</a>
				</td>				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
		<h3><?php esc_html_e( 'Add new book', 'reading_list' ); ?></h3>
		<form method="post">
			<?php wp_nonce_field( 'add_book_action', 'add_book_nonce' ); ?>
			<label>Title:</label>
			<input type="text" name="title">
			<label>Author:</label>
			<input type="text" name="author">
			<label>Status:</label>
			<input type="text" name="status">
			<label>Notes:</label>
			<input type="text" name="notes">
			<input type="submit" value="<?php esc_attr_e( 'Add book', 'reading-list' ); ?>">
		</form>
		<?php
	endif;
}