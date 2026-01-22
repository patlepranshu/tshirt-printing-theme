<?php
/**
 * TEXT CUSTOMIZER – STABLE BASE (STEP 1)
 */

/* ============================
   CANVAS OVER PRODUCT IMAGE
============================ */
add_action('woocommerce_product_thumbnails', 'sp_add_canvas_overlay', 20);
function sp_add_canvas_overlay() {
  ?>
  <div class="sp-canvas-overlay">
    <canvas id="tshirt-canvas"></canvas>
  </div>
  <?php
}

/* ============================
   TEXT CUSTOMIZATION PANEL
============================ */
add_action('woocommerce_before_add_to_cart_button', 'sp_text_customizer_ui');
function sp_text_customizer_ui() {
  ?>
  <div class="sp-customizer-panel">

    <h3>Customize Text</h3>

    <!-- TEXT INPUT -->
    <label>Add Text</label>
    <textarea
      id="cust-text-input"
      rows="3"
      placeholder="Enter your text here"></textarea>

    <!-- FONT FAMILY -->
    <label>Font Family</label>
    <select id="sp-font-family">
      <option value="Poppins">Poppins</option>
      <option value="Roboto">Roboto</option>
      <option value="Montserrat">Montserrat</option>
      <option value="Oswald">Oswald</option>
      <option value="Raleway">Raleway</option>
      <option value="Playfair Display">Playfair Display</option>
      <option value="Nunito">Nunito</option>
    </select>

 <!-- FONT WEIGHT -->
    <label>Font Weight</label>
<select id="sp-font-weight">
  <option value="300">Light</option>
  <option value="400" selected>Regular</option>
  <option value="500">Medium</option>
  <option value="600">Semi Bold</option>
  <option value="700">Bold</option>
  <option value="800">Extra Bold</option>
</select>

    <!-- FONT SIZE -->
    <label>Font Size</label>
    <input type="range" id="text-font-size" min="14" max="80" value="28">

    <!-- FONT COLOR -->
    <label>Font Color</label>
    <input type="color" id="text-font-color" value="#000000">

    <!-- TEXT STYLE -->
    <div class="text-style-controls">
      <button type="button" id="text-bold"><strong>B</strong></button>
      <button type="button" id="text-italic"><em>I</em></button>
      <button type="button" id="text-underline"><u>U</u></button>
    </div>

    <!-- TEXT ALIGN -->
    <div class="text-align-controls">
      <button type="button" data-align="left">Left</button>
      <button type="button" data-align="center" class="active">Center</button>
      <button type="button" data-align="right">Right</button>
    </div>

  </div>
  <?php
}



/* ============================
   IMAGE CUSTOMIZATION UI
============================ */
add_action('woocommerce_before_add_to_cart_button', 'sp_image_customizer_ui', 15);
function sp_image_customizer_ui() {
  ?>
  <div class="sp-image-customizer">

  <h3>Upload Image</h3>

  <!-- Custom button -->
  <button type="button" id="sp-upload-btn">
    Choose Image
  </button>

  <!-- Hidden real input -->
  <input
    type="file"
    id="cust-image-upload"
    accept="image/png,image/jpeg,image/jpg,image/svg+xml"
  />

  <!-- File name -->
  <span id="image-name" class="image-name"></span>

  <!-- Remove -->
  <button type="button" id="remove-image" style="display:none;">
    Remove Image
  </button>

</div>

<!-- image customizer HTML -->
<div class="sp-price-summary">
  <p>
    <strong>Customization:</strong>
    <span id="sp-addon-price">₹0</span>
  </p>
  <p>
    <strong>Final Price:</strong>
    <span id="sp-final-price">₹0</span>
  </p>
</div>

  <?php
}

/*=====================*/
add_filter(
    'woocommerce_order_item_display_meta_value',
    'sp_clean_canvas_json_display',
    10,
    3
);

