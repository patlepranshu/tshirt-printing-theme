/* ===============================
 * GLOBAL PRICING (SAFE)
 * =============================== */

let baseProductPrice = 0;

const pricingState = {
  front: { text: false, image: false },
  back: { text: false, image: false }
};

const PRICING_RULES = {
  text: 100,
  image: 150
};

function calculateAddonPrice() {
  let addon = 0;

  ['front', 'back'].forEach(view => {
    if (pricingState[view].text) addon += PRICING_RULES.text;
    if (pricingState[view].image) addon += PRICING_RULES.image;
  });

  return addon;
}

function updateFinalPrice() {
  if (!baseProductPrice) return;

  const addon = calculateAddonPrice();
  const finalPrice = baseProductPrice + addon;

  jQuery('#sp-addon-price').text(`₹${addon}`);
  jQuery('#sp-final-price').text(`₹${finalPrice}`);
}

/*========================================= */


jQuery(function ($) {

  /* ===============================
   * GLOBALS
   * =============================== */
  const PRINT_AREA = { width: 280, height: 340 };
  let canvas = null;

  const canvasStore = { front: null, back: null };

const imageStore = { front: null, back: null };


window.spCanvasView = 'front';

  /* ===============================
   * INIT CANVAS
   * =============================== */
  const canvasEl = document.getElementById('tshirt-canvas');
  if (!canvasEl) return;

  canvas = new fabric.Canvas(canvasEl, {
    preserveObjectStacking: true,
    selection: true
  });

  window.spCanvas = canvas;

  /* ===============================
   * SYNC CANVAS WITH IMAGE
   * =============================== */
  function syncCanvasWithImage() {
    const img = document.querySelector('.woocommerce-product-gallery__image img');
    if (!img) return;

    const rect = img.getBoundingClientRect();
    const parent = img.parentElement.getBoundingClientRect();

    canvas.setWidth(rect.width);
    canvas.setHeight(rect.height);

    const el = canvas.getElement();
    el.style.position = 'absolute';
    el.style.left = (rect.left - parent.left) + 'px';
    el.style.top = (rect.top - parent.top) + 'px';

    applyPrintArea();
    canvas.calcOffset();
    canvas.requestRenderAll();
  }

  window.syncCanvasWithImage = syncCanvasWithImage;
  $(window).on('load', syncCanvasWithImage);
  

  /* ===============================
   * PRINT AREA (CLIP PATH)
   * =============================== */
  function applyPrintArea() {
    const left = (canvas.width - PRINT_AREA.width) / 2;
    const top  = (canvas.height - PRINT_AREA.height) / 2;

    const clipRect = new fabric.Rect({
      left,
      top,
      width: PRINT_AREA.width,
      height: PRINT_AREA.height,
      absolutePositioned: true
    });

    canvas.clipPath = clipRect;
  }

  function getCenter() {
    return {
      left: canvas.width / 2,
      top: canvas.height / 2
    };
  }

  /* ===============================
   * SAVE / LOAD VIEW
   * =============================== */
  window.spSaveCanvas = function () {
    canvasStore[window.spCanvasView] = canvas.toJSON();
  };

  window.spLoadCanvas = function (view) {
   canvas.clear();
applyPrintArea();
resetTextControls(); // 🔑 RESET UI FIRST

    if (canvasStore[view]) {
  canvas.loadFromJSON(canvasStore[view], () => {
    applyPrintArea();
    canvas.requestRenderAll();
    syncUI();
    syncTextStyleButtons();
    syncImageUI();
  });
} else {
  canvas.requestRenderAll();
  syncUI();
  syncTextStyleButtons();
  syncImageUI();
}


updateFinalPrice();

  };

  window.canvasStore = canvasStore;

  /* ===============================
   * TEXT
   * =============================== */
  const textarea = $('#cust-text-input');

  function getText() {
    return canvas.getObjects('textbox')[0] || null;
  }

  textarea.on('input', function () {
    let t = getText();
    if (!this.value.trim()) {
      if (t) canvas.remove(t);
jQuery('#text-bold, #text-italic, #text-underline').removeClass('active');

// ✅ PRICING: text removed
    pricingState[window.spCanvasView].text = false;
    updateFinalPrice();

      canvas.requestRenderAll();
      return;
    }

    if (!t) {
      const c = getCenter();
      t = new fabric.Textbox(this.value, {
        ...c,
        originX: 'center',
        originY: 'center',
        width: PRINT_AREA.width,
        textAlign: 'center',
        fontSize: 28,
        fill: '#000'
      });
      canvas.add(t);
    } else {
      t.text = this.value;
    }

    // ✅ PRICING: text added
  pricingState[window.spCanvasView].text = true;
  updateFinalPrice();

    canvas.setActiveObject(t);
t.setCoords();
canvas.requestRenderAll();
syncUI(); // 🔑 THIS LINE FIXES ISSUE #1

  });

  /* TEXT CONTROLS */

function applyFontAfterLoad(textObj, fontFamily) {
  document.fonts.load(`16px ${fontFamily}`).then(() => {
    textObj.set('fontFamily', fontFamily);
    textObj.initDimensions();
    textObj.setCoords();
    canvas.requestRenderAll();
  });
}

$('#sp-font-family').on('change', e => {
  const t = getText();
  if (!t) return;

  const font = e.target.value;
  applyFontAfterLoad(t, font);
});


  $('#sp-font-weight').on('change', e => {
    const t = getText(); if (!t) return;
    t.set('fontWeight', e.target.value);
    t.setCoords();
    canvas.requestRenderAll();
  });

  $('#text-font-size').on('input', e => {
    const t = getText(); if (!t) return;
    t.set('fontSize', parseInt(e.target.value));
    t.setCoords();
    canvas.requestRenderAll();
  });

  $('#text-font-color').on('input', e => {
    const t = getText(); if (!t) return;
    t.set('fill', e.target.value);
    canvas.requestRenderAll();
  });

  $('#text-bold').on('click', () => {
  const t = getText(); if (!t) return;

  t.set('fontWeight', t.fontWeight === 'bold' ? 'normal' : 'bold');
  canvas.requestRenderAll();
  syncUI();
});

$('#text-italic').on('click', () => {
  const t = getText(); if (!t) return;

  t.set('fontStyle', t.fontStyle === 'italic' ? 'normal' : 'italic');
  canvas.requestRenderAll();
  syncUI();
});

$('#text-underline').on('click', () => {
  const t = getText(); if (!t) return;

  t.set('underline', !t.underline);
  canvas.requestRenderAll();
  syncUI();
});

$('.text-align-controls button').on('click', function () {
  const t = getText(); if (!t) return;

  const align = $(this).data('align');
  t.set('textAlign', align);
  canvas.requestRenderAll();
  syncUI();
});


  /* ===============================
   * IMAGE
   * =============================== */
  const removeBtn = $('#remove-image');

  $('#cust-image-upload').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = ev => {
      fabric.Image.fromURL(ev.target.result, img => {
        const c = getCenter();
        img.set({
          ...c,
          originX: 'center',
          originY: 'center',
          scaleX: 0.3,
          scaleY: 0.3
        });

        canvas.add(img);
        canvas.setActiveObject(img);
        canvas.requestRenderAll();
      imageStore[window.spCanvasView] = file.name;

// ✅ PRICING: image added
pricingState[window.spCanvasView].image = true;
updateFinalPrice();

syncImageUI();
      });
    };
    reader.readAsDataURL(file);
  });

