<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ------------------------------------------------------
   Parent + Child + Bootstrap + Custom CSS/JS + Fabric + Fonts
------------------------------------------------------- */
function tshirt_child_enqueue_scripts() {

    // Parent Theme CSS
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    // Child Theme CSS
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        filemtime( get_stylesheet_directory() . '/style.css' )
    );

    // Optional Bootstrap (remove if not needed)
    wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' );
    wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true );

    // Core child script (site-wide)
    wp_enqueue_script(
        'child-custom-js',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array('jquery'),
        filemtime( get_stylesheet_directory() . '/assets/js/custom.js' ),
        true
    );

    // Core child css
    wp_enqueue_style(
        'child-custom-css',
        get_stylesheet_directory_uri() . '/assets/css/custom.css',
        array(),
        filemtime( get_stylesheet_directory() . '/assets/css/custom.css' )
    );
}
add_action('wp_enqueue_scripts', 'tshirt_child_enqueue_scripts');


/* ------------------------------------------------------
   Customizer: Fabric.js, Customizer CSS/JS, fonts
   Only enqueued on single product pages to keep site fast
------------------------------------------------------- */
function sp_enqueue_customizer_assets() {

  // Fabric.js FIRST
  wp_enqueue_script(
    'fabric-js',
    'https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js',
    [],
    '5.3.0',
    true
  );

  // Your Customizer JS (AFTER Fabric)
  wp_enqueue_script(
    'sp-customizer',
    get_stylesheet_directory_uri() . '/assets/js/customizer.js',
    ['jquery', 'fabric-js'],
    time(),
    true
  );

  // Styles
  wp_enqueue_style(
    'sp-customizer-css',
    get_stylesheet_directory_uri() . '/assets/css/customizer.css',
    [],
    time()
  );
}

add_action('wp_enqueue_scripts', 'sp_enqueue_customizer_assets');


wp_enqueue_style(
  'sp-google-fonts',
  'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&
family=Roboto:wght@300;400;500;700&
family=Montserrat:wght@300;400;500;600;700&
family=Open+Sans:wght@300;400;600;700&
family=Lato:wght@300;400;700&
family=Playfair+Display:wght@400;700&
family=Oswald:wght@300;400;700&
family=Raleway:wght@300;400;700&
family=Nunito:wght@300;400;700&display=swap',
  [],
  null
);

/* ---------------------------------------------------
 * ENQUEUE ADMIN SCRIPT
 * --------------------------------------------------- */

add_action('admin_enqueue_scripts', 'enqueue_back_image_admin_assets');
function enqueue_back_image_admin_assets($hook) {

    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_script(
        'variation-back-image',
        get_stylesheet_directory_uri() . '/assets/js/variation-back-image.js',
        ['jquery'],
        time(),
        true
    );

    wp_enqueue_script(
        'variation-back-image-admin',
        get_stylesheet_directory_uri() . '/assets/js/variation-back-image-admin.js',
        ['jquery'],
        time(),
        true
    );

    wp_enqueue_style(
        'variation-back-image-css',
        get_stylesheet_directory_uri() . '/assets/css/variation-back-image.css',
        [],
        time()
    );
}




add_action('wp_enqueue_scripts', 'enqueue_josefin_sans_font');
function enqueue_josefin_sans_font() {

    wp_enqueue_style(
        'google-font-josefin-sans',
        'https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap',
        [],
        null
    );
}
