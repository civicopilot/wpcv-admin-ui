# Developer Guide

## Code layout

Components own their hooks, validation,
templates, and assets.

| Want to change… | Where to change it |
| --- | --- |
| Component registration | `includes/class-wpcv-admin-ui.php` |
| Settings storage and capabilities | `includes/class-settings-store.php` |
| Settings page, nonces, and save handling | `admin/class-settings-page.php` |
| Contact autocomplete discovery | `components/quick-search/class-quick-search-displays.php` |
| Report discovery and links | `components/welcome-panel/class-afforms.php` |
| Report selection and appearance settings | `components/welcome-panel/class-settings.php` |
| Ordered report cards | `components/welcome-panel/class-cards.php` |
| Feature hooks and asset loading | `class-quick-search.php`, `class-welcome-panel.php`, and `class-ui-enhancements.php` in their component folders |
| Feature markup, scripts, and styles | Each component’s `templates/` and `assets/` |

## Naming

Use `wpcv-admin-ui-` for plugin-owned HTML/CSS names and asset handles,
`wpcv_admin_ui_` for PHP markers, and `wpcvAdminUI` for JavaScript globals.
Classes use `WPCV_Admin_UI_`; local variables and component filenames stay descriptive and concise.

## Welcome panel

Replaces the core welcome panel on site dashboards. Users need CiviCRM access or an available site link; Reports require CiviCRM access.

### Greeting

The greeting uses the user's first or display name and site title. Filter
`wpcv_admin_ui/welcome_panel/greeting` to replace it; return an empty string to hide it.

### Site management links

You can use `wpcv_admin_ui/welcome_panel/site_management_links` to add links above Reports.

Each item requires `title` and `url`. Optional: `description`, `primary`, and `dashicon`.

### Site management example

```php
add_filter( 'wpcv_admin_ui/welcome_panel/site_management_links', function ( $sections ) {
	$items = array();
	$homepage_id = 'page' === get_option( 'show_on_front' ) ? (int) get_option( 'page_on_front' ) : 0;
	if ( $homepage_id && current_user_can( 'edit_post', $homepage_id ) ) {
		$url = get_edit_post_link( $homepage_id, 'raw' );
		if ( $url ) {
			$items[] = array(
				'title'    => __( 'Edit homepage', 'your-text-domain' ),
				'url'      => $url,
				'primary'  => true,
				'dashicon' => 'dashicons-admin-home',
			);
		}
	}

	// Example destinations supplied by your own plugin.
	if ( current_user_can( 'edit_pages' ) ) {
		$items[] = array(
			'title'    => __( 'Site Guide', 'your-text-domain' ),
			'url'      => admin_url( 'index.php?page=site-guide' ),
			'dashicon' => 'dashicons-book',
		);
	}
	if ( current_user_can( 'manage_options' ) ) {
		$items[] = array(
			'title'    => __( 'Change website contact details', 'your-text-domain' ),
			'url'      => admin_url( 'admin.php?page=contact-details' ),
			'dashicon' => 'dashicons-email',
		);
	}
	$officer = get_post_type_object( 'officer' );
	if ( $officer && current_user_can( $officer->cap->edit_posts ) ) {
		$items[] = array(
			'title'    => __( 'Update officers shown on the website', 'your-text-domain' ),
			'url'      => admin_url( 'edit.php?post_type=officer' ),
			'dashicon' => 'dashicons-groups',
		);
	}
	if ( $items ) {
		$items[] = array(
			'title'    => __( 'Help', 'your-text-domain' ),
			'url'      => 'mailto:support@example.org',
			'dashicon' => 'dashicons-sos',
		);
		$sections[] = array(
			'title' => __( 'Manage your site', 'your-text-domain' ),
			'items' => $items,
		);
	}
	return $sections;
} );
```