removeBtn.on('click', function () {
  canvas.getObjects('image').forEach(o => canvas.remove(o));

  imageStore[window.spCanvasView] = null;

 // ✅ PRICING: image removed
  pricingState[window.spCanvasView].image = false;
  updateFinalPrice();


  syncImageUI();

  canvas.requestRenderAll();
});

function syncUI() {
  const t = getText();

  // Reset all controls first
  resetTextControls();

  if (!t) {
    textarea.val('');
    return;
  }

  // Text fields
  textarea.val(t.text || '');

  $('#sp-font-family').val(t.fontFamily || '');
  $('#sp-font-weight').val(t.fontWeight || '');
  $('#text-font-size').val(t.fontSize || 28);
  $('#text-font-color').val(t.fill || '#000000');

  // Alignment
  if (t.textAlign) {
    $(`.text-align-controls button[data-align="${t.textAlign}"]`)
      .addClass('active');
  }

  // Style buttons
  $('#text-bold').toggleClass('active', t.fontWeight === 'bold');
  $('#text-italic').toggleClass('active', t.fontStyle === 'italic');
  $('#text-underline').toggleClass('active', t.underline === true);

  syncTextStyleButtons();

}

function syncTextStyleButtons() {
  const t = getText();

  // Reset buttons
  jQuery('#text-bold, #text-italic, #text-underline').removeClass('active');

  if (!t) return;

  if (t.fontWeight === 'bold') {
    jQuery('#text-bold').addClass('active');
  }

  if (t.fontStyle === 'italic') {
    jQuery('#text-italic').addClass('active');
  }

  if (t.underline === true) {
    jQuery('#text-underline').addClass('active');
  }
}



