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

    /* Custom child theme css file */
    wp_enqueue_script(
        'child-custom-css',
        get_stylesheet_directory_uri() . '/assets/css/custom.css',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/assets/css/custom.css'),
        true
    );
}
add_action('wp_enqueue_scripts', 'tshirt_child_enqueue_scripts');

// disable for posts
add_filter('use_block_editor_for_post', '__return_false', 10);

// disable for post types
add_filter('use_block_editor_for_post_type', '__return_false', 10);




// Add custom HTML after product summary
add_action( 'woocommerce_after_single_product_summary', 'tshirt_custom_after_summary', 5 );
function tshirt_custom_after_summary() {
    echo '<div class="my-custom-block">Custom content</div>';
}
