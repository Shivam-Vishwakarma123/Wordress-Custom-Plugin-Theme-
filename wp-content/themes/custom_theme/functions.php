<?php
// Add support for menus
function custom_blog_register_menus()
{
    register_nav_menus(array(
        'primary-menu' => esc_html__('Primary Menu', 'custom-blog-theme'),
    ));
}

add_action('after_setup_theme', 'custom_blog_register_menus');

// Register widget area
function custom_blog_widgets_init()
{
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'custom-blog-theme'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'custom-blog-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}

add_action('widgets_init', 'custom_blog_widgets_init');
