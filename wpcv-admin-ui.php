<?php

/**
 * Declare the plugin and start WPCV Admin UI.
 *
 * @link              https://civicopilot.com
 * @since             0.1
 * @package           WPCV_Admin_UI
 *
 * @wordpress-plugin
 * Plugin Name:       WPCV Admin UI
 * Plugin URI:        https://github.com/civicopilot/wpcv-admin-ui
 * Description:       Adds a configurable CiviCRM report welcome panel, quick search, and dashboard enhancements to WordPress
 * Version:           0.2
 * Requires Plugins:  civicrm
 * Author:            Andy Burns
 * Author URI:        https://civicopilot.com/
 * License:           GPL-3.0-only
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wpcv-admin-ui
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin asset and release version.
 */
define( 'WPCV_ADMIN_UI_VERSION', '0.2' );

/** Load the component coordinator. */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpcv-admin-ui.php';

/** Start the components and settings page. */
function run_wpcv_admin_ui() {

	$plugin = new WPCV_Admin_UI();
	$plugin->run();

}
run_wpcv_admin_ui();
