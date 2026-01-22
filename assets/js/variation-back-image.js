/* ===============================
 * CART / ORDER CUSTOM DATA
 * =============================== */

window.spCartData = {
  view: 'front',

  front: {
    canvas: null,
    hasText: false,
    hasImage: false
  },

  back: {
    canvas: null,
    hasText: false,
    hasImage: false
  },

  pricing: {
    base: 0,
    addon: 0,
    final: 0
  }
};


jQuery(function ($) {

window.currentVariationId = null;


  /* ===============================
   * COLOR CHANGE (FOUND VARIATION)
   * =============================== */
  $('form.variations_form').on('found_variation', function (e, variation) {

$('.woocommerce-product-gallery')
  .addClass('sp-freeze-img')
  .removeClass('sp-show-img');


    window.currentVariationId = variation.variation_id;

    // ❌ Do NOT reset canvas here
    // Only sync canvas position after image update
    setTimeout(() => {
      if (typeof syncCanvasWithImage === 'function') {
        syncCanvasWithImage();
      }
    }, 100);


// 🔑 Respect current view (front/back) on variation change
if (
  typeof variationImages !== 'undefined' &&
  variationImages[currentVariationId]
) {
  const currentView = window.spCanvasView || 'front';
  const imgUrl = variationImages[currentVariationId][currentView];

  setTimeout(() => {
  if (typeof window.spForceCorrectImage === 'function') {
    window.spForceCorrectImage();
  }
}, 200);

}


  });

  /* ===============================
   * FRONT / BACK BUTTON CLICK
   * =============================== */
  $('body').on('click', '.sp-front-back-btn', function () {

    if (!currentVariationId) {
      alert('Please select a color first');
      return;
    }

    const view = $(this).data('view');

    // Save current canvas state
    if (typeof window.spSaveCanvas === 'function') {
      window.spSaveCanvas();
    }

    window.spCanvasView = view;
    window.spCartData.view = view;

    // Toggle active button
    $('.sp-front-back-btn').removeClass('active');
    $(this).addClass('active');

    // Switch Woo image (front/back)
   if (
  typeof variationImages !== 'undefined' &&
  variationImages[currentVariationId]
) {
  const imgUrl = variationImages[currentVariationId][view];

  if (imgUrl) {
    switchWooImage(imgUrl);
  }

 
}

// Load correct canvas state
if (typeof window.spLoadCanvas === 'function') {
  window.spLoadCanvas(view);
}

// Reset upload UI (optional, but fine)
jQuery('#cust-image-upload').val('');
jQuery('#remove-image').hide();


setTimeout(() => {
  if (typeof window.spForceCorrectImage === 'function') {
    window.spForceCorrectImage();
  }
}, 100);



  });



});

/* ===============================
 * SWITCH WOO IMAGE
 * =============================== */
window.switchWooImage = function (url) {
  const $img = jQuery('.woocommerce-product-gallery__image img');
  if (!$img.length || !url) return;

  // Force reload (avoid cache)
  const newUrl = url + '?t=' + Date.now();

  $img
    .attr('src', newUrl)
    .attr('srcset', newUrl)
    .attr('data-src', newUrl)
    .attr('data-large_image', newUrl);

  // Re-sync canvas AFTER image switch
  setTimeout(() => {
    if (typeof syncCanvasWithImage === 'function') {
      syncCanvasWithImage();
    }
  }, 50);
};



function syncCartPricing() {
  if (typeof baseProductPrice !== 'undefined') {
    window.spCartData.pricing.base = baseProductPrice;
  }

  if (typeof calculateAddonPrice === 'function') {
    window.spCartData.pricing.addon = calculateAddonPrice();
    window.spCartData.pricing.final =
      window.spCartData.pricing.base + window.spCartData.pricing.addon;
  }
}

/*================= SUBMIT HANDLER ================ */
jQuery(function ($) {

  $('form.cart').on('submit', function () {

    // Safety check
    if (typeof window.spFinalCustomization !== 'function') {
      return;
    }

    const customizationData = window.spFinalCustomization();

    // Create / update hidden input
    let $input = $(this).find('input[name="sp_customization"]');

    if (!$input.length) {
      $('<input>', {
        type: 'hidden',
        name: 'sp_customization',
        value: JSON.stringify(customizationData)
      }).appendTo(this);
    } else {
      $input.val(JSON.stringify(customizationData));
    }

  });

});