function sp_clean_canvas_json_display($display_value, $meta, $item) {

    // If it's not JSON, leave it alone
    if (!is_string($meta->value)) {
        return $display_value;
    }

    // Only target Fabric.js canvas JSON
    if (strpos($meta->value, '"canvas"') === false) {
        return $display_value;
    }

    $data = json_decode($meta->value, true);

    if (!is_array($data) || !isset($data['canvas'])) {
        return $display_value;
    }

    $output = '';

    foreach ($data['canvas'] as $side => $canvas) {

        $hasText  = false;
        $hasImage = false;

        if (!empty($canvas['objects'])) {
            foreach ($canvas['objects'] as $obj) {
                if ($obj['type'] === 'textbox') {
                    $hasText = true;
                }
                if ($obj['type'] === 'image') {
                    $hasImage = true;
                }
            }
        }

        $output .= ucfirst($side) . ' Design: ';

        if ($hasText)  $output .= 'Text';
        if ($hasText && $hasImage) $output .= ' + ';
        if ($hasImage) $output .= 'Image';

        if (!$hasText && !$hasImage) {
            $output .= 'No customization';
        }

        $output .= '<br>';
    }

    return wp_kses_post($output);
}

/* =================== SAVE CUSTOMIZATION DATA TO CART =================== */
add_filter( 'woocommerce_get_item_data', 'sp_show_customization_in_cart', 10, 2 );
function sp_show_customization_in_cart( $item_data, $cart_item ) {

    if ( empty( $cart_item['sp_customization']['flags'] ) ) {
        return $item_data;
    }

    $flags   = $cart_item['sp_customization']['flags'];
    $pricing = $cart_item['sp_customization']['pricing'] ?? [];

    foreach ( ['front' => 'Front Design', 'back' => 'Back Design'] as $side => $label ) {

        if ( empty( $flags[$side] ) ) {
            continue;
        }

        $has_text  = ! empty( $flags[$side]['text'] );
        $has_image = ! empty( $flags[$side]['image'] );

        if ( ! $has_text && ! $has_image ) {
            continue;
        }

        if ( $has_text && $has_image ) {
            $value = 'Text + Image';
        } elseif ( $has_text ) {
            $value = 'Text';
        } else {
            $value = 'Image';
        }

        $item_data[] = [
            'name'  => $label,
            'value' => $value,
        ];
    }

    // ---- Pricing rows (CART + CHECKOUT ONLY) ----
    if ( ! empty( $pricing ) ) {
        $item_data[] = [
            'name'  => 'Base Price',
            'value' => wc_price( $pricing['base'] ),
        ];

        $item_data[] = [
            'name'  => 'Customization Price',
            'value' => wc_price( $pricing['addon'] ),
        ];
    }

    return $item_data;
}

/*=====================*/
add_action(
    'woocommerce_checkout_create_order_line_item',
    'sp_save_customization_to_order',
    10,
    4
);

function sp_save_customization_to_order( $item, $cart_item_key, $values, $order ) {

    if ( empty( $values['sp_customization'] ) ) {
        return;
    }

    $custom = $values['sp_customization'];

    // ---------- FRONT / BACK DESIGN ----------
    if ( ! empty( $custom['flags'] ) ) {

        foreach ( ['front' => 'Front Design', 'back' => 'Back Design'] as $side => $label ) {

            if ( empty( $custom['flags'][ $side ] ) ) {
                continue;
            }

            $has_text  = ! empty( $custom['flags'][ $side ]['text'] );
            $has_image = ! empty( $custom['flags'][ $side ]['image'] );

            if ( ! $has_text && ! $has_image ) {
                continue;
            }

            if ( $has_text && $has_image ) {
                $value = 'Text + Image';
            } elseif ( $has_text ) {
                $value = 'Text';
            } else {
                $value = 'Image';
            }

            $item->add_meta_data( $label, $value, true );
        }
    }

    // ---------- PRICING ----------
    if ( isset( $custom['pricing']['base'] ) ) {
        $item->add_meta_data(
            'Base Price',
            wc_price( $custom['pricing']['base'] ),
            true
        );
    }

    if ( isset( $custom['pricing']['addon'] ) && $custom['pricing']['addon'] > 0 ) {
        $item->add_meta_data(
            'Customization Price',
            wc_price( $custom['pricing']['addon'] ),
            true
        );
    }
}
