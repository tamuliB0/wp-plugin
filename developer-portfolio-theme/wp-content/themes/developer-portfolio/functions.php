<?php
/**
 * Theme functions and definitions.
 *
 * @package Developer_Portfolio
 */

/**
 * Enqueue parent theme style
 */
function theme_style() {
	wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_style' );

/**
 * Add theme support features
 */
function post_thumbnail() {
	add_theme_support( 'post-thumbnails', array( 'post' ) );
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
}
add_action( 'after_setup_theme', 'post_thumbnail' );

/**
 * Register theme navigation menus
 */
function developer_portfolio_theme_register_menus() {
	register_nav_menus(
		array(
			'header-menu' => __( 'Primary Menu', 'developer-portfolio' ),
			'footer-menu' => __( 'Footer Menu', 'developer-portfolio' ),
		)
	);
}
add_action( 'init', 'developer_portfolio_theme_register_menus' );

/**
 * Register theme widgets
 */
function developer_portfolio_theme_register_sidebar() {
	register_sidebar(
		array(
			'name' => __( 'Primary sidebar', 'developer-portfolio' ),
			'id'   => 'primary',
		)
	);
}
add_action( 'widgets_init', 'developer_portfolio_theme_register_sidebar' );

/**
 * Register settings for Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Object customizer.
 */
function developer_portfolio_theme_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'footer_section',
		array(
			'title'    => __( 'Footer Settings', 'developer-portfolio' ),
			'priority' => 160,
		)
	);

	$wp_customize->add_setting(
		'footer_text',
		array(
			'default'           => '2026 Developer Portfolio',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'footer_text',
		array(
			'label'   => __( 'Footer Text', 'developer-portfolio' ),
			'section' => 'footer_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_section(
		'color_section',
		array(
			'title'    => __( 'Color Settings', 'developer-portfolio' ),
			'priority' => 110,
		)
	);

	$wp_customize->add_setting(
		'accent_color',
		array(
			'default'           => '#f72525',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'accent_color',
			array(
				'label'   => __( 'Accent Color', 'developer-portfolio' ),
				'section' => 'color_section',
				'type'    => 'color',
			)
		)
	);
}
add_action( 'customize_register', 'developer_portfolio_theme_customize_register' );
