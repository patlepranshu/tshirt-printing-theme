<?php
add_action('woocommerce_before_single_product', 'sp_product_banner', 5);
function sp_product_banner() {

    if ( ! is_product() ) return;

    global $product;

    // Default banner image
    $banner_img = get_stylesheet_directory_uri() . '/assets/images/product-banner.jpg';

    // Optional: Use product featured image as banner
    if ( has_post_thumbnail() ) {
        $banner_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    }
    ?>

    <div class="sp-product-banner" style="background-image:url('/wp-content/uploads/2026/01/bg.jpg');">
        <div class="sp-banner-overlay">
            <h1 class="sp-product-title">
                <?php echo esc_html( get_the_title() ); ?>
            </h1>
        </div>
    </div>

    <?php
}

/**====================== Banner for woocoomerce pages =============**/
// WooCommerce Page Banner (Cart, Checkout, Order Received)

// CART PAGE
// add_action('woocommerce_before_cart', 'tshirt_wc_cart_banner');
// function tshirt_wc_cart_banner() {
//     tshirt_wc_banner_output('Your Shopping Cart');
// }

// // CHECKOUT PAGE
// add_action('woocommerce_before_checkout_form', 'tshirt_wc_checkout_banner', 5);
// function tshirt_wc_checkout_banner() {
//     tshirt_wc_banner_output('Secure Checkout');
// }

// // ORDER RECEIVED PAGE
// add_action('woocommerce_thankyou', 'tshirt_wc_thankyou_banner', 5);
// function tshirt_wc_thankyou_banner() {
//     tshirt_wc_banner_output('Thank You for Your Order');
// }

// // Banner HTML
// function tshirt_wc_banner_output($title) {
//     echo '
//     <section class="wc-page-banner">
//         <div class="wc-banner-overlay"></div>
//         <div class="wc-banner-content">
//             <h1>' . esc_html($title) . '</h1>
//         </div>
//     </section>';
// }

