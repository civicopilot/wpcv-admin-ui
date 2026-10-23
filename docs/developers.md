# Developer Guide

## Code layout

Components own their hooks, validation,
templates, and assets.

| Want to change… | Where to change it |
| --- | --- |
| Component registration | `includes/class-wpcv-admin.php` |
| Settings storage and capabilities | `includes/class-settings-store.php` |
| Settings page, nonces, and save handling | `admin/class-settings-page.php` |
| Contact autocomplete discovery | `components/quick-search/class-quick-search-displays.php` |
| Report discovery and links | `components/welcome-panel/class-afforms.php` |
| Report selection and appearance settings | `components/welcome-panel/class-settings.php` |
| Ordered report cards | `components/welcome-panel/class-cards.php` |
| Feature hooks and asset loading | `class-quick-search.php`, `class-welcome-panel.php`, and `class-ui-enhancements.php` in their component folders |
| Feature markup, scripts, and styles | Each component’s `templates/` and `assets/` |
