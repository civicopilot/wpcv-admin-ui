<?php

/**
 * Declare the plugin and start WPCV Admin.
 *
 * @link              https://civicopilot.com
 * @since             0.1
 * @package           Wpcv_Admin
 *
 * @wordpress-plugin
 * Plugin Name:       WPCV Admin Enhancements
 * Plugin URI:        https://github.com/civicopilot/wpcv-admin
 * Description:       Adds a configurable CiviCRM report welcome panel, quick search, and dashboard enhancements to WordPress
 * Version:           0.1
 * Requires Plugins:  civicrm
 * Author:            Andy Burns
 * Author URI:        https://civicopilot.com/
 * License:           GPL-3.0-only
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wpcv-admin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin asset and release version.
 */
define( 'WPCV_ADMIN_VERSION', '0.1' );

/** Load the component coordinator. */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpcv-admin.php';

/** Start the components and settings page. */
function run_wpcv_admin() {

	$plugin = new Wpcv_Admin();
	$plugin->run();

}
run_wpcv_admin();
