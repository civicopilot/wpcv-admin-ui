<?php
/** Display the autocomplete selector and its setup guidance. */
defined( 'ABSPATH' ) || exit;

// Explain why an enabled search has no toolbar control yet.
if ( '' === $selected_display_key && ! is_wp_error( $autocomplete_displays ) ) :
?>
	<div class="notice notice-warning inline"><p><?php esc_html_e( 'Select an autocomplete display to show Quick Search in the admin bar.', 'wpcv-admin' ); ?></p></div>
<?php endif; ?>
<p><?php esc_html_e( 'Search CiviCRM contacts from the WordPress admin bar.', 'wpcv-admin' ); ?></p>
<?php if ( is_wp_error( $autocomplete_displays ) ) : ?>
	<div class="notice notice-warning inline"><p><?php echo esc_html( $autocomplete_displays->get_error_message() ); ?></p></div>
<?php endif; ?>
<fieldset <?php disabled( is_wp_error( $autocomplete_displays ) ); ?>>
	<input type="hidden" name="wpcv_quick_search_submitted" value="1">
	<p><label for="wpcv-quick-search-display"><strong><?php esc_html_e( 'Quick Search Autocomplete', 'wpcv-admin' ); ?></strong></label></p>
	<select id="wpcv-quick-search-display" name="quick_search_display" aria-describedby="wpcv-quick-search-help">
		<option value="" <?php selected( '', $selected_display_key ); ?>><?php esc_html_e( '— Select a display —', 'wpcv-admin' ); ?></option>
		<?php if ( $selected_display_key && ( is_wp_error( $autocomplete_displays ) || $unavailable ) ) : ?>
			<option value="<?php echo esc_attr( $selected_display_key ); ?>" selected><?php echo esc_html( sprintf( __( '%1$s (%2$s) — unavailable', 'wpcv-admin' ), $selection['display'], $selection['saved_search'] ) ); ?></option>
		<?php endif; ?>
		<?php foreach ( is_wp_error( $autocomplete_displays ) ? array() : $autocomplete_displays as $key => $display ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $selected_display_key ); ?>><?php echo esc_html( $display['label'] ?: $display['name'] ); ?></option>
		<?php endforeach; ?>
	</select>
</fieldset>
<p id="wpcv-quick-search-help" class="description">
	<?php
	printf(

		/* translators: %s: Link to the companion CiviCRM extension. */
		esc_html__( 'Choose a Contact autocomplete display, or install and enable %s for a turn-key search out of the box.', 'wpcv-admin' ),
		'<a href="' . esc_url( 'https://github.com/civicopilot/wpcvquicksearch' ) . '">WPCV Quick Search</a>'
	);

	if ( is_multisite() ) {
		echo ' ';
		esc_html_e( 'This setting applies to all sites in the network.', 'wpcv-admin' );
	}
	?>
</p>
<?php if ( ! is_wp_error( $autocomplete_displays ) && ! $autocomplete_displays ) : ?>
	<p><?php esc_html_e( 'No accessible Contact autocomplete displays were found. Create one in SearchKit or enable the companion extension.', 'wpcv-admin' ); ?></p>
<?php endif; ?>
<?php if ( $unavailable ) : ?>
	<p><?php esc_html_e( 'The saved display is unavailable on this site. Choose another display or clear the selection before saving.', 'wpcv-admin' ); ?></p>
<?php endif; ?>
