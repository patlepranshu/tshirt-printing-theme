<?php
/**
 * Load Child Theme Modules
 */

// Enqueue scripts & styles
require_once get_stylesheet_directory() . '/includes/enqueue.php';

// WooCommerce customizations
require_once get_stylesheet_directory() . '/includes/woo-functions.php';

// Custom WordPress hooks
require_once get_stylesheet_directory() . '/includes/custom-hooks.php';

// Custom WordPress variation back image hooks
require_once get_stylesheet_directory() . '/includes/variation-back-image.php';

// Custom WordPress banner hooks
require_once get_stylesheet_directory() . '/includes/single-product-banner.php';