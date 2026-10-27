<?php
/** Display Welcome Panel appearance and visibility fields. */
defined( 'ABSPATH' ) || exit;
?>
		<table class="form-table" role="presentation">

			<?php // Shared panel heading. ?>
			<tr><th><label for="wpcv-admin-ui-welcome-panel-heading"><?php esc_html_e( 'Heading', 'wpcv-admin-ui' ); ?></label></th><td><input id="wpcv-admin-ui-welcome-panel-heading" type="text" name="heading" class="regular-text" value="<?php echo esc_attr( WPCV_Admin_UI_Settings::get_settings()['heading'] ); ?>"></td></tr>

			<?php // Optional welcome message. ?>
			<tr><th><label for="wpcv-admin-ui-welcome-panel-subheading"><?php esc_html_e( 'Welcome message', 'wpcv-admin-ui' ); ?></label></th><td><input id="wpcv-admin-ui-welcome-panel-subheading" type="text" name="subheading" class="regular-text" value="<?php echo esc_attr( WPCV_Admin_UI_Welcome_Panel_Settings::get_subheading() ); ?>"></td></tr>

			<?php // Header and icon color; defaults to the theme variable. ?>
			<tr><th><label for="wpcv-admin-ui-welcome-panel-color"><?php esc_html_e( 'Background color', 'wpcv-admin-ui' ); ?></label></th><td><input id="wpcv-admin-ui-welcome-panel-color" type="color" name="background_color" value="<?php echo esc_attr( WPCV_Admin_UI_Welcome_Panel_Settings::get_background_color() ); ?>"></td></tr>

			<?php // Force visibility or respect Screen Options. ?>
			<tr><th><?php esc_html_e( 'Panel visibility', 'wpcv-admin-ui' ); ?></th><td>
				<label><input type="checkbox" name="persist_panel" value="1" <?php checked( ! empty( WPCV_Admin_UI_Settings::get_settings()['persist_panel'] ) ); ?>> <?php esc_html_e( 'Keep welcome panel visible', 'wpcv-admin-ui' ); ?></label>
				<p class="description"><?php esc_html_e( 'When unchecked, users can hide the panel through Screen Options.', 'wpcv-admin-ui' ); ?></p>
			</td></tr>
		</table>
