<?php
/**
 * WooCommerce Custom Functions
 */

// Add custom HTML after product summary
add_action('woocommerce_after_single_product_summary', 'tshirt_custom_after_summary', 5);

function tshirt_custom_after_summary() {
    echo '<div class="my-custom-block">Custom content</div>';
}
