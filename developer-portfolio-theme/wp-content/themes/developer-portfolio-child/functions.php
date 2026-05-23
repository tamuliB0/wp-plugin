<?php
/**
 * Theme functions and definitions.
 *
 * @package Developer_Portfolio_Child
 */

/**
 * Enqueue parent and child theme styles
 */
function child_theme_style() {
	wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_style( 'child-theme-style', get_stylesheet_uri(), array( 'theme-style' ), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'child_theme_style' );

/**
 * Setup child theme supports
 */
function child_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
}
add_action( 'after_setup_theme', 'child_theme_setup' );

/**
 * Register primary sidebar
 */
function child_theme_register_sidebar() {
	register_sidebar(
		array(
			'name' => __( 'Child Primary sidebar', 'developer-portfolio-child' ),
			'id'   => 'child-primary',
		)
	);
}
add_action( 'widgets_init', 'child_theme_register_sidebar' );
