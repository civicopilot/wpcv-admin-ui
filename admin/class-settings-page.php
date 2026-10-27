<?php
/** Build the settings page and handle authorized saves. */
class WPCV_Admin_UI_Settings_Page {
	private $components;
	private $page_hook;

	public function __construct( $components ) { $this->components = $components; }

	/** Connect the settings screen and save handler to WordPress. */
	public function register_hooks() {
		$plugin = plugin_basename( dirname( __DIR__ ) . '/wpcv-admin-ui.php' );
		add_filter( 'plugin_action_links_' . $plugin, array( $this, 'settings_link' ) );
		add_filter( 'network_admin_plugin_action_links_' . $plugin, array( $this, 'settings_link' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'network_admin_menu', array( $this, 'add_network_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_post_wpcv_admin_ui_save_settings', array( $this, 'save_settings' ) );
		add_action( 'network_admin_edit_wpcv_admin_ui_save_settings', array( $this, 'save_settings' ) );
	}

	/** Add a plugin-row Settings link only for users who can manage settings. */
	public function settings_link( $links ) {
		if ( WPCV_Admin_UI_Settings::user_can_manage_settings() ) {
			$links[] = '<a href="' . esc_url( WPCV_Admin_UI_Settings::get_settings_page_url() ) . '">' . esc_html__( 'Settings', 'wpcv-admin-ui' ) . '</a>';
		}
		return $links;
	}

	/** Add local settings on single-site installations. */
	public function add_admin_page() {
		if ( ! is_multisite() ) {
			$this->page_hook = add_options_page( __( 'WPCV Admin UI', 'wpcv-admin-ui' ), __( 'WPCV Admin UI', 'wpcv-admin-ui' ), 'manage_options', 'wpcv-admin-ui', array( $this, 'render' ) );
		}
	}

	/** Keep multisite settings in Network Admin. */
	public function add_network_admin_page() {
		if ( is_multisite() ) {
			$this->page_hook = add_submenu_page( 'settings.php', __( 'WPCV Admin UI', 'wpcv-admin-ui' ), __( 'WPCV Admin UI', 'wpcv-admin-ui' ), 'manage_network_options', 'wpcv-admin-ui', array( $this, 'render' ) );
		}
	}

	/** Load shared and component settings assets only on this page. */
	public function enqueue( $hook ) {
		if ( ! $this->page_hook || $hook !== $this->page_hook ) { return; }
		wp_enqueue_script( 'wpcv-admin-ui-settings', plugin_dir_url( __FILE__ ) . 'assets/settings-page.js', array( 'jquery', 'postbox' ), filemtime( __DIR__ . '/assets/settings-page.js' ), true );
		$settings = WPCV_Admin_UI_Settings::get_settings();
		foreach ( $this->components as $component ) {
			$enabled_setting = $component['enabled_setting'];
			if ( empty( $settings[ $enabled_setting ] ) ) {
				continue;
			}
			$component_class = $component['class'];
			$component_class::enqueue_settings();
		}
	}

	/** Check access, ask components for their metaboxes, and render the form. */
	public function render() {
		if ( ! WPCV_Admin_UI_Settings::user_can_manage_settings() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcv-admin-ui' ) );
		}
		$screen_id = get_current_screen()->id;
		$settings = WPCV_Admin_UI_Settings::get_settings();
		add_meta_box( 'wpcv-admin-ui-components', __( 'Components', 'wpcv-admin-ui' ), array( $this, 'render_components' ), $screen_id, 'side', 'high' );
		foreach ( $this->components as $component ) {
			$enabled_setting = $component['enabled_setting'];
			if ( empty( $settings[ $enabled_setting ] ) ) {
				continue;
			}
			$component_class = $component['class'];
			$component_class::register_settings( $screen_id );
		}
		$action = is_multisite() ? network_admin_url( 'edit.php?action=wpcv_admin_ui_save_settings' ) : admin_url( 'admin-post.php?action=wpcv_admin_ui_save_settings' );
		include __DIR__ . '/templates/settings-page.php';
	}

	/** Show component switches */
	public function render_components() {
		$settings = WPCV_Admin_UI_Settings::get_settings();
		include __DIR__ . '/templates/components.php';
	}

	/** Validate submitted components before saving; omitted components retain their values. */
	public function save_settings() {
		if ( ! WPCV_Admin_UI_Settings::user_can_manage_settings() ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'wpcv-admin-ui' ) );
		}
		check_admin_referer( 'wpcv_admin_ui_save_settings', 'wpcv_admin_ui_nonce' );
		$post = wp_unslash( $_POST );

		// Start from saved values so disabled or omitted component fields are retained.
		$settings = WPCV_Admin_UI_Settings::get_settings();
		foreach ( $this->components as $component ) {
			$enabled_setting = $component['enabled_setting'];

			// Save switches first; disabling a component preserves its configuration.
			if ( isset( $post['wpcv_admin_ui_components_submitted'] ) ) {
				$settings[ $enabled_setting ] = isset( $post[ $enabled_setting ] ) && '1' === $post[ $enabled_setting ];
			}
			if ( empty( $settings[ $enabled_setting ] ) ) {
				continue;
			}
			$component_class = $component['class'];
			$values = $component_class::validate_settings( $post );
			if ( is_wp_error( $values ) ) {
				wp_die( esc_html( $values->get_error_message() ), '', array( 'response' => 400, 'back_link' => true ) );
			}
			$settings = array_merge( $settings, $values );
		}

		// Write once, after all submitted components pass validation.
		WPCV_Admin_UI_Settings::update_settings( $settings );
		wp_safe_redirect( add_query_arg( 'updated', '1', WPCV_Admin_UI_Settings::get_settings_page_url() ) );
		exit;
	}
}
