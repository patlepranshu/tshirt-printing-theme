<?php
/**
 * Enqueue Parent + Child CSS and Bootstrap via CDN
 */

function tshirt_child_enqueue_scripts() {

    /* Parent Theme CSS */
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    /* Child Theme CSS */
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    /* Bootstrap CSS CDN */
    wp_enqueue_style(
        'bootstrap-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
    );

    /* Bootstrap JS Bundle (with Popper) */
    wp_enqueue_script(
        'bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        array('jquery'),
        null,
        true
    );

    /* Custom child theme JS file */
    wp_enqueue_script(
        'child-custom-js',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/assets/js/custom.js'),
        true
    );

    /* Custom child theme CSS file */
    wp_enqueue_style(
        'child-custom-css',
        get_stylesheet_directory_uri() . '/assets/css/custom.css',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/css/custom.css')
    );
}
add_action('wp_enqueue_scripts', 'tshirt_child_enqueue_scripts');