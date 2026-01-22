jQuery(document).ready(function ($) {

  let mediaFrame = null;

  /* ===============================
   * UPLOAD BACK IMAGE
   * =============================== */
  $(document).on('click', '.upload-back-image', function (e) {
    e.preventDefault();

    const $btn = $(this);
    const $wrapper = $btn.closest('.back-image-wrapper');
    const $input = $wrapper.find('.back-image-id');
    const $previewBox = $wrapper.find('.back-image-preview');
    const $removeBtn = $wrapper.find('.remove-back-image');

    // Create media frame (do NOT reuse blindly)
    mediaFrame = wp.media({
      title: 'Select Back Side Image',
      button: { text: 'Use this image' },
      multiple: false
    });

    mediaFrame.on('select', function () {
      const attachment = mediaFrame
        .state()
        .get('selection')
        .first()
        .toJSON();

      // Save image ID
      $input.val(attachment.id).trigger('change');

      // Find or create image
      let $img = $previewBox.find('img');

      if (!$img.length) {
        $img = $('<img />').css({
          maxWidth: '100px',
          display: 'block',
          marginTop: '8px'
        });

        $previewBox.empty().append($img);
      }

      // Delay fixes Woo admin reflow issue
      setTimeout(function () {
        $img.attr('src', attachment.url).show();
      }, 50);

      $removeBtn.show();
    });

    mediaFrame.open();
  });

  /* ===============================
   * REMOVE BACK IMAGE
   * =============================== */
  $(document).on('click', '.remove-back-image', function (e) {
    e.preventDefault();

    const $wrapper = $(this).closest('.back-image-wrapper');

    $wrapper.find('.back-image-id').val('').trigger('change');
    $wrapper.find('.back-image-preview').empty();
    $(this).hide();
  });

});
