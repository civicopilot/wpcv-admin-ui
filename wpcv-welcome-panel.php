<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://civicopilot.com
 * @since             1.0.0
 * @package           Wpcv_Welcome_Panel
 *
 * @wordpress-plugin
 * Plugin Name:       WPCV Welcome Panel
 * Plugin URI:        https://civicopilot.com
 * Description:       Adds a configurable quick links welcome panel for WordPress
 * Version:           1.0.0
 * Author:            Andy Burns
 * Author URI:        https://civicopilot.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpcv-welcome-panel
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPCV_WELCOME_PANEL_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpcv-welcome-panel.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpcv_welcome_panel() {

	$plugin = new Wpcv_Welcome_Panel();
	$plugin->run();

}
run_wpcv_welcome_panel();
