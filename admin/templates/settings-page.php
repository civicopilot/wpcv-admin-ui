<?php
/** Display the shared settings form and component metaboxes. */
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap" id="wpcv-admin-ui-settings">
	<h1><?php esc_html_e( 'WPCV Admin UI', 'wpcv-admin-ui' ); ?></h1>
	<?php if ( isset( $_GET['updated'] ) && '1' === $_GET['updated'] ) : ?>
		<div class="notice notice-success"><p><?php esc_html_e( 'Settings saved.', 'wpcv-admin-ui' ); ?></p></div>
	<?php endif; ?>

	<?php // Submit every settings metabox together through the protected save handler. ?>
	<form method="post" action="<?php echo esc_url( $action ); ?>">
		<?php wp_nonce_field( 'wpcv_admin_ui_save_settings', 'wpcv_admin_ui_nonce' ); ?>

		<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
		<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<div id="poststuff" data-wpcv-admin-ui-settings-screen="<?php echo esc_attr( $screen_id ); ?>">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( $screen_id, 'side', null ); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( $screen_id, 'normal', null ); ?>
				</div>
			</div>
			<br class="clear">
		</div>
	</form>
</div>
