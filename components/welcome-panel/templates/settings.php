<?php
/** Group panel appearance and report settings in one metabox. */
defined( 'ABSPATH' ) || exit;
?>
<?php if ( is_wp_error( $forms ) ) : ?>
	<div class="notice notice-error inline"><p><?php echo esc_html( $forms->get_error_message() ); ?></p><p><?php esc_html_e( 'Welcome Panel settings are unavailable and will be preserved when saving other components.', 'wpcv-admin' ); ?></p></div>
<?php elseif ( ! $forms ) : ?>
	<p><?php esc_html_e( 'No accessible Form Builder reports were found.', 'wpcv-admin' ); ?></p>
<?php endif; ?>
<fieldset <?php disabled( is_wp_error( $forms ) ); ?>>
	<input type="hidden" name="wpcv_welcome_panel_submitted" value="1">
	<?php include __DIR__ . '/appearance.php'; ?>
	<h3><?php esc_html_e( 'Reports Configuration', 'wpcv-admin' ); ?></h3>
	<?php if ( is_multisite() ) : ?><p><?php esc_html_e( 'Report selections are shared across the network and resolved on each site.', 'wpcv-admin' ); ?></p><?php endif; ?>
	<?php include __DIR__ . '/reports.php'; ?>
</fieldset>
