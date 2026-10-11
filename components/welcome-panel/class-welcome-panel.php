<?php
/** Register and display the dashboard Welcome Panel. */
require_once __DIR__ . '/class-afforms.php';
require_once __DIR__ . '/class-cards.php';
require_once __DIR__ . '/class-settings.php';

class Wpcv_Admin_Welcome_Panel {

	/** Connect dashboard rendering and asset loading. */
	public static function register_hooks() {
		add_action( 'admin_init', array( __CLASS__, 'remove_core_welcome_panel' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'register_dashboard_widget' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}
	/** Provide initial panel values without overwriting saved settings. */
	public static function defaults() {
		return array( 'subheading' => __( 'Start with these reports to plan your follow-ups', 'wpcv-admin' ), 'background_color' => '#151515', 'afforms' => array(), 'persist_panel' => true, 'heading' => __( 'Reports', 'wpcv-admin' ) );
	}
	/** Keep appearance and report configuration together. */
	public static function register_settings( $screen_id ) {
		add_meta_box( 'wpcv-panel-settings', __( 'Welcome Panel', 'wpcv-admin' ), array( 'Wpcv_Admin_Welcome_Panel_Settings', 'render' ), $screen_id, 'normal', 'high' );
	}
	/** Let the panel settings class validate its own submitted fields. */
	public static function validate_settings( $post ) {
		return Wpcv_Admin_Welcome_Panel_Settings::validate( $post );
	}
	/** Load panel assets only on a dashboard the user can access. */
	public static function enqueue() {
		$screen = get_current_screen();
		if ( ! $screen || ! is_blog_admin() || 'dashboard' !== $screen->id || ! self::user_can_view_panel() ) {
			return;
		}
		wp_enqueue_style( 'wpcv-welcome-panel', plugin_dir_url( __FILE__ ) . 'assets/welcome-panel.css', array(), filemtime( __DIR__ . '/assets/welcome-panel.css' ) );
		wp_enqueue_script( 'wpcv-welcome-panel', plugin_dir_url( __FILE__ ) . 'assets/welcome-panel.js', array( 'jquery' ), filemtime( __DIR__ . '/assets/welcome-panel.js' ), true );
		Wpcv_Admin_Afforms::enqueue_icons();
	}
	/** Load report filtering and ordering assets on the settings screen. */
	public static function enqueue_settings() {
		wp_enqueue_style( 'wpcv-report-configuration', plugin_dir_url( __FILE__ ) . 'assets/report-configuration.css', array(), filemtime( __DIR__ . '/assets/report-configuration.css' ) );
		wp_enqueue_script( 'wpcv-report-configuration', plugin_dir_url( __FILE__ ) . 'assets/report-configuration.js', array( 'jquery', 'jquery-ui-sortable' ), filemtime( __DIR__ . '/assets/report-configuration.js' ), true );
	}


	/** Determine whether the current user should see the custom welcome panel. */
	public static function user_can_view_panel() {
		return current_user_can( 'access_civicrm' );
	}

	/** Remove the core WordPress welcome panel. */
	public static function remove_core_welcome_panel() {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}

	/** Render the custom welcome panel. */
	public static function render_panel() {
		if ( ! self::user_can_view_panel() ) {
			return;
		}

		// Resolve settings and current-user report data before rendering.
		$settings         = Wpcv_Admin_Settings::get_settings();
		$heading          = is_string( $settings['heading'] ) ? $settings['heading'] : '';
		$cards            = Wpcv_Admin_Cards::get_cards();
		$subheading       = Wpcv_Admin_Welcome_Panel_Settings::get_subheading();
		$background_color = Wpcv_Admin_Welcome_Panel_Settings::get_background_color();
		// The default hex delegates to the CSS variable; other values override it.
		$background_style = $background_color === Wpcv_Admin_Settings::get_default_settings()['background_color']
			? ''
			: '--wpcv-admin-bg-color: ' . $background_color . ';';
		include __DIR__ . '/templates/panel.php';
	}

	/** Register the hidden dashboard widget used to place the custom welcome panel. */
	public static function register_dashboard_widget() {
		if ( ! self::user_can_view_panel() || ! is_blog_admin() ) {
			return;
		}

		// Register a named shell for Screen Options; JavaScript positions the panel.
		wp_add_dashboard_widget(
			'wpcv_admin_widget',
			__( 'WPCV Admin', 'wpcv-admin' ),
			array( __CLASS__, 'render_panel' )
		);
	}
}
