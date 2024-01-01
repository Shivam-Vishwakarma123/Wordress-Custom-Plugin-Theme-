<?php
// Enqueue styles and scripts
function custom_theme_scripts()
{
    // Enqueue CSS
    wp_enqueue_style('custom-theme-style', get_template_directory_uri() . '/assets/style.css', array(), '1.0', 'all');

    // Enqueue JS
    wp_enqueue_script('custom-theme-script', get_template_directory_uri() . '/assets/script.js', array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'custom_theme_scripts');





// Add custom admin menu page
function custom_theme_admin_menu()
{
    add_menu_page(
        'Custom Theme Settings',
        'Theme Settings',
        'manage_options',
        'custom-theme-settings',
        'custom_theme_settings_page',
        'dashicons-admin-generic',
        20
    );
}

add_action('admin_menu', 'custom_theme_admin_menu');

// Callback function for the custom admin menu page
function custom_theme_settings_page()
{
?>
    <div class="wrap">
        <h2>Custom Theme Settings</h2>
        <p>This is your custom theme settings page content.</p>
    </div>
<?php
}
