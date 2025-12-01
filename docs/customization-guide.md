# Customization Guide

This guide explains how to modify and maintain the Hello Elementor Child Theme.

---

## 🔧 1. Editing CSS

Add your CSS rules here:

```
/assets/css/custom.css
```

Or enqueue new files inside `includes/enqueue.php`.

---

## 🔧 2. Adding JavaScript

Place scripts in:

```
/assets/js/custom.js
```

Register and enqueue them in:

```php
function child_enqueue_scripts() {
    wp_enqueue_script(
        'child-custom-js',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array(),
        '1.0',
        true
    );
}
```

---

## 🛍️ 3. WooCommerce Customizations

All WooCommerce functions are inside:

```
includes/woo-functions.php
```

Template overrides go inside:

```
woocommerce/
```

---

## 🔧 4. Adding New Hooks

Add WordPress or WooCommerce hooks inside:

```
includes/custom-hooks.php
```

Example:
```php
add_action('wp_footer', function() {
    echo '<!-- Custom footer script -->';
});
```

---

## 🧩 5. Editing Template Parts

Modify template partials inside:

```
templates/global-header.php
```

Call using:
```php
get_template_part('templates/global-header');
```

---

## ✔ Summary

This child theme is structured for easy updates and clean organization.  
Every modification has its own place, making the theme developer-friendly and scalable.

