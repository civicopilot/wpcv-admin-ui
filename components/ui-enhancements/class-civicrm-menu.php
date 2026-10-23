<?php
/** Consolidate CiviCRM navigation without changing stored menu records. */
class Wpcv_Admin_CiviCRM_Menu {
	public static function register_hooks() {
		add_action( 'civicrm_config', array( __CLASS__, 'register_navigation' ) );
	}

	/** Restore cached navigation when the component is disabled, including on other sites. */
	public static function register_cache_hooks() {
		add_action( 'civicrm_initialized', array( __CLASS__, 'refresh_cache' ) );
		add_action( 'updated_option', array( __CLASS__, 'settings_saved' ) );
		add_action( 'updated_site_option', array( __CLASS__, 'settings_saved' ) );
	}

	/** Run after extensions have added their navigation entries. */
	public static function register_navigation() {
		static $registered = false;
		if ( $registered ) { return; }
		$registered = true;
		Civi::dispatcher()->addListener( 'hook_civicrm_navigationMenu', array( __CLASS__, 'navigation' ), -600 );
	}

	public static function navigation( $event ) {
		$settings = Wpcv_Admin_Settings::get_settings();
		if ( ( empty( $settings['ui_enhancements_enabled'] ) || empty( $settings['consolidate_civicrm_menu'] ) ) ) { return; }
		self::consolidate( $event->params );
		self::hide_menu_items( $event->params );
		self::group_contacts( $event->params );
		self::group_mailings( $event->params );
		self::group_surveys( $event->params );
		self::move_support( $event->params );
	}

	/** Move the whole Support branch last under Administer, inheriting its access restriction. */
	public static function move_support( &$menu ) {
		$support = $admin = null;
		foreach ( $menu as $id => $item ) {
			$name = $item['attributes']['name'] ?? '';
			if ( 'Support' === $name ) { $support = $id; }
			if ( 'Administer' === $name ) { $admin = $id; }
		}
		if ( null === $support || null === $admin || empty( $menu[$admin]['attributes']['active'] ) ) { return; }
		$weight = 0;
		foreach ( $menu[$admin]['child'] ?? array() as $child ) {
			$weight = max( $weight, (int) ( $child['attributes']['weight'] ?? 0 ) );
		}
		$item = $menu[$support];
		$item['attributes']['parentID'] = $menu[$admin]['attributes']['navID'];
		$item['attributes']['weight'] = $weight + 1;
		$menu[$admin]['child'][] = $item;
		unset( $menu[$support] );
	}

	/** Move selected Search children, keeping their IDs, permissions and descendants. */
	public static function consolidate( &$menu ) {
		$search = $contacts = null;
		foreach ( $menu as $id => $item ) {
			$name = $item['attributes']['name'] ?? '';
			if ( 'Search' === $name ) { $search = $id; }
			if ( 'Contacts' === $name ) { $contacts = $id; }
		}

		// Never hide Search if its destination is missing or disabled.
		if ( null === $search || null === $contacts || empty( $menu[$contacts]['attributes']['active'] ) ) { return; }
		$names = array( 'Find Contacts', 'Advanced Search', 'Find Activities', 'search_kit' );
		$children = $menu[$contacts]['child'] ?? array();
		$weight = 0;
		foreach ( $children as $item ) {
			$weight = min( $weight, (int) ( $item['attributes']['weight'] ?? 0 ) );
		}
		$weight -= count( $names );
		$moved = false;
		foreach ( $names as $name ) {
			foreach ( $menu[$search]['child'] ?? array() as $id => $item ) {
				if ( $name !== ( $item['attributes']['name'] ?? '' ) ) { continue; }

				// Another extension may already have copied this item into Contacts.
				foreach ( $children as $existing_id => $existing ) {
					if ( $name === ( $existing['attributes']['name'] ?? '' ) ) { unset( $children[$existing_id] ); }
				}
				$item['attributes']['parentID'] = $menu[$contacts]['attributes']['navID'];
				$item['attributes']['weight'] = $weight++;
				$item['attributes']['separator'] = 'search_kit' === $name ? 1 : 0;
				$children[] = $item;
				unset( $menu[$search]['child'][$id] );
				$moved = true;
			}
		}
		if ( ! $moved ) { return; }
		$menu[$contacts]['child'] = $children;
		$menu[$search]['attributes']['active'] = 0;
	}

