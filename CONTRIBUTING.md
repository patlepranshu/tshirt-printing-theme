# Contributing Guidelines

Thank you for contributing!

## 🧩 Branch Strategy
Use feature branches:
git checkout -b feature/header-redesign

When completed:
git push origin feature/header-redesign


If working in a team → create a **Pull Request** on GitHub.

---

## 📝 Commit Message Format
Use this format:

type(scope): short description


Types:
- **feat** – new feature  
- **fix** – bug fix  
- **style** – CSS/UI changes  
- **refactor** – code improvement  
- **docs** – README/Docs changes  
- **chore** – configurations, minor updates  

Examples:
feat(product-page): added custom upload field
fix(cart): corrected CSS for checkout button
style(header): improved spacing and layout


---

## 📂 File Structure Rules
- Never modify parent theme files  
- Child theme overrides only  
- Use hooks/filters instead of overriding big templates  
- Keep assets in `/assets/css` and `/assets/js`  

---

## 🧪 Testing
Test each feature:
- On desktop & mobile  
- Using WooCommerce test products  
- With Elementor preview  
