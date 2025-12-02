<?php
/**
 * WooCommerce Custom Functions
 */

// Add custom HTML after product summary
add_action('woocommerce_after_single_product_summary', 'tshirt_custom_after_summary', 5);

function tshirt_custom_after_summary() {
    echo '<div class="my-custom-block">Custom content</div>';
}


// Show upload field before add to cart
add_action( 'woocommerce_before_add_to_cart_button', 'pp_tshirt_upload_field' );
function pp_tshirt_upload_field() {
    echo '<div class="pp-upload"><label for="pp_design">Upload design (PNG/JPG)</label><input type="file" name="pp_design" id="pp_design" accept="image/*" /></div>';
}


// Save upload to cart item data
add_filter( 'woocommerce_add_cart_item_data', 'pp_save_upload_to_cart', 10, 3 );
function pp_save_upload_to_cart( $cart_item_data, $product_id, $variation_id ) {
    if ( ! empty( $_FILES['pp_design']['name'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $uploaded = wp_handle_upload( $_FILES['pp_design'], array( 'test_form' => false ) );
        if ( isset( $uploaded['url'] ) ) {
            $cart_item_data['pp_design'] = $uploaded['url'];
            // prevent items merging
            $cart_item_data['unique_key'] = md5( microtime() . rand() );
        }
    }
    return $cart_item_data;
}


// Show upload in cart item meta
add_filter( 'woocommerce_get_item_data', 'pp_show_upload_cart', 10, 2 );
function pp_show_upload_cart( $item_data, $cart_item ) {
    if ( ! empty( $cart_item['pp_design'] ) ) {
        $item_data[] = array(
            'key' => 'Design',
            'value' => '<img src="'.esc_url( $cart_item['pp_design'] ).'" style="max-width:80px;height:auto;" />',
        );
    }
    return $item_data;
}


// Save it to the order (order items meta) on checkout
add_action( 'woocommerce_checkout_create_order_line_item', 'pp_add_upload_to_order_item', 10, 4 );
function pp_add_upload_to_order_item( $item, $cart_item_key, $values, $order ) {
    if ( ! empty( $values['pp_design'] ) ) {
        $item->add_meta_data( 'Design', $values['pp_design'] );
    }
}



