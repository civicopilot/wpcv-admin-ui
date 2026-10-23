<?php
/** Display UI Enhancement controls in WordPress's two-column settings layout. */
defined( 'ABSPATH' ) || exit;
$settings = Wpcv_Admin_Settings::get_settings();

// Reflect the shared selection using content types registered on this site.
$selected = is_array( $settings['activity_post_types'] ) ? $settings['activity_post_types'] : array();
?>
<input type="hidden" name="wpcv_ui_enhancements_submitted" value="1">
<table class="form-table" role="presentation">
	<tr>
		<th scope="row"><?php esc_html_e( 'Activity widget content', 'wpcv-admin' ); ?></th>
		<td>
			<fieldset>
				<legend class="screen-reader-text"><?php esc_html_e( 'Activity widget content', 'wpcv-admin' ); ?></legend>
				<?php foreach ( Wpcv_Admin_UI_Enhancements::post_types() as $name => $type ) : ?>
					<p><label><input type="checkbox" name="activity_post_types[]" value="<?php echo esc_attr( $name ); ?>" <?php checked( in_array( $name, $selected, true ) ); ?>> <?php echo esc_html( $type->labels->name ); ?></label></p>
				<?php endforeach; ?>
				<p class="description"><?php esc_html_e( 'Choose the content types shown in the Activity widget’s recently published and scheduled lists.', 'wpcv-admin' ); ?></p>
			</fieldset>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'CiviCRM Icon menu', 'wpcv-admin' ); ?></th>
		<td>
			<input type="checkbox" id="wpcv-remove-civicrm-icon-menu" name="remove_civicrm_icon_menu" value="1" <?php checked( ! empty( $settings['remove_civicrm_icon_menu'] ) ); ?>>
			<label for="wpcv-remove-civicrm-icon-menu"><?php esc_html_e( 'Remove CiviCRM Icon menu item', 'wpcv-admin' ); ?></label>
			<p class="description"><?php esc_html_e( 'You may not need this menu when CAU’s command palette integration is enabled. Additionally, Log Out and View My Contact are already available in the account menu at the top right of the WordPress admin bar.', 'wpcv-admin' ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'CiviCRM navigation', 'wpcv-admin' ); ?></th>
		<td>
			<label><input type="checkbox" name="consolidate_civicrm_menu" value="1" <?php checked( ! empty( $settings['consolidate_civicrm_menu'] ) ); ?>> <?php esc_html_e( 'Consolidate CiviCRM Menu', 'wpcv-admin' ); ?></label>
			<p class="description"><?php esc_html_e( 'An opinionated menu simplification: remove redundant links, grouped items, and administration menu items moved under Administer.', 'wpcv-admin' ); ?> <a href="<?php echo esc_url( 'https://github.com/civicopilot/wpcv-admin/blob/main/docs/configuration.md#consolidate-civicrm-menu' ); ?>"><?php esc_html_e( 'See menu changes.', 'wpcv-admin' ); ?></a></p>
		</td>
	</tr>
</table>
