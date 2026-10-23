<?php
/** Load the components and connect them to WordPress. */
class Wpcv_Admin {
	/** Declare each component’s folder, label, class, and enable setting explicitly. */
	public static function components() {
		return array(
			'quick-search' => array(
				'label' => __( 'Quick Search', 'wpcv-admin' ),
				'class' => 'Wpcv_Admin_Quick_Search',
				'enabled_setting' => 'quick_search_enabled',
			),
			'welcome-panel' => array(
				'label' => __( 'Welcome Panel', 'wpcv-admin' ),
				'class' => 'Wpcv_Admin_Welcome_Panel',
				'enabled_setting' => 'welcome_panel_enabled',
			),
			'ui-enhancements' => array(
				'label' => __( 'UI Enhancements', 'wpcv-admin' ),
				'class' => 'Wpcv_Admin_UI_Enhancements',
				'enabled_setting' => 'ui_enhancements_enabled',
			),
		);
	}

	/** Keep configuration available; register runtime hooks only for enabled components. */
	public function run() {
		require_once __DIR__ . '/class-settings-store.php';
		require_once dirname( __DIR__ ) . '/admin/class-settings-page.php';
		foreach ( array_keys( self::components() ) as $directory ) {
			require_once dirname( __DIR__ ) . '/components/' . $directory . '/class-' . $directory . '.php';
		}
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Menu cache maintenance must also run when UI Enhancements is disabled.
		Wpcv_Admin_CiviCRM_Menu::register_cache_hooks();
		$settings = Wpcv_Admin_Settings::get_settings();
		foreach ( self::components() as $component ) {
			$enabled_setting = $component['enabled_setting'];

			// If disabled, skip to the next component without registering its hooks.
			if ( empty( $settings[ $enabled_setting ] ) ) {
				continue;
			}
			$component_class = $component['class'];
			$component_class::register_hooks();
		}
		$page = new Wpcv_Admin_Settings_Page( self::components() );
		$page->register_hooks();
	}

	/** Load translations from this plugin’s languages folder. */
	public function load_textdomain() {
		load_plugin_textdomain( 'wpcv-admin', false, dirname( plugin_basename( dirname( __DIR__ ) . '/wpcv-admin.php' ) ) . '/languages/' );
	}
}
