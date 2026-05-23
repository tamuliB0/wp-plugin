<?php
/**
 * Header template.
 *
 * @package Developer_Portfolio
 */

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php bloginfo( 'name' ); ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<nav>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'header-menu',
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
	<?php wp_body_open(); ?>