	/** Hide selected links within their parent menu; keep stored navigation intact. */
	public static function hide_menu_items( &$menu ) {
		// Match parent and child by internal name, not display label (e.g. 'Premiums', not 'Premiums (Thank-you Gifts)').
		$hidden = array(
			'Campaigns' => array( 'New Petition', 'New Campaign', 'Campaign Reports' ),
			'Mailings' => array( 'Mailing Reports' ),
			'Memberships' => array( 'Membership Reports', 'Manage Price Sets' ),
			'Contacts' => array( 'New Activity', 'New Email', 'Contact Reports' ),
			'Contributions' => array( 'Contribution Reports', 'Premiums', 'Manage Price Sets', 'Personal Campaign Pages' ),
			'Events' => array( 'Register Event Participant', 'Event Reports', 'Personal Campaign Pages', 'Event Templates', 'Manage Price Sets' ),
		);
		foreach ( $menu as &$item ) {
			$parent = $item['attributes']['name'] ?? '';
			$names = $hidden[$parent] ?? array();
			if ( 'Mailings' === $parent && ! self::has_sms_provider() ) {
				$names = array_merge( $names, array( 'New SMS', 'Find Mass SMS' ) );
			}
			foreach ( $item['child'] ?? array() as $id => $child ) {
				if ( in_array( $child['attributes']['name'] ?? '', $names, true ) ) {
					$item['child'][$id]['attributes']['active'] = 0;
				}
			}
		}
	}

	/** Group specialist mailing tools and remove configuration links only when Administer provides access. */
	public static function group_mailings( &$menu ) {
		$admin_urls = array();
		foreach ( $menu as $item ) {
			if ( 'Administer' !== ( $item['attributes']['name'] ?? '' ) || ! self::accessible( $item ) ) { continue; }
			foreach ( $item['child'] ?? array() as $component ) {
				if ( 'CiviMail' !== ( $component['attributes']['name'] ?? '' ) || ! self::accessible( $component ) ) { continue; }
				foreach ( $component['child'] ?? array() as $child ) {
					if ( self::accessible( $child ) && ! empty( $child['attributes']['url'] ) ) { $admin_urls[] = $child['attributes']['url']; }
				}
			}
		}
		foreach ( $menu as &$item ) {
			if ( 'Mailings' !== ( $item['attributes']['name'] ?? '' ) ) { continue; }
			foreach ( $item['child'] ?? array() as $id => $child ) {
				if ( in_array( $child['attributes']['name'] ?? '', array( 'Headers, Footers, and Automated Messages', 'Message Templates', 'From Email Addresses' ), true )
					&& in_array( $child['attributes']['url'] ?? '', $admin_urls, true ) ) {
					$item['child'][$id]['attributes']['active'] = 0;
				}
			}
			self::group_children( $item, 'wpcv_ab_testing', __( 'A/B Testing', 'wpcv-admin' ), array( 'New A/B Test', 'Manage A/B Tests' ), 100 );
			self::group_children( $item, 'wpcv_sms', __( 'SMS', 'wpcv-admin' ), array( 'New SMS', 'Find Mass SMS' ), 101 );
		}
	}

	/** Keep survey creation and respondent workflows together under Campaigns. */
	public static function group_surveys( &$menu ) {
		foreach ( $menu as &$item ) {
			if ( 'Campaigns' !== ( $item['attributes']['name'] ?? '' ) ) { continue; }
			self::group_children( $item, 'wpcv_surveys', __( 'Surveys', 'wpcv-admin' ), array( 'New Survey', 'Conduct Survey', 'Reserve Respondents', 'Interview Respondents', 'Release Respondents' ), 2 );

			// Place the dashboard first and Surveys second, ahead of the remaining entries.
			$dashboards = array( 'afsearchCampaignDashboard', 'Dashboard', 'Campaign Dashboard' );
			$weight = 0;
			foreach ( $item['child'] ?? array() as $child ) {
				$name = $child['attributes']['name'] ?? '';
				if ( 'wpcv_surveys' !== $name && ! in_array( $name, $dashboards, true ) ) {
					$weight = min( $weight, (int) ( $child['attributes']['weight'] ?? 0 ) );
				}
			}
			foreach ( $item['child'] ?? array() as $id => $child ) {
				$name = $child['attributes']['name'] ?? '';
				if ( in_array( $name, $dashboards, true ) ) { $item['child'][$id]['attributes']['weight'] = $weight - 2; }
				if ( 'wpcv_surveys' === $name ) { $item['child'][$id]['attributes']['weight'] = $weight - 1; }
			}
		}
	}

