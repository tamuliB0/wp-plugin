<?php
/**
 * Footer template.
 *
 * @package Developer_Portfolio
 */

wp_nav_menu(
	array(
		'theme_location' => 'footer-menu',
		'fallback_cb'    => false,
	)
); ?>
<p style="color:<?php echo esc_attr( get_theme_mod( 'accent_color', '#f72525' ) ); ?>">
	<?php echo esc_html( get_theme_mod( 'footer_text', '2026 Developer Portfolio' ) ); ?>
</p>
<?php wp_footer(); ?>
</body>
</html>