<?php
/** Read and save shared site or network settings. */
class WPCV_Admin_UI_Settings {

	// Network settings are managed centrally; single-site settings locally.

	public static function get_settings_capability() {
		return is_multisite() ? 'manage_network_options' : 'manage_options';
	}

	public static function user_can_manage_settings() {
		return current_user_can( self::get_settings_capability() );
	}

	public static function get_settings_page_url() {
		return is_multisite() ? network_admin_url( 'settings.php?page=wpcv-admin-ui' ) : admin_url( 'options-general.php?page=wpcv-admin-ui' );
	}

	public static function get_option_name() {
		return is_multisite() ? 'wpcv_admin_ui_network' : 'wpcv_admin_ui';
	}

	/** Merge defaults declared by the registered components. */
	public static function get_default_settings() {
		$defaults = array();
		foreach ( WPCV_Admin_UI::components() as $component ) {
			$component_class = $component['class'];
			$defaults = array_merge( $defaults, $component_class::defaults() );
		}
		return $defaults;
	}

	/** Read the appropriate option and fill missing settings with defaults. */
	public static function get_settings() {
		$value = is_multisite() ? get_site_option( self::get_option_name(), array() ) : get_option( self::get_option_name(), array() );
		return wp_parse_args( is_array( $value ) ? $value : array(), self::get_default_settings() );
	}

	public static function update_settings( $settings ) {
		$defaults = self::get_default_settings();

		// Fill missing settings with their defaults.
		$settings = wp_parse_args( $settings, $defaults );

		// Save only keys declared by the components.
		$settings = array_intersect_key( $settings, $defaults );
		return is_multisite() ? update_site_option( self::get_option_name(), $settings ) : update_option( self::get_option_name(), $settings );
	}
}
