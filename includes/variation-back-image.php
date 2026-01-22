<?php

/* ---------------------------------------------------
 * ADD BACK IMAGE FIELD TO VARIATIONS (ADMIN)
 * --------------------------------------------------- */

add_action('woocommerce_variation_options', 'add_back_image_field_to_variations', 10, 3);
function add_back_image_field_to_variations($loop, $variation_data, $variation) {

    $image_id  = get_post_meta($variation->ID, '_back_image_id', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>

    <div class="form-row form-row-full back-image-wrapper">
        <label><strong>Back Image</strong></label>

        <!-- IMPORTANT: LOOP INDEX -->
        <input type="hidden"
               class="back-image-id"
               name="back_image_id[<?php echo esc_attr($loop); ?>]"
               value="<?php echo esc_attr($image_id); ?>">

        <button type="button" class="button upload-back-image">
            Upload Back Image
        </button>

        <button type="button"
                class="button remove-back-image"
                style="<?php echo empty($image_id) ? 'display:none;' : ''; ?>">
            Remove
        </button>

        <div class="back-image-preview" style="margin-top:8px;">
            <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width:100px;">
            <?php endif; ?>
        </div>
    </div>

    <?php
}

/* ---------------------------------------------------
 * SAVE BACK IMAGE (FINAL & RELIABLE)
 * --------------------------------------------------- */

add_action('woocommerce_process_product_meta_variable', 'save_back_images_for_variations');
function save_back_images_for_variations($product_id) {

    if (!isset($_POST['variable_post_id'], $_POST['back_image_id'])) {
        return;
    }

    foreach ($_POST['variable_post_id'] as $index => $variation_id) {

        if (!isset($_POST['back_image_id'][$index])) {
            continue;
        }

        $image_id = absint($_POST['back_image_id'][$index]);

        if ($image_id) {
            update_post_meta($variation_id, '_back_image_id', $image_id);
        } else {
            delete_post_meta($variation_id, '_back_image_id');
        }
    }
}

///////////////////////////////////////
// ===============================
// FRONT / BACK VIEW (FRONTEND)
// ===============================

add_action('wp_enqueue_scripts', 'sp_enqueue_front_back_assets');

function sp_enqueue_front_back_assets() {

    if ( ! is_product() ) {
        return;
    }

    $product = wc_get_product( get_the_ID() );

    if ( ! $product || ! $product->is_type('variable') ) {
        return;
    }

    // CSS
    wp_enqueue_style(
        'sp-front-back-css',
        get_stylesheet_directory_uri() . '/assets/css/variation-back-image.css',
        array(),
        time()
    );

    // JS
    wp_enqueue_script(
        'sp-front-back-js',
        get_stylesheet_directory_uri() . '/assets/js/variation-back-image.js',
        array('jquery'),
        time(),
        true
    );

    // Pass variation images to JS
    $variation_images = [];

    foreach ( $product->get_available_variations() as $variation ) {

        $vid = $variation['variation_id'];

        $variation_images[$vid] = [
            'front' => $variation['image']['full_src'] ?? '',
            'back'  => get_post_meta( $vid, '_back_image_id', true )
                ? wp_get_attachment_url( get_post_meta( $vid, '_back_image_id', true ) )
                : ''
        ];
    }

    wp_localize_script(
        'sp-front-back-js',
        'variationImages',
        $variation_images
    );
}


// ===============================
// FRONT / BACK BUTTON MARKUP
// ===============================
add_action('woocommerce_after_single_product_summary', 'sp_render_front_back_buttons', 5);
function sp_render_front_back_buttons() {
    if (!is_product()) return;
    ?>
    <div class="sp-front-back-wrapper">
        <button type="button" class="sp-front-back-btn active" data-view="front">Front</button>
        <button type="button" class="sp-front-back-btn" data-view="back">Back</button>
    </div>
    <?php
}



/*============== SAVE DATA TO CART ================ */
add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id) {

  if (isset($_POST['sp_customization'])) {
    $cart_item_data['sp_customization'] = json_decode(
      wp_unslash($_POST['sp_customization']),
      true
    );

    $cart_item_data['unique_key'] = md5(microtime() . rand());
  }

  return $cart_item_data;
}, 10, 2);



/*============== SHOW IN CART / CHECKOUT ================ */
add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {

  if (empty($cart_item['sp_customization'])) return $item_data;

  $data = $cart_item['sp_customization'];

  if (!empty($data['front']['hasText']) || !empty($data['front']['hasImage'])) {
    $item_data[] = [
      'name' => 'Front Customization',
      'value' => 'Yes'
    ];
  }

  if (!empty($data['back']['hasText']) || !empty($data['back']['hasImage'])) {
    $item_data[] = [
      'name' => 'Back Customization',
      'value' => 'Yes'
    ];
  }

  return $item_data;
}, 10, 2);


/*============== APPLY FINAL PRICE ================ */
add_action('woocommerce_before_calculate_totals', function ($cart) {

  if (is_admin() && !defined('DOING_AJAX')) return;

  foreach ($cart->get_cart() as $cart_item) {

    if (!empty($cart_item['sp_customization']['pricing']['final'])) {

      $price = (float) $cart_item['sp_customization']['pricing']['final'];

      if ($price > 0) {
        $cart_item['data']->set_price($price);
      }
    }
  }
}, 999);

/*============== ADMIN VIEW ================ */
add_action('woocommerce_admin_order_item_headers', function () {
  echo '<th>Customization</th>';
});

add_action('woocommerce_admin_order_item_values',
  function ($product, $item) {

    $data = $item->get_meta('Customization Data');
    if (!$data) {
      echo '<td>-</td>';
      return;
    }

    echo '<td><pre style="white-space:pre-wrap;">' .
         esc_html($data) .
         '</pre></td>';
  },
  10,
  2
);

/*============== Show Size & Color in Cart (FIX ATTRIBUTES) ================ */
add_filter('woocommerce_get_item_data', 'sp_show_variations_in_cart', 10, 2);
function sp_show_variations_in_cart($item_data, $cart_item) {

    if (!empty($cart_item['variation'])) {
        foreach ($cart_item['variation'] as $key => $value) {
            $item_data[] = [
                'name'  => wc_attribute_label(str_replace('attribute_', '', $key)),
                'value' => $value
            ];
        }
    }

    return $item_data;
}
