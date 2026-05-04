<?php
/**
 * Theme functions and definitions.
 *
 * @package Developer_Portfolio
 */

function theme_style() 
{
    wp_enqueue_style("theme-style", get_template_directory_uri() . "/style.css");
}
add_action("wp_enqueue_scripts", "theme_style");

function post_thumbnail()
{
    add_theme_support("post-thumbnails", array("post"));
}
add_action("after_setup_theme", "post_thumbnail");