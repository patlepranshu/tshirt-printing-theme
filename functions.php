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
