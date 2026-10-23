<?php
/** Configure dashboard Activity and optional admin UI simplifications. */
require_once __DIR__ . '/class-civicrm-menu.php';
class Wpcv_Admin_UI_Enhancements {

	/** Connect Activity queries, result labels, and dashboard assets. */
	public static function register_hooks() {
		Wpcv_Admin_CiviCRM_Menu::register_hooks();
		add_filter( 'the_posts', array( __CLASS__, 'label_posts' ), 10, 2 );
		add_filter( 'dashboard_recent_posts_query_args', array( __CLASS__, 'query_args' ) );

		// Temporary EO workaround: remove this filter activity_is_eo_event_query() below when https://develop.tadpole.cc/plugins/wpcv-event-organiser/-/merge_requests/7 is merged.
		add_filter( 'eventorganiser_is_event_query', array( __CLASS__, 'activity_is_eo_event_query' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_menu_styles' ) );
	}

	/** Default to posts and preserve the existing hidden logo menu. */
	public static function defaults() { return array( 'ui_enhancements_enabled' => true, 'activity_post_types' => array( 'post' ), 'remove_civicrm_icon_menu' => true, 'consolidate_civicrm_menu' => false ); }

	/** Add the UI Enhancements configuration metabox. */
	public static function register_settings( $screen_id ) {
		add_meta_box( 'wpcv-dashboard-enhancements', __( 'UI Enhancements', 'wpcv-admin' ), array( __CLASS__, 'render_settings' ), $screen_id, 'normal', 'default' );
	}

	/** Display the available content types and saved choices. */
	public static function render_settings() { include __DIR__ . '/templates/settings.php'; }

	/** The checkbox settings need no additional assets. */
	public static function enqueue_settings() {}

	/** Preserve omitted settings and accept only available content types. */
	public static function validate_settings( $post ) {
		if ( ! isset( $post['wpcv_ui_enhancements_submitted'] ) ) { return array(); }
		$types = isset( $post['activity_post_types'] ) && is_array( $post['activity_post_types'] ) ? $post['activity_post_types'] : array();
		return array(
			'activity_post_types' => array_values( array_intersect( array_keys( self::post_types() ), array_filter( $types, 'is_string' ) ) ),
			'consolidate_civicrm_menu' => isset( $post['consolidate_civicrm_menu'] ) && '1' === $post['consolidate_civicrm_menu'],
			'remove_civicrm_icon_menu' => isset( $post['remove_civicrm_icon_menu'] ) && '1' === $post['remove_civicrm_icon_menu'],
		);
	}

	/** Hide the logo menu before paint only when the checkbox is enabled. */
	public static function enqueue_menu_styles() {
		$settings = Wpcv_Admin_Settings::get_settings();
		if ( empty( $settings['remove_civicrm_icon_menu'] ) ) { return; }
		wp_enqueue_style(
			'wpcv-civicrm-menu',
			plugin_dir_url( __FILE__ ) . 'assets/civicrm-menu.css',
			array(),
			filemtime( __DIR__ . '/assets/civicrm-menu.css' )
		);
	}

	/** Load Activity labels and styling only on the site dashboard. */
	public static function enqueue() {
		$screen = get_current_screen();
		if ( ! $screen || ! is_blog_admin() || 'dashboard' !== $screen->id ) { return; }
		wp_enqueue_style( 'wpcv-dashboard-activity', plugin_dir_url( __FILE__ ) . 'assets/dashboard-activity.css', array(), filemtime( __DIR__ . '/assets/dashboard-activity.css' ) );
		wp_enqueue_script( 'wpcv-dashboard-activity', plugin_dir_url( __FILE__ ) . 'assets/dashboard-activity.js', array( 'jquery' ), filemtime( __DIR__ . '/assets/dashboard-activity.js' ), true );
	}

	/** Offer public content types with an editor, excluding media. */
	public static function post_types() {
		$types = get_post_types( array( 'public' => true, 'show_ui' => true ), 'objects' );
		unset( $types['attachment'] );
		return $types;
	}

	/** Apply shared selections to this site’s Activity lists. */
	public static function query_args( $args ) {
		// Marker for Activity labels
		$args['wpcv_activity'] = true;
		$settings = Wpcv_Admin_Settings::get_settings();
		$selected = is_array( $settings['activity_post_types'] ) ? $settings['activity_post_types'] : array();
		$available = self::post_types();
		$types = array();

		// Use only locally registered types the user can edit.
		foreach ( $available as $name => $type ) {
			if ( in_array( $name, $selected, true ) && current_user_can( $type->cap->edit_posts ) ) {
				$types[] = $name;
			}
		}
		if ( $types ) {
			$args['post_type'] = $types;
		} else {
			// An empty post_type array would broaden the query instead of disabling it.
			$args['post__in'] = array( 0 );
		}
		return $args;
	}

	/**
	 * Temporary EO workaround: keep Activity queries based on publication dates.
	 * Remove activity_is_eo_event_query() and its eventorganiser_is_event_query
	 * filter registration in register_hooks() once MR !7 is merged to fix mixed post typesquery in activity widget.
	 * https://develop.tadpole.cc/plugins/wpcv-event-organiser/-/merge_requests/7
	 */
	public static function activity_is_eo_event_query( $is_event_query, $query ) {
		// Queries outside the Activity widget keep Event Organiser’s original behavior.
		return $query->get( 'wpcv_activity' ) ? false : $is_event_query;
	}

	/** Pass labels for this widget's results to the dashboard script. */
	public static function label_posts( $posts, $query ) {
		if ( ! $query->get( 'wpcv_activity' ) ) {
			return $posts;
		}
		$labels = array();
		foreach ( $posts as $post ) {
			$type = get_post_type_object( $post->post_type );
			if ( ! $type ) {
				continue;
			}
			$url = current_user_can( 'edit_post', $post->ID ) ? get_edit_post_link( $post->ID, 'raw' ) : get_permalink( $post->ID );
			if ( $url ) {
				$labels[ $url ] = $type->labels->singular_name;
			}
		}

		// Pass labels to the footer script without another database query.
		wp_add_inline_script(
			'wpcv-dashboard-activity',
			'window.wpcvActivityLabels = Object.assign(window.wpcvActivityLabels || {}, ' . wp_json_encode( $labels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ');',
			'before'
		);
		return $posts;
	}

}
