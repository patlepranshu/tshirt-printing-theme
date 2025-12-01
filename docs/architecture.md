# Theme Architecture

This document explains the file and folder structure of the **Hello Elementor Child Theme** used for Tshirt Printing.

---

## 📁 Folder Structure

```
hello-elementor-child/
│
├── assets/                → CSS, JS, Images
├── includes/              → All PHP modules
├── templates/             → Custom template parts
├── woocommerce/           → WooCommerce template overrides
├── docs/                  → Documentation
├── functions.php          → Loads modules from includes/
├── style.css              → Theme header + base styles
└── README.md
```

---

## 🧩 `includes/` Files

| File | Purpose |
|------|---------|
| enqueue.php | Load CSS + JS properly |
| woo-functions.php | WooCommerce customizations |
| custom-hooks.php | Custom actions and filters |
| helpers.php | Optional helper functions |

---

## 🎨 WooCommerce Overrides (`woocommerce/`)

All WooCommerce template changes go inside this folder, maintaining the same folder structure as WooCommerce core.

Example:
```
woocommerce/single-product/add-to-cart/custom-button.php
```

---

## 🧱 Templates

Any reusable HTML/PHP block is stored in:
```
templates/global-header.php
```

---

## 🔧 How Files Are Loaded

`functions.php` loads modules:

```php
require_once get_stylesheet_directory() . '/includes/enqueue.php';
require_once get_stylesheet_directory() . '/includes/custom-hooks.php';
require_once get_stylesheet_directory() . '/includes/woo-functions.php';
```

---

## ✔ Summary

This architecture keeps your theme:
- Clean  
- Scalable  
- Easy for other developers to understand  
- Professional for GitHub & portfolio  

