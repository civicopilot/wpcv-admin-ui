<?php
/** Register and display the dashboard Welcome Panel. */
require_once __DIR__ . '/class-afforms.php';
require_once __DIR__ . '/class-cards.php';
require_once __DIR__ . '/class-settings.php';

class WPCV_Admin_UI_Welcome_Panel {

	/** Connect dashboard rendering and asset loading. */
	public static function register_hooks() {
		add_action( 'admin_init', array( __CLASS__, 'remove_core_welcome_panel' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'register_dashboard_widget' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	/** Provide initial panel values without overwriting saved settings. */
	public static function defaults() {
		return array(
			'welcome_panel_enabled' => true,
			'subheading' => __( 'Quick links to manage your site.', 'wpcv-admin-ui' ),
			'background_color' => '#151515',
			'afforms' => array(),
			'persist_panel' => true,
		);
	}

	/** Keep appearance and report configuration together. */
	public static function register_settings( $screen_id ) {
		add_meta_box( 'wpcv-admin-ui-welcome-panel-settings', __( 'Welcome Panel', 'wpcv-admin-ui' ), array( 'WPCV_Admin_UI_Welcome_Panel_Settings', 'render' ), $screen_id, 'normal', 'default' );
	}

	/** Let the panel settings class validate its own submitted fields. */
	public static function validate_settings( $post ) {
		return WPCV_Admin_UI_Welcome_Panel_Settings::validate( $post );
	}

	/** Load panel assets only on a dashboard the user can access. */
	public static function enqueue() {
		$screen = get_current_screen();
		if ( ! $screen || ! is_blog_admin() || 'dashboard' !== $screen->id || ! self::user_can_view_panel() ) {
			return;
		}
		wp_enqueue_style( 'wpcv-admin-ui-welcome-panel', plugin_dir_url( __FILE__ ) . 'assets/welcome-panel.css', array(), filemtime( __DIR__ . '/assets/welcome-panel.css' ) );
		wp_enqueue_script( 'wpcv-admin-ui-welcome-panel', plugin_dir_url( __FILE__ ) . 'assets/welcome-panel.js', array( 'jquery' ), filemtime( __DIR__ . '/assets/welcome-panel.js' ), true );
		WPCV_Admin_UI_Afforms::enqueue_icons();
	}

	/** Load report filtering and ordering assets on the settings screen. */
	public static function enqueue_settings() {
		wp_enqueue_style( 'wpcv-admin-ui-report-configuration', plugin_dir_url( __FILE__ ) . 'assets/report-configuration.css', array(), filemtime( __DIR__ . '/assets/report-configuration.css' ) );
		wp_enqueue_script( 'wpcv-admin-ui-report-configuration', plugin_dir_url( __FILE__ ) . 'assets/report-configuration.js', array( 'jquery', 'jquery-ui-sortable' ), filemtime( __DIR__ . '/assets/report-configuration.js' ), true );
	}


	/** Determine whether the current user should see the custom welcome panel. */
	public static function user_can_view_panel() {
		return current_user_can( 'access_civicrm' ) || ! empty( self::get_sections() );
	}

	/** Remove the core WordPress welcome panel. */
	public static function remove_core_welcome_panel() {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}

	/** Get optional task sections. Providers resolve destinations and check permissions. */
	public static function get_sections() {
		/**
		 * Supply sections before the configured reports.
		 *
		 * @param array $sections Sections with title, description, and items.
		 *                        Items use title, description, url, and optional primary and dashicon values.
		 */
		$sections = apply_filters( 'wpcv_admin_ui/welcome_panel/site_management_links', array() );
		$visible = array();
		foreach ( (array) $sections as $section ) {
			if ( ! is_array( $section ) || empty( $section['title'] ) ) {
				continue;
			}
			$items = array();
			foreach ( (array) ( $section['items'] ?? array() ) as $item ) {
				if ( ! is_array( $item ) || empty( $item['title'] ) || empty( $item['url'] ) || ! esc_url( $item['url'] ) ) {
					continue;
				}
				$items[] = wp_parse_args( $item, array( 'description' => '', 'primary' => false, 'dashicon' => '' ) );
			}
			if ( $items ) {
				$section['items'] = $items;
				$visible[] = wp_parse_args( $section, array( 'description' => '' ) );
			}
		}
		return $visible;
	}

	/** Render the custom welcome panel. */
	public static function render_panel() {
		if ( ! self::user_can_view_panel() ) {
			return;
		}

		// Resolve settings and current-user report data before rendering.
		$user = wp_get_current_user();
		$first_name = trim( (string) $user->first_name );
		/**
		 * Customize the greeting displayed to the current user.
		 *
		 * @param string $heading Personalized greeting text.
		 */
		$heading = apply_filters( 'wpcv_admin_ui/welcome_panel/greeting', sprintf(
			/* translators: 1: User's first or display name. 2: Site title. */
			__( 'Hi %1$s — your %2$s dashboard', 'wpcv-admin-ui' ),
			'' !== $first_name ? $first_name : $user->display_name,
			get_bloginfo( 'name' )
		) );
		$heading = is_string( $heading ) ? $heading : '';
		$sections         = self::get_sections();
		$show_reports     = current_user_can( 'access_civicrm' );
		$cards            = $show_reports ? WPCV_Admin_UI_Cards::get_cards() : array();
		$subheading       = WPCV_Admin_UI_Welcome_Panel_Settings::get_subheading();
		$background_color = WPCV_Admin_UI_Welcome_Panel_Settings::get_background_color();

		// The default hex delegates to the CSS variable; other values override it.
		$background_style = $background_color === WPCV_Admin_UI_Settings::get_default_settings()['background_color']
			? ''
			: '--wpcv-admin-ui-welcome-panel-bg-color: ' . $background_color . ';';
		include __DIR__ . '/templates/panel.php';
	}

	/** Register the hidden dashboard widget used to place the custom welcome panel. */
	public static function register_dashboard_widget() {
		if ( ! self::user_can_view_panel() || ! is_blog_admin() ) {
			return;
		}

		// Register a named shell for Screen Options; JavaScript positions the panel.
		wp_add_dashboard_widget(
			'wpcv_admin_ui_widget',
			__( 'WPCV Admin UI', 'wpcv-admin-ui' ),
			array( __CLASS__, 'render_panel' )
		);
	}
}
