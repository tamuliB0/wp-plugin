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

function mytheme_register_menus()
{
    register_nav_menus(array(
            "header-menu" => __("Primary Menu"),
            "footer-menu" => __("Footer Menu")
        )
    );
}
add_action("init", "mytheme_register_menus");

function mytheme_register_sidebar()
{
    register_sidebar(array(
            "name" => __("Primary sidebar", "myWidget"),
            "id" => "primary"
        ));
}
add_action("widgets_init", "mytheme_register_sidebar");