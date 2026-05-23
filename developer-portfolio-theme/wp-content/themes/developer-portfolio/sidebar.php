<?php
/**
 * Template for sidebar.
 *
 * @package Developer_Portfolio
 */

if ( is_active_sidebar( 'primary' ) ) : ?>
	<?php dynamic_sidebar( 'primary' ); ?>
	<?php
endif;
