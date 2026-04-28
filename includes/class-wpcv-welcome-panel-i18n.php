<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://civicopilot.com
 * @since      1.0.0
 *
 * @package    Wpcv_Welcome_Panel
 * @subpackage Wpcv_Welcome_Panel/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wpcv_Welcome_Panel
 * @subpackage Wpcv_Welcome_Panel/includes
 * @author     Andy Burns <andy@andyburns.co>
 */
class Wpcv_Welcome_Panel_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wpcv-welcome-panel',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
