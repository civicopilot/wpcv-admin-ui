<?php
/** Configure Quick Search and add it to the admin bar. */
require_once __DIR__ . '/class-quick-search-displays.php';

class Wpcv_Admin_Quick_Search {

	/** Connect search asset loading and admin-bar rendering. */
	public static function register_hooks() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar' ), 11 );
	}

	/** Null means never configured; an empty array means deliberately cleared. */
	public static function defaults() {
		return array( 'quick_search_enabled' => true, 'quick_search_display' => null );
	}

	/** Honor saved choices; suggest the companion display only before configuration. */
	public static function selected_display() {
		$settings = Wpcv_Admin_Settings::get_settings();
		$selection = $settings['quick_search_display'];
		if ( null === $selection ) {
			return Wpcv_Admin_Quick_Search_Displays::companion_default();
		}
		if (
			! is_array( $selection )
			|| ! isset( $selection['saved_search'], $selection['display'] )
			|| ! is_string( $selection['saved_search'] )
			|| ! is_string( $selection['display'] )
		) {
			return array();
		}
		return array( 'saved_search' => $selection['saved_search'], 'display' => $selection['display'] );
	}

	/** Give Quick Search its own configuration metabox. */
	public static function register_settings( $screen_id ) {
		add_meta_box( 'wpcv-quick-search-settings', __( 'WPCV Quick Search', 'wpcv-admin' ), array( __CLASS__, 'render_settings' ), $screen_id, 'normal', 'default' );
	}

	/** The native dropdown needs no extra JavaScript or styles. */
	public static function enqueue_settings() {}

	/** Validate submitted names against currently accessible Contact displays. */
	public static function validate_settings( $post ) {
		if ( ! isset( $post['wpcv_quick_search_submitted'] ) ) { return array(); }
		$value = $post['quick_search_display'] ?? null;
		if ( ! is_string( $value ) ) {
			return new WP_Error( 'quick_search_invalid', __( 'Choose a valid autocomplete display.', 'wpcv-admin' ) );
		}

		// An empty choice leaves the display unconfigured.
		if ( '' === $value ) { return array( 'quick_search_display' => array() ); }
		$selection = json_decode( $value, true );
		if (
			! is_array( $selection )
			|| ! isset( $selection['saved_search'], $selection['display'] )
			|| ! is_string( $selection['saved_search'] )
			|| ! is_string( $selection['display'] )
			|| '' === $selection['saved_search']
			|| '' === $selection['display']
		) {
			return new WP_Error( 'quick_search_invalid', __( 'Choose a valid autocomplete display.', 'wpcv-admin' ) );
		}

		// Retain an unchanged saved display even if it is temporarily unavailable.
		$settings = Wpcv_Admin_Settings::get_settings();
		if ( $selection === $settings['quick_search_display'] ) { return array(); }

		// Recheck access and entity type rather than trusting the submitted option.
		$displays = Wpcv_Admin_Quick_Search_Displays::get( $selection );
		if ( is_wp_error( $displays ) ) { return $displays; }
		if ( ! $displays ) {
			return new WP_Error( 'quick_search_missing', __( 'The selected Contact autocomplete display is unavailable or inaccessible.', 'wpcv-admin' ) );
		}
		return array( 'quick_search_display' => Wpcv_Admin_Quick_Search_Displays::selection( reset( $displays ) ) );
	}

	/** List eligible displays and retain an unavailable saved choice visibly. */
	public static function render_settings() {
		$displays = Wpcv_Admin_Quick_Search_Displays::get();
		$selection = self::selected_display();
		$selected_key = $selection ? Wpcv_Admin_Quick_Search_Displays::key( $selection ) : '';
		$unavailable = $selected_key && ! is_wp_error( $displays ) && ! isset( $displays[ $selected_key ] );
		include __DIR__ . '/templates/settings.php';
	}

	private static $ready = false;

	/** Load resources before CiviCRM prints its admin header. */
	public static function enqueue( $hook ) {
		if ( ! is_blog_admin() || ! current_user_can( 'access_civicrm' ) ) {
			return;
		}
		try {
			$selection = self::selected_display();
			if ( ! $selection ) { return; }
			$displays = Wpcv_Admin_Quick_Search_Displays::get( $selection );
			if ( is_wp_error( $displays ) || ! $displays ) {
				return;
			}

			// CiviCRM supplies Select2, API requests, result formatting and keyboard behavior.
			Civi::resources()->addCoreResources();
			self::enqueue_styles();
			wp_enqueue_script(
				'wpcv-quick-search',
				plugin_dir_url( __FILE__ ) . 'assets/wpcv-quick-search.js',
				array(),
				filemtime( __DIR__ . '/assets/wpcv-quick-search.js' ),
				true
			);
			wp_add_inline_script( 'wpcv-quick-search', 'const wpcvQuickSearch = ' . wp_json_encode( array(
				'savedSearch' => $selection['saved_search'],
				'display' => $selection['display'],
				'contactUrl' => CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1', true, null, false, false, true ),
				'placeholder' => __( 'Search contacts', 'wpcv-admin' ),
				'error' => __( 'Contact search could not load. Please reload the page.', 'wpcv-admin' ),
			), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
			self::$ready = true;
		} catch ( \Throwable $e ) {
			// Keep native CiviCRM search available if resources cannot initialize.
			self::$ready = false;
		}
	}

	/** Search styles also belong on admin pages without the welcome panel. */
	private static function enqueue_styles() {
		wp_enqueue_style(
			'wpcv-quick-search',
			plugin_dir_url( __FILE__ ) . 'assets/wpcv-quick-search.css',
			array(),
			filemtime( __DIR__ . '/assets/wpcv-quick-search.css' )
		);
	}

	/** Insert after the WordPress logo, before My Sites. */
	public static function admin_bar( $bar ) {
		if ( ! self::$ready ) {
			return;
		}
		ob_start();
		self::render();
		$bar->add_node( array(
			'id' => 'wpcv-quick-search',
			'title' => '',
			'meta' => array( 'html' => ob_get_clean() ),
		) );
	}

	/** Render only the input; CiviCRM builds and manages its dropdown. */
	public static function render() {
		if ( self::$ready ) {
			include __DIR__ . '/templates/control.php';
		}
	}
}
