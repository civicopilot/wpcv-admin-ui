<?php
/** Load the components and connect them to WordPress. */
class Wpcv_Admin {
	/** Map component folders to the classes that own their behavior. */
	public static function components() {
		return array(
			'welcome-panel' => 'Wpcv_Admin_Welcome_Panel',
			'quick-search' => 'Wpcv_Admin_Quick_Search',
			'ui-enhancements' => 'Wpcv_Admin_UI_Enhancements',
		);
	}

	/** Load every component, then register its hooks and the shared settings page. */
	public function run() {
		require_once __DIR__ . '/class-settings-store.php';
		require_once dirname( __DIR__ ) . '/admin/class-settings-page.php';
		foreach ( array_keys( self::components() ) as $directory ) {
			require_once dirname( __DIR__ ) . '/components/' . $directory . '/class-' . $directory . '.php';
		}
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		foreach ( self::components() as $component ) {
			$component::register_hooks();
		}
		$page = new Wpcv_Admin_Settings_Page( self::components() );
		$page->register_hooks();
	}

	/** Load translations from this plugin’s languages folder. */
	public function load_textdomain() {
		load_plugin_textdomain( 'wpcv-admin', false, dirname( plugin_basename( dirname( __DIR__ ) . '/wpcv-admin.php' ) ) . '/languages/' );
	}
}
