<?php

/**
 * Settings helpers for the plugin.
 *
 * @since      1.0.0
 * @package    Wpcv_Welcome_Panel
 * @subpackage Wpcv_Welcome_Panel/includes
 */
class Wpcv_Welcome_Panel_Settings {

	/**
	 * Return the capability required to manage settings.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_settings_capability() {
		return is_multisite() ? 'manage_network_options' : 'manage_options';
	}

	/**
	 * Determine whether the current user can access the plugin settings page.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function user_can_manage_settings() {
		return current_user_can( self::get_settings_capability() );
	}

	/**
	 * Return the settings page URL.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_settings_page_url() {
		if ( is_multisite() ) {
			return network_admin_url( 'settings.php?page=wpcv-welcome-panel' );
		}

		return admin_url( 'options-general.php?page=wpcv-welcome-panel' );
	}

	/**
	 * Return the option key used to store plugin settings.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_option_name() {
		return is_multisite() ? 'wpcv_welcome_panel_network' : 'wpcv_welcome_panel';
	}

	/**
	 * Return the default subheading.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_default_subheading() {
		return __( 'Thank you for your efforts!', 'wpcv-welcome-panel' );
	}

	/**
	 * Return the default panel background color.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_default_background_color() {
		return '#ffcd00';
	}

	/**
	 * Return the default settings payload.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_default_settings() {
		return array(
			'subheading'       => self::get_default_subheading(),
			'background_color' => self::get_default_background_color(),
			'cards'            => array(),
		);
	}

	/**
	 * Return the stored plugin settings.
	 *
	 * Uses a network option on multisite and a normal option on single-site installs.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_settings() {
		$option_name = self::get_option_name();
		$settings    = is_multisite()
			? get_site_option( $option_name, array() )
			: get_option( $option_name, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return wp_parse_args( $settings, self::get_default_settings() );
	}

	/**
	 * Persist the plugin settings.
	 *
	 * Uses a network option on multisite and a normal option on single-site installs.
	 *
	 * @since    1.0.0
	 * @param    array $settings Settings payload.
	 * @return   bool
	 */
	public static function update_settings( $settings ) {
		$option_name = self::get_option_name();
		$settings    = wp_parse_args( $settings, self::get_default_settings() );

		if ( is_multisite() ) {
			return update_site_option( $option_name, $settings );
		}

		return update_option( $option_name, $settings );
	}

	/**
	 * Delete the stored plugin settings.
	 *
	 * Uses a network option on multisite and a normal option on single-site installs.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function delete_settings() {
		$option_name = self::get_option_name();

		if ( is_multisite() ) {
			return delete_site_option( $option_name );
		}

		return delete_option( $option_name );
	}

	/**
	 * Return the stored panel subheading.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_subheading() {
		$settings   = self::get_settings();
		$subheading = isset( $settings['subheading'] ) ? $settings['subheading'] : '';

		if ( ! is_string( $subheading ) || '' === trim( $subheading ) ) {
			return self::get_default_subheading();
		}

		return $subheading;
	}

	/**
	 * Return the stored panel background color.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public static function get_background_color() {
		$settings         = self::get_settings();
		$background_color = isset( $settings['background_color'] ) ? sanitize_hex_color( $settings['background_color'] ) : '';

		if ( empty( $background_color ) ) {
			return self::get_default_background_color();
		}

		return $background_color;
	}

	/**
	 * Persist the panel subheading.
	 *
	 * @since    1.0.0
	 * @param    string $subheading Sanitized subheading text.
	 * @return   bool
	 */
	public static function update_subheading( $subheading ) {
		$settings               = self::get_settings();
		$settings['subheading'] = $subheading;

		return self::update_settings( $settings );
	}

	/**
	 * Persist the panel background color.
	 *
	 * @since    1.0.0
	 * @param    string $background_color Sanitized hex color.
	 * @return   bool
	 */
	public static function update_background_color( $background_color ) {
		$settings                     = self::get_settings();
		$settings['background_color'] = $background_color;

		return self::update_settings( $settings );
	}

	/**
	 * Delete the stored panel subheading.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function delete_subheading() {
		$settings               = self::get_settings();
		$settings['subheading'] = self::get_default_subheading();

		return self::update_settings( $settings );
	}

	/**
	 * Delete the stored panel background color.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function delete_background_color() {
		$settings                     = self::get_settings();
		$settings['background_color'] = self::get_default_background_color();

		return self::update_settings( $settings );
	}

	/**
	 * Return stored welcome panel cards.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_stored_cards() {
		$settings = self::get_settings();
		$cards    = isset( $settings['cards'] ) ? $settings['cards'] : array();

		return is_array( $cards ) ? $cards : array();
	}

	/**
	 * Persist welcome panel cards.
	 *
	 * @since    1.0.0
	 * @param    array $cards Normalized card data.
	 * @return   bool
	 */
	public static function update_stored_cards( $cards ) {
		$settings          = self::get_settings();
		$settings['cards'] = $cards;

		return self::update_settings( $settings );
	}

	/**
	 * Delete stored welcome panel cards.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function delete_stored_cards() {
		$settings          = self::get_settings();
		$settings['cards'] = array();

		return self::update_settings( $settings );
	}
}
