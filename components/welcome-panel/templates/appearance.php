<?php
/** Display Welcome Panel appearance and visibility fields. */
defined( 'ABSPATH' ) || exit;
?>
		<table class="form-table" role="presentation">

			<?php // Shared panel heading. ?>
			<tr><th><label for="wpcv-heading"><?php esc_html_e( 'Heading', 'wpcv-admin' ); ?></label></th><td><input id="wpcv-heading" type="text" name="heading" class="regular-text" value="<?php echo esc_attr( Wpcv_Admin_Settings::get_settings()['heading'] ); ?>"></td></tr>

			<?php // Optional welcome message. ?>
			<tr><th><label for="wpcv-subheading"><?php esc_html_e( 'Welcome message', 'wpcv-admin' ); ?></label></th><td><input id="wpcv-subheading" type="text" name="subheading" class="regular-text" value="<?php echo esc_attr( Wpcv_Admin_Welcome_Panel_Settings::get_subheading() ); ?>"></td></tr>

			<?php // Header and icon color; defaults to the theme variable. ?>
			<tr><th><label for="wpcv-color"><?php esc_html_e( 'Background color', 'wpcv-admin' ); ?></label></th><td><input id="wpcv-color" type="color" name="background_color" value="<?php echo esc_attr( Wpcv_Admin_Welcome_Panel_Settings::get_background_color() ); ?>"></td></tr>

			<?php // Force visibility or respect Screen Options. ?>
			<tr><th><?php esc_html_e( 'Panel visibility', 'wpcv-admin' ); ?></th><td>
				<label><input type="checkbox" name="persist_panel" value="1" <?php checked( ! empty( Wpcv_Admin_Settings::get_settings()['persist_panel'] ) ); ?>> <?php esc_html_e( 'Keep welcome panel visible', 'wpcv-admin' ); ?></label>
				<p class="description"><?php esc_html_e( 'When unchecked, users can hide the panel through Screen Options.', 'wpcv-admin' ); ?></p>
			</td></tr>
		</table>
