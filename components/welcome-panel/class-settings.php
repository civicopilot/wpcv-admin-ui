<?php
/** Prepare and validate Welcome Panel settings. */
class Wpcv_Admin_Welcome_Panel_Settings {

	/** Load report choices before displaying the configuration fields. */
	public static function render() {
		$forms = Wpcv_Admin_Afforms::get();
		$names = self::get_selected_afforms();
		include __DIR__ . '/templates/settings.php';
	}
	/** Preserve omitted fields and reject invalid reports before saving. */
	public static function validate( $post ) {
		if ( ! isset( $post['wpcv_welcome_panel_submitted'] ) ) {
			return array();
		}
		$names = self::validate_selection( $post['afforms'] ?? array() );
		if ( is_wp_error( $names ) ) {
			return $names;
		}
		return array(
			'heading' => isset( $post['heading'] ) && is_string( $post['heading'] ) ? sanitize_text_field( $post['heading'] ) : '',
			'afforms' => $names,
			'persist_panel' => isset( $post['persist_panel'] ) && '1' === $post['persist_panel'],
			'subheading' => isset( $post['subheading'] ) && is_string( $post['subheading'] ) ? sanitize_text_field( $post['subheading'] ) : '',
			'background_color' => isset( $post['background_color'] ) && is_string( $post['background_color'] ) ? ( sanitize_hex_color( $post['background_color'] ) ?: '#151515' ) : '#151515',
		);
	}

	const MAX_CARDS = 8;
	public static function get_subheading() {
		$settings = Wpcv_Admin_Settings::get_settings();
		return is_string( $settings['subheading'] ) ? $settings['subheading'] : '';
	}
	public static function get_background_color() {
		$settings = Wpcv_Admin_Settings::get_settings();
		return is_string( $settings['background_color'] ) ? ( sanitize_hex_color( $settings['background_color'] ) ?: '#151515' ) : '#151515';
	}
	/** Return unique, nonempty names within the configured limit. */
	public static function get_selected_afforms() {
		$settings = Wpcv_Admin_Settings::get_settings();
		$names = is_array( $settings['afforms'] ) ? array_filter( $settings['afforms'], 'is_string' ) : array();
		return array_slice( array_values( array_unique( array_filter( $names, 'strlen' ) ) ), 0, self::MAX_CARDS );
	}

	/** Validate before saving anything; a failed API request must not erase selections. */
	public static function validate_selection( $names ) {
		// Reject malformed input before querying CiviCRM.
		if ( ! is_array( $names ) || count( $names ) > self::MAX_CARDS ) {
			return new WP_Error( 'invalid_afforms', sprintf( __( 'Select no more than %d forms.', 'wpcv-admin' ), self::MAX_CARDS ) );
		}
		foreach ( $names as $name ) {
			if ( ! is_string( $name ) || '' === $name ) {
				return new WP_Error( 'invalid_afforms', __( 'Invalid form selection.', 'wpcv-admin' ) );
			}
		}
		if ( count( array_unique( $names ) ) !== count( $names ) ) {
			return new WP_Error( 'duplicate_afforms', __( 'Select each form only once.', 'wpcv-admin' ) );
		}
		// Confirm every selected report remains eligible and accessible.
		$available = Wpcv_Admin_Afforms::get( $names );
		if ( is_wp_error( $available ) ) {
			return $available;
		}
		if ( array_diff( $names, array_keys( $available ) ) ) {
			return new WP_Error( 'missing_afforms', __( 'A selected form is unavailable or inaccessible. Remove it and try again.', 'wpcv-admin' ) );
		}
		return array_values( $names );
	}
}
