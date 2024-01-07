<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php bloginfo('name'); ?></title>

    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <?php wp_head(); ?>

    <style>
        /* Add custom styles here */
    </style>
</head>

<body <?php body_class(); ?>>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
           
            <?php
            // Display Navigation Menu
            wp_nav_menu(array(
                'theme_location' => 'primary-menu',
                'menu_class'     => 'navbar-nav',
                'container_class' => 'collapse navbar-collapse',
                'container_id'    => 'navbarNav',
                'fallback_cb'    => '__return_false',
            ));
            ?>
        </nav>
    </header>