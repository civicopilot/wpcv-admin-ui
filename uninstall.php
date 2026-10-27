<?php
/** Delete plugin settings when WordPress uninstalls WPCV Admin UI. */
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( is_multisite() ) {
	delete_site_option( 'wpcv_admin_ui_network' );
} else {
	delete_option( 'wpcv_admin_ui' );
}