	/** Group contact creation and imports at their original menu positions. */
	public static function group_contacts( &$menu ) {
		foreach ( $menu as &$item ) {
			if ( 'Contacts' !== ( $item['attributes']['name'] ?? '' ) ) { continue; }
			self::group_children( $item, 'wpcv_new_contact', __( 'New Contact', 'wpcv-admin' ), array( 'New Individual', 'New Household', 'New Organization' ) );
			self::group_children( $item, 'wpcv_contact_import', __( 'Import Data', 'wpcv-admin' ), array( 'Import Contacts', 'Import Activities', 'Import MultiValued Custom' ) );
		}
	}

	/** Use core permissions so an empty or inaccessible submenu is never created. */
	private static function accessible( $item ) {
		return ! empty( $item['attributes']['active'] ) && CRM_Core_BAO_Navigation::checkPermission( $item['attributes'] );
	}

	/** Reparent existing entries; let CiviCRM assign the new group's navigation ID. */
	private static function group_children( &$parent, $name, $label, $names, $weight = null ) {
		$children = array();
		$original_weights = array();
		foreach ( $names as $child_name ) {
			foreach ( $parent['child'] ?? array() as $id => $child ) {
				if ( $child_name !== ( $child['attributes']['name'] ?? '' ) ) { continue; }
				$original_weights[] = (int) ( $child['attributes']['weight'] ?? 0 );
				unset( $parent['child'][$id] );
				if ( ! self::accessible( $child ) ) { continue; }
				unset( $child['attributes']['parentID'] );
				$child['attributes']['weight'] = count( $children );
				$child['attributes']['separator'] = 0;
				$children[] = $child;
			}
		}
		if ( ! $children ) { return; }

		// Unless explicitly positioned, replace the earliest original entry.
		$weight = $weight ?? min( $original_weights );
		$parent['child'][] = array(
			'attributes' => array( 'name' => $name, 'label' => $label, 'parentID' => $parent['attributes']['navID'], 'url' => null, 'active' => 1, 'weight' => $weight ),
			'child' => $children,
		);
	}

	/** Check provider existence once per request, without requiring administrator permissions. */
	private static function has_sms_provider() {
		static $configured = null;
		if ( null === $configured ) {
			try {
				$configured = (bool) count( \Civi\Api4\SmsProvider::get( false )->addSelect( 'id' )->setLimit( 1 )->execute() );
			} catch ( \Throwable $error ) {
				// Leave SMS links available if provider discovery fails.
				$configured = true;
			}
		}
		return $configured;
	}

	/** Each CiviCRM cache notices network changes on its next initialization. */
	public static function refresh_cache() {
		$settings = Wpcv_Admin_Settings::get_settings();

		// Also invalidate cached menus when the consolidation rules change.
		$state = ( empty( $settings['ui_enhancements_enabled'] ) || empty( $settings['consolidate_civicrm_menu'] ) ) ? 'off' : 'on:' . filemtime( __FILE__ ) . ':' . (int) self::has_sms_provider();
		$cache = Civi::cache( 'navigation' );
		if ( $cache->get( 'wpcv_admin_menu_state' ) === $state ) { return; }
		CRM_Core_BAO_Navigation::flushCache();
		$cache->set( 'wpcv_admin_menu_state', $state );
	}

	/** Refresh the current site's menu immediately after settings are saved. */
	public static function settings_saved( $option ) {
		if ( Wpcv_Admin_Settings::get_option_name() !== $option ) { return; }
		if ( civi_wp()->initialize() ) { self::refresh_cache(); }
	}
}
