<?php
/** Choose which components run and show their configuration after saving. */
defined( 'ABSPATH' ) || exit;
?>
<input type="hidden" name="wpcv_admin_ui_components_submitted" value="1">

<?php
// Show each component checkbox using its saved enabled state.
foreach ( WPCV_Admin_UI::components() as $component ) :
	$enabled_setting = $component['enabled_setting'];
?>
	<p><label><input type="checkbox" name="<?php echo esc_attr( $enabled_setting ); ?>" value="1" <?php checked( ! empty( $settings[ $enabled_setting ] ) ); ?>> <?php echo esc_html( $component['label'] ); ?></label></p>
<?php endforeach; ?>
<p class="description"><?php esc_html_e( 'Save to show settings for enabled components. Disabled components retain their configuration.', 'wpcv-admin-ui' ); ?></p>

<div id="major-publishing-actions">
	<div id="publishing-action">
		<?php
		// One submit button saves component switches and all visible configuration fields.
		submit_button( __( 'Save settings', 'wpcv-admin-ui' ), 'primary', 'submit', false );
		?>
	</div>
	<div class="clear"></div>
</div>
