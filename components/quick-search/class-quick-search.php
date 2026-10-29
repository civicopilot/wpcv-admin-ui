<?php
/** Configure Quick Search and add it to the admin bar. */
require_once __DIR__ . '/class-quick-search-displays.php';

class WPCV_Admin_UI_Quick_Search {

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
		$settings = WPCV_Admin_UI_Settings::get_settings();
		$selection = $settings['quick_search_display'];
		if ( null === $selection ) {
			return WPCV_Admin_UI_Quick_Search_Displays::companion_default();
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
		add_meta_box( 'wpcv-admin-ui-quick-search-settings', __( 'Quick Search', 'wpcv-admin-ui' ), array( __CLASS__, 'render_settings' ), $screen_id, 'normal', 'high' );
	}

	/** The native dropdown needs no extra JavaScript or styles. */
	public static function enqueue_settings() {}

	/** Validate submitted names against currently accessible Contact displays. */
	public static function validate_settings( $post ) {
		if ( ! isset( $post['wpcv_admin_ui_quick_search_submitted'] ) ) { return array(); }
		$value = $post['quick_search_display'] ?? null;
		if ( ! is_string( $value ) ) {
			return new WP_Error( 'quick_search_invalid', __( 'Choose a valid autocomplete display.', 'wpcv-admin-ui' ) );
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
			return new WP_Error( 'quick_search_invalid', __( 'Choose a valid autocomplete display.', 'wpcv-admin-ui' ) );
		}

		// Retain an unchanged saved display even if it is temporarily unavailable.
		$settings = WPCV_Admin_UI_Settings::get_settings();
		if ( $selection === $settings['quick_search_display'] ) { return array(); }

		// Recheck access and entity type rather than trusting the submitted option.
		$autocomplete_displays = WPCV_Admin_UI_Quick_Search_Displays::get( $selection );
		if ( is_wp_error( $autocomplete_displays ) ) { return $autocomplete_displays; }
		if ( ! $autocomplete_displays ) {
			return new WP_Error( 'quick_search_missing', __( 'The selected Contact autocomplete display is unavailable or inaccessible.', 'wpcv-admin-ui' ) );
		}
		return array( 'quick_search_display' => WPCV_Admin_UI_Quick_Search_Displays::selection( reset( $autocomplete_displays ) ) );
	}

	/** List eligible displays and retain an unavailable saved choice visibly. */
	public static function render_settings() {
		$autocomplete_displays = WPCV_Admin_UI_Quick_Search_Displays::get();
		$selection = self::selected_display();
		$selected_display_key = $selection ? WPCV_Admin_UI_Quick_Search_Displays::key( $selection ) : '';
		$unavailable = $selected_display_key && ! is_wp_error( $autocomplete_displays ) && ! isset( $autocomplete_displays[ $selected_display_key ] );
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
			$autocomplete_displays = WPCV_Admin_UI_Quick_Search_Displays::get( $selection );
			if ( is_wp_error( $autocomplete_displays ) || ! $autocomplete_displays ) {
				return;
			}

			// Load CiviCRM autocomplete scripts and styles.
			Civi::resources()
				->addBundle( 'coreResources' )
				->addBundle( 'coreStyles' );
			self::enqueue_styles();
			wp_enqueue_script(
				'wpcv-admin-ui-quick-search',
				plugin_dir_url( __FILE__ ) . 'assets/quick-search.js',
				array(),
				filemtime( __DIR__ . '/assets/quick-search.js' ),
				true
			);
			wp_enqueue_script(
				'wpcv-admin-ui-quick-search-civicrm-menu',
				plugin_dir_url( __FILE__ ) . 'assets/quick-search-civicrm-menu.js',
				array( 'wpcv-admin-ui-quick-search' ),
				filemtime( __DIR__ . '/assets/quick-search-civicrm-menu.js' ),
				true
			);
			wp_add_inline_script( 'wpcv-admin-ui-quick-search', 'const wpcvAdminUIQuickSearch = ' . wp_json_encode( array(
				'savedSearch' => $selection['saved_search'],
				'display' => $selection['display'],
				'contactUrl' => CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1', true, null, false, false, true ),
				'refine' => __( 'Refine your search for more matches.', 'wpcv-admin-ui' ),
				'noResults' => __( 'No contacts found.', 'wpcv-admin-ui' ),
				'error' => __( 'Contact search could not load. Please reload the page.', 'wpcv-admin-ui' ),
			), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
			add_action( 'admin_head', array( __CLASS__, 'restore_civicrm_resources' ), 20 );
			self::$ready = true;
		} catch ( \Throwable $e ) {
			// Keep native CiviCRM search available if resources cannot initialize.
			self::$ready = false;
		}
	}

	/** Keep CiviCRM's resources available when Quick Search needs them. */
	public static function restore_civicrm_resources() {
		// CAU removes this callback at priority 10 when disabling the shortcode button.
		// Restore CiviCRM's original priority of 50.
		add_action( 'admin_head', array( civi_wp(), 'wp_head' ), 50 );
	}

	/** Search styles also belong on admin pages without the welcome panel. */
	private static function enqueue_styles() {
		wp_enqueue_style(
			'wpcv-admin-ui-quick-search',
			plugin_dir_url( __FILE__ ) . 'assets/quick-search.css',
			array(),
			filemtime( __DIR__ . '/assets/quick-search.css' )
		);

		wp_enqueue_style(
			'wpcv-admin-ui-quick-search-civicrm-menu',
			plugin_dir_url( __FILE__ ) . 'assets/quick-search-civicrm-menu.css',
			array( 'wpcv-admin-ui-quick-search' ),
			filemtime( __DIR__ . '/assets/quick-search-civicrm-menu.css' )
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
			'id' => 'wpcv-admin-ui-quick-search',
			'title' => '',
			'meta' => array( 'html' => ob_get_clean() ),
		) );
	}

	/** Render the toolbar input and search status. */
	public static function render() {
		if ( self::$ready ) {
			include __DIR__ . '/templates/control.php';
		}
	}
}