function syncImageUI() {
  const view = window.spCanvasView || 'front';
  const name = imageStore[view];

  if (name) {
    $('#image-name').text(name);
    $('#remove-image').show();
  } else {
    $('#image-name').text('');
    $('#remove-image').hide();
    $('#cust-image-upload').val('');
  }
}

window.spForceCorrectImage = function () {
  if (
    typeof variationImages === 'undefined' ||
    !window.currentVariationId
  ) return;

  const view = window.spCanvasView || 'front';
  const img = variationImages[window.currentVariationId][view];

  if (img && typeof switchWooImage === 'function') {
    switchWooImage(img);

    requestAnimationFrame(() => {
      $('.woocommerce-product-gallery')
        .removeClass('sp-freeze-img')
        .addClass('sp-show-img');
    });
  }
};




  /* INIT */
  window.spLoadCanvas('front');


function resetTextControls() {
  $('#sp-font-family').val('');
  $('#sp-font-weight').val('');
  $('#text-font-size').val(28);
  $('#text-font-color').val('#000000');

  $('.text-align-controls button').removeClass('active');
}

});


jQuery(document).on('click', '#sp-upload-btn', function () {
  jQuery('#cust-image-upload').trigger('click');
});


  /* ===============================
   * Pricing update on text/image - Base Price Storage
   * =============================== */

// let baseProductPrice = 0;

// Watch Woo price changes
const observePrice = () => {
  const priceEl = document.querySelector('.summary .price');
  if (!priceEl) return;

  const observer = new MutationObserver(() => {
    const txt = priceEl.textContent.replace(/[^\d.]/g, '');
    baseProductPrice = parseFloat(txt) || baseProductPrice;
    updateFinalPrice();
  });

  observer.observe(priceEl, { childList: true, subtree: true });
};

observePrice();

/*========================*/
jQuery(document).on('found_variation', function (e, variation) {
  baseProductPrice = parseFloat(variation.display_price) || 0;
  updateFinalPrice();
});



window.spFinalCustomization = function () {
  return {
    canvas: window.canvasStore || { front: null, back: null },

    pricing: {
      base: typeof baseProductPrice !== 'undefined' ? baseProductPrice : 0,
      addon: typeof calculateAddonPrice === 'function' ? calculateAddonPrice() : 0,
      final:
        (typeof baseProductPrice !== 'undefined' ? baseProductPrice : 0) +
        (typeof calculateAddonPrice === 'function' ? calculateAddonPrice() : 0)
    },

    flags: {
      front: pricingState.front,
      back: pricingState.back
    }
  };
};
